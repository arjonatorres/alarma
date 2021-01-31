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
    /*if ($mensaje != '') {
        $sqlSent .= "WHERE mensaje LIKE CONCAT('%', :mensaje, '%') ";
        $params[':mensaje'] = $mensaje;
    }

    if ($date != '') {
        if ($mensaje != '') {
            $sqlSent .= 'AND ';
        } else {
            $sqlSent .= 'WHERE ';
        }
        $sqlSent .= "created_at >= STR_TO_DATE(:fecha, '%Y-%m-%d') 
            AND created_at < STR_TO_DATE(:fecha, '%Y-%m-%d') + INTERVAL '1' DAY ";
        $params[':fecha'] = $date;
    }

    $sqlSent .= 'ORDER BY id DESC ';
    if ($limit != 0) {
        $sqlSent .= "LIMIT $limit ";
    }*/

	//$sqlSent .= "WHERE activo = '1' ";
	//$sqlSent .= "AND hora = '$hora' ";
	//$sqlSent .= "AND dias LIKE '%$dia%' ";
		//$sqlSent = "
		//-- horario hora normal
		//SELECT @hora_alba";
	$sent = $pdo->prepare($sqlSent);
	$sent->execute();

	return $sent->fetchAll(PDO::FETCH_ASSOC);
}

function updateTemporizadores($pdo) {
	$sqlSent = "UPDATE eventos
		SET hora = DATE_SUB(hora , INTERVAL 1 MINUTE)
		WHERE tipo = 'temporizador'";

	$sent = $pdo->prepare($sqlSent);
	$sent->execute();
}


//updateTemporizadores($pdo);
$codigosPersianas = getCodigosPersianas($pdo);

$hora = date('H:i'). ':00';
$dia = date('w');
$hora = '08:30:00';
$horarios = getHorarios($pdo, $hora, $dia);


var_dump($horarios);
//var_dump($hora);
//var_dump($dia);
//var_dump($codigosPersianas);
//exit(1);

foreach ($horarios as $horario) {
	$perVarias = false;
	$codigo = $horario['codigo'];
	$orden = substr($horario['orden'], 0, 2);

	foreach ($codigosPersianas as $key => $codigosPersiana) {
		if ($orden == $codigosPersiana['valor']) {
			$nombreOrden = $codigosPersiana['adicional'];
		} elseif ($codigo == substr($codigosPersiana['valor'], 0, 2)) {
			$nombreLugar = $codigosPersiana['adicional'];
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
		$mensaje = ucfirst($nombreOrden) . ' persianas ' . $nombreLugar . ' por BearControl';
		echo $mensaje;
		continue;
		exec("sudo python /home/bear/py_scripts/per_varias.py $codigo $orden \"$pers\" \"$nombreOrden\" $codigoSolicitar > /dev/null 2>/dev/null &", $output, $retVar);
		escribirLog($pdo, $mensaje);
		enviarHangouts($mensaje);
	} else {
		// Hay que llamar a arduino.py
		//echo 'arduino' . PHP_EOL;
		$rooms = getHabitaciones($pdo);

		if (strlen($horario['orden']) == 2) {
			//exec("sudo python /home/bear/py_scripts/arduino.py enviar $codigo $orden", $output, $retVar);
		} else {
			$switch_v = substr($horario['orden'], 2, 2);
			//exec("sudo python /home/bear/py_scripts/arduino.py enviar $codigo $orden $switch_v", $output, $retVar);
		}

		// Logs
		$mensaje = '';
		$nombreHabitacion = '';
		$idHabitacion = 0;
		$idArduino = 0;

		if (isset($switch_v)) {
			$dispositivos = getDispositivos($pdo);
			$numSwitch = '';
			foreach ($codigosPersianas as $key => $codigosPersiana) {
				if ($codigosPersiana['valor'] == $switch_v) {
					$numSwitch = substr($key, 10);
					break;
				}
			}
			foreach ($dispositivos as $dispositivoArray) {
				foreach ($dispositivoArray as $dispositivo) {
					if ($codigo == $dispositivo['codigo'] && $numSwitch == $dispositivo['switch']) {
						$idHabitacion = $dispositivo['habitacion_id'];
						break;
					}
				}
			}
		} else {
			$persianas = getPersianas($pdo);
			foreach ($persianas as $persianasArray) {
				foreach ($persianasArray as $persiana) {
					if ($codigo == $persiana['codigo']) {
						$idHabitacion = $persiana['habitacion_id'];
						break;
					}
				}
			}
		}


		foreach ($rooms as $room) {
			if ($room['id'] == $idHabitacion) {
				$nombreHabitacion = $room['nombre'];
				break;
			}
		}

		var_dump($idHabitacion);

		$adicional = '';
		foreach ($codigosPersianas as $key => $codigosPersiana) {
			if ($codigosPersiana['valor'] == $orden) {
				if ($idHabitacion != 0) {
					if (isset($switch_v)) {
						foreach ($dispositivos[$idHabitacion] as $dispositivo) {
							if ($dispositivo['switch'] == $numSwitch) {
								$nombreDispositivo = $dispositivo['nombre'];
							}
						}
						$adicional = ucfirst($codigosPersiana['adicional']) . ' ' . (isset($nombreDispositivo)? $nombreDispositivo: 'dispositivos') . ' ' . $nombreHabitacion . ' ';
						break;
					} else {
						$adicional = ucfirst($codigosPersiana['adicional']) . ' persianas ' . $nombreHabitacion . ' ';
						break;
					}
				} else {
					if (isset($switch_v)) {
						$adicional = ucfirst($codigosPersiana['adicional']) . ' dispositivos ' . (isset($nombreLugar) && $nombreLugar != 'todas'? ($nombreLugar . ' '): '');
						break;
					} else {
						$adicional = ucfirst($codigosPersiana['adicional']) . ' persianas ' . (isset($nombreLugar) && $nombreLugar != 'todas'? ($nombreLugar . ' '): '');
						break;
					}
				}
			}
		}
		if ($adicional != '') {
			$mensaje = $adicional . 'por BearControl';
			echo $mensaje;
			//escribirLog($pdo, $mensaje);
			//enviarHangouts($mensaje);
		}
	}
}