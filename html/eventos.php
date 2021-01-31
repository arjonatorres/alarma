<?php
$titulo = 'Eventos';
$imagen = 'eventos';

if (!empty($_POST)) {
	session_start();
	require 'auxiliar.php';
	comprobarLogueado($pdo);
	$jsondata = [];
	$username = ucwords($_SESSION['usuario']['usuario']);

	if (isset($_POST['id'])) {
		$id = $_POST['id'];
		$orden = $_POST['orden'];
		$nombre = $_POST['nombre'];

		$sqlExec = actDesacEvento($pdo, $id, $orden);

		if ($sqlExec) {
			$jsondata["success"] = true;
			$jsondata["message"] = '';
			if ($orden == '0') {
				$ordenMsg = "desactivado";
			} else {
				$ordenMsg = "activado";
			}
			$mensaje = "Evento \"$nombre\" $ordenMsg por $username";
			escribirLog($pdo, $mensaje);
		} else {
			$jsondata["success"] = false;
			$jsondata["message"] = 'Error al modificar el evento2';
		}
		
	} else {
		$jsondata["success"] = false;
		$jsondata["message"] = 'Error al modificar el evento';
	}

	header('Content-type: application/json; charset=utf-8');
	echo json_encode($jsondata, JSON_FORCE_OBJECT);
	exit();
}

function getEventos($pdo) {
	$sqlSent = "SELECT * FROM eventos e ORDER BY id DESC";
	$sent = $pdo->prepare($sqlSent);
	$sent->execute();

	return $sent->fetchAll(PDO::FETCH_ASSOC);
}

include ('header.php');
$dias = getDiasSemana();
$eventos = getEventos($pdo);

$dists = [
	'all',
	'palta',
	'pbaja',
	'paonorte',
	'paosur'
];
$codigosPersianas = getCodigosPersianas($pdo);
$roomsPersianas = getHabitaciones($pdo, true);
$rooms = getHabitaciones($pdo);
$dispositivos = getDispositivos($pdo);
$ordenPersianas = [
	'per_bajar',
	'per_pos1',
	'per_pos2',
	'per_pos3',
	'per_subir'
];

?>


<button type="button" class="new-event btn btn-success btn-circle-md">
	<i class="fas fa-plus"></i>
</button>

<div class="row justify-content-center no-margin">
	<div class="col-md-6 no-padding">
		<?php foreach ($eventos as $evento) { ?>
			<div class="card text-white bg-dark card-collapse card-event info-data" data-id="<?= $evento['id'] ?>" data-nombre="<?= $evento['nombre'] ?>" data-tipo="<?= $evento['tipo'] ?>">
				<div class="card-header">
					<div class="nombre-evento">
					<img src="imagenes/eventos/<?= $evento['tipo'] ?>.png?r=4">
						<?= ($evento['nombre'] != ''? ucfirst($evento['nombre']): 'Temporizador' . ' ' . substr($evento['hora'], 0, -3)) ?>
					</div>
					<div class="switch-slider">
						<div class="custom-control custom-switch custom-switch-xl">
							<input type="checkbox" <?= $evento['activo']? 'checked': '' ?> class="custom-control-input evento-input" id="customSwitch<?= $evento['id'] ?>">
							<label class="custom-control-label" for="customSwitch<?= $evento['id'] ?>"></label>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>

<?php include ('footer.php') ?>

<script type="text/javascript">
	dispositivos = <?= json_encode($dispositivos) ?>;
	codigosPersianas = <?= json_encode($codigosPersianas); ?>;
	var ready = false;

	$('button.new-event').on('click', function() {
		$(location).attr('href','edit_eventos.php')
	});

	$('div.nombre-evento > img').on('click', function(e) {
		$(location).attr('href','edit_eventos.php?id=' + $(this).closest('.info-data').data('id'));
	});

	$('input.evento-input').on('click', function(e) {
		if (ready) {
			ready = false; // reset flag
			return; // let the event bubble away
		}
		$('.loader').fadeIn('fast');
		vibrar();
		e.preventDefault();
		let boton = $(this);

		let id = $(this).closest('.info-data').data('id');
		let nombre = $(this).closest('.info-data').data('nombre');
		let tipo = $(this).closest('.info-data').data('tipo');
		if (boton.prop('checked')) {
			orden = 1;
		} else {
			orden = 0;
		}

		$.ajax({
			url: $(location).attr('pathname'),
			type: 'POST',
			data: {id: id, orden: orden, nombre: nombre},
			dataType: "json",
			success: function(data) {
				if (data.success) {
					boton.trigger('click');
				} else {
					alertPersonalizado(data.message);
					return false;
				}
			},
			error: function(e) {
				alertPersonalizado(e.message);
				return false;
			},
			complete: function(e) {
				$('.loader').fadeOut('fast');
			}
		});
		ready = true; // set flag
	});
</script>
