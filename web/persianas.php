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
		<title>Persianas</title>
        <script src="auxiliar.js" charset="utf-8"></script>
		<link rel="shortcut icon" href="arjonatorres.ddns.net/php/mifavicon.png" type="image/x-icon">
    	<link rel=StyleSheet href="estilo.css" type="text/css">
	</head>

    <?php
    $salida = file_get_contents('/home/pi/estado_alarma.txt');
    $array = ['p0', 'p1', 'p2', 'p3', 'p4', 'pa0', 'pa1', 'pa2', 'pa3', 'pa4',
    		  'pb0', 'pb1', 'pb2', 'pb3', 'pb4'];
    $cont = 0;
    $cont2 = 0;
    $orden = filter_input(INPUT_POST, 'orden');
    if ($orden !== '') {
    	exec("sudo python /home/pi/$orden.py");
    }
    function crearFormulario(&$cont, $array)
    {
    	for($i = 0; $i < 5; $i++):
    	?>
    		<form id="form<?= $cont ?>" action="persianas.php" method="post" style="margin: 0px">
    			<input type="hidden" name="orden" value="<?= $array[$cont++] ?>">
    		</form>
    	<?php
    	endfor;
    }
    function crearImagenes(&$cont2)
    {
    	for($i = 0; $i < 5; $i++):
    	?>
    		<img class=persianas onClick="enviar(form<?= $cont2++ ?>)" src="imagenes/blind<?= $i ?>-icon.png">
    	<?php
    	endfor;
    }
    ?>
<body>
    <?php
	cabeceraMenu('Persianas', 'blind-icon');
	?>

	<!-- <table class=boton border="0" cellpadding="0" style="padding:40px; padding-bottom:5px;">
    	<tr class=persianas >
    		<td align="center" >
    		<table cellspacing="0" cellpadding="0" align="center" border="0">
    			<tr class=persianas >
    				<td width="35%"></td>
    				<td width="30%" style="background: rgba(0, 0, 0, 1);background: linear-gradient(#777777, #000000);"
    					class=boton onClick=""><p id=eti2 class=persianas>Todas</p></td>
    				<td width="35%"></td>
    			</tr>
    			<tr class=persianas >
    				<td colspan="3" class=luces style="background: rgba(0, 0, 0, 0.5);" onClick="">
    				<?php
    				crearFormulario($cont, $array);
    				crearImagenes($cont2);
    				?>
    				</td>
    			</tr>
    		</table>
    		</td>
    	</tr>
	</table> -->

	<table class=boton border="0" cellpadding="0" style="padding:40px; padding-bottom:5px; padding-top:10px;">
    	<tr class=persianas>
    		<td align="center" >
    		<table cellspacing="0" cellpadding="0" align="center" border="0">
    			<tr class=persianas>
    				<td width="30%"></td>
    				<td width="40%" style="background: rgba(0, 0, 0, 1);background: linear-gradient(#777777, #000000);"
    					class=boton onClick=""><p id=eti2 class=persianas>Planta Alta</p></td>
    				<td width="30%"></td>
    			</tr>
    			<tr class=persianas>
    				<td colspan="3" class=luces style="background: rgba(0, 0, 0, 0.5);" onClick="">
    					<?php
    					crearFormulario($cont, $array);
    					crearImagenes($cont2);
    					?>
    				</td>
    			</tr>
    		</table>
    		</td>
    	</tr>
	</table>

	<!-- <table class=boton border="0" cellpadding="0" style="padding:40px; padding-bottom:5px; padding-top:10px;">
    	<tr class=persianas>
    		<td align="center" >
    		<table cellspacing="0" cellpadding="0" align="center" border="0">
    			<tr class=persianas>
    				<td width="30%"></td>
    				<td width="40%" style="background: rgba(0, 0, 0, 1);background: linear-gradient(#777777, #000000);"
    					class=boton onClick=""><p id=eti2 class=persianas>Planta Baja</p></td>
    				<td width="30%"></td>
    			</tr>
    			<tr class=persianas>
    				<td colspan="3" class=luces style="background: rgba(0, 0, 0, 0.5);" onClick="">
    					<?php
    					crearFormulario($cont, $array);
    					crearImagenes($cont2);
    					?>
    				</td>
    			</tr>
    		</table>
    		</td>
    	</tr>
	</table> -->

	<table class=boton border="0" cellpadding="0" style="padding:40px; padding-bottom:5px; padding-top:10px;">
    	<tr class=persianas>
    		<td align="center" >
    		<table cellspacing="0" cellpadding="0" align="center" border="0">
    			<tr class=persianas>

    				<td style="background: rgba(0, 0, 0, 1);background: linear-gradient(#777777, #000000);
                    -webkit-box-shadow: inset 0px 0px 32px 0px rgba(0,0,0,1);
                		-moz-box-shadow: inset 0px 0px 32px 0px rgba(0,0,0,1);
                		box-shadow: inset 0px 0px 32px 0px rgba(0,0,0,1);
                        border-radius: 40px; font-size:xx-large;" onClick="posicion()"><p id=eti2 class=persianas>&nbsp;Posición&nbsp;</p></td>
    			</tr>
    		</table>
    		</td>
    	</tr>
	</table>
	<?php pieMenu($salida); ?>
    <script>
        function enviar(formulario) {
        	formulario.submit();
        }
        function all0() {
        	vibrar();
        	form0.submit();
        }

        function posicion() {
        	vibrar();
        	location.href="posicion.php";
        }
    </script>

 </body>
</html>
