<?php
$titulo = 'Placas solares';
$imagen = 'placas';

if (isset($_GET['fecha'])) {
    session_start();
    require 'auxiliar.php';
    comprobarLogueado($pdo);

    $fecha = $_GET['fecha'];
    $res = [];

    $res['results'] = tempActual($pdo, $fecha);
    $results = $res['results'];
    if (count($results) > 0) {
        $res['max'] = max($results);
        $res['min'] = min($results);
        $res['avg'] = round(array_sum($results) / count($results));
    } else {
        for($i = 0; $i < 24; $i++) {
            $res['results'][] = 0;
        }
        $res['max'] = '---'; $res['min'] = '---'; $res['avg'] = '---';
    }

    header('Content-type: application/json; charset=utf-8');
    // echo json_encode($jsondata, JSON_FORCE_OBJECT);
    echo json_encode($res);
    exit();
}

include ('header.php');

$fecha = date("Y-m-d");
$actual = tempActual($pdo);

?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>


<div class="row justify-content-center no-margin">
    <div class="col-md-6 no-padding">
        <div class="card text-white bg-dark card-collapse card-logs">
            <div class="card-header">
                <div class="text-center" style="margin-top:15px; margin-bottom: 20px;">
                    <img src="imagenes/tank.png" style="height: 180px;">
                    <h1 class="color-temp"><span id="estado" class="badge"><?= $actual?> ºC</span></h1>
                </div>
                <form>
                    <div class="search-placas">
                        <div class="form-group row">
                            <label for="fech" class="col-2 col-form-label" style="padding-right: 0px;padding-left: 8px;">Fecha:</label>
                            <div class="col-5" style="padding: 0px 0px 0px 4px;">
                                <input id="fech" class="form-control" type="date" name="ffecha" value="<?= $fecha ?>">
                            </div>
                            <div class="col-5 text-center" style="padding: 0px 5px 0px 0px;">
                                <button id="diaAnterior" type="button" class="btn btn-light">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button id="diaActual" type="button" class="btn btn-light">
                                    <i class="fas fa-calendar-week"></i>
                                </button>
                                <button id="diaSiguiente" type="button" class="btn btn-light">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body" style="margin-top: 10px;">
                <div class="row">
                    <div class="col-4 no-padding text-right">
                        <h5><span id="maxc" class="badge badge-danger">Max: --- ºC</span></h5>
                    </div>
                    <div class="col-4 no-padding text-center">
                        <h5><span id="medc" class="badge badge-warning">Med: --- ºC</span></h5>
                    </div>
                    <div class="col-4 no-padding">
                        <h5><span id="minc" class="badge badge-primary">Min: --- ºC</span></h5>
                    </div>
                </div>
                <div class="canv" style="margin-top: 15px;">
                    <canvas id="chart-area"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>


<?php include ('footer.php') ?>

    <script>
        fechaPhp = '<?= $fecha ?>';
        $(document).ready(function(e) {
            envio();
        });
        $('#fech').on('change', function(e) {
            envio();
        });

        $('.search-placas').on('click', '.btn', function() {
            if ($('#fech').val() == '') {
                return;
            }

            var idName = $(this).prop('id');
            var fecha = $('#fech').val();
            fechaDate = new Date(fecha);

            switch (idName) {
                case 'diaAnterior':
                    numDias = -1;
                    break;
                case 'diaSiguiente':
                    numDias = 1;
                    break;
                case 'diaActual':
                    fechaDate = new Date();
                    numDias = 0;
                    break;
            }

            fechaDate.setDate(fechaDate.getDate() + numDias);
            var month = ('0' + (fechaDate.getMonth() + 1)).slice(-2);
            var date = ('0' + fechaDate.getDate()).slice(-2);
            var year = fechaDate.getFullYear();
            var shortDate = year + '-' + month + '-' + date;
            $('#fech').val(shortDate);
            envio(shortDate);
        });

        temphp = parseInt('<?= $actual?>');
        pintaTemp();

        function pintaTemp() {
            var res = '';
            resFont = '#000';
            switch (true) {
                case temphp < 30:
                    res = '#518eff';
                    resFont = '#fff';
                    break;
                case temphp >= 30 && temphp < 35:
                    res = '#39eeff';
                    break;
                case temphp >= 35 && temphp < 40:
                    res = '#baffce';
                    break;
                case temphp >= 40 && temphp < 45:
                    res = '#fdff7a';
                    break;
                case temphp >= 45 && temphp < 50:
                    res = '#ffad01';
                    break;
                case temphp >= 50:
                    res = '#fa2306';
                    break;
            }
            estado.style.color = resFont;
            estado.style.backgroundColor = res;
        }

        function pintaDatos(resultados) {
            var lineChartData = {
                labels : ["0","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23"],
                datasets : [
                    {
                        label: '',
                        data : resultados,
                        backgroundColor : "rgba(250,107,107,0.5)",
                        borderColor : "#FF3232",
                        borderWidth: 6,
                        pointBackgroundColor : "#D71E1E",
                        pointBorderColor : "#fff",
                        pointBorderWidth: 2,
                        pointHoverBackgroundColor : "#fff",
                        pointHoverBorderColor : "rgba(220,220,220,1)"
                    }
                ]
            }
            var ctx = document.getElementById("chart-area").getContext("2d");
            var chart = new Chart(ctx, {
                type: 'line',
                data: lineChartData,
                options:
                {
                    responsive: true,
                    legend: {
                        display: false
                    },
                    layout: {
                        padding: {
                            left: 0,
                            right: 0,
                            top: 0,
                            bottom: 0
                        }
                    },
                    scales: {
                        xAxes: [{
                            gridLines: {
                              color: "#FFFFFF"
                            },
                            ticks: {
                              fontColor: "#FFFFFF"
                            }
                        }],
                        yAxes: [{
                            gridLines: {
                              color: "#FFFFFF"
                            },
                            ticks: {
                              fontColor: "#FFFFFF"
                            }
                        }]
                    },
                }
            });
        }

        function envio(fecha) {
            if (fecha == undefined) {
                fecha = $('#fech').val();
            }
            if (fecha == '') {
                return;
            }
            $.getJSON('placas.php', {fecha: fecha}, function(data) {
                $('#maxc').text(`Max: ${data.max} ºC`);
                $('#minc').text(`Min: ${data.min} ºC`);
                $('#medc').text(`Med: ${data.avg} ºC`);
                $('#chart-area').remove();
                $('.canv').append($('<canvas id="chart-area"></canvas>'));
                pintaDatos(data.results);
            });
        }
    </script>
