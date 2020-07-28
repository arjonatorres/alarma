var ready = false;
evento = {};
const MULTIP = 1000;

let todasPersianas = $('.card-elem-per');
todasPersianas.each(function (index) {
	evento[$(this).data('codigo')] = undefined
});
$('.card-collapse > .card-header > .btn-chevron').on('click', function() {
    let objeto_pulsado = $(this);
    let id = objeto_pulsado.closest('.info-data').data('id');
    vibrar();
    if (objeto_pulsado.find('i').hasClass('fa-chevron-down')) {
    	if (typeof evento[id] !== 'undefined') {
			clearTimeout(evento[id]);
			evento[id] = undefined;
		}
        desplegar(objeto_pulsado);
        return;
    }

	recibirDatos($(this));
});

function recibirDatos(objeto_pulsado) {
	sincronizando = true;
    $('.loader').fadeIn('fast');
    let codigos = [];
    let nums_bytes = [];
    let tipos_arduino = {};
    let dispositivos = objeto_pulsado.closest('.info-data').find('.card-elem-dis');
    dispositivos.each(function(index) {
        let codigo = $(this).data('codigo');
        codigos.push(codigo);
        let tipoard = $(this).data('tipoard');
        let tipo_arduino = tipoard.split('');
        tipos_arduino[codigo] = tipo_arduino[0];
        if (tipo_arduino[0] == 'R') {
            nums_bytes.push(1 + parseInt(tipo_arduino[1]));
        } else {
            nums_bytes.push(3 + parseInt(tipo_arduino[1]));
        }
    });
    let persianas = objeto_pulsado.closest('.info-data').find('.card-elem-per');
    persianas.each(function(index) {
        let codigo = $(this).data('codigo');
        codigos.push(codigo);
    });
    codigos = [...new Set(codigos)]

    let orden = orden_per_solicitar;

    $.ajax({
        url: $(location).attr('pathname'),
        type: 'POST',
        data: {tipo_envio: 'recibir', codigos: codigos, orden: orden, nums_bytes: nums_bytes},
        dataType: "json",
        success: function(data) {
            if (data.success) {
                let datosTotal = Object.values(data.message);
                for (let i=0; i < datosTotal.length; i++) {
                    datos = Object.values(datosTotal[i]);
                    let codigo = datos[0];
                    if (tipos_arduino[codigo] == 'P') {
                        let pertemp = objeto_pulsado.closest('.info-data').find('.card-elem-per').filter('[data-codigo="' + codigo + '"]');
                        let dispositivos = objeto_pulsado.closest('.info-data').find('.card-elem-dis').filter('[data-codigo="' + codigo + '"]');
                        let estado = datos[1];
                        let alturaPersiana = parseInt(datos[2], 16);
                        let pos4 = pertemp.data('pos4');
                        let estados = datos.slice(3);
                        valoresPersiana(pertemp, objeto_pulsado, codigo, estado, alturaPersiana, pos4);
                        valoresDispositivos(dispositivos, estados);
                    }
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
    dispositivos.each(function() {
        let inputdisp = $(this).find('input');
        let index = (inputdisp.data('switch')) - 1;
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
    let habid = $(this).closest('.info-data').data('habid');
    let codigo = $(this).closest('.card-elem-dis').data('codigo');
    let switch_v = $(this).data('switch_v');
    let orden = $(this).data('orden');;

    $.ajax({
        url: $(location).attr('pathname'),
        type: 'POST',
        data: {tipo_envio: 'enviar', codigo: codigo,switch_v: switch_v, orden: orden, habid: habid},
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
    let habid = $(this).closest('.info-data').data('habid');
    let codigo = $(this).closest('.card-elem-dis').data('codigo');
    let switch_v = $(this).data('switch_v');
    let orden = '';
    if (boton.prop('checked')) {
        orden = codigosPersianas['per_switch_encender']['valor'];
    } else {
        orden = codigosPersianas['per_switch_apagar']['valor'];
    }

    $.ajax({
        url: $(location).attr('pathname'),
        type: 'POST',
        data: {tipo_envio: 'enviar', codigo: codigo, switch_v: switch_v, orden: orden, habid: habid},
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
    let codigo = $(this).closest('.card-elem').data('codigo');
    let orden = $(this).data('orden');
    let tipo = $(this).data('tipo');
    let habid = $(this).closest('.info-data').data('habid');

    vibrar();
    boton.closest('.info-data').find('.card-elem .btn-per:not(.btn-stop), .card-elem .btn-por').attr('disabled', true);
    $.ajax({
        url: $(location).attr('pathname'),
        type: 'POST',
        data: {tipo_envio: 'enviar', codigo: codigo, orden: orden, tipo: tipo, habid: habid},
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