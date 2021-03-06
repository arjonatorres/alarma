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
    $orden = filter_input(INPUT_POST, 'orden', FILTER_VALIDATE_INT);
    $orden = $orden === null? false: $orden;
    if (!empty($_POST) && $orden !== false) {
        if ($orden >= 1 && $orden <= 5){
            exec("sudo python /home/pi/php/ls$orden.py");
        }
    }
    exec("sudo python /home/pi/php/lucesphp.py", $light);

    function filas($light)
    {
        $nombres = ['Salón', 'Cuarto ordenador', 'Dormitorio matrimonio', 'Baño', 'Distribuidor arriba'];
        for($i = 0; $i < 5; $i++): ?>
            <tr class=boton>
                <td class="luces" onClick="accion(<?= $i + 1 ?>)"><p class="luces"><?= $nombres[$i] ?></p>
                <img class="luces" width="150px" src="imagenes/bulb-icon.png"
                    <?= $light[$i] == '01'? 'style="filter:grayscale(0%);float:right;"': '' ?>></td>
            </tr>
        <?php
        endfor;
    }
    ?>
<body>
	<?php
	cabeceraMenu('Luces', 'bulb-icon');
	?>

    <form id="form" action="luces.php" method="post" style="margin: 0px">
        <input id="campo" type="hidden" name="orden">
    </form>
	<table class=boton cellspacing="40" border="0">
    	<?php filas($light); ?>
	</table>
	<?php pieMenu($salida); ?>

    <script>
        function accion(num) {
            campo.value = num;
            vibrar();
            form.submit();
        }
    </script>

 </body>
</html>
