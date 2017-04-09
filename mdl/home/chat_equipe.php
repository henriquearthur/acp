<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>

	<div class="chat-status"></div>

	<div class="chat">
		<div id="messages">
			<div class="messages">
				<div class="chat-load">Carregando (...)</div>
			</div>
		</div>

		<div id="send">
			<form action="javascript:chat.send();" method="post">
				<div class="form-group msg">
					<input type="text" class="form-input" id="chat-msg" placeholder="Digite sua mensagem">
				</div>

				<div class="form-group submit">
					<button type="submit" class="btn btn-primary" id="chat-submit">Enviar</button>
				</div>
			</form>
		</div>
	</div>

	<div class="well">
		<b>Comandos disponíveis:</b><br><br>
		<b>/onlines</b> - ver usuários online<br>
		<? if($permissoes[4] == 's') { ?><b>/limpar</b> - limpar o chat<br><? } ?>
	</div>
</div>