tipo_horario_select = 'horario';
tipo_select = 'persiana';

$('#dispositivo').on('change', function(e) {
	var optionSelected = $('#dispositivo option:checked');
	var tipo_dispositivo = optionSelected.data('tipo');
	if (tipo_dispositivo == 'P') {
		$('.orden-on-off').hide();
		$('.orden-pulsador').show();
	} else {
		$('.orden-pulsador').hide();
		$('.orden-on-off').show();
	}
});

$('#codigoDispositivo').on('change', function(e) {
	var optionSelected = $('#codigoDispositivo option:checked');
	var tipo_ubicacion = optionSelected.data('tipo');
	var habitacion_id = optionSelected.data('id');
	$('#dispositivo').empty();

	if (tipo_ubicacion == 'room') {
		let dispositivoObj = dispositivos[habitacion_id];
		if (dispositivoObj) {
			$('#error-hab-no-disp').hide();
			$('#dispositivos_div').show();
			$('#dispositivo').append($('<option/>', {'html': 'Todos', 'value': codigosPersianas['per_switch_all']['valor']}));
			for (let i=0; i < dispositivoObj.length; i++) {
				disp = dispositivoObj[i];
				let optionItem = $('<option/>', {'html': disp['nombre'], 'value': codigosPersianas['per_switch' + disp['switch']]['valor'], 'data-codigo': disp['codigo'], 'data-tipo': disp['tipo']});
				$('#dispositivo').append(optionItem);
			}
		} else {
			$('#error-hab-no-disp').show();
			$('#dispositivos_div').hide();
		}
	} else {
		$('#error-hab-no-disp').hide();
		$('#dispositivos_div').hide();
	}
});

$('input[name="tipo"]').on('click', function(e) {
	if ($(this).val() == tipo_select) {
		return;
	}

	$('#codigoPersiana option:first').prop('selected', true);
	$('#codigoDispositivo option:first').prop('selected', true);
	$('#orden option:first').prop('selected', true);
	$('#dispositivos_div').hide();
	$('#dispositivo').empty();
	if ($(this).val() == 'persiana') {
		$('#codigoDispositivo').hide();
		$('#codigoPersiana').show();
		$('.orden-on-off').hide();
		$('.orden-persianas').show();
		$('#orden-label').css({'margin-top': '4px'});
		tipo_select = 'persiana';
	} else {
		$('#codigoPersiana').hide();
		$('#codigoDispositivo').show();
		$('.orden-persianas').hide();
		$('.orden-on-off').show();
		$('#orden-label').css({'margin-top': '0px'});
		tipo_select = 'dispositivo';
	}
});

$('input[name="tipo_comienzo"]').on('click', function(e) {
	if ($(this).val() == 'hora') {
		$('#tipo_sol_div').slideUp();
	} else {
		$('#tipo_sol_div').slideDown();
	}
});

$('input.input-dias').on('click', function(e) {
	let labelAso = $(this).closest('label.label-dias');
	labelAso.toggleClass('active');
	let diasval = $('input.input-dias:checked').map(function(_, el) {
	    return $(el).val();
	}).get();
});

$('a.nav-link').on('click', function(e) {
	var data_tipo = $(this).data('tipo');
	if (data_tipo == tipo_horario_select) {
		return;
	}
	tipo_horario_select = data_tipo;
	$('#tipo_comienzo1').prop('checked', true);
	$('#tipo_sol1').prop('checked', true);
	$('#tipo_sol_div').hide();

	$('a.nav-link').removeClass('active');
	$(this).addClass('active');
	if (data_tipo == 'horario') {
		$('.div-hora_alba_ocaso').show();
		$('#nombre_div').show();
		$('#repetir_div').show();
		$('#dias_div').show();
		$('#activo_div').show();
		$('label.tiempo').html('Comienzo: ');
	} else if (data_tipo == 'temporizador') {
		$('.div-hora_alba_ocaso').hide();
		$('#nombre_div').hide();
		$('#repetir_div').hide();
		$('#dias_div').hide();
		$('#activo_div').hide();
		$('label.tiempo').html('Tiempo: ');
	}
});

// Crear o editar evento
$('#boton_crear').on('click', function(e) {
	$(".loader").fadeIn('fast');

	// Tipo horario o temporizador
	var tipo_bd = $('.nav-link.active').data('tipo');

	// Nombre
	var nombre_bd = '';
	if (tipo_bd == 'horario') {
		nombre_bd = $('#nombre').val();
		// Error
		if (nombre_bd == '') {
			$('#error-nombre').show();
			$('html, body').animate({scrollTop:0}, 500);
			$(".loader").fadeOut('fast');
			return;
		} else {
			$('#error-nombre').hide();
		}
	}

	// Errores
	if ($('#error-hab-no-disp').css('display') != 'none') {
		$('html, body').animate({
			scrollTop: $("#error-hab-no-disp").offset().top
			}, 500);
		$(".loader").fadeOut('fast');
		return;
	}

	// Tipo persiana o dispositivo
	var tipo_dispositivo_bd = $('input[name="tipo"]:checked').val();

	// Ubicacion y código
	if (tipo_dispositivo_bd == 'persiana') {
		var ubicacion_bd = $('#codigoPersiana option:checked').data('tipo');
		if (ubicacion_bd == 'room') {
			ubicacion_bd += '-' + $('#codigoPersiana option:checked').data('id');
		}
		var codigo_bd = $('#codigoPersiana option:checked').val();
		var orden_bd = $('#orden option:checked').val();
	} else {
		var ubicacion_bd = $('#codigoDispositivo option:checked').data('tipo');
		if (ubicacion_bd == 'all') {
			var codigo_temp = $('#codigoDispositivo option:checked').val().split(' ');
			var codigo_bd = codigo_temp[0];
			var orden_temp = $('input[name="orden"]:checked').val();
			var orden_bd = codigosPersianas['per_switch_' + orden_temp]['valor'] + codigosPersianas['per_switch_all']['valor'];
		} else {
			ubicacion_bd += '-' + $('#codigoDispositivo option:checked').data('id');
			var codigo_bd = $('#dispositivo option:checked').data('codigo');
			if (codigo_bd == undefined) {
				codigo_bd = '';
				var orden_temp = $('input[name="orden"]:checked').val();
				var orden_bd = codigosPersianas['per_switch_' + orden_temp]['valor'] + codigosPersianas['per_switch_all']['valor'];
			} else {
				if ($('#dispositivo option:checked').data('tipo') == 'I') {
					var orden_temp = $('input[name="orden"]:checked').val();
					var orden_bd = codigosPersianas['per_switch_' + orden_temp]['valor'] + $('#dispositivo option:checked').val(); 
				} else {
					var orden_bd = codigosPersianas['per_switch_pulsador']['valor'] + $('#dispositivo option:checked').val();
				}
			}
		}
	}

	// Repetir
	var repetir_bd = '';
	if (tipo_bd == 'horario') {
		repetir_bd = $('input[name="repetir"]:checked').val();
	}

	// Días
	var dias_bd = '';
	if (tipo_bd == 'horario') {
		dias_bd = $('input.input-dias:checked').map(function(_, el) {
				return $(el).val();
			}).get().toString();
	}

	// Comienzo
	var comienzo_bd = '';
	if (tipo_bd == 'horario') {
		comienzo_bd += $('input[name="tipo_comienzo"]:checked').val();
		if (comienzo_bd != 'hora') {
			comienzo_bd += $('input[name="tipo_sol"]:checked').val();
		}
	}

	// Hora
	var hora_bd = $('#time').val();

	// Activo
	var activo_bd = 'true';
	if (tipo_bd == 'horario') {
		activo_bd = $('input[name="activo"]:checked').val();
	}

	var datos = {tipo_bd: tipo_bd, nombre_bd: nombre_bd, tipo_dispositivo_bd: tipo_dispositivo_bd, ubicacion_bd: ubicacion_bd,
		codigo_bd: codigo_bd, orden_bd: orden_bd, repetir_bd: repetir_bd, dias_bd: dias_bd, comienzo_bd: comienzo_bd,
		hora_bd: hora_bd, activo_bd: activo_bd};

	if (evento) {
		datos['id_bd'] = evento['id'];
	}

	$.ajax({
		url: $(location).attr('pathname'),
		type: 'POST',
		data: datos,
		dataType: "json",
		success: function(data) {
			if (data.success) {
				$(location).attr('href','eventos.php');
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
});

// Borrar evento
$('#boton_borrar').on('click', function(e) {
	$('#borrarModal').modal('show');
});

$('#boton_borrar_modal').on('click', function(e) {
	$(".loader").fadeIn('fast');
	var id_bd = evento['id'];
	var nombre_bd = $('#nombre').val();

	$.ajax({
		url: $(location).attr('pathname'),
		type: 'POST',
		data: {id_bd: id_bd, nombre_bd: nombre_bd, borrar: ''},
		dataType: "json",
		success: function(data) {
			if (data.success) {
				$(location).attr('href','eventos.php');
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
			$('#borrarModal').modal('hide');
			$('.loader').fadeOut('fast');
		}
	});
});

$(window).on( 'load', function() {
	// Editar
	if (evento) {
		// Tipo
		if (evento['tipo'] == 'horario') {
			// Nombre
			$('#nombre').val(evento['nombre']);
			// Repetir
			if (evento['repetir'] == '0') {
				$('#repetir2').prop('checked', true);
			}
			// Dias
			for (let i = 0; i <= 6; i++) {
				if (!evento['dias'].includes(i)) {
					$('#dias_div input[value="' + i +'"]').closest('label').removeClass('active');
					$('#dias_div input[value="' + i +'"]').prop('checked', false);
				}
			}
			// Comienzo
			if (!evento['comienzo'].includes('hora')) {
				//$('#tipo_comienzo_' + (evento['comienzo'].slice(0, -1))).prop('checked', true);
				$('#tipo_comienzo_' + (evento['comienzo'].slice(0, -1))).trigger('click');
				if (evento['comienzo'].includes('+')) {
					$('#tipo_sol2').prop('checked', true);
				}
			}
			// Activo
			if (evento['activo'] == '0') {
				$('#activo2').prop('checked', true);
			}
		} else if (evento['tipo'] == 'temporizador')  {
			$('#timer-link').trigger('click');
		}

		// Tipo persiana
		if (evento['tipo_dispositivo'] == 'persiana') {
			// Ubicacion
			$('#codigoPersiana option[value="' + evento['codigo'] +'"]').attr('selected',true);
			// Orden
			$('#orden option[value="' + evento['orden'] +'"]').attr('selected',true);
		// Tipo dispositivo
		} else if (evento['tipo_dispositivo'] == 'dispositivo') {
			$('#tipo2').trigger('click');
			if (evento['ubicacion'] == 'all') {
				$('#codigoDispositivo option[value^="' + evento['codigo'] +'"]').attr('selected',true);
			} else {
				// Ubicación
				let idHabitacion = evento['ubicacion'].split('-')[1];
				$('#codigoDispositivo option[data-id="' + idHabitacion +'"]').attr('selected',true);
				$('#codigoDispositivo').trigger('change');
				// Dispositivo
				$('#dispositivo option[value="' + evento['orden'].substring(2,4) +'"][data-codigo="' + evento['codigo'] +'"]').attr('selected',true);
			}
			// Orden
			if (evento['orden'].substring(0,2) == codigosPersianas['per_switch_apagar']['valor']) {
				$('#orden2').prop('checked', true);
			}
		}

		// Hora
		$('#time').val(evento['hora']);
	}
});