<?php
    $titulo = 'Logs';
    $imagen = 'logs';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!empty($_POST)) {
    session_start();
    require 'auxiliar.php';
    comprobarLogueado($pdo);
    $jsondata = [];
    if (isset($_POST['filas'])) {
    	$mensaje = $_POST['mensaje'];
    	$fecha = $_POST['fecha'];
    	$filas = $_POST['filas'];
    	$logs = getLogs($pdo, $mensaje, $fecha, $filas);
    	$jsondata["success"] = true;
    	$jsondata["message"] = $logs;
    }
    header('Content-type: application/json; charset=utf-8');
    echo json_encode($jsondata, JSON_FORCE_OBJECT);
    exit();
}

include ('header.php');

$logs = getLogs($pdo);

?>
<div class="row justify-content-center no-margin">
	<div class="col-md-6 no-padding">
		<div class="card text-white bg-dark card-collapse card-logs">
			<div class="card-header">
				<form id="search-form" style="margin: 0px;">
					<div class="titulo-log">Logs</div>
					<button type="button" class="btn btn-dark btn-chevron">Filtrar
						<i class="fas fa-chevron-right"></i>
					</button>
					<div class="search-log" style="display: none;">
						<div class="form-group row">
							<label for="search_text" class="col-3 col-form-label" style="padding-right: 0px;">Mensaje</label>
							<div class="col-9">
								<input type="text" class="form-control" id="search_text">
							</div>
						</div>
						<div class="form-group row">
							<label for="search_date" class="col-3 col-form-label" style="padding-right: 0px;">Fecha</label>
							<div class="col-9">
								<input type="date" class="form-control" id="search_date">
							</div>
						</div>
						<div class="form-group row">
							<label for="search_filas" class="col-3 col-form-label" style="padding-right: 0px;">Filas</label>
							<div class="col-9">
								<select class="form-control" id="search_filas">
									<option value="0">Sin límite</option>
									<option selected value="100">100</option>
									<option value="200">200</option>
									<option value="500">500</option>
									<option value="1000">1000</option>
								</select>
							</div>
						</div>
						<div class="form-group row" style="margin-bottom: 0px;">
							<div class="col-12 text-right">
								<button id="boton_reset" type="reset" class="btn btn-danger pull-right">Borrar</button>
								<button id="boton_buscar" type="submit" class="btn btn-success pull-right" style="margin-left: 10px;">Buscar</button>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="text-center logs-num-results" style="margin-top: 15px;">
				Mostrando <span id="num_results"><?= count($logs) ?></span> resultados
			</div>
			<div class="card-body table-responsive">
				<table class="table table-sm table-logs">
					<tbody id="body_content">
					<?php foreach ($logs as $log): ?>
						<tr>
							<td class="log"><?= $log['mensaje'] ?></td>
							<td class="log"><?= $log['created_at'] ?></td>
						</tr>
					<?php endforeach ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php include ('footer.php') ?>

<script type="text/javascript">
	$('.btn-chevron').on('click', function() {
		desplegar($(this), true);
	});

	$('#search-form').on('submit', function(e) {
		$('.loader').fadeIn('fast');
		e.preventDefault();

		let mensaje = $('#search_text').val();
		let fecha = $('#search_date').val();
		let filas = $('#search_filas').val();

		$.ajax({
	        url: $(location).attr('pathname'),
	        type: 'POST',
	        data: {mensaje: mensaje, fecha: fecha, filas: filas},
	        dataType: "json",
	        success: function(data) {
	            if (data.success) {
	                let logs = data.message;
	                let num_res = Object.values(logs).length;
	                $('#num_results').text(num_res);
	                $('#body_content').empty();

			        for (log in logs) {
			        	let tdItem = $('<tr/>');
			        	let item = logs[log];
			            let liItem1 = $('<td/>', {'class': 'log', 'html': item['mensaje']});
			            let liItem2 = $('<td/>', {'class': 'log', 'html': item['created_at']});
			            tdItem.append(liItem1);
			            tdItem.append(liItem2);
			            $('#body_content').append(tdItem);
			        };
			        
	            }
	        },
	        error: function(e) {
	            alertPersonalizado(e.message);
	            return false;
	        },
	        complete: function(e) {
	        	$(".loader").fadeOut('fast');
	        }
	    });

	});
</script>