$(document).ready(function() {
    $('.go').on('click', function() {
        var destino = $(this).data('href');
        if (destino != '') {
            vibrar();
            location.href = destino + ".php";
        }
    });

    $('#icono-estado-alarma').on('click', function() {
        if ($(location).attr('pathname') == '/alarma.php') {
            return;
        }
        vibrar();
        $('#alarmModal').modal('show');
    });

    $('#button-alarm-modal').on('click', function() {
        vibrar();
        $('#button-alarm-modal').css({'box-shadow': ''});
        $('#button-alarm-modal').css({'background-color': ''});
        $('.loader').fadeIn('fast');
        estado = $(this).data('estado') == '0'? '1': '0';
        if (clase_boton == 'btn-warning' && estado != '0') {
            estado = '2';
        }
        $.ajax({
            url: '/alarma.php',
            type: 'POST',
            data: {estado_alarma: estado},
            dataType: "json",
            success: function(data) {
                if (data.success) {
                    cambiarModal();
                    $('#button-alarm-modal').css({'box-shadow': '0 0 0 0.0rem'});
                    if ($('#button-alarm-modal').hasClass('btn-success')) {
                        $('#button-alarm-modal').css({'background-color': '#28a745'});
                    } else if ($('#button-alarm-modal').hasClass('btn-danger')) {
                        $('#button-alarm-modal').css({'background-color': '#dc3545'});
                    } else if ($('#button-alarm-modal').hasClass('btn-warning')) {
                        $('#button-alarm-modal').css({'background-color': '#ffc107'});
                    }
                    setTimeout(function() { $('#alarmModal').modal('hide'); }, 500);
                    
                } else {
                    alertPersonalizado(data.message);
                }
            },
            error: function(e) {
                ajaxError(e);
            },
            complete: function(e) {
                $('.loader').fadeOut('fast');
            }
        });
    });
});

function desplegar(objeto, selfObj = false) {
    let icono = objeto.find('i');
    if (icono.hasClass('fa-chevron-right')) {
        icono.removeClass('fa-chevron-right');
        icono.addClass('fa-chevron-down');
    } else if (icono.hasClass('fa-chevron-down')) {
        icono.removeClass('fa-chevron-down');
        icono.addClass('fa-chevron-right');
    }
    if (selfObj) {
        objeto.next().slideToggle();
    } else {
        objeto.parent().next().slideToggle();
    }
    
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

function cambiar() {
    boton = $('#button-alarm, #icono-estado-alarma');
    all = $('input').length == $('input:checked').length;
    if (boton.hasClass('btn-success')) {
        boton.removeClass('btn-success');
        clase = 'btn-' + (all? 'danger': 'warning');
        boton.addClass(clase);
        $('#button-alarm').data('estado', 1);
    } else {
        boton.removeClass('btn-danger');
        boton.removeClass('btn-warning');
        boton.addClass('btn-success');
        $('#button-alarm').data('estado', 0);
    }
}

function cambiarModal() {
    boton = $('#button-alarm-modal, #icono-estado-alarma');
    if (boton.hasClass('btn-success')) {
        boton.removeClass('btn-success');
        clase = clase_boton;
        boton.addClass(clase);
        $('#button-alarm-modal').data('estado', 1);
    } else {
        boton.removeClass('btn-danger');
        boton.removeClass('btn-warning');
        boton.addClass('btn-success');
        $('#button-alarm-modal').data('estado', 0);
    }
}
