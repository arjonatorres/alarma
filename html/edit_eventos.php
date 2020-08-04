<?php
$titulo = 'Eventos';
$imagen = 'eventos';

if (!empty($_POST)) {
	session_start();
	require 'auxiliar.php';
	comprobarLogueado($pdo);
	$jsondata = [];
	$username = ucwords($_SESSION['usuario']['usuario']);

	if (isset($_POST['tipo_bd'])) {
		$tipo = $_POST['tipo_bd'];
		$nombre = $_POST['nombre_bd'];
		$tipo_dispositivo = $_POST['tipo_dispositivo_bd'];
		$ubicacion = $_POST['ubicacion_bd'];
		$codigo = $_POST['codigo_bd']?: null;
		$orden = $_POST['orden_bd'];
		$repetir = ($_POST['repetir_bd'] == 'true')? 1: 0;
		$dias = $_POST['dias_bd'];
		$comienzo = $_POST['comienzo_bd']?: null;
		$hora = $_POST['hora_bd'];
		$activo = ($_POST['activo_bd'] == 'true')? 1: 0;

		if (isset($_POST['id_bd'])) {
			$sent = $pdo->prepare("UPDATE eventos
				SET tipo=:tipo, nombre=:nombre, tipo_dispositivo=:tipo_dispositivo, ubicacion=:ubicacion, codigo=:codigo, orden=:orden, repetir=:repetir, dias=:dias, comienzo=:comienzo, hora=:hora, activo=:activo
				WHERE id=:id");
			$sqlExec = $sent->execute([
				':id' => $_POST['id_bd'],
				':tipo' => $_POST['tipo_bd'],
				':nombre' => $_POST['nombre_bd'],
				':tipo_dispositivo' => $_POST['tipo_dispositivo_bd'],
				':ubicacion' => $_POST['ubicacion_bd'],
				':codigo' => $_POST['codigo_bd']?: null,
				':orden' => $_POST['orden_bd'],
				':repetir' => ($_POST['repetir_bd'] == 'true')? 1: 0,
				':dias' => $_POST['dias_bd'],
				':comienzo' => $_POST['comienzo_bd']?: null,
				':hora' => $_POST['hora_bd'],
				':activo' => ($_POST['activo_bd'] == 'true')? 1: 0
			]);

			if ($sqlExec) {
				$jsondata["success"] = true;
				$jsondata["message"] = '';
				if ($tipo == 'horario') {
					$mensaje = "Evento \"$nombre\" modificado por $username";
				} else {
					$mensaje = "Temporizador de \"$hora\" modificado por $username";
				}
				escribirLog($pdo, $mensaje);
			} else {
				$jsondata["success"] = false;
				$jsondata["message"] = 'Error al modificar el evento';
			}
		} else {
			$sent = $pdo->prepare("INSERT INTO eventos (tipo,nombre,tipo_dispositivo,ubicacion,codigo,orden,repetir,dias,comienzo,hora,activo) VALUES (:tipo,:nombre,:tipo_dispositivo,:ubicacion,:codigo,:orden,:repetir,:dias,:comienzo,:hora,:activo)");
			$sqlExec = $sent->execute([
				':tipo' => $_POST['tipo_bd'],
				':nombre' => $_POST['nombre_bd'],
				':tipo_dispositivo' => $_POST['tipo_dispositivo_bd'],
				':ubicacion' => $_POST['ubicacion_bd'],
				':codigo' => $_POST['codigo_bd']?: null,
				':orden' => $_POST['orden_bd'],
				':repetir' => ($_POST['repetir_bd'] == 'true')? 1: 0,
				':dias' => $_POST['dias_bd'],
				':comienzo' => $_POST['comienzo_bd']?: null,
				':hora' => $_POST['hora_bd'],
				':activo' => ($_POST['activo_bd'] == 'true')? 1: 0
			]);

			if ($sqlExec) {
				$jsondata["success"] = true;
				$jsondata["message"] = '';
				if ($tipo == 'horario') {
					$mensaje = "Evento \"$nombre\" creado por $username";
				} else {
					$mensaje = "Temporizador de \"$hora\" creado por $username";
				}
				escribirLog($pdo, $mensaje);
			} else {
				$jsondata["success"] = false;
				$jsondata["message"] = 'Error al crear el evento';
			}
		}
	} elseif (isset($_POST['borrar'])) {
		$id = $_POST['id_bd'];
		$tipo = $_POST['tipo'];
		$nombre = $_POST['nombre_bd'];
		$hora = $_POST['hora_bd'];
		$sqlExec = borrarEvento($pdo, $id);
		if ($sqlExec) {
			$jsondata["success"] = true;
			$jsondata["message"] = '';
			if ($tipo == 'horario') {
				$mensaje = "Evento \"$nombre\" borrado por $username";
			} else {
				$mensaje = "Temporizador de \"$hora\" borrado por $username";
			}
			escribirLog($pdo, $mensaje);
		} else {
			$jsondata["success"] = false;
			$jsondata["message"] = 'Error al borrar el evento';
		}
	} else {
		$jsondata["success"] = false;
		$jsondata["message"] = 'Error al crear el evento';
	}

	header('Content-type: application/json; charset=utf-8');
	echo json_encode($jsondata, JSON_FORCE_OBJECT);
	exit();
}


include ('header.php');
$dias = getDiasSemana();
$dists = [
	'all',
    'palta',
    'pbaja',
    'onorte',
    'osur',
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

$evento = null;
if (isset($_GET['id'])) {
	$sent = $pdo->prepare('SELECT *
						   FROM eventos
						   WHERE id = :id');
    $sent->execute([':id' => $_GET['id']]);
    $evento = $sent->fetch(PDO::FETCH_ASSOC);
}

?>

<!-- Modal -->
<div class="modal fade" id="borrarModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content card bg-light">
			<div class="modal-header card-header bg-danger">
				<h5 class="modal-title" id="borrarModalTitle" style="color: #fff;">Borrar evento</h5>
			</div>
			<div class="modal-body" id="borrarModalContent">¿Estás seguro?</div>
			<div class="modal-footer">
				<button id="boton_borrar_modal" type="button" class="btn btn-danger">Borrar</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
			</div>
		</div>
	</div>
</div>

<div class="row justify-content-center no-margin">
	<div class="col-md-6 no-padding">
		<div class="card text-white bg-dark card-collapse" style="border: 0px;">
			<div class="card-header card-tabs">
				<ul class="nav nav-tabs">
					<li class="nav-item">
						<a id="event-link" class="nav-link active" href="#" data-tipo="horario">Horario</a>
					</li>
					<li class="nav-item">
						<a id="timer-link" class="nav-link" href="#" data-tipo="temporizador">Temporizador</a>
					</li>
				</ul>
			</div>

			<div class="card-body card-tabs bg-dark">

				<div id="nombre_div" class="row event-row" style="margin-top: 12px; margin-bottom: 25px;">
					<div class="col-3 text-left" style="padding-left: 0px;">
						<div class="form-check">
							<label class="form-check-label" style="margin-top: 4px;">Nombre:</label>
						</div>
					</div>
					<div class="col-9">
						<div class="form-group" style="margin-bottom: 0px;">
							<input id="nombre" class="form-control" type="text" />
							<div id="error-nombre" class="invalid-feedback">El nombre no puede estar vacío</div>
						</div>
					</div>
				</div>

				<div class="row event-row" style="margin-top: 15px;">
					<div class="col-3 text-left" style="padding-left: 0px;">
						<div class="form-check">
							<label class="form-check-label">Tipo:</label>
						</div>
					</div>
					<div class="col-9">
						<div class="col-12" style="padding-left: 0px;">
							<div class="row">
								<div class="col-6">
									<div class="custom-control custom-radio">
										<input id="tipo1" class="custom-control-input" type="radio" name="tipo" value="persiana" checked style="width:40px; height: 40px;">
										<label class="custom-control-label" for="tipo1">Persiana</label>
									</div>
								</div>
								<div class="col-6">
									<div class="custom-control custom-radio">
										<input id="tipo2" class="custom-control-input" type="radio" name="tipo" value="dispositivo">
										<label class="custom-control-label" for="tipo2">Dispositivo</label>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row event-row">
					<div class="col-3 text-left" style="padding-left: 0px;">
						<div class="form-check">
							<label class="form-check-label" style="margin-top: 4px;">Ubicación:</label>
						</div>
					</div>
					<div class="col-9" style="padding-right: 20px;">
						<div class="form-group" style="margin-bottom: 0px;">
							<select id="codigoPersiana" class="form-control">
								<?php foreach ($dists as $dist) { ?>
									<option value="<?= $codigosPersianas['per_'.$dist]['valor'] ?>" data-tipo="all"><?= ucwords($codigosPersianas['per_'.$dist]['adicional']) ?></option>
								<?php } ?>
								<?php foreach ($roomsPersianas as $key => $roomPersiana) { ?>
									<option value="<?= $roomPersiana['codigo'] ?>" data-id="<?= $roomPersiana['habitacion_id'] ?>" data-tipo="room"><?= $roomPersiana['habnom'] ?></option>
								<?php } ?>
							</select>
							<select id="codigoDispositivo" class="form-control" style="display: none;">
								<?php foreach ($dists as $dist) { ?>
									<option value="<?= $codigosPersianas['per_'.$dist]['valor'] ?>" data-tipo="all"><?= ucwords($codigosPersianas['per_'.$dist]['adicional']) ?></option>
								<?php } ?>
								<?php foreach ($rooms as $key => $room) { ?>
									<option value="<?= $room['codigo'] ?>" data-id="<?= $room['id'] ?>" data-tipo="room"><?= $room['habnom'] ?></option>
								<?php } ?>
							</select>
							<div id="error-hab-no-disp" class="invalid-feedback">Esta habitación no posee ningún dispositivo</div>
						</div>
					</div>
					<div class="col-9" style="padding-right: 20px;">
						<div class="form-group" style="margin-bottom: 0px;">
							
						</div>
					</div>
				</div>

				<div id="dispositivos_div" class="row event-row" style="display: none;">
					<div class="col-3 text-left" style="padding-left: 0px;">
						<div class="form-check">
							<label class="form-check-label" style="margin-top: 4px;">Dispositivo:</label>
						</div>
					</div>
					<div class="col-9" style="padding-right: 20px;">
						<div class="form-group" style="margin-bottom: 0px;">
							<select id="dispositivo" class="form-control"></select>
						</div>
					</div>
				</div>

				<div class="row event-row">
					<div class="col-3 text-left" style="padding-left: 0px;">
						<div class="form-check">
							<label id="orden-label" class="form-check-label" style="margin-top: 4px;">Orden:</label>
						</div>
					</div>
					<div class="col-9 orden-persianas" style="padding-right: 20px;">
						<div class="form-group" style="margin-bottom: 0px;">
							<select id="orden" class="form-control">
								<?php foreach ($ordenPersianas as $keyOrden) { ?>
									<option value="<?= $codigosPersianas[$keyOrden]['valor'] ?>"><?= ucwords($codigosPersianas[$keyOrden]['adicional']) ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-4 orden-on-off" style="display: none;">
						<div class="custom-control custom-radio">
							<input id="orden1" type="radio" class="custom-control-input" name="orden" checked="" value="encender">
							<label class="custom-control-label" for="orden1">ON</label>
						</div>
					</div>
					<div class="col-4 orden-on-off" style="display: none;">
						<div class="custom-control custom-radio">
							<input id="orden2" type="radio" class="custom-control-input" name="orden" value="apagar">
							<label class="custom-control-label" for="orden2">OFF</label>
						</div>
					</div>
					<div class="col-4 orden-pulsador" style="display: none;">
						<div class="custom-control custom-radio">
							<input id="orden_puls" type="radio" class="custom-control-input" checked="" value="pulsador">
							<label class="custom-control-label" for="orden1">Pulsador</label>
						</div>
					</div>
				</div>

				<div id="repetir_div" class="row event-row">
					<div class="col-3 text-left" style="padding-left: 0px;">
						<div class="form-check">
							<label class="form-check-label">Repetir:</label>
						</div>
					</div>
					<div class="col-4">
						<div class="custom-control custom-radio">
							<input id="repetir1" class="custom-control-input" type="radio" name="repetir" value="true" checked style="width:40px; height: 40px;">
							<label class="custom-control-label" for="repetir1">Siempre</label>
						</div>
					</div>
					<div class="col-4">
						<div class="custom-control custom-radio">
							<input id="repetir2" class="custom-control-input" type="radio" name="repetir" value="false">
							<label class="custom-control-label" for="repetir2">Una</label>
						</div>
					</div>
				</div>

				<div id="dias_div" class="row event-row">
					<div class="col-2 text-left" style="padding-left: 0px;">
						<div class="form-check">
							<label class="form-check-label" style="margin-top: 4px;">Días:</label>
						</div>
					</div>
					<div class="col-10 dist-center">
						<?php foreach ($dias as $key => $dia) { ?>
							<label class="btn btn-rounded-lg form-check-label label-dias active">
								<input class="form-check-input input-dias" type="checkbox" name="dias[]" checked value="<?= $key ?>"> <?= $dia ?>
							</label>
						<?php } ?>
					</div>
				</div>

				<div class="row event-row">
					<div class="col-3 text-left" style="padding-left: 0px; margin-top: 5px">
						<div class="form-check">
							<label class="form-check-label tiempo">Comienzo:</label>
						</div>
					</div>
					<div class="col-9">
						<div class="col-12 div-hora_alba_ocaso" style="padding-left: 0px; margin-bottom: 10px;">
							<div class="row" style="margin-top: 5px;">
								<div class="col-4">
									<div class="custom-control custom-radio">
										<input type="radio" class="custom-control-input" id="tipo_comienzo1" name="tipo_comienzo" checked="" value="hora">
										<label class="custom-control-label" for="tipo_comienzo1">Hora</label>
									</div>
								</div>
								<div class="col-4 opt-int">
									<div class="custom-control custom-radio">
										<input id="tipo_comienzo_alba" type="radio" class="custom-control-input" name="tipo_comienzo" value="alba">
										<label class="custom-control-label" for="tipo_comienzo_alba">Alba</label>
									</div>
								</div>
								<div class="col-4 opt-int">
									<div class="custom-control custom-radio">
										<input id="tipo_comienzo_ocaso" type="radio" class="custom-control-input" name="tipo_comienzo" value="ocaso">
										<label class="custom-control-label" for="tipo_comienzo_ocaso">Ocaso</label>
									</div>
								</div>
							</div>
						</div>
						<div class="col-9" style="padding-left: 0px;">
							<div class="form-group" style="margin-bottom: 10px;">
								<input id="time" type="time" class="form-control" name="time" value="00:00">
							</div>
						</div>
						<div id="tipo_sol_div" class="col-12" style="padding-top: 5px; padding-left: 0px; display: none;">
							<div class="row">
								<div class="col-4">
									<div class="custom-control custom-radio">
										<input id="tipo_sol1" type="radio" class="custom-control-input" name="tipo_sol" checked="" value="-">
										<label class="custom-control-label" for="tipo_sol1">Antes</label>
									</div>
								</div>
								<div class="col-4 opt-int">
									<div class="custom-control custom-radio">
										<input id="tipo_sol2" type="radio" class="custom-control-input" name="tipo_sol" value="+">
										<label class="custom-control-label" for="tipo_sol2">Después</label>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div id="activo_div" class="row event-row" style="margin-bottom: 15px;">
					<div class="col-3 text-left" style="padding-left: 0px;">
						<div class="form-check">
							<label class="form-check-label">Activo:</label>
						</div>
					</div>
					<div class="col-4">
						<div class="custom-control custom-radio">
							<input id="activo1" class="custom-control-input" type="radio" name="activo" value="true" checked style="width:40px; height: 40px;">
							<label class="custom-control-label" for="activo1">Si</label>
						</div>
					</div>
					<div class="col-4">
						<div class="custom-control custom-radio">
							<input id="activo2" class="custom-control-input" type="radio" name="activo" value="false">
							<label class="custom-control-label" for="activo2">No</label>
						</div>
					</div>
				</div>

				<div class="form-group row" style="border-top: 1px solid rgba(23,162,184, 0.3); margin: 10px -10px 15px -10px; padding-top: 20px;">
					<div class="col-12 text-center">
						<?php if ($evento) { ?>
							<button id="boton_borrar" type="submit" class="btn btn-danger" style="margin-right: 10px;">Borrar evento</button>
						<?php } ?>
						<button id="boton_crear" type="submit" class="btn btn-success"><?= $evento? 'Modificar': 'Crear' ?> evento</button>
					</div>
				</div>
			</div>

			
		</div>
	</div>
</div>

<?php include ('footer.php') ?>

<script type="text/javascript">
	dispositivos = <?= json_encode($dispositivos) ?>;
	codigosPersianas = <?= json_encode($codigosPersianas); ?>;
	evento = <?= json_encode($evento); ?>
</script>
<script src="edit_eventos.js?r=20200713" charset="utf-8"></script>