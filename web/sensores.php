<?php
session_start();
?>
<?php
require 'auxiliar.php';
if (!comprobarLogueado()){
    return;
}
?>
<?php
if (isset($_GET['peticion'])) {
    header('Content-Type: application/json;charset=utf-8');

    exec("sudo python /home/pi/pir.py", $res);
    echo json_encode($res);
    return;
}

$salida = file_get_contents('/home/pi/estado_alarma.txt');

?>
<html>
    <head>
        <title>Sensores</title>
        <script src="auxiliar.js" charset="utf-8"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <!-- <link rel="shortcut icon" href="arjonatorres.ddns.net/php/mifavicon.png" type="image/x-icon"> -->
        <link rel=StyleSheet href="estilo.css" type="text/css">
        <style media="screen">
            .ojo {
                width: 200px;
            }
            .div-ojo {
                margin: 20px;
                display: inline;
            }
            .boton {
                padding: 40px;
            }
            .div-boton input {
                margin-top: 40px;
                font-size: 70px;
            }
        </style>
    </head>

<body>
	<?php
	cabeceraMenu('Sensores', 'motion');
	?>

	<table class=boton cellspacing="40" border="0">
    	<tr class=boton>
    		<td colspan=3 class=boton>
                <div class="salon div-ojo">
                    <img class="ojo" src="imagenes/motion2bn.png" alt="">
                </div>
                <div class="ordenador div-ojo">
                    <img class="ojo" src="imagenes/motion2bn.png" alt="">
                </div>
                <div class="distribuidor div-ojo">
                    <img class="ojo" src="imagenes/motion2bn.png" alt="">
                </div>
                <!-- <div class="div-boton">
                    <input type="button" name="" value="Comprobar" />
                </div> -->
    		</td>
    	</tr>
	</table>

	<?php pieMenu($salida); ?>

    <script>
        $(document).ready(function () {
            setInterval(envio, 2000);
        });

        function envio() {
            $.getJSON('sensores.php', { peticion: 1 }, function(data) {
                $.each(data, function (key, value) {
                    if (value == 1) {
                        $('.div-ojo').eq(key).children('.ojo').attr('src', 'imagenes/motion2.png');
                    } else {
                        $('.div-ojo').eq(key).children('.ojo').attr('src', 'imagenes/motion2bn.png');
                    }
                });
            });
        }
    </script>
</body>
</html>
