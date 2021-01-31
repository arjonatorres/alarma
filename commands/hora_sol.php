<?php
require '/var/www/html/auxiliar.php';

date_default_timezone_set('Europe/Madrid'); // Pone la hora local, UTC+1 ó UTC +2
$utc = (int)date('P'); // Convierte a entero el UTC+1

$horaAlba = date_sunrise(time(), SUNFUNCS_RET_STRING, 36.70, -6.1024, 90, $utc); //Devuelve la hora de la salida de sol
$sent = $pdo->prepare("UPDATE parametros
                       SET valor = :hora
                       WHERE nombre = 'hora_alba'");
$sent->execute([':hora' => $horaAlba]);

$horaOcaso = date_sunset(time(), SUNFUNCS_RET_STRING, 36.70, -6.1024, 90, $utc); //Devuelve la hora de la puesta de sol
$sent = $pdo->prepare("UPDATE parametros
                       SET valor = :hora
                       WHERE nombre = 'hora_ocaso'");
$sent->execute([':hora' => $horaOcaso]);
