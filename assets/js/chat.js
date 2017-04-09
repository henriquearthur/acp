/*! IceHabbo - by Henrique Arthur <eu@henriquearthur.me> */

chat = {
	last: 0,
	interval: 0,
	msgs: [],
	update: function() {
		$.ajax({
			url: 'lib/chat_update.php?last='+chat.last,
			type: 'GET',
			dataType: 'JSON',
		}).done(function(data) {
			$(".chat-load").remove();

			if(data.cleaned) {
				$(".messages").html('');
				chat.addInfo("Chat limpo com sucesso!");
			}

			for (var i = data['msgs'].length - 1; i >= 0; i--) {
				dados = data['msgs'][i];

				if(chat.last != dados['id'] && $.inArray(dados['id'], chat.msgs) === -1) {
					chat.msgs.push(dados['id']);
					chat.last = dados['id'];

					chat.addMsg(dados['id'], dados['nick'], dados['msg'], dados['data']);
				}
			};

			clearTimeout(chat.interval);
			chat.interval = setTimeout(function () {
				chat.update();
			}, 1500);
		});
	},
	addMsg: function(id, nick, msg, data) {
		msg_add  = '<div class="box">';
		msg_add += '<div id="img" style="background:url(https://www.habbo.com.br/habbo-imaging/avatarimage?img_format=gif&user='+nick+'&action=std&direction=2&head_direction=3&gesture=sml&size=b) -10px -14px;"></div>';
		msg_add += '<div id="author">'+nick+'<br><small>'+data+'</small></div>';
		msg_add += '<div id="infos">'+msg+'</div>';
		msg_add += '<br></div>';

		$(".messages").prepend(msg_add);
	},
	addInfo: function(info) {
		msg_add = '<div class="box info"><div id="infos">' + info + '</div></div>';

		$(".messages").prepend(msg_add);
	},
	send: function() {
		msg = $("#chat-msg").val();

		if(msg == '/limpar') {
			chat.last = 0;
			chat.msgs = [];
		}

		if(msg != '') {
			$("#chat-msg, #chat-submit").attr('disabled', true);

			$.ajax({
				url: 'lib/chat_enviar.php',
				type: 'POST',
				data: {'msg': msg},
			}).done(function(data) {
				$("#chat-msg, #chat-submit").attr('disabled', false);
				$("#chat-msg").focus();

				$("#chat-msg").val('');

				if(data == 'success') {
					clearTimeout(chat.interval);
					chat.update();
				} else {
					chat.addInfo(data);
				}
			});
		}
	},
	start: function() {
		chat.update();
	}
}

$(document).ready(function() {
	$('.messages').slimScroll({
		height: '390px'
	});

	chat.start();
})