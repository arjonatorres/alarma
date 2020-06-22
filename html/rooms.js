var ready = false;

$('.card-collapse > .card-header > .btn-chevron').on('click', function() {
    sincronizando = true;
    let objeto_pulsado = $(this);
    if (objeto_pulsado.find('i').hasClass('fa-chevron-down')) {
        desplegar(objeto_pulsado);
        return;
    }

    $('.loader').fadeIn('fast');
    let orden = orden_per_solicitar;
    let codigo = $(this).closest('.info-data').data('codigo');
    let tipohab = $(this).closest('.info-data').data('tipohab');

    $.ajax({
        url: $(location).attr('pathname'),
        type: 'POST',
        data: {tipo_envio: 'recibir', codigo: codigo, orden: orden},
        dataType: "json",
        success: function(data) {
            if (data.success) {
                let datos = Object.values(data.message);
                desplegar(objeto_pulsado);
                tipo_arduino = tipohab.split('');
                if (tipo_arduino[0] == 'P') {
                    let pertemp = objeto_pulsado.closest('.info-data').find('.card-elem-per');
                    actuadores = objeto_pulsado.closest('.info-data').find('.card-elem-act');
                    let alturaPersiana = datos[2];
                    let pos1 = pertemp.data('pos1');
                    let pos2 = pertemp.data('pos2');
                    let pos3 = pertemp.data('pos3');
                    let pos4 = pertemp.data('pos4');
                    let estados = datos.slice(3, 3 + actuadores.length);
                    valoresPersiana(pertemp, alturaPersiana, pos1, pos2, pos3, pos4);
                    valoresActuadores(actuadores, estados);
                }
            } else {
                alertPersonalizado(data.message);
                return false;
            }
        },
        error: function(e) {
            alertPersonalizado(e.message);
            return false;
        },
        complete: function(e) {
            $(".loader").fadeOut('fast');
            sincronizando = false;
        }
    });
});

function valoresActuadores(actuadores, estados) {
    actuadores.each(function(index) {
        let inputact = $(this).find('input');
        inputact.prop('checked')
        if (estados[index] == 1 && !inputact.prop('checked')) {
            inputact.trigger('click');
        } else if (estados[index] == 0 && inputact.prop('checked')) {
            inputact.trigger('click');
        }
    });
}

function valoresPersiana(perelem, altura, pos1, pos2, pos3, pos4) {
    let percent = Math.round((altura*100)/pos4);
    let iconNumber = Math.round(percent/10);
    let textoPer = ''
    if (percent == 0) {
        textoPer = 'Cerrada';
    } else {
        textoPer = 'Abierta (' + percent + '%)';
    }
    perelem.find('.altura-per').text(textoPer);
    perelem.find('.icon-per').attr('src', 'imagenes/persianas/p' + iconNumber + '.png');
}

$('input.actuador-input').on('click', function(e) {
    if (sincronizando) {
        return;
    }
    if (ready) {
        ready = false; // reset flag
        return; // let the event bubble away
    }

    e.preventDefault();
    let boton = $(this);
    let codigo = $(this).closest('.info-data').data('codigo');
    let orden = $(this).data('orden');
    let tipo = $(this).data('tipo');

    $.ajax({
        url: $(location).attr('pathname'),
        type: 'POST',
        data: {tipo_envio: 'enviar', codigo: codigo, orden: orden, tipo: tipo},
        dataType: "json",
        success: function(data) {
            if (data.success) {
                boton.trigger('click');
            } else {
                alertPersonalizado(data.message);
                return false;
            }
        },
        error: function(e) {
            alertPersonalizado(e.message);
            return false;
        }
    });
    ready = true; // set flag
   
});

$('.card-elem .btn-per, .card-elem .btn-por').on('click', function() {
    let codigo = $(this).closest('.card-header').data('codigo');
    let orden = $(this).data('orden');
    let tipo = $(this).data('tipo');

    $.ajax({
        url: $(location).attr('pathname'),
        type: 'POST',
        data: {tipo_envio: 'enviar', codigo: codigo, orden: orden, tipo: tipo},
        dataType: "json",
        success: function(data) {
            if (data.success) {
            } else {
                alertPersonalizado(data.message);
            }
        },
        error: function(e) {
            alertPersonalizado(e.message);
        }
    });
});