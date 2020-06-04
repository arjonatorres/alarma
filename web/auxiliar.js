$(document).ready(function() {
    $('.go').on('click', function() {
        var destino = $(this).data('href');
        if (destino != '') {
            vibrar();
            location.href = destino + ".php";
        }
    });
});

function refresh() {
    vibrar();
	window.location.reload();
}

function vibrar() {
	navigator.vibrate(50);
}

function casa() {
	vibrar();
	location.href="menu.php";
}
