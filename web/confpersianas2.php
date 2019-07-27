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
    $nombre=$_GET['nombre'];
    $lon=strlen($nombre) * 3.8;
    $cod=$_GET['cod'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $poss1 = dechex($_POST['pos1']);
        $poss2 = dechex($_POST['pos2']);
        $poss3 = dechex($_POST['pos3']);
        $poss4 = dechex($_POST['pos4']);
        exec("sudo python /home/pi/grabar.py $cod $poss1 $poss2 $poss3 $poss4");
    }
	exec("sudo python /home/pi/pos.py $cod", $pos);
?>
<body>
	<?php
   	cabeceraMenu('Conf. persianas', 'blind-icon', true);
   	?>

	<table class=boton border="0" cellpadding="0" style="padding:40px; padding-bottom:5px;">
    	<tr class=persianas >
    		<td align="center">
    		<table width="100%" cellspacing="0" cellpadding="0" align="center" border="0">
    			<tr class=persianas >
    				<td></td>
    				<td class=boton style="background: rgba(0, 0, 0, 1);background: linear-gradient(#777777, #000000); width:<?php echo $lon?>%"
    					 ><p id=eti2 class=persianas><?php echo $nombre?></p></td>
    				<td></td>
    			</tr>
    		</table>
            </td>
    		<tr class=boton>
				<td colspan="3" class=boton style="background: rgba(0, 0, 0, 0.4);" width="100%">
				<form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>" id="forma">
					<p style="margin-left:50px"><br><font size=8><b>Posicion 1: &nbsp;</b></font>
						<input type="number" name="pos1" value="<?php echo $pos[0]?>" style="width:100px;height:80px; font-size:40"></p>
					<p style="margin-left:50px; line-height:0px"><br><font size=8><b>Posicion 2: &nbsp;</b></font>
						<input type="number" name="pos2" value="<?php echo $pos[1]?>" style="width:100px;height:80px; font-size:40"></p>
					<p style="margin-left:50px; line-height:0px"><br><font size=8><b>Posicion 3: &nbsp;</b></font>
						<input type="number" name="pos3" value="<?php echo $pos[2]?>" style="width:100px;height:80px; font-size:40"></p>
					<p style="margin-left:50px; line-height:0px"><br><font size=8><b>Posicion 4: &nbsp;</b></font>
						<input type="number" name="pos4" value="<?php echo $pos[3]?>" style="width:100px;height:80px; font-size:40"></p><br>
					<button id="sub1" type="submit" style="width:200px;height:100px; font-size:50;background-color:red;color: white"><b>Grabar</b></button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<button id="sub2" type="button" style="width:200px;height:100px; font-size:50;background-color:green;color: white" onClick="leer()"><b>Leer</b></button>
				</form>
				</td>
			</tr>
    	</tr>
	</table>
	<?php pieMenu($salida); ?>

    <script>
        function leer() {
        	vibrar();
        	location.href="confpersianas2.php?cod=<?php echo $cod;?>&nombre=<?php echo $nombre;?>";
        }
        function volver() {
        	vibrar();
        	location.href="confpersianas.php";
        }
    </script>

</body>
</html>
