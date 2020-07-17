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
    $rooms = getHabitaciones($pdo);
    $dispositivos = getDispositivos($pdo);

    if (isset($_POST['tipo_envio'])) {
        $tipo_envio = $_POST['tipo_envio'];
        $orden = $_POST['orden'];

        switch ($tipo_envio) {
            case 'enviar':
                $codigo = $_POST['codigo'];
                $numBytes = $_POST['num_bytes'];
                $habId = $_POST['habid'];
                if (isset($_POST['switch_v'])) {
                    $switch_v = $_POST['switch_v'];
                    exec("sudo python /home/bear/py_scripts/arduino.py $tipo_envio $codigo $orden $switch_v", $output, $retVar);
                } else {
                    exec("sudo python /home/bear/py_scripts/arduino.py $tipo_envio $codigo $orden", $output, $retVar);
                }
                
                if ($retVar == 0) {
                    $jsondata["success"] = true;
                    // Logs
                    $mensaje = '';
                    $nombreHabitacion = '';
                    $idHabitacion = 0;
                    foreach ($rooms as $room) {
                        if ($room['id'] == $habId) {
                            $idHabitacion = $room['id'];
                            $nombreHabitacion = $room['nombre'];
                            break;
                        }
                    }
                    if (isset($_POST['switch_v'])) {
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
                            if (isset($_POST['switch_v'])) {
                                foreach ($dispositivos[$idHabitacion] as $dispositivo) {
                                    if ($dispositivo['switch'] == $numSwitch) {
                                        $nombreDispositivo = $dispositivo['nombre'];
                                    }
                                }
                                $adicional = ucfirst($codigosPersiana['adicional']) . ' ' . $nombreDispositivo . ' ';
                            } else {
                                $adicional = ucfirst($codigosPersiana['adicional']) . ' persiana ';
                            }
                            break;
                        }
                    }
                    if ($adicional != '') {
                        $mensaje = $adicional . $nombreHabitacion . ' por ' . $username;
                        escribirLog($pdo, $mensaje);
                    }
                } else {
                    $jsondata["success"] = false;
                    $jsondata["message"] = 'Error al enviar la orden';
                }
                break;
            case 'recibir':
                $codigos = $_POST['codigos'];
                $numsBytes = $_POST['nums_bytes'];
                $datosValidos = false;
                $results = [];
                foreach ($codigos as $key => $codigo) {
                    $numBytes = $numsBytes[$key];
                    $codigo = '11'; // todo eliminar esta fila
                    exec("sudo python /home/bear/py_scripts/arduino.py $tipo_envio $codigo $orden $numBytes", $output, $retVar);
                    $output = strtoupper($output[0]);
                    $outputValues = str_split($output, 2);
                    if ($outputValues[0] == $codigo && $retVar == 0) {
                        $outputValues[0] = strval($codigo+$key);
                        $datosValidos = true;
                        $results[] = $outputValues;
                    } else {
                        $datosValidos = false;
                        break;
                    }
                }

                if ($datosValidos) {
                    $jsondata["success"] = true;
                    $jsondata["message"] = $results;
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
    }

    header('Content-type: application/json; charset=utf-8');
    echo json_encode($jsondata, JSON_FORCE_OBJECT);
    exit();
}

include ('header.php');

$rooms = getHabitaciones($pdo);
$codigosPersianas = getCodigosPersianas($pdo);
$persianas = getPersianas($pdo);
$dispositivos = getDispositivos($pdo);

$ordenIcono = [
    'per_bajar' => '0',
    'per_pos1' => '2',
    'per_pos2' => '5',
    'per_pos3' => '7',
    'per_subir' => '10',
];
?>


<?php foreach ($rooms as $room) { ?>
<div class="row justify-content-center no-margin">
    <div class="col-md-6 no-padding">
        <div class="card text-white bg-dark card-collapse info-data" data-habid="<?= $room['id'] ?>" >
            <div class="card-header">
                <img src="imagenes/habitaciones/<?= $room['icono'] ?>.png"> <?= $room['nombre'] ?>
                <button type="button" class="btn btn-dark btn-chevron">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="card-body" style="display: none;">
                <?php foreach ($persianas[$room['id']] as $blind) { ?>
                    <div class="card text-white bg-dark card-elem card-elem-per" style="display:block" data-codigo="<?= $blind['codigo'] ?>" data-pos="0" data-pos1="<?= $blind['posicion1'] ?>" data-pos2="<?= $blind['posicion2'] ?>" data-pos3="<?= $blind['posicion3'] ?>" data-pos4="<?= $blind['posicion4'] ?>">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <img class="icon-per" src="imagenes/persianas.png">
                                    <div class="pos-persiana">
                                        <p><?= $blind['nombre'] ?></p>
                                        <p style="font-size: 12px;margin-top: -18px;"><span class="altura-per"></span></p>
                                    </div>
                                </div>
                                <div class="col-6 text-right btn-per-inst">
                                    <button type="button" class="btn btn-outline-light btn-per btn-circle btn-stop" data-orden="<?= $codigosPersianas['per_parar']['valor'] ?>" data-tipo="B">
                                        <i style="color:#dc3545" class="far fa-stop-circle"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-light btn-per btn-circle" data-orden="<?= $codigosPersianas['per_bajar']['valor'] ?>" data-tipo="B">
                                        <i class="fas fa-arrow-circle-down"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-light btn-per btn-circle" data-orden="<?= $codigosPersianas['per_subir']['valor'] ?>" data-tipo="B">
                                        <i class="fas fa-arrow-circle-up"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="row text-center btn-per-pos">
                                <?php $i = 0; ?>
                                <?php foreach ($ordenIcono as $key => $icon) { ?>
                                    <div class="col">
                                        <button type="button" class="btn btn-outline-light btn-circle btn-por" data-pos="<?= $blind['posicion'.($i)]?: '0' ?>" data-orden="<?= $codigosPersianas[$key]['valor'] ?>" data-tipo="B"><img src="imagenes/persianas/p<?= $icon ?>.png"></button>

                                    </div>
                                    <?php $i++; ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php foreach ($dispositivos[$room['id']] as $dispositivo) { ?>
                    
                    <div class="card text-white bg-dark card-elem card-elem-dis"  data-codigo="<?= $dispositivo['codigo'] ?>" data-tipoard="<?= $dispositivo['tipo_arduino'] ?>">
                        <div class="card-header">
                            <img src="imagenes/dispositivos/<?= $dispositivo['icono'] ?>.png?r=10">
                            <p><?= $dispositivo['nombre'] ?></p>
                            <?php
                            $switch_v = $codigosPersianas['per_switch'.$dispositivo['switch']]['valor'];
                            if ($dispositivo['tipo'] == 'P') {
                                $orden = $codigosPersianas['per_switch_pulsador']['valor']; ?>
                                <div class="pulsador">
                                    <button type="button" class="btn btn-outline-light btn-circle but-puls" style="right: 10px; position: absolute;border: 1px solid white!important;" data-switch_v="<?= $switch_v ?>" data-orden="<?= $orden ?>" data-tipo="<?= $dispositivo['tipo'] ?>">
                                        <i class="fas fa-circle"></i>
                                    </button>
                                </div>
                            <?php } else {
                                $orden = ''; ?>
                                <div class="switch-slider">
                                    <div class="custom-control custom-switch custom-switch-xl">
                                        <input type="checkbox" class="custom-control-input dispositivo-input" id="customSwitch<?= $dispositivo['id'] ?>" data-switch="<?= $dispositivo['switch'] ?>" data-switch_v="<?= $switch_v ?>" data-orden="<?= $orden ?>" data-tipo="<?= $dispositivo['tipo'] ?>">
                                        <label class="custom-control-label" for="customSwitch<?= $dispositivo['id'] ?>"></label>
                                    </div>
                                </div>
                            <?php } ?>
                            
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php include ('footer.php') ?>
<script>
    orden_per_solicitar = <?= $codigosPersianas['per_solicitar']['valor'] ?>;
    rooms = <?= json_encode($rooms) ?>;
    codigosPersianas = <?= json_encode($codigosPersianas); ?>
</script>
<script src="rooms.js?r=20200705" charset="utf-8"></script>