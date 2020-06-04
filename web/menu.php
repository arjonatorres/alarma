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
<body>
	<?php
	cabeceraMenu('Casa');
	?>
	<table class="boton" cellspacing="40" border="0">
        <tr class="boton">
            <td class="boton go" data-href="persianas"><img class=boton id="persianas2" src="imagenes/blind-icon.png"><br>Persianas</td>
            <td class="boton go" data-href="luces"><img class=boton src="imagenes/bulb-icon.png"><br>Luces</td>
            <td class="boton go" data-href="alarma"><img class=boton src="imagenes/thief-icon.png"><br>Alarma</td>
        </tr >
        <tr class="boton">
            <td class="boton go" data-href="placas"><img class=boton id="placas2" src="imagenes/solar-panel-icon2.png"><br>Placas solares</td>
            <td class="boton go" data-href="sensores"><img class=boton src="imagenes/motion.png"><br>Sensores</td>
            <td class="boton go" data-href="<?= $_SESSION['usuario']['admin']? 'config': '' ?>"><img class=boton src="imagenes/config-icon.png"><br>Configuración</td>
        </tr>
    	<!---->

	</table>

	<?php pieMenu($salida); ?>
</body>
</html>
