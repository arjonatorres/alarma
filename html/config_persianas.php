<?php
    $titulo = 'Config. persianas';
    $imagen = 'persianas';

if (!empty($_POST)) {
    session_start();
    require 'auxiliar.php';
    comprobarLogueado($pdo);
    $jsondata = [];
    $username = ucwords($_SESSION['usuario']['usuario']);
    $rooms = getHabitaciones($pdo);
    $nombresHabitaciones = [];

    foreach ($rooms as $key => $value) {
		$nombresHabitaciones[$value['codigo']] = $value['nombre'];
	}

    if (isset($_POST['codigo'])) {
        $codigo = $_POST['codigo'];
        $orden = $_POST['orden'];
        $ordenSolicitarEeprom = $_POST['orden_solicitar_eeprom'];
        $pos1 = $_POST['pos1'];
        $pos2 = $_POST['pos2'];
        $pos3 = $_POST['pos3'];
        $pos4 = $_POST['pos4'];
        $pos1hex = zeropad(dechex($_POST['pos1']));
        $pos2hex = zeropad(dechex($_POST['pos2']));
        $pos3hex = zeropad(dechex($_POST['pos3']));
        $pos4hex = zeropad(dechex($_POST['pos4']));
        $numBytes = 5;

        exec("sudo python /home/bear/py_scripts/arduino.py grabar $codigo $orden $pos1hex $pos2hex $pos3hex $pos4hex", $output, $retVar);

        if ($retVar == 0) {
	        exec("sudo python /home/bear/py_scripts/arduino.py recibir $codigo $ordenSolicitarEeprom $numBytes", $output, $retVar);
            $datosValidos = false;
            $output = strtoupper($output[0]);
            $outputValues = str_split($output, 2);

            if ($outputValues[0] == $codigo && hexdec($outputValues[1]) == $pos1 && hexdec($outputValues[2]) == $pos2 && hexdec($outputValues[3]) == $pos3 && hexdec($outputValues[4]) == $pos4) {
                $datosValidos = true;
            }
            if ($retVar == 0 && $datosValidos) {
                $jsondata["success"] = true;
                grabarPersiana($pdo, $codigo, $pos1, $pos2, $pos3, $pos4);
            	$mensaje = 'Grabada persiana ' . $nombresHabitaciones[$codigo] . ' por ' . $username;
	        	escribirLog($pdo, $mensaje);
            } else {
                $jsondata["success"] = false;
                $jsondata["message"] = 'Error al comprobar los datos grabados';
            }

	    } else {
            $jsondata["success"] = false;
            $jsondata["message"] = 'Error al grabar';
        }
    }
    header('Content-type: application/json; charset=utf-8');
    echo json_encode($jsondata, JSON_FORCE_OBJECT);
    exit();
}

include ('header.php');

$rooms = getHabitaciones($pdo, true);

$posiciones = [];
foreach ($rooms as $key => $value) {
	$posiciones[$value['codigo']] = [
		'pos1' => $value['posicion1'],
		'pos2' => $value['posicion2'],
		'pos3' => $value['posicion3'],
		'pos4' => $value['posicion4'],
	];
}

$codigosPersianas = getCodigosPersianas($pdo);

// $tempActual = tempActual($pdo);
?>
<div class="row justify-content-center no-margin">
	<div class="col-md-6 no-padding">
		<div class="card text-white bg-dark card-collapse info-data" data-codigo="<?= $room['codigo'] ?>" data-tipohab="<?= $room['tipo'] ?>">
			<div class="card-header">
				<div class="form-group">
					<label for="formControlHab">Habitaciones</label>
					<select class="form-control" id="formControlHab">
						<option hidden selected value>Selecciona una habitación</option>
						<?php foreach ($rooms as $key => $room) { ?>
						<option value="<?= $room['codigo'] ?>"><?= $room['nombre'] ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="card-body datos-pos" style="display: none;">
				<div class="row justify-content-center no-margin" style="margin-top: 18px;">
					<div class="col-8">
						<?php for($i = 1; $i <= 4; $i++): ?>
						<div class="form-group row">
							<label for="input<?= $i ?>" class="col-6 col-form-label text-right">Posicion <?= $i ?></label>
							<div class="col-6">
								<input type="number" class="form-control text-right" id="input<?= $i ?>">
							</div>
						</div>
						<?php endfor?>
						<div class="form-group row justify-content-center" style="margin-top: 1.5rem; padding-top: 1.3rem; border-top: 2px solid rgba(52, 58, 64, 0.6); margin-bottom: 20px;">
							<button id="boton_grabar" type="button" class="btn btn-danger">Grabar</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include ('footer.php') ?>

<script type="text/javascript">
	posiciones = <?= json_encode($posiciones) ?>;
	orden_per_grabar = <?= $codigosPersianas['per_grabar']['valor'] ?>;
	orden_per_solicitar_eeprom = <?= $codigosPersianas['per_solicitar_eeprom']['valor'] ?>;

	$('#formControlHab').on('change', function() {		
		$('.loader').fadeIn('fast');
		let codigo = $(this).val();
		
		setTimeout(function() {
			$(".loader").fadeOut('fast');
			cargarDatos(codigo);
			if ($('.datos-pos').css('display') == 'none') {
				$('.datos-pos').slideDown();
			}
		}, 300);

		
	});

	$('#boton_grabar').on('click', function() {
		$('.loader').fadeIn('fast');

		let codigo = $('#formControlHab').val();
		let orden = orden_per_grabar;
		let orden_solicitar_eeprom = orden_per_solicitar_eeprom;
		let pos1 = $('#input1').val();
		let pos2 = $('#input2').val();
		let pos3 = $('#input3').val();
		let pos4 = $('#input4').val();

		$.ajax({
	        url: $(location).attr('pathname'),
	        type: 'POST',
	        data: {codigo: codigo, orden: orden, orden_solicitar_eeprom: orden_solicitar_eeprom, pos1: pos1, pos2: pos2, pos3: pos3, pos4: pos4},
	        dataType: "json",
	        success: function(data) {
	            if (data.success) {
	                grabarDatos(codigo);
	            } else {
	                alertPersonalizado(data.message);
	                cargarDatos(codigo);
	                return false;
	            }
	        },
	        error: function(e) {
	            alertPersonalizado(e.message);
	            cargarDatos(codigo);
	            return false;
	        },
	        complete: function(e) {
	            $(".loader").fadeOut('fast');
	        }
	    });
	});

	function cargarDatos(codigo) {
		let datos = posiciones[codigo];
		$('#input1').val(datos['pos1']);
		$('#input2').val(datos['pos2']);
		$('#input3').val(datos['pos3']);
		$('#input4').val(datos['pos4']);
	}

	function grabarDatos(codigo) {
		posiciones[codigo] = {
			pos1: $('#input1').val(),
			pos2: $('#input2').val(),
			pos3: $('#input3').val(),
			pos4: $('#input4').val()
		};
	}
</script>