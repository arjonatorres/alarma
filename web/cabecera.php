<head>
    <title>Menu casa</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="auxiliar.js" charset="utf-8"></script>
    <link rel="icon" href="http://arjonatorres.ddns.net/php/imagenes/home.png" type="image/png">
    <link rel=StyleSheet href="estilo.css" type="text/css">
    <meta name='viewport' content='user-scalable=0'>
</head>

<?php
$salida = $dev ? file_get_contents('estado_alarma.txt') : (file_get_contents('/home/pi/estado_alarma.txt'));
?>