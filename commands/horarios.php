<?php
require '/var/www/html/auxiliar.php';

function getHorarios($pdo, $hora, $dia) {
	$sqlSets = "
		SET @hora_alba = (SELECT valor FROM parametros WHERE nombre = 'hora_alba');
		SET @hora_ocaso = (SELECT valor FROM parametros WHERE nombre = 'hora_ocaso');";

	$sent = $pdo->prepare($sqlSets);
	$sent->execute();

	$sqlSent = "
		-- horario hora normal
		SELECT * FROM eventos e 
		WHERE tipo = 'horario'
		AND activo = '1'
		AND hora = '$hora'
		AND dias LIKE '%$dia%'
		AND comienzo = 'hora'
		UNION
		-- horario alba
		SELECT * FROM eventos e
		WHERE tipo = 'horario'
		AND activo = '1'
		AND dias LIKE '%$dia%'
		AND comienzo LIKE 'alba%'
		AND '$hora' = IF (comienzo LIKE '%+', DATE_ADD(STR_TO_DATE(@hora_alba, '%h:%i') , INTERVAL (TIME_TO_SEC(e.hora)/60) MINUTE), DATE_SUB(STR_TO_DATE(@hora_alba, '%h:%i') , INTERVAL (TIME_TO_SEC(e.hora)/60) MINUTE)) 
		UNION
		-- horario ocaso
		SELECT * FROM eventos e
		WHERE tipo = 'horario'
		AND activo = '1'
		AND dias LIKE '%$dia%'
		AND comienzo LIKE 'ocaso%'
		AND '$hora' = IF (comienzo LIKE '%+', DATE_ADD(STR_TO_DATE(@hora_ocaso, '%h:%i') , INTERVAL (TIME_TO_SEC(e.hora)/60) MINUTE), DATE_SUB(STR_TO_DATE(@hora_ocaso, '%h:%i') , INTERVAL (TIME_TO_SEC(e.hora)/60) MINUTE)) 
		UNION
		-- temporizador
		SELECT * from eventos 
		WHERE tipo = 'temporizador'
		AND hora = '00:00:00'";

	$sent = $pdo->prepare($sqlSent);
	$sent->execute();

	return $sent->fetchAll(PDO::FETCH_ASSOC);
}

function updateTemporizadores($pdo) {
	$sqlSent = "UPDATE eventos
		SET hora = DATE_SUB(hora , INTERVAL 1 MINUTE)
		WHERE tipo = 'temporizador'
		AND activo = '1'";

	$sent = $pdo->prepare($sqlSent);
	$sent->execute();
}


updateTemporizadores($pdo);
$codigosPersianas = getCodigosPersianas($pdo);

$hora = date('H:i'). ':00';
$dia = date('w');
//$hora = '08:30:00';
$horarios = getHorarios($pdo, $hora, $dia);


//var_dump($horarios);
//var_dump($hora);
//var_dump($dia);
//var_dump($codigosPersianas);
//exit(1);

foreach ($horarios as $horario) {
	$perVarias = false;
	$codigo_total = $horario['codigo'];
	$orden = substr($horario['orden'], 0, 2);

	$codigo = substr($codigo_total, 0, 2);
	$pers = substr($codigo_total, 3);

	foreach ($codigosPersianas as $key => $codigosPersiana) {
		if ($orden == $codigosPersiana['valor']) {
			$nombreOrden = $codigosPersiana['adicional'];
		} elseif ($codigo == substr($codigosPersiana['valor'], 0, 2)) {
			if (strlen($horario['orden']) == 2) {
				// Hay que llamar a per_varias.py
				$pers = substr($codigosPersiana['valor'], 3);
				$perVarias = true;
			}
		} elseif ($key == 'per_solicitar') {
			$codigoSolicitar = $codigosPersiana['valor'];
		}
	}

	if ($perVarias) {
		exec("sudo python /home/bear/py_scripts/per_varias.py $codigo $orden \"$pers\" \"$nombreOrden\" $codigoSolicitar > /dev/null 2>/dev/null &", $output, $retVar);
	} else {
		// Hay que llamar a arduino.py
		$orden = $horario['orden'];

		if ($codigo == null) {
			$idHabitacion = explode('-', $horario['ubicacion'])[1];
			echo $idHabitacion . PHP_EOL;
			$sqlSent = "
				SELECT a.codigo, d.switch FROM dispositivos d 
				INNER JOIN arduinos a ON d.arduino_id = a.id 
				WHERE d.habitacion_id = $idHabitacion
				AND d.tipo = 'I';";

			$sent = $pdo->prepare($sqlSent);
			$sent->execute();

			$dispositivos = $sent->fetchAll(PDO::FETCH_ASSOC);
			$codigoDisp = [];
			foreach ($dispositivos as $dispositivo) {
				$codigoDisp[$dispositivo['codigo']][] = $dispositivo['switch'];
			}

			foreach ($codigoDisp as $key => $value) {
				$codigo = $key;
				$ordenTemp = $orden;
				foreach ($value as $idSwitch) {
					$ordenTemp .= $codigosPersianas['per_switch' . $idSwitch]['valor'];
				}
				exec("sudo python /home/bear/py_scripts/arduino.py enviar $codigo $orden", $output, $retVar);
			}
		} else {
			if (strlen($horario['orden']) != 2) {
				$switch_v = substr($horario['orden'], 2, 2);
			}
			exec("sudo python /home/bear/py_scripts/arduino.py enviar $codigo $orden", $output, $retVar);
		}
	}
	if ($horario['tipo'] == 'horario') {
		$nombre = $horario['nombre'];
		$mensaje = "Evento \"$nombre\" ";
		//echo $mensaje . PHP_EOL;
		escribirLog($pdo, $mensaje . 'ejecutado');
		enviarHangouts($mensaje);
		if ($horario['repetir'] == '0' && $dia == substr($horario['dias'], -1)) {
			actDesacEvento($pdo, $horario['id'], 0);
			escribirLog($pdo, $mensaje . 'desactivado');
		}
	} else {
		$mensaje = "Temporizador ejecutado";
		//echo $mensaje . PHP_EOL;
		escribirLog($pdo, $mensaje);
		borrarEvento($pdo, $horario['id']);
		enviarHangouts($mensaje);
	}
}
