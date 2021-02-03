var ready = false;

$('input.custom-control-input').on('click', function(e) {
    if (ready) {
        ready = false; // reset flag
        return; // let the event bubble away
    }

    $('.loader').fadeIn('fast');
    vibrar();
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
        },
        complete: function(e) {
            $('.loader').fadeOut('fast');
        }
    });
    ready = true; // set flag
   
});


$('#button-alarm').on('click', function() {
    $('.loader').fadeIn('fast');
    vibrar();
    $('#button-alarm').css({'box-shadow': ''});
    $('#button-alarm').css({'background-color': ''});
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
                    alarmaCam(0);
                } else if ($('#button-alarm').hasClass('btn-danger')) {
                    $('#button-alarm').css({'background-color': '#dc3545'});
                    alarmaCam(1);
                } else if ($('#button-alarm').hasClass('btn-warning')) {
                    $('#button-alarm').css({'background-color': '#ffc107'});
                    alarmaCam(1);
                }
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


$('.card-collapse > .card-header > .btn-chevron').on('click', function() {
    desplegar($(this));
});