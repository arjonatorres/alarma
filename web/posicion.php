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
    	<title>Posicion persianas</title>
        <script src="auxiliar.js" charset="utf-8"></script>
    	<link rel="shortcut icon" href="arjonatorres.ddns.net/php/mifavicon.png" type="image/x-icon">
        <link rel=StyleSheet href="estilo.css" type="text/css">
    </head>

    <?php
    $salida = file_get_contents('/home/pi/estado_alarma.txt');
    exec("sudo python /home/pi/posicionphp.py", $pos);

    ?>
<body>
    <?php
 	cabeceraMenu('Posición', 'blind-icon', true);
 	?>
	<table class=boton cellspacing="40" border="0">
    	<tr class=boton>
    		<td class=luces onClick="ver1()"><p id=eti1 class=per>Salón</p>
    		<img class=luces id="po1" width="115px" src="imagenes/vacio-icon.png"></td>
    	</tr>
    	<tr class=boton>
    		<td class=luces onClick="ver2()"><p id=eti2 class=per>Cuarto ordenador</p>
    		<img class=luces id="po2" width="115px" src="imagenes/vacio-icon.png"></td>
    	</tr>
    	<tr class=boton>
    		<td class=luces onClick="ver3()"><p id=eti3 class=per>Dormitorio matrimonio</p>
    		<img class=luces id="po3" width="115px" src="imagenes/vacio-icon.png"></td>
    	</tr>
    	<tr class=boton>
    		<td class=luces onClick="ver4()"><p id=eti4 class=per>Baño</p>
    		<img class=luces id="po4" width="115px" src="imagenes/vacio-icon.png"></td>
    	</tr>
    	<tr class=boton>
    		<td class=luces onClick="ver5()"><p id=eti5 class=per>Dormitorio Derecha</p>
    		<img class=luces id="po5" width="115px" src="imagenes/vacio-icon.png"></td>
    	</tr>
    	<tr class=boton>
    		<td class=luces onClick="ver6()"><p id=eti6 class=per>Dormitorio Izquierda</p>
    		<img class=luces id="po6" width="115px" src="imagenes/vacio-icon.png"></td>
    	</tr>
	</table>
	<?php pieMenu($salida); ?>

    <script>
        var posic1 = '<?php echo $pos[0]?>';
        var posic2 = '<?php echo $pos[1]?>';
        var posic3 = '<?php echo $pos[2]?>';
        var posic4 = '<?php echo $pos[3]?>';
        var posic5 = '<?php echo $pos[4]?>';
        var posic6 = '<?php echo $pos[5]?>';
        var posicphp1 = parseInt(posic1);
        var posicphp2 = parseInt(posic2);
        var posicphp3 = parseInt(posic3);
        var posicphp4 = parseInt(posic4);
        var posicphp5 = parseInt(posic5);
        var posicphp6 = parseInt(posic6);
        var cli1 = 0;
        var cli2 = 0;
        var cli3 = 0;
        var cli4 = 0;
        var cli5 = 0;
        var cli6 = 0;

        function ver1() {
        	vibrar();
        	if (cli1 == 0) {
        		document.getElementById('eti1').innerHTML="<?php echo $pos[0]?>";
        		cli1 = 1;
        	} else if (cli1 == 1) {
        		document.getElementById('eti1').innerHTML="Salón";
        		cli1 = 0;
        	}
        }
        function ver2() {
        	vibrar();
        	if (cli2 == 0) {
        		document.getElementById('eti2').innerHTML="<?php echo $pos[1]?>";
        		cli2 = 1;
        	} else if (cli2 == 1) {
        		document.getElementById('eti2').innerHTML="Cuarto ordenador";
        		cli2 = 0;
        	}
        }
        function ver3() {
        	vibrar();
        	if (cli3 == 0) {
        		document.getElementById('eti3').innerHTML="<?php echo $pos[2]?>";
        		cli3 = 1;
        	} else if (cli3 == 1) {
        		document.getElementById('eti3').innerHTML="Dormitorio matrimonio";
        		cli3 = 0;
        	}
        }
        function ver4() {
        	vibrar();
        	if (cli4 == 0) {
        		document.getElementById('eti4').innerHTML="<?php echo $pos[3]?>";
        		cli4 = 1;
        	} else if (cli4 == 1) {
        		document.getElementById('eti4').innerHTML="Baño";
        		cli4 = 0;
        	}
        }
        function ver5() {
        	vibrar();
        	if (cli5 == 0) {
        		document.getElementById('eti5').innerHTML="<?php echo $pos[4]?>";
        		cli5 = 1;
        	} else if (cli5 == 1) {
        		document.getElementById('eti5').innerHTML="Dormitorio Derecha";
        		cli5 = 0;
        	}
        }
        function ver6() {
        	vibrar();
        	if (cli6 == 0) {
        		document.getElementById('eti6').innerHTML="<?php echo $pos[5]?>";
        		cli6 = 1;
        	} else if (cli6 == 1) {
        		document.getElementById('eti6').innerHTML="Dormitorio Izquierda";
        		cli6 = 0;
        	}
        }

        if (isNaN(posicphp1)) {
        	//document.getElementById('eti1').innerHTML="<?php echo $pos[0]?>";
        	//cli1 = 1;
        	document.getElementById('po1').src="imagenes/blindmov.gif";
        }
        if (isNaN(posicphp2)) {
        	//document.getElementById('eti2').innerHTML="<?php echo $pos[1]?>";
        	//cli2 = 1;
        	document.getElementById('po2').src="imagenes/blindmov.gif";
        }
        if (isNaN(posicphp3)) {
        	//document.getElementById('eti3').innerHTML="<?php echo $pos[2]?>";
        	//cli3 = 1;
        	document.getElementById('po3').src="imagenes/blindmov.gif";
        }
        if (isNaN(posicphp4)) {
        	//document.getElementById('eti4').innerHTML="<?php echo $pos[3]?>";
        	//cli4 = 1;
        	document.getElementById('po4').src="imagenes/blindmov.gif";
        }
        if (isNaN(posicphp5)) {
        	//document.getElementById('eti5').innerHTML="<?php echo $pos[4]?>";
        	//cli5 = 1;
        	document.getElementById('po5').src="imagenes/blindmov.gif";
        }
        if (isNaN(posicphp6)) {
        	//document.getElementById('eti6').innerHTML="<?php echo $pos[5]?>";
        	//cli6 = 1;
        	document.getElementById('po6').src="imagenes/blindmov.gif";
        }

        if (posicphp1 == 0) { // 4, 12, 19, 28 - Salon
        	document.getElementById('po1').src="imagenes/blind0-icon.png";
        } else if (posicphp1 > 0 && posicphp1 <=8) {
        	document.getElementById('po1').src="imagenes/blind1-icon.png";
        } else if (posicphp1 > 8 && posicphp1 <=16) {
        	document.getElementById('po1').src="imagenes/blind2-icon.png";
        } else if (posicphp1 > 16 && posicphp1 <=24) {
        	document.getElementById('po1').src="imagenes/blind3-icon.png";
        } else if (posicphp1 > 24 && posicphp1 <=28) {
        	document.getElementById('po1').src="imagenes/blind4-icon.png";
        }


        if (posicphp2 == 0) { // 5, 10, 18, 25 - Cuarto ordenador
        	document.getElementById('po2').src="imagenes/blind0-icon.png";
        } else if (posicphp2 > 0 && posicphp2 <=8) {
        	document.getElementById('po2').src="imagenes/blind1-icon.png";
        } else if (posicphp2 > 8 && posicphp2 <=15) {
        	document.getElementById('po2').src="imagenes/blind2-icon.png";
        } else if (posicphp2 > 15 && posicphp2 <=22) {
        	document.getElementById('po2').src="imagenes/blind3-icon.png";
        } else if (posicphp2 > 22 && posicphp2 <=25) {
        	document.getElementById('po2').src="imagenes/blind4-icon.png";
        }

        if (posicphp3 == 0) { // 7, 20, 28, 35 - Dormitorio matrimonio
        	document.getElementById('po3').src="imagenes/blind0-icon.png";
        } else if (posicphp3 > 0 && posicphp3 <=13) {
        	document.getElementById('po3').src="imagenes/blind1-icon.png";
        } else if (posicphp3 > 13 && posicphp3 <=24) {
        	document.getElementById('po3').src="imagenes/blind2-icon.png";
        } else if (posicphp3 > 24 && posicphp3 <=32) {
        	document.getElementById('po3').src="imagenes/blind3-icon.png";
        } else if (posicphp3 > 32 && posicphp3 <=35) {
        	document.getElementById('po3').src="imagenes/blind4-icon.png";
        }

        if (posicphp4 == 0) { // 5, 12, 18, 23 - Baño
        	document.getElementById('po4').src="imagenes/blind0-icon.png";
        } else if (posicphp4 > 0 && posicphp4 <=9) {
        	document.getElementById('po4').src="imagenes/blind1-icon.png";
        } else if (posicphp4 > 9 && posicphp4 <=15) {
        	document.getElementById('po4').src="imagenes/blind2-icon.png";
        } else if (posicphp4 > 15 && posicphp4 <=21) {
        	document.getElementById('po4').src="imagenes/blind3-icon.png";
        } else if (posicphp4 > 21 && posicphp4 <=23) {
        	document.getElementById('po4').src="imagenes/blind4-icon.png";
        }

        if (posicphp5 == 0) { // 5, 12, 19, 27 - Dormitorio derecha
        	document.getElementById('po5').src="imagenes/blind0-icon.png";
        } else if (posicphp5 > 0 && posicphp5 <=9) {
        	document.getElementById('po5').src="imagenes/blind1-icon.png";
        } else if (posicphp5 > 9 && posicphp5 <=16) {
        	document.getElementById('po5').src="imagenes/blind2-icon.png";
        } else if (posicphp5 > 16 && posicphp5 <=24) {
        	document.getElementById('po5').src="imagenes/blind3-icon.png";
        } else if (posicphp5 > 24 && posicphp5 <=27) {
        	document.getElementById('po5').src="imagenes/blind4-icon.png";
        }

        if (posicphp6 == 0) { // 7, 18, 28, 37 - Dormitorio izquierda
        	document.getElementById('po6').src="imagenes/blind0-icon.png";
        } else if (posicphp6 > 0 && posicphp6 <=13) {
        	document.getElementById('po6').src="imagenes/blind1-icon.png";
        } else if (posicphp6 > 13 && posicphp6 <=23) {
        	document.getElementById('po6').src="imagenes/blind2-icon.png";
        } else if (posicphp6 > 23 && posicphp6 <=33) {
        	document.getElementById('po6').src="imagenes/blind3-icon.png";
        } else if (posicphp6 > 33 && posicphp6 <=37) {
        	document.getElementById('po6').src="imagenes/blind4-icon.png";
        }

        function refresh() {
        	vibrar();
        	window.location.reload();
        }
        function vibrar() {
        	navigator.vibrate(50);
        }
        function casa() {
        	vibrar();
        	location.href="menu.php";
        }
        function volver() {
        	vibrar();
        	location.href="persianas.php";
        }
    </script>

 </body>
</html>
