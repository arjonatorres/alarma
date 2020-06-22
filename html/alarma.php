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
            <img src="imagenes/sensores/<?= $sensor['icono'] ?>.png">
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

<script src="alarma.js?r=20200622" charset="utf-8"></script>