var ready = false;

$('input.custom-control-input').on('click', function(e) {
    if (ready) {
        ready = false; // reset flag
        return; // let the event bubble away
    }

    e.preventDefault();
    boton = $(this);
    pin_sensor = $(this).data('id');
    activo_sensor = $(this).prop('checked')? '1': '0';
    nombre_sensor = $(this).data('nombre');
    $.ajax({
        url: 'alarma.php',
        type: 'POST',
        data: {pin_sensor: pin_sensor, activo_sensor: activo_sensor, nombre_sensor: nombre_sensor},
        dataType: "json",
        success: function(data) {
            if (data.success) {
                $('#button-alarm').attr('style', '')
                boton.trigger('click');
                if (activo_sensor == '0' && $('#button-alarm').hasClass('btn-danger')) {
                    $('#button-alarm, #icono-estado-alarma').removeClass('btn-danger');
                    $('#button-alarm, #icono-estado-alarma').addClass('btn-warning');
                } else if (activo_sensor == '1' && $('#button-alarm').hasClass('btn-warning') && $('input').length == $('input:checked').length) {
                    $('#button-alarm, #icono-estado-alarma').removeClass('btn-warning');
                    $('#button-alarm, #icono-estado-alarma').addClass('btn-danger');
                }
            } else {
                alertPersonalizado(data.message);
                return false;
            }
        },
        error: function(e) {
            ajaxError(e);
            return false;
        }
    });
    ready = true; // set flag
   
});


$('#button-alarm').on('click', function() {
    vibrar();
    estado = $(this).data('estado') == '0'? '1': '0';
    if (estado == '1' && ($('input').length != $('input:checked').length)) {
        estado = '2';
    }
    $.ajax({
        url: '/alarma.php',
        type: 'POST',
        data: {estado_alarma: estado},
        dataType: "json",
        success: function(data) {
            if (data.success) {
                cambiar();
                $('.card-info p').html(data.message);
                $('#button-alarm').css({'box-shadow': '0 0 0 0.0rem'});
                if ($('#button-alarm').hasClass('btn-success')) {
                    $('#button-alarm').css({'background-color': '#28a745'});
                } else if ($('#button-alarm').hasClass('btn-danger')) {
                    $('#button-alarm').css({'background-color': '#dc3545'});
                } else if ($('#button-alarm').hasClass('btn-warning')) {
                    $('#button-alarm').css({'background-color': '#ffc107'});
                }
            } else {
                alertPersonalizado(data.message);
            }
        },
        error: function(e) {
            ajaxError(e);
        }
    });
});

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

$('.card-collapse > .card-header > .btn-chevron').on('click', function() {
    desplegar($(this));
});