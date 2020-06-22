$(document).ready(function() {
    $('.go').on('click', function() {
        var destino = $(this).data('href');
        if (destino != '') {
            vibrar();
            location.href = destino + ".php";
        }
    });
});

function desplegar(objeto) {
    icono = objeto.find('i');
    if (icono.hasClass('fa-chevron-right')) {
        icono.removeClass('fa-chevron-right');
        icono.addClass('fa-chevron-down');
    } else if (icono.hasClass('fa-chevron-down')) {
        icono.removeClass('fa-chevron-down');
        icono.addClass('fa-chevron-right');
    }
    objeto.parent().next().slideToggle();
}

function refresh() {
    vibrar();
	window.location.reload();
}

function vibrar() {
	navigator.vibrate(50);
}

function casa() {
	vibrar();
	location.href="index.php";
}

function ajaxError(e) {
    var temporal = document.createElement("div");
    temporal.innerHTML = e.responseText;
    var texto = temporal.textContent || temporal.innerText || "Error";
    alert(texto.trim());
}

function alertPersonalizado(texto) {
    $('#alertModalTitle').text('Info')
    $('#alertModalContent').text(texto);
    $('#alertModal').modal('show');
}
