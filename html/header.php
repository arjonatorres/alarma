<?php
session_start();

require 'auxiliar.php';
comprobarLogueado($pdo);
$salida = leerEstadoAlarma($pdo);
$sensores = getSensores($pdo);
$boton = getBotonEstado($sensores);
?>

<html>
    <head>
        <title>BeAr Control</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
        <script src="auxiliar.js?r=20200624" charset="utf-8"></script>
        <link rel="icon" href="http://bearcontrol.ddns.net/imagenes/home.png" type="image/png">
        <link rel=StyleSheet href="estilo.css?r=20200626" type="text/css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
<!--        <meta name='viewport' content='user-scalable=0'>-->
    </head>
    <body>
        <script>clase_boton = "<?= $boton ?>"</script>
        <div class="loader"></div>
        <!-- Modal -->
        <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content card bg-light">
              <div class="modal-header card-header">
                <h5 class="modal-title" id="alertModalTitle"></h5>
              </div>
              <div class="modal-body" id="alertModalContent"></div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Alarma -->
        <div class="modal fade" id="alarmModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content card bg-light">
              <div class="modal-body" id="alarmModalContent">
                <div id="clickFondo" class="col-sm text-center">
                    <button id="button-alarm-modal" data-estado="<?= $salida ?>" type="button" class="btn <?= $salida == '1'? $boton: 'btn-success' ?> btn-circle btn-xl">
                        <i class="fas fa-lock"></i>
                    </button>
                </div>
              </div>
            </div>
          </div>
        </div>


        <div class="card text-white bg-info card-prime">
            <div class="card-header text-center">
                <h1><?= $titulo?: '<span style="color: #aaf0f2">BeAr</span> Control' ?></h1>
                <img class=menu src="imagenes/<?= $imagen?: 'home' ?>.png">
            </div>
            <div class="card-body">