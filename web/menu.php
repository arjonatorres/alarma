<?php
session_start();
?>
<?php
require 'auxiliar.php';
if (!comprobarLogueado()){
    return;
}
?>

<html>
    <head>
        <title>Menu casa</title>
        <script src="auxiliar.js" charset="utf-8"></script>
        <link rel="icon" href="http://arjonatorres.ddns.net/php/imagenes/home.png" type="image/png">
        <link rel=StyleSheet href="estilo.css" type="text/css">
        <meta name='viewport' content='user-scalable=0'>
    </head>

    <?php
    $salida = file_get_contents('/home/pi/estado_alarma.txt');
    ?>

<body>
	<?php
	cabeceraMenu('Casa');
	?>
	<table class=boton cellspacing="40" border="0">
    	<tr class=boton>
    		<td class=boton onClick="persianas()"><img class=boton id="persianas2" src="imagenes/blind-icon.png"><br>Persianas</td>
    		<td class=boton onClick="luces()"><img class=boton src="imagenes/bulb-icon.png"><br>Luces</td>
    		<td class=boton onClick="alarma()"><img class=boton src="imagenes/thief-icon.png"><br>Alarma</td>
    	</tr >
    	<tr class=boton>
    		<td class=boton onClick="placas()"><img class=boton id="placas2" src="imagenes/solar-panel-icon2.png"><br>Placas solares</td>
    		<td class=boton onClick="sensores()"><img class=boton src="imagenes/motion.png"><br>Sensores</td>
    		<td class=boton onClick="config()"><img class=boton src="imagenes/config-icon.png"><br>Configuración</td>
    	</tr>
    	<!---->
    	<tr class=boton style="visibility:hidden">
    		<td class=boton onClick="placastemp()"><img class=boton id="placas3" src="imagenes/solar-panel-icon2.png"><br>Placas solares 2</td>
    		<td class=boton style="visibility:hidden"><img class=boton src="imagenes/home-icon.png"><br>Casa</td>
    		<td class=boton style="visibility:hidden"><img class=boton src="imagenes/thief-icon.png"><br>Alarma</td>
    	</tr>
	</table>

	<?php pieMenu($salida); ?>

	<script>
        var str = Boolean('<?= $_SESSION['usuario']['admin']?>');

        function persianas() {
        	vibrar();
        	location.href="persianas.php";
        }
        function alarma() {
        	vibrar();
        	location.href="alarma.php";
        }
        function placas() {
        	vibrar();
        	location.href="placas.php";
        }
        function placastemp() {
        	vibrar();
        	location.href="placas2.php";
        }
        function luces() {
        	vibrar();
        	location.href="luces.php";
        }
        function sensores() {
        	vibrar();
        	location.href="sensores.php";
        }
        function config() {
        	if (str){
        		vibrar();
        		location.href="config.php";
        	}
        }
    </script>

</body>
</html>
