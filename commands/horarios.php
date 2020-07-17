<?php
require '/var/www/html/auxiliar.php';

function getHorarios($pdo, $hora, $dia) {
	$sqlSent = 'SELECT * FROM horarios ';

	$params = [];

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

	$sqlSent .= "WHERE activo = '1' ";
	$sqlSent .= "AND hora = '$hora' ";
	$sqlSent .= "AND dias LIKE '%$dia%' ";
	$sent = $pdo->prepare($sqlSent);
	$sent->execute($params);

	return $sent->fetchAll(PDO::FETCH_ASSOC);
}


$hora = date('H:i');
$dia = date('w');
$horarios = getHorarios($pdo, $hora, $dia);
$codigosPersianas = getCodigosPersianas($pdo);


var_dump($horarios);
//var_dump($hora);
//var_dump($dia);
//var_dump($codigosPersianas);
exit(1);

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
		//echo 'per varias';
		// continue;
		exec("sudo python /home/bear/py_scripts/per_varias.py $codigo $orden \"$pers\" \"$nombreOrden\" $codigoSolicitar > /dev/null 2>/dev/null &", $output, $retVar);
		escribirLog($pdo, $mensaje);
		enviarHangouts($mensaje);
		//exec("sudo python /home/bear/py_scripts/enviar_hangouts.py \"$mensaje\"");
	} else {
		// Hay que llamar a arduino.py
		//echo 'arduino' . PHP_EOL;
		$rooms = getHabitaciones($pdo);
		$dispositivos = getDispositivos($pdo);

		if (strlen($horario['orden']) == 2) {
			exec("sudo python /home/bear/py_scripts/arduino.py enviar $codigo $orden", $output, $retVar);
		} else {
			$switch_v = substr($horario['orden'], 2, 2);
			exec("sudo python /home/bear/py_scripts/arduino.py enviar $codigo $orden $switch_v", $output, $retVar);
		}

		// Logs
		$mensaje = '';
		$nombreHabitacion = '';
		$idHabitacion = 0;
		foreach ($rooms as $room) {
			if ($room['codigo'] == $codigo) {
				$idHabitacion = $room['id'];
				$nombreHabitacion = $room['nombre'];
				break;
			}
		}
		if (isset($switch_v)) {
			$numSwitch = '';
			foreach ($codigosPersianas as $key => $codigosPersiana) {
				if ($codigosPersiana['valor'] == $switch_v) {
					$numSwitch = substr($key, 10);
				}
			}
		}

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
						$adicional = ucfirst($codigosPersiana['adicional']) . ' dispositivos ' . ($nombreLugar != 'todas'? ($nombreLugar . ' '): '');
						break;
					} else {
						$adicional = ucfirst($codigosPersiana['adicional']) . ' persianas ' . ($nombreLugar != 'todas'? ($nombreLugar . ' '): '');
						break;
					}
				}
			}
		}
		if ($adicional != '') {
			$mensaje = $adicional . 'por BearControl';
			//echo $mensaje;
			escribirLog($pdo, $mensaje);
			enviarHangouts($mensaje);
		}
	}
}
