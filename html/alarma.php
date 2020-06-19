<?php
$titulo = 'Alarma';
$imagen = 'thief-icon';

if (!empty($_POST)) {
    session_start();
    require 'auxiliar.php';
    comprobarLogueado($pdo);
    $jsondata = [];
    $username = ucwords($_SESSION['usuario']['usuario']);
    if (isset($_POST['estado_alarma'])) {
        $estado = $_POST['estado_alarma'];
        if ($estado == '0') {
            $mensaje = 'Alarma desconectada via movil por ' . $username;
        } elseif ($estado == '1') {
            $mensaje = 'Alarma conectada via movil por ' . $username;
        } elseif ($estado == '2') {
            $mensaje = 'Alarma parcial conectada via movil por ' . $username;
            $estado = '1';
        } else {
            $jsondata["success"] = false;
            $jsondata["message"] = 'Orden no válida';
        }

        if (isset($mensaje)) {
            escribirEstadoAlarma($pdo, $estado);
            escribirLog($pdo, $mensaje);
            exec("sudo python /home/bear/py_scripts/activar_flag.py 28");
            $jsondata["success"] = true;
            $date = new DateTime();
            $fecha = $date->format('h:i:s d/m/Y');
            $men = ucfirst(substr($mensaje, 7));
            $jsondata["message"] = $men . ' - ' . $fecha;
        }
        
    } else if (isset($_POST['pin_sensor']) && isset($_POST['activo_sensor']) && isset($_POST['nombre_sensor'])) {
        $pin = $_POST['pin_sensor'];
        $activo = $_POST['activo_sensor'];
        $nombre = $_POST['nombre_sensor'];
        if ($activo == '0') {
            $mensaje = 'Sensor ' . $nombre . ' desactivado por ' . $username;
        } elseif ($activo == '1') {
            $mensaje = 'Sensor ' . $nombre . ' activado por ' . $username;
        } else {
            $jsondata["success"] = false;
            $jsondata["message"] = 'Orden no válida';
        }

        if (isset($mensaje)) {
            escribirEstadoSensor($pdo, $pin, $activo);
            escribirLog($pdo, $mensaje);
            exec("sudo python /home/bear/py_scripts/activar_flag.py 29");
            $jsondata["success"] = true;
        }
    }
    header('Content-type: application/json; charset=utf-8');
    echo json_encode($jsondata, JSON_FORCE_OBJECT);
    exit();
}

include ('header.php');
$ultimaAccionAlarma = ultimaAccionAlarma($pdo);
?>
<div class="card text-white bg-dark card-info">
    <div class="card-header">
        <p class="text-right"><?= $ultimaAccionAlarma ?></p>
    </div>
</div>

<div class="container-fluid">
    <div class="row" style="margin-top: 80px; margin-bottom: 100px;">
        <div id="clickFondo" class="col-sm text-center">
            <button id="button-alarm" data-estado="<?= $salida ?>" type="button" class="btn <?= $salida == '1'? $boton: 'btn-success' ?> btn-circle btn-xl">
                <i class="fas fa-lock" style="margin-top: -30px;"></i>
            </button>
        </div>
    </div>
</div>

<div class="card text-white bg-dark card-collapse">
    <div class="card-header">
        <img src="imagenes/sensor2.png"> Sensores
        <button type="button" class="btn btn-dark btn-chevron">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
    <div class="card-body" style="display: none;">

<?php foreach ($sensores as $sensor) { ?>
    <div class="card text-white bg-dark card-elem">
        <div class="card-header">
            <img src="imagenes/<?= $sensor['icono'] ?>.png">
            <p><?= ucfirst($sensor['nombre']) ?></p>
            <div class="switch-slider">
                <div class="custom-control custom-switch custom-switch-xl">
                    <input type="checkbox" data-id="<?= $sensor['pin'] ?>" data-nombre="<?= $sensor['nombre'] ?>" <?= $sensor['activo']? 'checked': '' ?> class="custom-control-input" id="customSwitch<?= $sensor['pin'] ?>">
                    <label class="custom-control-label" for="customSwitch<?= $sensor['pin'] ?>"></label>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
    </div>
</div>

<?php include ('footer.php') ?>

<script>
var ready = false;

$('input.custom-control-input').on('click', function(e) {
    if (ready) {
        ready = false; // reset flag
        return; // let the event bubble away
    }

    e.preventDefault();
    boton = $(this);
    pin_sensor = $(this).data('id');
    activo_sensor = $(this).prop('checked')? '1': '0';
    nombre_sensor = $(this).data('nombre');
    $.ajax({
        url: 'alarma.php',
        type: 'POST',
        data: {pin_sensor: pin_sensor, activo_sensor: activo_sensor, nombre_sensor: nombre_sensor},
        dataType: "json",
        success: function(data) {
            if (data.success) {
                $('#button-alarm').attr('style', '')
                boton.trigger('click');
                if (activo_sensor == '0' && $('#button-alarm').hasClass('btn-danger')) {
                    $('#button-alarm, #icono-estado-alarma').removeClass('btn-danger');
                    $('#button-alarm, #icono-estado-alarma').addClass('btn-warning');
                } else if (activo_sensor == '1' && $('#button-alarm').hasClass('btn-warning') && $('input').length == $('input:checked').length) {
                    $('#button-alarm, #icono-estado-alarma').removeClass('btn-warning');
                    $('#button-alarm, #icono-estado-alarma').addClass('btn-danger');
                }
            } else {
                alert (data.message);
                return false;
            }
        },
        error: function(e) {
            ajaxError(e);
            return false;
        }
    });
    ready = true; // set flag
   
});


$('#button-alarm').on('click', function() {
    vibrar();
    estado = $(this).data('estado') == '0'? '1': '0';
    if (estado == '1' && ($('input').length != $('input:checked').length)) {
        estado = '2';
    }
    $.ajax({
        url: 'alarma.php',
        type: 'POST',
        data: {estado_alarma: estado},
        dataType: "json",
        success: function(data) {
            if (data.success) {
                cambiar();
                $('.card-info p').html(data.message);
                $('#button-alarm').css({'box-shadow': '0 0 0 0.0rem'});
                if ($('#button-alarm').hasClass('btn-success')) {
                    $('#button-alarm').css({'background-color': '#28a745'});
                } else if ($('#button-alarm').hasClass('btn-danger')) {
                    $('#button-alarm').css({'background-color': '#dc3545'});
                } else if ($('#button-alarm').hasClass('btn-warning')) {
                    $('#button-alarm').css({'background-color': '#ffc107'});
                }
            } else {
                alert (data.message);
            }
        },
        error: function(e) {
            ajaxError(e);
        }
    });
});

function cambiar() {
    boton = $('#button-alarm, #icono-estado-alarma');
    all = $('input').length == $('input:checked').length;
    if (boton.hasClass('btn-success')) {
        boton.removeClass('btn-success');
        clase = 'btn-' + (all? 'danger': 'warning');
        boton.addClass(clase);
        $('#button-alarm').data('estado', 1);
    } else {
        boton.removeClass('btn-danger');
        boton.removeClass('btn-warning');
        boton.addClass('btn-success');
        $('#button-alarm').data('estado', 0);
    }

}
</script>