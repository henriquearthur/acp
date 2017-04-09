/*! IceHabbo - by Henrique Arthur <eu@henriquearthur.me> */

adverter = function(element) {
	link = $(element).attr('rel');

	$( "#advert-dialog" ).dialog({
		title: "Adverter este usuário?",
		modal: true,
		show: { effect: "fade", duration: 400 },
		buttons: {
			"Adverter": function() {
				$.post('admin.php'+link, {}, function(html) {
					$("#advert-dialog.ui-dialog-content").html('Advertido com sucesso!');
					$(".ui-dialog-buttonpane").remove();

					location.reload();
				});
			},
			"Cancelar": function() {
				$(this).dialog( "close" );
			}
		}
	});
}

alertar = function(element) {
	link = $(element).attr('rel');

	$( "#alert-dialog" ).dialog({
		title: "Enviar alerta",
		modal: true,
		show: { effect: "fade", duration: 400 },
		buttons: {
			"Enviar": function() {
				alerta = $("#al-dl-alerta").val();

				$.post('admin.php'+link, { 'alerta': alerta }, function(html) {
					$("#alert-dialog.ui-dialog-content").html('Enviado com sucesso!');
					$(".ui-dialog-buttonpane").remove();

					location.reload();
				});
			},
			"Cancelar": function() {
				$(this).dialog( "close" );
			}
		}
	});
}

banir = function(element) {
	link = $(element).attr('rel');

	$( "#banir-dialog" ).dialog({
		title: "Banir usuário",
		modal: true,
		show: { effect: "fade", duration: 400 },
		buttons: {
			"Enviar": function() {
				motivo = $("#ban-motivo").val();
				tempo = $("#ban-tempo").val();

				$.post('admin.php'+link, { 'motivo': motivo, 'tempo': tempo }, function(html) {
					$("#banir-dialog.ui-dialog-content").html('Enviado com sucesso!');
					$(".ui-dialog-buttonpane").remove();

					location.reload();
				});
			},
			"Cancelar": function() {
				$(this).dialog( "close" );
			}
		}
	});
}

desbanir = function(element) {
	link = $(element).attr('rel');

	$( "#desbanir-dialog" ).dialog({
		title: "Desbanir este usuário?",
		modal: true,
		show: { effect: "fade", duration: 400 },
		buttons: {
			"Desbanir": function() {
				$.post('admin.php'+link, {}, function(html) {
					$("#desbanir-dialog.ui-dialog-content").html('Desbanido com sucesso!');
					$(".ui-dialog-buttonpane").remove();

					location.reload();
				});
			},
			"Cancelar": function() {
				$(this).dialog( "close" );
			}
		}
	});
}

darIds = function(element) {
	link = $(element).attr('rel');
	id = $(element).attr('id');

	$( "#dar"+id+"-dialog" ).dialog({
		title: "Dar moedas",
		modal: true,
		show: { effect: "fade", duration: 400 },
		buttons: {
			"Dar moedas": function() {
				moedas = $("#moedas-"+id).val();

				$.post('admin.php'+link, { 'moedas': moedas }, function(html) {
					$("#darids-dialog.ui-dialog-content").html('Dado com sucesso!');
					$(".ui-dialog-buttonpane").remove();

					location.reload();
				});
			},
			"Cancelar": function() {
				$(this).dialog( "close" );
			}
		}
	});
}