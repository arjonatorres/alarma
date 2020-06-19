<?php

$dev = (gethostname() != 'raspberrypi');
$pdo = conectar();
$codigosPersianas = getCodigosPersianas();
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
    $sent->execute([':token' => $token]);
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

function getCodigosPersianas()
{
    return [
        'per_all' => '0x1A',
        'per_central' => '0x1B',
        'per_pbaja' => '0x1D',
        'per_palta' => '0x1E',
        'per_paonorte' => '0x1F',
        'per_paosur' => '0x20',
        'per_switch1' => '0x62',
        'per_switch2' => '0x63',
        'per_subir' => '0x64',
        'per_bajar' => '0x65',
        'per_pos1' => '0x66',
        'per_pos2' => '0x67',
        'per_pos3' => '0x68',
        'per_grabar' => '0x69',
        'per_solicitar' => '0x6A',
        'per_parar' => '0x6B',
        'per_onorte' => '0x6C',
        'per_osur' => '0x6D'
    ];
}