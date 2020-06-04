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
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $ps0 = $_POST['p0'];
        $on = $_POST['on'];
        exec("sudo bash /home/pi/horagrabar.sh $ps0 $on");
    }
    exec("sudo bash /home/pi/horaleer.sh", $hor);
    if ($hor[1] == 1) {
        $chk="checked";
    } else {
        $chk="";
    }
    ?>
<body>
	<?php
   	cabeceraMenu('Cron', 'blind-icon', true);
   	?>

	<table class=boton border="0" cellpadding="0" style="padding:40px; padding-bottom:5px;">
    	<tr class=persianas >
    		<td align="center" >
    		<table width="100%" cellspacing="0" cellpadding="0" align="center" border="0">
    			<tr class=persianas >
    				<td ></td>
    				<td class=boton style="background: rgba(0, 0, 0, 1);background: linear-gradient(#777777, #000000); width:40%"
    					 ><p id=eti2 class=persianas>Cron</p></td>
    				<td ></td>
    			</tr>

    		</table>

    		<tr class=boton>
    				<td colspan="3" class=boton style="background: rgba(0, 0, 0, 0.4);" width="100%">
    				<form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>" id="forma">
    				<table style="margin: 50px auto;">
    				<tr>

    					<td><br><font size=8><b>P0: &nbsp;</b></font></td>
    					<td><input type="time" value="<?php echo $hor[0]?>" name="p0" style="width:200px;height:80px; font-size:40"></td>
    					<td><input type="checkbox" name="on" value="1" <?php echo $chk?> style="width:80px;height:80px;margin-left:80px"></td>
    					<td><br><font size=8>On</font></td>
    				</tr>
    				</table><br>
    					<button id="sub1" type="submit" style="width:200px;height:100px; font-size:50;background-color:red;color: white"><b>Grabar</b></button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    					<button id="sub2" type="button" style="width:200px;height:100px; font-size:50;background-color:green;color: white" onClick="leer()"><b>Leer</b></button>

    				</form>
    				</td>
    			</tr>
    		</td>
    	</tr>
	</table>

	<?php pieMenu($salida); ?>

    <script>
        function leer() {
        	vibrar();
        	location.href="cron.php";
        }
        function volver() {
        	vibrar();
        	location.href="confpersianas.php";
        }
    </script>

</body>
</html>
