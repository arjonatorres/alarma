<?php

date_default_timezone_set('Europe/Madrid'); // Pone la hora local, UTC+1 ó UTC +2
$utc = (int)date('P'); // Convierte a entero el UTC+1
$hora = date_sunset(time(), SUNFUNCS_RET_STRING, 36.70, -6.1024, 90, $utc); //Devuelve la hora de la puesta de sol
// echo $hora;
$fecha = new DateTime($hora);
$fecha->modify('+30 minutes');
echo $fecha->format('H:i');
// date('i', $fecha->getTimestamp); // Extrae los minutos
// date('H', $fecha->getTimestamp); // Extrae las horas
