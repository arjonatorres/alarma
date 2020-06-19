<?php
//    $titulo = 'Casa';
//    $imagen = 'home';
include ('header.php');
$tempActual = tempActual($pdo);
?>
<div class="card text-white bg-dark card-info">
    <div class="card-header">
        <p class="text-right">Placas solares: <?= $tempActual ?>º</p>
    </div>
</div>

<div class="container-fluid">
    <div class="row boton-row">
        <div class="col-sm">
            <button type="button" data-href="persianas" class="btn btn-dark boton go">
                <img class=boton src="imagenes/persianas.png"><br>Persianas
            </button>
        </div>
        <div class="col-sm">
            <button type="button" data-href="rooms" class="btn btn-dark boton go">
                <img class=boton src="imagenes/rooms.png"><br>Habitaciones
            </button>
        </div>
        <div class="col-sm">
            <button type="button" data-href="alarma" class="btn btn-dark boton go">
                <img class=boton src="imagenes/thief-icon.png"><br>Alarma
            </button>
        </div>
    </div>
    <div class="row boton-row">
        <div class="col-sm">
            <button type="button" data-href="placas" class="btn btn-dark boton go">
                <img class=boton src="imagenes/solar-panel-icon2.png"><br>Placas solares
            </button>
        </div>
        <div class="col-sm">
            <button type="button" data-href="sensores" class="btn btn-dark boton go">
                <img class=boton src="imagenes/sensor.png"><br>Sensores
            </button>
        </div>
        <div class="col-sm">
            <button type="button" data-href="config" class="btn btn-dark boton go">
                <img class=boton src="imagenes/config-icon.png"><br>Configuración
            </button>
        </div>
    </div>
</div>

<?php include ('footer.php') ?>
