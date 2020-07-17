<?php
$titulo = 'Persianas';
$imagen = 'persianas';

if (!empty($_POST)) {
    session_start();
    require 'auxiliar.php';
    comprobarLogueado($pdo);
    $jsondata = [];
    $username = ucwords($_SESSION['usuario']['usuario']);
    $codigosPersianas = getCodigosPersianas($pdo);

    if (isset($_POST['codigo_total'])) {
        $codigo_total = $_POST['codigo_total'];
        $codigo = substr($codigo_total, 0, 2);
        $orden = $_POST['orden'];
        $pers = substr($codigo_total, 3);
        $nombreOrden = '';
        $nombreLugar = '';
        $codigoSolicitar = '';


        foreach ($codigosPersianas as $key => $codigosPersiana) {
            if ($codigosPersiana['valor'] == $orden) {
                $nombreOrden = $codigosPersiana['adicional'];
            } elseif ($codigosPersiana['valor'] == $codigo_total) {
                $nombreLugar = $codigosPersiana['adicional'];
            } elseif ($key == 'per_solicitar') {
                $codigoSolicitar = $codigosPersiana['valor'];
            }
        }


        if ($orden == $codigosPersianas['per_parar']['valor']) {
            exec("sudo kill $(ps aux | grep per_varias | grep -v grep | awk '{print $2}')", $lineas, $retVar);
            $retVar = 0;
        } else {
            exec("ps aux | grep per_varias | grep -v grep | wc -l", $lineas, $retVar);

            if ($lineas[0] > 0) {
                $jsondata["success"] = false;
                $jsondata["message"] = 'Existe una orden ejecutándose';
                $retVar = 1;
            }
        }
        
        if ($retVar == 0) {
            $jsondata["success"] = true;

            exec("sudo python /home/bear/py_scripts/per_varias.py $codigo $orden \"$pers\" \"$nombreOrden\" $codigoSolicitar > /dev/null 2>/dev/null &", $output, $retVar);
            // Logs
            if ($nombreOrden != '' && $nombreLugar != '') {
                $mensaje = ucfirst($nombreOrden) . ' persianas ' . $nombreLugar . ' por ' . $username;
                escribirLog($pdo, $mensaje);
                exec("sudo python /home/bear/py_scripts/enviar_hangouts.py \"$mensaje\"");
            }
        } else {
            $jsondata["success"] = false;
            if (!isset($jsondata["message"])) {
                $jsondata["message"] = 'Error al enviar la orden';
            }
        }

    }

    header('Content-type: application/json; charset=utf-8');
    echo json_encode($jsondata, JSON_FORCE_OBJECT);
    exit();
}

include ('header.php');

$rooms = getHabitaciones($pdo);
$nombresHabitaciones = [];

foreach ($rooms as $key => $value) {
    $nombresHabitaciones[$value['codigo']] = $value['nombre'];
}
$codigosPersianas = getCodigosPersianas($pdo);

$dists = [
    'all',
    'palta',
    'pbaja',
    'paonorte',
    'paosur'
];

$ordenIcono = [
    'per_bajar' => '0',
    'per_pos1' => '2',
    'per_pos2' => '5',
    'per_pos3' => '7',
    'per_subir' => '10',
];
?>

<!-- Modal -->
<div class="modal fade" id="infoPerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content card bg-light">
            <div class="modal-header card-header">
                <h5 class="modal-title" id="infoPerModalTitle"></h5>
            </div>
            <div class="modal-body" id="infoPerModalContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php foreach ($dists as $dist) { ?>
<div class="row justify-content-center no-margin">
    <div class="col-md-6 no-padding">
        <div class="card text-white bg-dark card-collapse info-data info-data-all" data-codigo="<?= $codigosPersianas['per_'.$dist]['valor'] ?>">
            <div class="card-header">
                <img src="imagenes/point.png?r=4"> <?= ucwords($codigosPersianas['per_'.$dist]['adicional']) ?>
                <button type="button" class="btn btn-outline-light btn-circle btn-info-per">
                    <i class="fas fa-info-circle"></i>
                </button>
                <button type="button" class="btn btn-dark btn-chevron">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="card-body" style="display: none;">
                <div class="card text-white bg-dark card-elem card-elem-per" style="display:block">
                    <div class="card-header">
                        <div class="row text-center btn-per-pos btn-per-pos-conj">
                            <?php $i = 0; ?>
                            <?php foreach ($ordenIcono as $key => $icon) { ?>
                                <div class="col">
                                    <button type="button" class="btn btn-outline-light btn-circle btn-por" data-pos="<?= $blind['posicion'.($i)]?: '0' ?>" data-orden="<?= $codigosPersianas[$key]['valor'] ?>"><img src="imagenes/persianas/p<?= $icon ?>.png"></button>
                                </div>
                                <?php $i++; ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<div style="border-top: 2px solid rgba(52, 58, 64, 0.6); margin: 12px 34px;">
</div>

<div class="row justify-content-center info-data-all no-margin" data-codigo="<?= $codigosPersianas['per_all']['valor'] ?>">
<button class="btn btn-dark btn-per" style="font-size: 18px;padding: 11px 11px 9px 11px;border-radius: 6px;" data-orden="<?= $codigosPersianas['per_parar']['valor'] ?>">
    <div style="display: inline-block;vertical-align: top;margin-top: 2px;">Parar todo</div>

    <i style="color:#dc3545;font-size: 31px;" class="far fa-stop-circle"></i>

</button>

</div>

<?php include ('footer.php') ?>

<script>
    nombresHabitaciones = <?= json_encode($nombresHabitaciones) ?>;

    $('.btn-info-per').on('click', function() {
        let codigosCompleto = $(this).closest('.info-data-all').data('codigo');
        let codigos = codigosCompleto.split(' ');
        let titulo = $(this).closest('.card-header').text().trim();
        $('#infoPerModalTitle').text(titulo);
        codigos.shift();
        $('#infoPerModalContent').empty();
        let ulItem = $('<ul/>', {'class': 'list-group'});
        codigos.forEach(function(item) {
            let liItem = $('<li/>', {
                'html': nombresHabitaciones[item],
                'class': 'list-group-item list-group-item-info'
            });
            ulItem.append(liItem);
        });
        $('#infoPerModalContent').append(ulItem);


        $('#infoPerModal').modal('show');
    });

    $('.card-elem .btn-por, .info-data-all .btn-per').on('click', function() {
        boton = $(this);
        let codigo_total = $(this).closest('.info-data-all').data('codigo');
        let orden = $(this).data('orden');

        vibrar();
        $.ajax({
            url: $(location).attr('pathname'),
            type: 'POST',
            data: {codigo_total: codigo_total, orden: orden},
            dataType: "json",
            success: function(data) {
                if (data.success) {
                    textoPer = '';
                } else {
                    alertPersonalizado(data.message);
                }
            },
            error: function(e) {
                alertPersonalizado(e.message);
            }
        });
    });

    $('.card-collapse > .card-header > .btn-chevron').on('click', function() {
        let objeto_pulsado = $(this);
        pestanas = $('.info-data > .card-body');
        pestanas.each(function(index, item) {
            if ($(item).css('display') != 'none') {
                let icono = $(item).closest('.info-data').find('.btn-chevron').find('i');
                icono.removeClass('fa-chevron-down');
                icono.addClass('fa-chevron-right');
                $(item).slideUp();
            }
        });

        if (objeto_pulsado.closest('.info-data').find('.card-body').css('display') == 'none') {
            desplegar(objeto_pulsado);
        }
    });
</script>
