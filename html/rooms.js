var ready = false;
var evento = [];
const MULTIP = 1000;

rooms.forEach(function (item, index) {
	evento[item['codigo']] = undefined
});
$('.card-collapse > .card-header > .btn-chevron').on('click', function() {
    let objeto_pulsado = $(this);
    let codigo = objeto_pulsado.closest('.info-data').data('codigo');
    vibrar();
    if (objeto_pulsado.find('i').hasClass('fa-chevron-down')) {
    	if (typeof evento[codigo] !== 'undefined') {
			clearTimeout(evento[codigo]);
			evento[codigo] = undefined;
		}
        desplegar(objeto_pulsado);
        return;
    }

	recibirDatos($(this));
});

function recibirDatos(objeto_pulsado) {
	sincronizando = true;
    $('.loader').fadeIn('fast');
    let codigo = objeto_pulsado.closest('.info-data').data('codigo');
    let orden = orden_per_solicitar;
    let tipohab = objeto_pulsado.closest('.info-data').data('tipohab');
    let num_bytes = 0;
    let tipo_arduino = tipohab.split('');
    if (tipo_arduino[0] == 'R') {
    	num_bytes = 1 + parseInt(tipo_arduino[1]);
    } else {
    	num_bytes = 3 + parseInt(tipo_arduino[1]);
    }

    $.ajax({
        url: $(location).attr('pathname'),
        type: 'POST',
        data: {tipo_envio: 'recibir', codigo: codigo, orden: orden, num_bytes: num_bytes},
        dataType: "json",
        success: function(data) {
            if (data.success) {
                let datos = Object.values(data.message);
                
                if (tipo_arduino[0] == 'P') {
                    let pertemp = objeto_pulsado.closest('.info-data').find('.card-elem-per');
                    let dispositivos = objeto_pulsado.closest('.info-data').find('.card-elem-act');
                    let estado = datos[1];
                    let alturaPersiana = parseInt(datos[2], 16);
                    let pos1 = pertemp.data('pos1');
                    let pos2 = pertemp.data('pos2');
                    let pos3 = pertemp.data('pos3');
                    let pos4 = pertemp.data('pos4');
                    let estados = datos.slice(3, 3 + dispositivos.length);
                    valoresPersiana(pertemp, objeto_pulsado, codigo, estado, alturaPersiana, pos4);
                    valoresDispositivos(dispositivos, estados);
                }
                if (objeto_pulsado.closest('.info-data').find('i').hasClass('fa-chevron-right')) {
                	desplegar(objeto_pulsado);
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
}

function valoresDispositivos(dispositivos, estados) {
    dispositivos.each(function(index) {
        let inputdisp = $(this).find('input');
        // inputdisp.prop('checked');
        if (estados[index] == 1 && !inputdisp.prop('checked')) {
            inputdisp.trigger('click');
        } else if (estados[index] == 0 && inputdisp.prop('checked')) {
            inputdisp.trigger('click');
        }
    });
}

function valoresPersiana(perelem, objeto_pulsado, codigo, estado, altura, pos4) {
    let percent = Math.round((altura*100)/pos4);
    let iconNumber = Math.round(percent/10);
    let textoPer = '';
    let iconName = 'p' + iconNumber + '.png';
    switch (estado) {
    	case codigosPersianas['per_normal']['valor']:
    		if (percent == 0) {
		        textoPer = 'Cerrada';
		    } else {
		        textoPer = 'Abierta (' + percent + '%)';
		    }
		    objeto_pulsado.closest('.info-data').find('.card-elem .btn-per:not(.btn-stop), .card-elem .btn-por').attr('disabled', false);
		    break;
	    case codigosPersianas['per_subiendo']['valor']:
	    	textoPer = 'Subiendo';
	    	iconName = 'subiendo.gif';
	    	if (typeof evento[codigo] === 'undefined') {
	    		posicion_final = parseInt(perelem.data('pos4'));
				evento[codigo] = setTimeout(function() {
        			recibirDatos(objeto_pulsado);
        		}, ((posicion_final-altura)+1)*MULTIP);
			}
			objeto_pulsado.closest('.info-data').find('.card-elem .btn-per:not(.btn-stop), .card-elem .btn-por').attr('disabled', true);
	    	break;
    	case codigosPersianas['per_bajando']['valor']:
    		textoPer = 'Bajando';
    		iconName = 'bajando.gif';
    		if (typeof evento[codigo] === 'undefined') {
				evento[codigo] = setTimeout(function() {
        			recibirDatos(objeto_pulsado);
        		}, ((altura)+1)*MULTIP);
			}
			objeto_pulsado.closest('.info-data').find('.card-elem .btn-per:not(.btn-stop), .card-elem .btn-por').attr('disabled', true);
    		break;
    }

    perelem.data('pos', altura);
    perelem.find('.altura-per').text(textoPer);
    perelem.find('.icon-per').attr('src', 'imagenes/persianas/' + iconName);
}

$('button.but-puls').on('click', function(e) {
    $('.loader').fadeIn('fast');
    vibrar();

    let boton = $(this);
    let codigo = $(this).closest('.info-data').data('codigo');
    let switch_v = $(this).data('switch_v');
    let orden = $(this).data('orden');;

    $.ajax({
        url: $(location).attr('pathname'),
        type: 'POST',
        data: {tipo_envio: 'enviar', codigo: codigo,switch_v: switch_v, orden: orden},
        dataType: "json",
        success: function(data) {
            if (!data.success) {
                alertPersonalizado(data.message);
                return false;
            }
        },
        error: function(e) {
            alertPersonalizado(e.message);
            return false;
        },
        complete: function(e) {
            $('.loader').fadeOut('fast');
        }
    });
   
});

$('input.dispositivo-input').on('click', function(e) {
    if (sincronizando) {
        return;
    }
    if (ready) {
        ready = false; // reset flag
        return; // let the event bubble away
    }

    $('.loader').fadeIn('fast');
	vibrar();
    e.preventDefault();
    let boton = $(this);
    let codigo = $(this).closest('.info-data').data('codigo');
    let switch_v = $(this).data('switch_v');
    let orden = '';
    if (boton.prop('checked')) {
        orden = codigosPersianas['per_encender']['valor'];
    } else {
        orden = codigosPersianas['per_apagar']['valor'];
    }

    $.ajax({
        url: $(location).attr('pathname'),
        type: 'POST',
        data: {tipo_envio: 'enviar', codigo: codigo, switch_v: switch_v, orden: orden},
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
        },
        complete: function(e) {
            $('.loader').fadeOut('fast');
        }
    });
    ready = true; // set flag
   
});

$('.card-elem .btn-per, .card-elem .btn-por').on('click', function() {
	boton = $(this);
    let codigo = $(this).closest('.info-data').data('codigo');
    let orden = $(this).data('orden');
    let tipo = $(this).data('tipo');

    vibrar();
    boton.closest('.info-data').find('.card-elem .btn-per:not(.btn-stop), .card-elem .btn-por').attr('disabled', true);
    $.ajax({
        url: $(location).attr('pathname'),
        type: 'POST',
        data: {tipo_envio: 'enviar', codigo: codigo, orden: orden, tipo: tipo},
        dataType: "json",
        success: function(data) {
            if (data.success) {
            	textoPer = '';
            	posicion_actual = parseInt(boton.closest('.card-elem-per').data('pos'));
            	if (codigosPersianas['per_subir']['valor'] == orden) {
            		posicion_final = parseInt(boton.closest('.card-elem-per').data('pos4'));
            		textoPer = 'Subiendo';
            		boton.closest('.card-header').find('.icon-per').attr('src', 'imagenes/persianas/subiendo.gif');
            		evento[codigo] = setTimeout(function() {
            			recibirDatos(boton);
            		}, ((posicion_final-posicion_actual)+1)*MULTIP);
            	} else if (codigosPersianas['per_bajar']['valor'] == orden) {
            		textoPer = 'Bajando';
            		boton.closest('.card-header').find('.icon-per').attr('src', 'imagenes/persianas/bajando.gif');
            		evento[codigo] = setTimeout(function() {
            			recibirDatos(boton);
            		}, (posicion_actual+1)*MULTIP);
            	} else if (codigosPersianas['per_parar']['valor'] == orden) {
            		boton.closest('.info-data').find('.card-elem .btn-per:not(.btn-stop), .card-elem .btn-por').attr('disabled', false);
            		if (typeof evento[codigo] !== 'undefined') {
            			clearTimeout(evento[codigo]);
            			evento[codigo] = undefined;
            		}
            		recibirDatos(boton);
            	} else {
            		posicion_final = boton.data('pos');
            		if (posicion_final > posicion_actual) {
            			textoPer = 'Subiendo';
	            		boton.closest('.card-header').find('.icon-per').attr('src', 'imagenes/persianas/subiendo.gif');
	            		evento[codigo] = setTimeout(function() {
	            			recibirDatos(boton);
	            		}, ((posicion_final-posicion_actual)+1)*MULTIP);
            		} else if (posicion_final < posicion_actual) {
	            		textoPer = 'Bajando';
	            		boton.closest('.card-header').find('.icon-per').attr('src', 'imagenes/persianas/bajando.gif');
	            		evento[codigo] = setTimeout(function() {
	            			recibirDatos(boton);
	            		}, ((posicion_actual-posicion_final)+1)*MULTIP);
            		} else {
            			boton.closest('.info-data').find('.card-elem .btn-per:not(.btn-stop), .card-elem .btn-por').attr('disabled', false);
            		}
            	}

            	if (textoPer != ''){
            		boton.closest('.card-header').find('.altura-per').text(textoPer);
            	}
            	
            } else {
                alertPersonalizado(data.message);
            }
        },
        error: function(e) {
            alertPersonalizado(e.message);
        }
    });
});