<?php
$titulo = 'Configuración';
$imagen = 'config';
include ('header.php');
// $tempActual = tempActual($pdo);
?>

<div class="container-fluid">
    <div class="row boton-row">
        <div class="col-4 text-center col-md-2">
            <button type="button" data-href="config_persianas" class="btn btn-dark boton go">
                <img class=boton src="imagenes/persianas.png?r=2"><p class="btn-title">Persianas</p>
            </button>
        </div>
    </div>
</div>

<?php include ('footer.php') ?>
