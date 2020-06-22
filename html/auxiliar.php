<?php

$dev = (gethostname() != 'raspberrypi');
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

function tempActual($pdo) {
    $resultado = $pdo->query(
        "SELECT TEMP
           FROM placas
           ORDER BY CREATED_AT DESC
           LIMIT 1");
    return $resultado->fetchColumn();
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
        "SELECT mensaje, created_at
           FROM logs
           WHERE mensaje LIKE 'Alarma%conectada%'
           ORDER BY created_at DESC
           LIMIT 1");
    $logs = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $mensaje = '';
    foreach ($logs as $log) {
        $date = new DateTime($log['created_at']);
        $fecha = $date->format('H:i:s d/m/Y');
        $men = ucfirst(substr($log['mensaje'], 7));
        $mensaje .= $men . ' - ' . $fecha;
    }
    return $mensaje;
}

function getHabitaciones($pdo)
{
    $sent = $pdo->prepare('SELECT * FROM habitaciones ORDER BY id');
    $sent->execute();
    return $sent->fetchAll(PDO::FETCH_ASSOC);
}

function getPersianas($pdo)
{
    $sent = $pdo->prepare('SELECT * FROM persianas');
    $sent->execute();
    $persql = $sent->fetchAll(PDO::FETCH_ASSOC);
    $pers = [];
    foreach ($persql as $per) {
        $pers[$per['habitacion_id']][] = $per;
    }
    return $pers;
}

function getActuadores($pdo)
{
    $sent = $pdo->prepare('SELECT * FROM actuadores ORDER BY switch');
    $sent->execute();
    $actsql = $sent->fetchAll(PDO::FETCH_ASSOC);
    $acts = [];
    foreach ($actsql as $act) {
        $acts[$act['habitacion_id']][] = $act;
    }
    return $acts;
}

function getCodigosPersianas($pdo)
{
    $sent = $pdo->prepare("SELECT * FROM parametros WHERE nombre LIKE 'per_%'");
    $sent->execute();
    $params = $sent->fetchAll();
    $codigos = [];
    foreach ($params as $param) {
        $codigos[$param[0]] = $param[1];
    }

    return $codigos;
}
