<?php
$titulo = 'Camaras';
$imagen = 'camaras';

$url = '192.168.1.18';
$puerto = '88';
$user = 'test';
$pass = 'test12345';

include ('header.php');?>

<div class="row justify-content-center no-margin">
    <div class="col-md-6 no-padding">
        <div class="card text-white bg-dark card-collapse info-data">
            <div class="card-header">
                <img src="imagenes/habitaciones/salon.png"> Salón
                <button type="button" class="btn btn-dark btn-chevron">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="card-body" style="padding: 5px;">
            	<img id="imagen" style="width:100%">
            </div>
        </div>
    </div>
</div>


<?php include ('footer.php') ?>

<script type='text/javascript'>
	var url='http://<?php echo"$url:$puerto/cgi-bin/CGIProxy.fcgi?cmd=snapPicture2&usr=$user&pwd=$pass";?>';
	var I=new Date().getTime();
	function refresca() {
		document.getElementById("imagen").src = url + '&' + I++;
	}

	$( document ).ready(function() {
		refresca();
	});

	$('#imagen').on('load', function () {
		setTimeout('refresca()',10);
	});
	$('#imagen').on('error', function () {
		setTimeout('refresca()',100);
	});
	$('.card-collapse > .card-header > .btn-chevron').on('click', function() {
	    desplegar($(this));
	});
</script>
