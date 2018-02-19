<?php

function cabeceraMenu($titulo, $imagen = 'home', $volver = false)
{
    ?>
    <table class=menu border="0">
        <tr class=menu>
            <td class=menu align="center" width="100%"><b><?= $titulo ?></b>
                <img class=menu style="padding-right:50px;" align="right" width="100px" src="imagenes/<?= $volver? 'volver': 'vacio-icon' ?>.png" <?= $volver? 'onClick="volver()"': '' ?>>
                <img class=menu style="padding-left:50px;" align="left" width="110px" src="imagenes/<?= $imagen ?>.png">
            </td>
        </tr>
    </table>
    <?php
}

function pieMenu($salida)
{
    ?>
    <table class=menu style="position:fixed; bottom:0px; right:0px;">
	<tr class=menu>
		<td class=menu align="center">
			<img class=menu width="110px" src="imagenes/home.png" onClick="casa()">
			<img class=menu style="padding-right:50px;" align="right" width="100px" src="imagenes/refresh-icon.png" onClick="refresh()">
			<img id='pie' class=menu style="padding-left:50px;" align="left" width="100px" src="imagenes/unlock<?= $salida ?>.png">
		</td>
	</tr>
	</table>
    <?php
}

function conectar()
{
    try {
        return new PDO('pgsql:host=localhost;dbname=jose', 'jose', 'jose');
    } catch (PDOException $e) {
        ?>
        <h1>Error catastrófico de base de datos: no se puede continuar</h1>
        <?php
        throw $e;
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

function comprobarLogueado()
{
    if (!isset($_SESSION['usuario'])) {
        if(!autoLogin()) {
            header('Location: login.php');
            return false;
        }
    }
    return true;
}

function autoLogin() {
    $pdo = conectar();
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
