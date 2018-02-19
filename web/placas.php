<?php
session_start();
?>
<?php
require 'auxiliar.php';
if (!comprobarLogueado()){
    return;
}
?>
<?php
if (isset($_GET['fecha'])) {
    header('Content-Type: application/json;charset=utf-8');
    $fecha = $_GET['fecha'];
    $res = [];
    $pdo = conectar();
    $resultado = $pdo->query("SELECT TEMP
                                FROM PLACAS
                               WHERE CREATED_AT::DATE = '$fecha'
                            ORDER BY CREATED_AT");
    $res['results'] = $resultado->fetchAll(PDO::FETCH_COLUMN); // $salida4
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

    echo json_encode($res);
    return;
}

$salida = file_get_contents('/home/pi/estado_alarma.txt');
$fecha = date("Y-m-d");

$pdo = conectar();
$resultado = $pdo->query("SELECT TEMP
    FROM PLACAS
    ORDER BY CREATED_AT DESC
    LIMIT 1");
    $actual = $resultado->fetchColumn(); //$salida5
?>
<html>
    <head>
        <title>Placas</title>
        <script src="auxiliar.js" charset="utf-8"></script>
        <script src="Chart.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <!-- <link rel="shortcut icon" href="arjonatorres.ddns.net/php/mifavicon.png" type="image/x-icon"> -->
        <link rel=StyleSheet href="estilo.css" type="text/css">
    </head>

<body>
	<?php
	cabeceraMenu('Placas solares', 'solar-panel-icon2');
	?>

	<table class=boton cellspacing="40" border="0">
    	<tr class=boton>
    		<td colspan=3 class=boton>
    			<p id="estado" align="center" style="font-size:80px; margin:106px"><b><?= $actual?> ºC</b></p>
    		</td>
    	</tr>
	<tr class=boton>
		<td colspan=3 class=boton>
            <div id="canvas-holder">
    	        <table width="100%" >
    	            <tr>
    		            <td width="65%">
    			            <p style="margin-left:100px"><br>
                            <font size=8><b>Fecha: &nbsp;</b></font>
                            <input id="fech" type="date" name="ffecha" value="<?= $fecha ?>" style="width:250px;height:80px; font-size:40px">
                            </p>
    		            </td>
    		            <td>
        		            <p style="margin-top:10px; font-size:40px">
        			        <font color="red"><b><span id="maxc">Max: --- ºC</span></b></font><br>
        			        <font color="blue"><b><span id="minc">Min: --- ºC</span></b></font><br>
        			        <font color="orange"><b><span id="medc">Med: --- ºC</span></b></font><br>
        		            </p>
    		            </td>
    	            </tr>
    	        </table>
                <div class="canv">
                    <canvas id="chart-area" width="850" height="500" style="margin:30px; margin-top:0px"></canvas>
                </div>

            </div>
		</td>
	</tr>
	</table>

	<?php pieMenu($salida); ?>

    <script>
        $(document).ready(envio);
        $('#fech').on('change', envio);
        temphp = parseInt('<?= $actual?>');
        pintaTemp();

        function pintaTemp() {
            var res = '';
            switch (true) {
                case temphp < 30:
                    res = '#518eff';
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
                case temphp >= 50:
                    res = '#fa2306';
                    break;
            }
            estado.style.color = res;
        }

        function pintaDatos(resultados) {
            var lineChartData = {
                labels : ["0","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23"],
                datasets : [
                    {
                        label: "Temperatura dia actual",
                        fillColor : "rgba(220,220,220,0.2)",
                        strokeColor : "#6b9dfa",
                        pointColor : "#1e45d7",
                        pointStrokeColor : "#fff",
                        pointHighlightFill : "#fff",
                        pointHighlightStroke : "rgba(220,220,220,1)",
                        data : resultados
                    }
                ]
            }
            var ctx = document.getElementById("chart-area").getContext("2d");
            window.myPie = new Chart(ctx).Line(
                lineChartData,
                {
                    responsive:false,
                    scaleFontColor: "white",
                    scaleFontSize: 24,
                    scaleLineColor: "white",
                    scaleGridLineColor: "white",
                    scaleOverride : true,
                    scaleSteps : 5,
                    scaleStepWidth : 10,
                    scaleStartValue : 20
                }
            );
        }

        function envio() {
            $.getJSON('placas.php', {fecha: $('#fech').val()}, function(data) {
                $('#maxc').text(`Max: ${data.max} ºC`);
                $('#minc').text(`Max: ${data.min} ºC`);
                $('#medc').text(`Max: ${data.avg} ºC`);
                $('#chart-area').remove();
                $('.canv').append($('<canvas id="chart-area" width="850" height="500" style="margin:30px; margin-top:0px"></canvas>'));
                pintaDatos(data.results);
            });
        }
    </script>
</body>
</html>
