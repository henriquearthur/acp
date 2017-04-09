/*! IceHabbo - by Henrique Arthur <eu@henriquearthur.me> */

deletar = function(element, tipo) {
	link = $(element).attr('rel');

	$( "#delete-dialog" ).dialog({
		title: "Inativar este registro?",
		modal: true,
		show: { effect: "fade", duration: 400 },
		buttons: {
			"Inativar": function() {
				$.post('admin.php'+link, {}, function(html) {
					$("#delete-dialog.ui-dialog-content").html('Inativado com sucesso!');
					$(".ui-dialog-buttonpane").remove();
					pgExitDisable();

					if(tipo == 1) {
						var parts = window.location.search.substr(1).split("&");
						var get = {};
						for (var i = 0; i < parts.length; i++) {
							var temp = parts[i].split("=");
							get[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
						}

						location.replace("?p="+get.p);
					} else {
						location.reload();
					}
				});
			},
			"Cancelar": function() {
				$(this).dialog( "close" );
			}
		}
	});
}

deletar2 = function(element, tipo) {
	link = $(element).attr('rel');

	$( "#delete-dialog-true" ).dialog({
		title: "Deletar este registro?",
		modal: true,
		show: { effect: "fade", duration: 400 },
		buttons: {
			"Inativar": function() {
				$.post('admin.php'+link, {}, function(html) {
					$("#delete-dialog.ui-dialog-content").html('Deletado com sucesso!');
					$(".ui-dialog-buttonpane").remove();
					pgExitDisable();

					if(tipo == 1) {
						var parts = window.location.search.substr(1).split("&");
						var get = {};
						for (var i = 0; i < parts.length; i++) {
							var temp = parts[i].split("=");
							get[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
						}

						location.replace("?p="+get.p);
					} else {
						location.reload();
					}
				});
			},
			"Cancelar": function() {
				$(this).dialog( "close" );
			}
		}
	});
}

ativar = function(element, tipo) {
	link = $(element).attr('rel');

	$( "#ativar-dialog" ).dialog({
		title: "Ativar este registro?",
		modal: true,
		show: { effect: "fade", duration: 400 },
		buttons: {
			"Ativar": function() {
				$.post('admin.php'+link, {}, function(html) {
					$("#delete-dialog.ui-dialog-content").html('Ativado com sucesso!');
					$(".ui-dialog-buttonpane").remove();
					pgExitDisable();

					if(tipo == 1) {
						var parts = window.location.search.substr(1).split("&");
						var get = {};
						for (var i = 0; i < parts.length; i++) {
							var temp = parts[i].split("=");
							get[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
						}

						location.replace("?p="+get.p);
					} else {
						location.reload();
					}
				});
			},
			"Cancelar": function() {
				$(this).dialog( "close" );
			}
		}
	});
}

searchShow = function() {
	if($(".search").css('display') == 'none') {
		$(".search").fadeIn();
	} else {
		$(".search").fadeOut();
	}
}

warnRead = function(id) {
	$.ajax({
		url: 'lib/warn_read.php?id='+id,
		type: 'GET'
	}).done(function() {
		$("#warn-read-"+id).attr('disabled', 'true').text('Você já leu este aviso');
	});
}

pxAceitar = function(element) {
	link = $(element).attr('rel');

	$( "#px-aceitar" ).dialog({
		title: "Aprovar este registro?",
		modal: true,
		show: { effect: "fade", duration: 400 },
		buttons: {
			"Aprovar": function() {
				$.post('admin.php'+link, {}, function(html) {
					$("#px-aceitar.ui-dialog-content").html('Aprovado com sucesso!');
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

pxRecusar = function(element) {
	link = $(element).attr('rel');

	$( "#px-recusar" ).dialog({
		title: "Reprovar este registro?",
		modal: true,
		show: { effect: "fade", duration: 400 },
		buttons: {
			"Reprovar": function() {
				$.post('admin.php'+link, {}, function(html) {
					$("#px-recusar.ui-dialog-content").html('Reprovado com sucesso!');
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

removerEmblema = function(element) {
	link = $(element).attr('rel');

	$( "#emblema-remover" ).dialog({
		title: "Remover o emblema do usuário?",
		modal: true,
		show: { effect: "fade", duration: 400 },
		buttons: {
			"Remover": function() {
				$.post('admin.php'+link, {}, function(html) {
					$("#emblema-remover.ui-dialog-content").html('Removido com sucesso!');
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

moderarTpc = function(element) {
	link = $(element).attr('rel');

	$( "#moderar-dialog" ).dialog({
		title: "Moderar este tópico?",
		modal: true,
		show: { effect: "fade", duration: 400 },
		buttons: {
			"Moderar": function() {
				$.post('admin.php'+link, {}, function(html) {
					$("#moderar-dialog.ui-dialog-content").html('Moderado com sucesso!');
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

fecharTpc = function(element) {
	link = $(element).attr('rel');

	$( "#fechar-dialog" ).dialog({
		title: "Fechar este tópico?",
		modal: true,
		show: { effect: "fade", duration: 400 },
		buttons: {
			"Fechar": function() {
				$.post('admin.php'+link, {}, function(html) {
					$("#fechar-dialog.ui-dialog-content").html('Fechado com sucesso!');
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
abrirTpc = function(element) {
	link = $(element).attr('rel');

	$( "#abrir-dialog" ).dialog({
		title: "Abrir este tópico?",
		modal: true,
		show: { effect: "fade", duration: 400 },
		buttons: {
			"Abrir": function() {
				$.post('admin.php'+link, {}, function(html) {
					$("#abrir-dialog.ui-dialog-content").html('Aberto com sucesso!');
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

ativarConta = function(element) {
	link = $(element).attr('rel');

	$( "#ativar-dialog" ).dialog({
		title: "Ativar esta conta?",
		modal: true,
		show: { effect: "fade", duration: 400 },
		buttons: {
			"Ativar": function() {
				$.post('admin.php'+link, {}, function(html) {
					$("#ativar-dialog.ui-dialog-content").html('Ativada com sucesso!');
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

ativarTopico = function(element) {
	link = $(element).attr('rel');

	$( "#ativar-topic-dialog" ).dialog({
		title: "Ativar este tópico?",
		modal: true,
		show: { effect: "fade", duration: 400 },
		buttons: {
			"Ativar": function() {
				$.post('admin.php'+link, {}, function(html) {
					$("#ativar-dialog.ui-dialog-content").html('Ativado com sucesso!');
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

inativarTopico = function(element) {
	link = $(element).attr('rel');

	$( "#inativar-topic-dialog" ).dialog({
		title: "Inativar este tópico?",
		modal: true,
		show: { effect: "fade", duration: 400 },
		buttons: {
			"Inativar": function() {
				$.post('admin.php'+link, {}, function(html) {
					$("#inativar-dialog.ui-dialog-content").html('Inativado com sucesso!');
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

inativarConta = function(element) {
	link = $(element).attr('rel');

	$( "#inativar-conta-dialog" ).dialog({
		title: "Inativar esta conta?",
		modal: true,
		show: { effect: "fade", duration: 400 },
		buttons: {
			"Inativar": function() {
				$.post('admin.php'+link, {}, function(html) {
					$("#inativar-conta-dialog.ui-dialog-content").html('Inativada com sucesso!');
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

ativarConta = function(element) {
	link = $(element).attr('rel');

	$( "#ativar-conta-dialog" ).dialog({
		title: "Ativar esta conta?",
		modal: true,
		show: { effect: "fade", duration: 400 },
		buttons: {
			"Ativar": function() {
				$.post('admin.php'+link, {}, function(html) {
					$("#ativar-conta-dialog.ui-dialog-content").html('Ativada com sucesso!');
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

expirarItem = function(element) {
	link = $(element).attr('rel');

	$( "#expirar-item-dialog" ).dialog({
		title: "Expirar este item?",
		modal: true,
		show: { effect: "fade", duration: 400 },
		buttons: {
			"Expirar": function() {
				$.post('admin.php'+link, {}, function(html) {
					$("#expirar-item-dialog.ui-dialog-content").html('Item expirado com sucesso!');
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

panel = {
	start: function() {
		$('.tip').tipr();
		$('.tip-2').tipr({
			'mode': 'top'
		})

		$('.chart').easyPieChart({
			lineWidth: 2,
			size: 110,
			scaleColor: '#ECF0F1',
			lineCap: 'square',
			trackColor: '#DDD'
		});

		$(".tabs").tabs();

		$('.cp-color .form-input').ColorPicker({
			onChange: function (hsb, hex, rgb) {
				$(".cp-color .form-input").val('#' + hex);
			},
			onBeforeShow: function () {
				$(this).ColorPickerSetColor($(".cp-color .form-input").val());
			}
		})
		.bind('keyup', function(){
			$(this).ColorPickerSetColor($(".cp-color .form-input").val());
		});

	},
	menu: function() {
		$("#menus li[id]").each(function(index, el) {
			sub = $(this).attr('id');

			off = $(this).offset();
			off_left = off.left;

			$("#sub-menus #"+sub).css('left', off_left);
		});
	}
}

$('.upl-gallery-file').on('change', function() {
	img = $(this).val();
	caminho = '/uploads/' + img;

	nome = $(this).attr('id');
	nome = nome.split('-');
	nome = nome[1];

	img_element = $("#img-"+nome+".upl-gallery-img");

	if(img != 0) {
		if(img_element.css('display') == 'none') {
			img_element.fadeIn();
		}

		img_element.attr('src', caminho);
	} else {
		img_element.fadeOut();
	}
});

$('.upl-input-file').on('change', function() {
	input = this;
	nome = $(this).attr('id');
	nome = nome.split('-');
	nome = nome[1];

	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$("#img-"+nome+".upl-file-img").attr('src', e.target.result);
		}

		reader.readAsDataURL(input.files[0]);
	}
});

$('.upl-url-file').on('change', function() {
	url = $(this).val();

	nome = $(this).attr('id');
	nome = nome.split('-');
	nome = nome[1];

	$("#img-"+nome+".upl-url-img").attr('src', url);
});

$(document).ready(function() {
	panel.start();

	$(".upl-gallery-file").each(function() {
		img = $(this).val();
		caminho = '/uploads/' + img;

		nome = $(this).attr('id');
		nome = nome.split('-');
		nome = nome[1];

		img_element = $("#img-"+nome+".upl-gallery-img");

		if(img != 0) {
			if(img_element.css('display') == 'none') {
				img_element.fadeIn();
			}

			img_element.attr('src', caminho);
		} else {
			img_element.fadeOut();
		}
	});
});

menu_opened = 0;

$("#menus li[id]").click(function(event) {
	id = $(this).attr('id');
	event.preventDefault();

	sub = $(this).attr('id');
	sub_menu = $("#sub-menus #"+sub);

	if(menu_opened == sub) {
		sub_menu.hide('blind');
		menu_opened = 0;
	} else {
		if(menu_opened == 0) {
			sub_menu.show('blind');
		} else {
			$("#sub-menus .sub[id!='"+sub+"']").hide('blind', function() {
				sub_menu.show('blind');
			});
		}

		menu_opened = sub;
	}
});

$(window).load(function() {
	panel.menu();
});

$(".form-submit").submit(function(event) {
	$(this).find('[type="submit"]').attr('disabled', true);
	$(this).animate({'opacity': 0.5}, 'fast');
});

$("#table-check-all").click(function() {
	source = document.getElementById('table-check-all');
	checkboxes = document.getElementsByName('table-check');
	for(var i=0, n=checkboxes.length;i<n;i++) {
		checkboxes[i].checked = source.checked;
	}
});

$('#table-check-select').on('change', function() {
	val = $(this).val();

	if(val > 0) {
		checkeds = [];

		$(".table-check").each(function() {
			if($(this).is(':checked')) {
				attr_id = $(this).attr('id');
				idd = attr_id.split('-');
				id = idd[2];

				checkeds.push(id);
			}
		});

		// Inativar
		if(val == 1) {
			url = $(this).attr('rel');

			for (var i = checkeds.length - 1; i >= 0; i--) {
				data = checkeds[i];
				url += data + ',';
			}

			$( "#delete-dialog2" ).dialog({
				title: "Inativar este registro?",
				modal: true,
				show: { effect: "fade", duration: 400 },
				buttons: {
					"Inativar": function() {
						$.post('admin.php'+url, {}, function(html) {
							$("#delete-dialog2.ui-dialog-content").html('Deletado com sucesso!');
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
	}
});

pgExitConfirm = function (ev) {
	ev = ev || window.event;
	var msg = 'Deseja mesmo sair dessa página? Alterações não salvas serão perdidas.';

	if (ev) {
		ev.returnValue = msg;
	}

	return msg;
};

pgExitDisable = function() { window.onbeforeunload = null; }
pgExitEnable = function() { window.onbeforeunload = pgExitConfirm; }

function geraCodigo() {
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < 10; i++ ) {
        text += possible.charAt(Math.floor(Math.random() * possible.length));
    }

    $("#codigo").val(text);

}