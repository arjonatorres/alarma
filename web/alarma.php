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
            $res = 6;
		} else if ($_POST['orden']=="1"){
            $res = 5;
		} else if ($_POST['orden']=="2"){
            $res = 7;
		}
        file_put_contents('/home/pi/estado_alarma.txt', $res);
		exec("sudo python /home/pi/php/lectura.py");
        return;
	}

?>
<body>
    <?php
	cabeceraMenu('Alarma', 'thief-icon');
	?>

	<table class=boton cellspacing="40" border="0">
    	<tr class=boton>
    		<td colspan=3 class=boton>
    			<p id="estado" align="center" style="font-size:80px; margin:106px">
                    <b>&nbsp;</b>
                </p>
    		</td>
    	</tr>
    	<tr class=boton>
            <form id="form" action="alarma.php" method="post" style="margin: 0px">
    			<input id="campo" type="hidden" name="orden">
    		</form>
    		<td class=boton onClick="accion(2)">
                <img id="boton1" src="imagenes/yellow-lock-off.png"><br>Parcial
            </td>
    		<td class=boton onClick="accion(1)">
                <img id="boton2" src="imagenes/red-lock-off.png"><br>Activar
            </td>
    		<td class=boton onClick="accion(0)">
                <img id="boton3" src="imagenes/green-unlock-off.png"><br>Desactivar
            </td>
    	</tr>
	</table>

	<?php pieMenu($salida); ?>
    <script>
        alarmaphp = '<?= $salida ?>';
        alarmaphp = alarmaphp == '6' ? '0': alarmaphp;
        http_request = new XMLHttpRequest();
        cambiar();

        function cambiar() {
            var texto;
            var sty;
            if (alarmaphp == '0') {
                boton1.src='imagenes/yellow-lock-off.png';
                boton2.src='imagenes/red-lock-off.png';
                boton3.src='imagenes/green-unlock-on.png';
                texto = "<b>Desactivada</b>";
                sty = "#6bbb3f";
            } else if (alarmaphp == '1') {
                boton1.src='imagenes/yellow-lock-off.png';
                boton2.src='imagenes/red-lock-on.png';
                boton3.src='imagenes/green-unlock-off.png';
                texto = "<b>Activada total</b>";
                sty = "#f44f41";
            } else if (alarmaphp == '2') {
                boton1.src='imagenes/yellow-lock-on.png';
                boton2.src='imagenes/red-lock-off.png';
                boton3.src='imagenes/green-unlock-off.png';
                texto = "<b>Activada parcial</b>";
                sty = "#EFB31D";
            }
            estado.innerHTML = texto;
            estado.style.color= sty;
            pie.src = `imagenes/unlock${alarmaphp}.png`;
        }

        function accion(num) {
            if (alarmaphp == num) { return; }
            vibrar();
            alarmaphp = num;
            http_request.open("POST", "alarma.php", true);
            http_request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            http_request.send('orden=' + num);
            http_request.onreadystatechange = manejaJSON;
        }
        function manejaJSON() {
            if (http_request.readyState == 4 && http_request.status == 200) {
                cambiar();
            }
        }
    </script>
</body>
</html>
