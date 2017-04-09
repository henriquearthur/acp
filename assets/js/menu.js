/*! IceHabbo - by Henrique Arthur <eu@henriquearthur.me> */

function getParameterByName(name) {
	name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
	var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
	results = regex.exec(location.search);
	return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

menu = {
	submit: function() {
		ids = [];
		ordem = [];

		i = 1;

		$(".box-menu-order").each(function(index, el) {
			ids.push($(this).attr('rel'));
			ordem.push(i);

			i++;
		});

		$.ajax({
			url: 'admin.php?a=6&p=' + getParameterByName('p'),
			type: 'POST',
			data: {'ids': ids, 'ordem': ordem},
		}).done(function() {
			$( "#menu-dialog" ).dialog({
				title: "Sucesso",
				modal: true,
				show: { effect: "fade", duration: 400 },
				buttons: {
					"Ok": function() {
						$(this).dialog( "close" );
					}
				}
			});
		});

	},
	start: function() {
		$( "#menu-sort" ).sortable();
		$( "#menu-sort" ).attr('unselectable','on').css({'-moz-user-select':'-moz-none','-moz-user-select':'none','-o-user-select':'none','-khtml-user-select':'none','-webkit-user-select':'none','-ms-user-select':'none','user-select':'none'}).bind('selectstart', function(){ return false; });
	}
}

$(document).ready(function() {
	menu.start();
})