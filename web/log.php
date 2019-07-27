<?php
session_start();
?>
<?php
require 'auxiliar.php';
if (!comprobarLogueado()){
    return;
}
$x = filter_input(INPUT_GET, 'x',FILTER_VALIDATE_INT);
$x = $x?: '';

?>
<html>
    <head>
        <title>Log<?= $x ?>.txt</title>
        <script src="auxiliar.js" charset="utf-8"></script>
        <link rel="shortcut icon" href="arjonatorres.ddns.net/php/mifavicon.png" type="image/x-icon">
        <link rel=StyleSheet href="estilo.css" type="text/css">
        <meta name='viewport' content='user-scalable=0'>
    </head>
    <?php
    $salida = file_get_contents('/home/pi/estado_alarma.txt');
    exec("sudo cp -f /home/pi/log{$x}.txt /var/www/html/php");
    $texto = file_get_contents("log{$x}.txt");
    $texto = nl2br($texto);
    ?>
<body>
	<?php
	cabeceraMenu("Log{$x}.txt", 'config-icon', true);
	?>

	<table class=boton cellspacing="40" border="0" style="margin-bottom:150px;">
    	<tr class=boton>
    		<td colspan=3 class=boton>
    			<p id="estado" align="left" style="font-size:26px; margin:20px"><b><?php echo $texto?></b></p>
    		</td>
    	</tr>
    </table>
	<?php pieMenu($salida); ?>

    <script>
        function volver() {
        	vibrar();
        	location.href="config.php";
        }
    </script>

</body>
</html>
