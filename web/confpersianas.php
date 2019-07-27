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
        <title>Configuracion persianas</title>
        <script src="auxiliar.js" charset="utf-8"></script>
        <link rel="shortcut icon" href="arjonatorres.ddns.net/php/mifavicon.png" type="image/x-icon">
        <link rel=StyleSheet href="estilo.css" type="text/css">
        <meta name='viewport' content='user-scalable=0'>
    </head>

<?php
$salida = file_get_contents('/home/pi/estado_alarma.txt');

?>
<body>
	<?php
 	cabeceraMenu('Conf. persianas', 'blind-icon', true);
 	?>

	<table class=boton cellspacing="40" border="0">
    	<tr class=boton>
    		<td class=boton onClick="ver1()"><img class=boton src="imagenes/blind-icon.png"><br>Salón</td>
    		<td class=boton onClick="ver2()"><img class=boton src="imagenes/blind-icon.png"><br>C. ordenador</td>
    		<td class=boton onClick="ver3()"><img class=boton src="imagenes/blind-icon.png"><br>D. matrimonio</td>
    	</tr >
    	<tr class=boton>
    		<td class=boton onClick="ver4()"><img class=boton src="imagenes/blind-icon.png"><br>Baño</td>
    		<td class=boton onClick="ver5()"><img class=boton src="imagenes/blind-icon.png"><br>D. Derecha</td>
    		<td class=boton onClick="ver6()"><img class=boton src="imagenes/blind-icon.png"><br>D. Izquierda</td>
    	</tr>

    	<tr class=boton>
    		<td class=boton onClick="ver7()"><img class=boton src="imagenes/reloj.png"><br>Cron</td>
    		<td class=boton style="visibility:hidden" onClick="ver8()"><img class=boton src="imagenes/blind-icon.png"><br>D. Derecha</td>
    		<td class=boton style="visibility:hidden" onClick="ver9()"><img class=boton src="imagenes/blind-icon.png"><br>D. Izquierda</td>
    	</tr>
	</table>
	<?php pieMenu($salida); ?>

    <script>
        function ver1() {
        	vibrar();
        	location.href="confpersianas2.php?cod=14&nombre=Salón";
        }
        function ver2() {
        	vibrar();
        	location.href="confpersianas2.php?cod=15&nombre=Cuarto Ordenador";
        }
        function ver3() {
        	vibrar();
        	location.href="confpersianas2.php?cod=16&nombre=Dormitorio Matrimonio";
        }
        function ver4() {
        	vibrar();
        	location.href="confpersianas2.php?cod=17&nombre=Baño";
        }
        function ver5() {
        	vibrar();
        	location.href="confpersianas2.php?cod=18&nombre=Dormitorio Derecha";
        }
        function ver6() {
        	vibrar();
        	location.href="confpersianas2.php?cod=19&nombre=Dormitorio Izquierda";
        }
        function ver7() {
        	vibrar();
        	location.href="cron.php";
        }
        function volver() {
        	vibrar();
        	location.href="config.php";
        }
    </script>

</body>
</html>
