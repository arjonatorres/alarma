<?php
//    $titulo = 'Casa';
//    $imagen = 'home';
include ('header.php');
$tempActual = tempActual($pdo);
$iconos = [
    [
        'href' => 'persianas',
        'title' => 'Persianas'
    ],
    [
        'href' => 'rooms',
        'title' => 'Habitaciones'
    ],
    [
        'href' => 'alarma',
        'title' => 'Alarma'
    ],
    [
        'href' => 'placas',
        'title' => 'Placas solares'
    ],
    [
        'href' => 'sensores',
        'title' => 'Sensores'
    ],
    [
        'href' => 'config',
        'title' => 'Configuración'
    ],
    [
        'href' => 'logs',
        'title' => 'Logs'
    ],
    [
        'href' => 'eventos',
        'title' => 'Eventos'
    ],
    [
        'href' => 'camaras',
        'title' => 'Camaras'
    ]
];
?>
<div class="card text-white bg-dark card-info">
    <div class="card-header">
        <p class="text-right">Placas solares: <?= $tempActual ?>º</p>
    </div>
</div>

<div class="container-fluid">
    <div class="row boton-row">
        <?php foreach ($iconos as $icono) { ?>
            <div class="col-4 text-center col-md-2">
                <button type="button" data-href="<?= $icono['href'] ?>" class="btn btn-dark boton go">
                    <img class=boton src="imagenes/<?= $icono['href'] ?>.png?r=2"><p class="btn-title"><?= $icono['title'] ?></p>
                </button>
            </div>
        <?php } ?>
    </div>
</div>

<?php include ('footer.php') ?>
