<?php
$titulo = 'Habitaciones';
$imagen = 'rooms';

if (!empty($_POST)) {
    session_start();
    require 'auxiliar.php';
    comprobarLogueado($pdo);
    $jsondata = [];
    $username = ucwords($_SESSION['usuario']['usuario']);
    $codigosPersianas = getCodigosPersianas($pdo);
    if (isset($_POST['tipo_envio'])) {
        $tipo_envio = $_POST['tipo_envio'];
        $codigo = $_POST['codigo'];
        $orden = $_POST['orden'];
        $tipo = $_POST['tipo'];

        switch ($tipo_envio) {
            case 'enviar':
                exec("sudo python /home/bear/py_scripts/arduino.py $tipo_envio $codigo $orden $tipo", $output, $retVar);
                if ($retVar == 0) {
                    $jsondata["success"] = true;
                } else {
                    $jsondata["success"] = false;
                    $jsondata["message"] = 'Error al enviar la orden';
                }
                break;
            case 'recibir':
                exec("sudo python /home/bear/py_scripts/arduino.py $tipo_envio $codigo $orden", $output, $retVar);
                $datosValidos = false;
                $outputValues = str_split($output[0], 2);
                if ($outputValues[0] == strtolower($codigosPersianas['per_central']) && $outputValues[1] == $codigo) {
                    $datosValidos = true;
                }
                if ($retVar == 0 && $datosValidos) {
                    $jsondata["success"] = true;
                    $jsondata["message"] = $outputValues;
                } else {
                    $jsondata["success"] = false;
                    $jsondata["message"] = 'Error al enviar la orden o recibir datos';
                }
                break;
            default:
                $jsondata["success"] = false;
                $jsondata["message"] = 'Orden no válida';
                break;
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
    }

    header('Content-type: application/json; charset=utf-8');
    echo json_encode($jsondata, JSON_FORCE_OBJECT);
    exit();
}

include ('header.php');

$rooms = getHabitaciones($pdo);
$codigosPersianas = getCodigosPersianas($pdo);
$persianas = getPersianas($pdo);
$actuadores = getActuadores($pdo);

$porcentajes = [
    'per_bajar' => '0%',
    'per_pos1' => '25%',
    'per_pos2' => '50%',
    'per_pos3' => '75%',
    'per_subir' => '100%',
];
?>

<?php foreach ($rooms as $room) { ?>
<div class="card text-white bg-dark card-collapse info-data" data-codigo="<?= $room['codigo'] ?>" data-tipohab="<?= $room['tipo'] ?>">
    <div class="card-header">
        <img src="imagenes/habitaciones/<?= $room['icono'] ?>.png"> <?= $room['nombre'] ?>
        <button type="button" class="btn btn-dark btn-chevron">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
    <div class="card-body" style="display: none;">
        <?php foreach ($persianas[$room['id']] as $blind) { ?>
            <div class="card text-white bg-dark card-elem card-elem-per" style="display:block" data-pos1="<?= $blind['posicion1'] ?>" data-pos2="<?= $blind['posicion2'] ?>" data-pos3="<?= $blind['posicion3'] ?>" data-pos4="<?= $blind['posicion4'] ?>">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">
                            <img class="icon-per" src="imagenes/persianas.png">
                            <div class="pos-persiana">
                                <p>Persiana</p>
                                <p style="font-size: 35px;margin-top: -20px;"><span class="altura-per">33</span></p>
                            </div>
                        </div>
                        <div class="col-6 text-right">
                            <button type="button" class="btn btn-outline-light btn-per btn-circle" data-orden="<?= $codigosPersianas['per_parar'] ?>" data-tipo="P">
                                <i style="color:#dc3545" class="far fa-stop-circle"></i>
                            </button>
                            <button type="button" class="btn btn-outline-light btn-per btn-circle" data-orden="<?= $codigosPersianas['per_subir'] ?>" data-tipo="P">
                                <i class="fas fa-arrow-circle-up"></i>
                            </button>
                            <button type="button" class="btn btn-outline-light btn-per btn-circle" data-orden="<?= $codigosPersianas['per_bajar'] ?>" data-tipo="P">
                                <i class="fas fa-arrow-circle-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row text-center" style="margin: 30px -15px 20px -40px">
                        <?php foreach ($porcentajes as $key => $porcent) { ?>
                            <div class="col">
                                <button type="button" class="btn btn-outline-light btn-por" data-orden="<?= $codigosPersianas[$key] ?>" data-tipo="P"><?= $porcent ?></button>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php foreach ($actuadores[$room['id']] as $action) { ?>
            <div class="card text-white bg-dark card-elem card-elem-act">
                <div class="card-header">
                    <img src="imagenes/actuadores/<?= $action['icono'] ?>.png">
                    <p><?= $action['nombre'] ?></p>
                    <div class="switch-slider">
                        <div class="custom-control custom-switch custom-switch-xl">
                            <input type="checkbox" class="custom-control-input actuador-input" id="customSwitch<?= $action['id'] ?>" data-orden="<?= $codigosPersianas['per_switch'.$action['switch']] ?>" data-tipo="<?= $action['tipo'] ?>">
                            <label class="custom-control-label" for="customSwitch<?= $action['id'] ?>"></label>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?php } ?>

<?php include ('footer.php') ?>
<script>
    orden_per_solicitar = <?= $codigosPersianas['per_solicitar'] ?>;
</script>
<script src="rooms.js?r=20200622" charset="utf-8"></script>