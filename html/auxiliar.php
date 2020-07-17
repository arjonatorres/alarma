<?php

// $dev = (gethostname() != 'raspberrypi');
$pdo = conectar();
$username = '';

function conectar()
{
    try {
        return new PDO('mysql:host=localhost;dbname=bearcontrol', 'bear', 'bear');
    } catch (PDOException $e) { ?>
        <h1>Error catastrófico de base de datos: no se puede continuar</h1>
        <?php throw $e;
    }
}

function comprobarUsuario($usuario)
{
    if ($usuario === '') {
        $_SESSION['mensaje'] = 'El usuario es obligatorio';
    } elseif (mb_strlen($usuario) > 255) {
        $_SESSION['mensaje'] = 'El usuario es demasiado largo';
    } elseif (mb_strpos($usuario, ' ') !== false) {
        $_SESSION['mensaje'] = 'El usuario no puede contener espacios';
    }
}
function comprobarPassword($password)
{
    if ($password === '') {
        $_SESSION['mensaje'] = 'La contraseña es obligatoria';
    }
}

function comprobarMensaje() {
    if (isset($_SESSION['mensaje'])) {
        throw new Exception;
    }
}

function buscarUsuario($pdo, $usuario, $password)
{
    $sent = $pdo->prepare('SELECT *
                             FROM usuarios
                            WHERE usuario = :usuario');
    $sent->execute([':usuario' => $usuario]);
    $fila = $sent->fetch();

    if (empty($fila)) {
        $_SESSION['mensaje'] = 'Usuario/contraseña no validos';
        throw new Exception;
    }

    comprobarIntentos($pdo, $fila);

    if (!password_verify($password, $fila['password'])) {
        incrementaIntentos($pdo, $usuario , $fila['intentos']);
        $_SESSION['mensaje'] = 'Usuario/contraseña no validos';
        throw new Exception;
    }
    return $fila;
}

function comprobarIntentos($pdo, $fila)
{
    if ($fila['intentos'] === 3) {
        $_SESSION['mensaje'] = 'Usuario/contraseña no validos*';
        throw new Exception;
    }
}

function incrementaIntentos($pdo, $usuario, $intentos)
{
    $sent = $pdo->prepare('UPDATE usuarios
                              SET intentos = :intentos + 1
                            WHERE usuario = :usuario');
    $sent->execute([':intentos' => $intentos, ':usuario' => $usuario]);
}

function guardarToken($pdo, $id, $token)
{
    $sent = $pdo->prepare('UPDATE usuarios
                              SET token = :token
                            WHERE id = :id');
    $sent->execute([':token' => $token, ':id' => $id]);
}

function comprobarLogueado($pdo)
{
    if (!isset($_SESSION['usuario'])) {
        if(!autoLogin($pdo)) {
            if ($_SERVER["REQUEST_URI"] != '/login.php') {
            header('Location: login.php');
            }
            return false;
        }
    }
    return true;
}

function autoLogin($pdo) {
    $token = isset($_COOKIE['SESSTK'])? $_COOKIE['SESSTK']: '';
    $sent = $pdo->prepare('SELECT *
                             FROM usuarios
                            WHERE token = :token');
    $sent->execute([':token' => $token]);
    $fila = $sent->fetch();

    if ($fila === false) {
        return false;
    } else {
        $_SESSION['usuario'] = [
            'id' => $fila['id'],
            'usuario' => $fila['usuario'],
            'admin' => $fila['admin']
        ];
        return true;
    }
}

function tempActual($pdo, $fecha = null) {
    $sqlSent = 'SELECT TEMP FROM placas ';

    $params = [];
    if ($fecha) {
        $sqlSent .= "WHERE created_at >= STR_TO_DATE(:fecha, '%Y-%m-%d') 
            AND created_at < STR_TO_DATE(:fecha, '%Y-%m-%d') + INTERVAL '1' DAY ";
        $params[':fecha'] = $fecha;
    }
    $sqlSent .= 'ORDER BY CREATED_AT ';
    if (!$fecha) {
        $sqlSent .= 'DESC LIMIT 1';
    }

    $sent = $pdo->prepare($sqlSent);
    $sent->execute($params);

    if ($fecha) {
        return $sent->fetchAll(PDO::FETCH_COLUMN);
    }else {
        return $sent->fetchColumn();
    }
}

function leerEstadoAlarma($pdo)
{
    $resultado = $pdo->query(
        "SELECT valor
           FROM parametros
           WHERE nombre='estado_alarma'");
    return $resultado->fetchColumn();
}

function escribirEstadoAlarma($pdo, $valor)
{
    $sent = $pdo->prepare("UPDATE parametros
                              SET valor = :valor
                            WHERE nombre='estado_alarma'");
    $sent->execute([':valor' => $valor]);
}

function escribirLog($pdo, $mensaje)
{
    $sent = $pdo->prepare("INSERT INTO logs (mensaje) VALUES (:mensaje)");
    $sent->execute([':mensaje' => $mensaje]);
}

function getSensores($pdo)
{
    $sent = $pdo->prepare('SELECT * FROM sensores ORDER BY pin');
    $sent->execute();
    return $sent->fetchAll(PDO::FETCH_ASSOC);
}

function getBotonEstado($sensores)
{
    $numTotal = count($sensores);
    $numActivos = 0;
    foreach ($sensores as $sensor) {
        if ($sensor['activo']) $numActivos++;
    }
    return $numActivos == $numTotal? 'btn-danger': 'btn-warning';
}

function escribirEstadoSensor($pdo, $pin, $activo)
{
    $sent = $pdo->prepare("UPDATE sensores
                              SET activo = :activo
                            WHERE pin = :pin");
    $sent->execute([':pin' => $pin, ':activo' => $activo]);
}

function ultimaAccionAlarma($pdo) {
    $resultado = $pdo->query(
        "SELECT mensaje, DATE_FORMAT(created_at, \"%H:%i:%s %d/%m/%Y\") as fecha
           FROM logs
           WHERE mensaje LIKE 'Alarma%conectada%'
           ORDER BY created_at DESC
           LIMIT 1");
    $logs = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $mensaje = '';
    foreach ($logs as $log) {
        $men = ucfirst(substr($log['mensaje'], 7));
        $mensaje .= $men . ' - ' . $log['fecha'];
    }
    return $mensaje;
}

function getHabitaciones($pdo, $persianas = false)
{
    $sqlSent = 'SELECT * FROM habitaciones h ';
    if ($persianas) {
        $sqlSent .= 'INNER JOIN persianas p ON h.id = p.habitacion_id ';
    }
    $sqlSent .= 'ORDER BY h.id';
    $sent = $pdo->prepare($sqlSent);
    $sent->execute();
    return $sent->fetchAll(PDO::FETCH_ASSOC);
}

function getPersianas($pdo)
{
    $sqlSent = 'SELECT p.*, a.codigo FROM persianas p ';
    $sqlSent .= 'INNER JOIN arduinos a ON a.id = p.arduino_id';
    $sent = $pdo->prepare($sqlSent);
    $sent->execute();
    $persql = $sent->fetchAll(PDO::FETCH_ASSOC);
    $pers = [];
    foreach ($persql as $per) {
        $pers[$per['habitacion_id']][] = $per;
    }
    return $pers;
}

function getDispositivos($pdo)
{
    $sqlSent = 'SELECT d.*, a.codigo, a.tipo as tipo_arduino FROM dispositivos d ';
    $sqlSent .= 'INNER JOIN arduinos a ON a.id = d.arduino_id ORDER BY switch';
    $sent = $pdo->prepare($sqlSent);
    $sent->execute();
    $dispsql = $sent->fetchAll(PDO::FETCH_ASSOC);
    $disps = [];
    foreach ($dispsql as $disp) {
        $disps[$disp['habitacion_id']][] = $disp;
    }
    return $disps;
}

function getCodigosPersianas($pdo)
{
    $sent = $pdo->prepare("SELECT * FROM parametros WHERE nombre LIKE 'per_%'");
    $sent->execute();
    $params = $sent->fetchAll();
    $codigos = [];
    foreach ($params as $param) {
        $codigos[$param[0]] = ['valor' => $param[1], 'adicional' => $param[2]];
    }

    return $codigos;
}

function getLogs($pdo, $mensaje = '', $date = '', $limit = 100)
{
    $sqlSent = 'SELECT mensaje, DATE_FORMAT(created_at, "%H:%i:%s %d/%m/%Y") as created_at FROM logs ';

    $params = [];

    if ($mensaje != '') {
        $sqlSent .= "WHERE mensaje LIKE CONCAT('%', :mensaje, '%') ";
        $params[':mensaje'] = $mensaje;
    }

    if ($date != '') {
        if ($mensaje != '') {
            $sqlSent .= 'AND ';
        } else {
            $sqlSent .= 'WHERE ';
        }
        $sqlSent .= "created_at >= STR_TO_DATE(:fecha, '%Y-%m-%d') 
            AND created_at < STR_TO_DATE(:fecha, '%Y-%m-%d') + INTERVAL '1' DAY ";
        $params[':fecha'] = $date;
    }

    $sqlSent .= 'ORDER BY id DESC ';
    if ($limit != 0) {
        $sqlSent .= "LIMIT $limit ";
    }
    $sent = $pdo->prepare($sqlSent);
    $sent->execute($params);

    return $sent->fetchAll(PDO::FETCH_ASSOC);
}

function zeropad($num)
{
   return (strlen($num) >= 2) ? $num : ('0' . $num);
}

function grabarPersiana($pdo, $codigo, $pos1, $pos2, $pos3, $pos4)
{
    $sent = $pdo->prepare("UPDATE persianas p
                             JOIN habitaciones h ON p.habitacion_id = h.id
                              SET p.posicion1 = :pos1, p.posicion2 = :pos2, p.posicion3 = :pos3, p.posicion4 = :pos4
                            WHERE h.codigo=:codigo");
    $sent->execute([':codigo' => $codigo, ':pos1' => $pos1, ':pos2' => $pos2, ':pos3' => $pos3, ':pos4' => $pos4]);
}

function enviarHangouts($mensaje)
{
    exec("sudo python /home/bear/py_scripts/enviar_hangouts.py \"$mensaje\"");
}

