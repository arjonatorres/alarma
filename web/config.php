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
<?php require('cabecera.php'); ?>
<?php
if (!empty($_POST)) {
	if ($_POST['orden']=="0"){
		exec("sudo reboot");
	} else if ($_POST['orden']=="1"){
		exec("sudo python /home/pi/llamar.py");
	}
}
?>
<body>
	<?php
	cabeceraMenu('Configuración', 'config-icon');
	?>
	<table class=boton cellspacing="40" border="0">
    	<tr class=boton>
    		<td class=boton onClick="log1()"><img class=boton id="persianas2" src="imagenes/txt-icon.png"><br>Log.txt</td>
    		<td class=boton onClick="log2()"><img class=boton src="imagenes/txt-icon.png"><br>Log2.txt</td>
    		<td class=boton onClick="persianas()"><img class=boton src="imagenes/blind-icon.png"><br>Conf. persianas</td>
    	</tr >
    	<tr class=boton>
            <form id="form" action="config.php" method="post" style="margin: 0px">
    			<input id="campo" type="hidden" name="orden">
    		</form>
    		<td class=boton onClick="accion(0)"><img class=boton src="imagenes/botonparcialg.png"><br>Reiniciar</td>
    		<td class=boton onClick="accion(1)"><img class=boton src="imagenes/phone-icon.png"><br>Llamar</td>

    	</tr>
    	<!---->
    	<tr class=boton style="visibility:hidden">
    		<td class=boton style="visibility:hidden"><img class=boton src="imagenes/camera-icon.png"><br></td>

    		<td class=boton><img class=boton src="imagenes/home-icon.png"><br>Casa</td>
    		<td class=boton><img class=boton src="imagenes/thief-icon.png"><br>Alarma</td>
    	</tr>
	</table>
	<?php pieMenu($salida); ?>

    <script>
        function log1() {
        	vibrar();
        	location.href="log.php";
        }
        function log2() {
        	vibrar();
        	location.href="log.php?x=2";
        }
        function borrar() {
        	vibrar();
        	location.href="borrar.php";
        }
        function persianas() {
        	vibrar();
        	location.href="confpersianas.php";
        }
        function accion(num) {
            if (num == 0) {
                var con = confirm("¿Reiniciar?");
                if (con === false) {
                    return;
                }
            }
            campo.value = num;
            vibrar();
            form.submit();
        }
    </script>
</body>
</html>
