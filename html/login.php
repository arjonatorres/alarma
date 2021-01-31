<?php
    $titulo = 'Login';
//    $imagen = 'home';
include ('header.php');

$usuario = trim(filter_input(INPUT_POST, 'usuario'));
$password = trim(filter_input(INPUT_POST, 'password'));

if (!empty($_POST)) {
    try {
        comprobarUsuario($usuario);
        comprobarPassword($password);
        comprobarMensaje();
//        $pdo = conectar();
        $fila = buscarUsuario($pdo, $usuario, $password);
        $token = bin2hex(openssl_random_pseudo_bytes(30));
        guardarToken($pdo, $fila['id'], $token);
        $_SESSION['usuario'] = [
            'id' => $fila['id'],
            'usuario' => $fila['usuario'],
            'admin' => $fila['admin']
        ];
        setCookie('SESSTK', $token, time()+31622400);
        header('location: index.php');
        return;
    } catch (Exception $e) {
        ?>
        <h1>Error catastrófico de base de datos: no se puede continuar</h1>
        <?php
    }
}

?>
<table class="boton" cellspacing="40" border="0" >
    <tr class="boton">
    <td class="boton">
        <form action="login.php" method="post" >
            <table width="90%" style="margin: 50px auto">
            <tr><td align="left" style="padding:20px"><label><font size="7" >Nombre Usuario:</td></tr>
            <tr><td style="padding:20px"><input name="usuario" style="width:100%;height:80px; font-size:40px" type="text" required></font></label></td></tr>

            <tr><td align="left" style="padding:20px"><label><font size="7">Password:</td></tr>
            <tr><td style="padding:20px"><input name="password" style="width:100%;height:80px; font-size:40px" type="password" required></label></td></tr>
            <tr><td align="left" style="padding:20px"><label><font size="7"><br></td></tr>
            <tr><td style="padding:20px"><input type="submit" style="width:100%;height:80px; font-size:40px" name="Submit" value="LOGIN"></td></tr>
            </table>
        </form>
    </td>
    </tr >
</table>

<?php
if (isset($_SESSION['mensaje'])): ?>
    <table class=boton cellspacing="40" border="0">
        <tr class=boton>
            <td colspan=3 class=boton>
                <p align="center" style="margin:106px">
                    <b><?= $_SESSION['mensaje'] ?></b>
                </p>
            </td>
        </tr>
    </table>
<?php
unset($_SESSION['mensaje']);
endif;
?>
</div>
</div>

</body>
</html>
