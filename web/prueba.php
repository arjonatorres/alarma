<?php
session_start();
?>
<?php
require 'auxiliar.php';
if (!comprobarLogueado()){
    return;
}
?>
<!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
            <style type="text/css">
                .panel {
                    background: rgba(0, 0, 0, 0.4)!important;
                    border-width: 0px!important;
                    border-radius: 8px!important;

                }
                .panel-body:active {
                    background: rgba(0, 0, 0, 0.3)!important;
                    border-top-left-radius: 8px!important;
                    border-top-right-radius: 8px!important;
                }
                .panel-heading {
                    background: rgba(0, 0, 0, 0.4)!important;
                    padding: 3px!important;
                    border-bottom:0px!important;
                    color: white!important;
                    border-bottom-left-radius: 8px!important;
                    border-bottom-right-radius: 8px!important;
                    border-top-left-radius: 0px!important;
                    border-top-right-radius: 0px!important;
                }
                .panel-body {
                    padding: 8px!important;
                }
                .col-xs-4 {
                    padding-left: 10px!important;
                    padding-right: 10px!important;
                }
                .boton {
                    background-color: #222222!important;
                    padding-top: 14px!important;
                    border-radius: 0px;
                }
                body {
                    background-image: url(imagenes/fondo.jpg);
                    background-repeat: no-repeat;
        			background-attachment: fixed;
                    background-size: cover;
                    background-color: transparent;
                    margin:0px;
                    padding-top: 70px;
                    color: white;
            	}
            </style>
            <title>FA</title>
        </head>
        <body>
            <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
                <div class="container">
                    <!-- El logotipo y el icono que despliega el menú se agrupan
                    para mostrarlos mejor en los dispositivos móviles -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse"
                            data-target=".navbar-ex1-collapse">
                            <span class="sr-only">Desplegar navegación</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    <a class="navbar-brand" style="color: white;" href="http://arjonatorres.ddns.net/php/prueba.php">Casa</a>
                    </div>

                    <!-- Agrupar los enlaces de navegación, los formularios y cualquier
                    otro elemento que se pueda ocultar al minimizar la barra -->
                    <div class="collapse navbar-collapse navbar-ex1-collapse">
                        <ul class="nav navbar-nav">
                            <li class="active"><a href="#">Enlace #1</a></li>
                            <li><a href="#">Enlace #2</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="container">
                <div class="row">
                    <div class="col-xs-4 col-md-4">
                        <div class="panel panel-default text-center">
                            <div class="panel-body">
                                <img src="imagenes/blind-icon.png" class="img-responsive center-block" alt="Imagen responsive">
                            </div>
                            <div class="panel-heading">
                                <span class="text-center">Persianas</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-4 col-md-4">
                        <div class="panel panel-default text-center">
                            <div class="panel-body cuerpo-panel">
                                <img src="imagenes/bulb-icon.png" class="img-responsive center-block" alt="Imagen responsive">
                            </div>
                            <div class="panel-heading cabecera-panel">
                                <span class="text-center">Luces</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-4 col-md-4">
                        <div class="panel panel-default text-center">
                            <div class="panel-body cuerpo-panel">
                                <img src="imagenes/thief-icon.png" class="img-responsive center-block" alt="Imagen responsive">
                            </div>
                            <div class="panel-heading cabecera-panel">
                                <span class="text-center">Alarma</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-4 col-md-4">
                        <div class="panel panel-default text-center">
                            <div class="panel-body">
                                <img src="imagenes/blind-icon.png" class="img-responsive center-block" alt="Imagen responsive">
                            </div>
                            <div class="panel-heading">
                                <span class="text-center">Persianas</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-4 col-md-4">
                        <div class="panel panel-default text-center">
                            <div class="panel-body cuerpo-panel">
                                <img src="imagenes/bulb-icon.png" class="img-responsive center-block" alt="Imagen responsive">
                            </div>
                            <div class="panel-heading cabecera-panel">
                                <span class="text-center">Luces</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-4 col-md-4">
                        <div class="panel panel-default text-center">
                            <div class="panel-body cuerpo-panel">
                                <img src="imagenes/thief-icon.png" class="img-responsive center-block" alt="Imagen responsive">
                            </div>
                            <div class="panel-heading cabecera-panel">
                                <span class="text-center">Alarma</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-4 col-md-4">
                        <div class="panel panel-default text-center">
                            <div class="panel-body">
                                <img src="imagenes/blind-icon.png" class="img-responsive center-block" alt="Imagen responsive">
                            </div>
                            <div class="panel-heading">
                                <span class="text-center">Persianas</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-4 col-md-4">
                        <div class="panel panel-default text-center">
                            <div class="panel-body cuerpo-panel">
                                <img src="imagenes/bulb-icon.png" class="img-responsive center-block" alt="Imagen responsive">
                            </div>
                            <div class="panel-heading cabecera-panel">
                                <span class="text-center">Luces</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-4 col-md-4">
                        <div class="panel panel-default text-center">
                            <div class="panel-body cuerpo-panel">
                                <img src="imagenes/thief-icon.png" class="img-responsive center-block" alt="Imagen responsive">
                            </div>
                            <div class="panel-heading cabecera-panel">
                                <span class="text-center">Alarma</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <nav class="navbar navbar-inverse navbar-fixed-bottom" role="navigation">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-4 col-md-4">
                            <button type="button" class="btn btn-lg boton" style="padding-top: 12px">
                                <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
                            </button>
                        </div>
                        <div class="col-xs-4 col-md-4">
                            <button type="button" class="btn btn-lg center-block boton" style="padding-top: 14px!important">
                                <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
                            </button>
                        </div>
                        <div class="col-xs-4 col-md-4 text-right">
                            <button type="button" class="btn btn-lg boton" style="padding-top: 12px">
                                <span class="glyphicon glyphicon-book" aria-hidden="true"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </nav>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
