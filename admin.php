<?php

/**
 * IceHabbo
 * by Henrique Arthur <eu@henriquearthur.me>
 * Não use sem autorização.
 */

session_start();

include "lib/config.php";
include "lib/functions.php";

include "lib/class.forms.php";
include "lib/class.tables.php";

if(!isset($_SESSION['login'])) {
	header("Location: index.php");
  exit;
}


// Caso tenha o Gerenciar módulos
if(isset($_GET['permissions']) && $permissoes[1] == 's') {
	$sql8 = $conn->query("SELECT * FROM acp_modulos ORDER BY permissao ASC");
	while($sql9 = $sql8->fetch()) {
		echo 'Permissão <b>'.$sql9['permissao'].'</b>: <b>'.$sql9['nome'].' (#'.$sql9['id'].')</b><br>';
	}
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<meta name="language" content="pt-br">
	<meta name="robots" content="noindex, nofollow">

	<title>IceHabbo - Painel de gerenciamento</title>

	<link rel="icon" href="/favicon.png">
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700">
	<link rel="stylesheet" href="assets/css/general.css">
	<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
	<link rel="stylesheet" href="assets/css/fontello.css">
	<link rel="stylesheet" href="assets/css/main.css" class="cssfx">

	<style type="text/css">
	.datetimepicker { position: relative; }
	</style>
</head>

<body>
	<header class="top"><div class="align">
		<a href="admin.php"><div id="logo"></div></a>

		<div id="user">
			<div class="avatar" style="background-image:url(<?=$dados2['avatar'];?>);"></div>
			<div class="infos"><?=$dados['nick'];?></div>
		</div>

		<div id="user-options">
			<a href="?p=3"><div class="opt tip" data-tip="<?=$core->getMdlName(3);?>"><i class="icon-user"></i></div></a>
			<a href="?p=4"><div class="opt tip" data-tip="<?=$core->getMdlName(4);?>"><i class="icon-chat"></i></div></a>
			<a href="?p=5"><div class="opt tip" data-tip="<?=$core->getMdlName(5);?>"><i class="icon-picture"></i></div></a>
			<a href="deslogar.php"><div class="opt tip" data-tip="Desconectar"><i class="icon-logout"></i></div></a>
		</div>
	</div></header>

	<nav class="menu"><div class="align">
		<div id="menus">
			<a href="admin.php"><li>Home</li></a>

			<? $sql = $conn->query("SELECT * FROM acp_modulos_cat WHERE id != 1 ORDER BY id ASC");
			while($sql2 = $sql->fetch()) {
				$mostra_cat = false;

				$sql3 = $conn->query("SELECT * FROM acp_modulos WHERE cat_id='".$sql2['id']."' AND oculto='n' ORDER BY nome ASC");
				while($sql4 = $sql3->fetch()) {
					if($permissoes[$sql4['permissao']] == 's' || $core->allAccess()) {
						$mostra_cat = true;
						break;
					}
				}

				if($mostra_cat) { ?>
			<li id="sub-<?=$sql2['id'];?>"><?=$core->clear($sql2['nome']);?></li>
			<? } } ?>
		</div>

		<div id="sub-menus">
			<? $_sql5 = $conn->query("SELECT * FROM acp_modulos_cat ORDER BY id ASC");
			while($sql5 = $_sql5->fetch()) { ?>
			<div id="sub-<?=$sql5['id'];?>" class="sub">
				<? $sql6 = $conn->query("SELECT * FROM acp_modulos WHERE cat_id='".$sql5['id']."' AND oculto='n' ORDER BY nome ASC");
				while($sql7 = $sql6->fetch()) {
					if($permissoes[$sql7['permissao']] == 's' || $core->allAccess()) { ?>
				<a href="?p=<?=$sql7['id'];?>"><li><i class="icon-right-open"></i> <?=$core->clear($sql7['nome']);?></li></a>
				<? } } ?>
			</div>
			<? } ?>
		</div>
	</div></nav>

	<section class="content"><div class="align">
		<? include "conteudo.php"; ?>

		<br>
	</div></section>

	<footer class="copyright">
		icehabbo.com.br &copy; - by <a href="http://www.twitter.com/@_theFX" target="_blank">@_theFX</a>
	</footer>

	<div class="dialog-hide" id="delete-dialog">Este item será inativado.<br><br>Tem certeza de que deseja inativar este registro?</div>
	<div class="dialog-hide" id="delete-dialog-true">Este item será deletado permanentemente.<br><br>Tem certeza de que deseja deletar este registro?</div>
	<div class="dialog-hide" id="ativar-dialog">Este item será ativado.<br><br>Tem certeza de que deseja ativar este registro?</div>
	<div class="dialog-hide" id="notfound-dialog">Este item não existe.<br><br>Você será redirecionado para a página anterior.<br><br>Caso ache que isto seja um erro, contate a administração.</div>
	<div class="dialog-hide" id="advert-dialog">Deseja dar uma advertência a este usuário?</div>
	<div class="dialog-hide" id="delete-dialog2">Estes itens serão inativados<br><br>Tem certeza de que deseja inativar estes registros?</div>
	<div class="dialog-hide" id="fzt-dialog">Você não tem permissão para visualizar esta página.<br><br>Redirecionando...</div>
	<div class="dialog-hide" id="menu-dialog">A ordem foi alterada com sucesso!</div>
	<div class="dialog-hide" id="px-aceitar">Deseja aprovar este registro?</div>
	<div class="dialog-hide" id="px-recusar">Deseja reprovar este registro?</div>
	<div class="dialog-hide" id="emblema-remover">Deseja remover o emblema deste usuário?</div>
	<div class="dialog-hide" id="desbanir-dialog">Deseja desbanir este usuário?</div>
	<div class="dialog-hide" id="fechar-dialog">Deseja fechar este tópico?</div>
	<div class="dialog-hide" id="abrir-dialog">Deseja abrir este tópico?</div>
	<div class="dialog-hide" id="moderar-dialog">Deseja marcar este tópico como moderado?</div>
	<div class="dialog-hide" id="ativar-dialog">Deseja ativar esta conta?</div>
	<div class="dialog-hide" id="ativar-topic-dialog">Deseja ativar este tópico?</div>
	<div class="dialog-hide" id="inativar-topic-dialog">Deseja inativar este tópico?</div>
	<div class="dialog-hide" id="inativar-conta-dialog">Deseja inativar esta conta?</div>
	<div class="dialog-hide" id="ativar-conta-dialog">Deseja ativar esta conta?</div>

	<div class="dialog-hide" id="alert-dialog">
		<div class="form-group no-label no-border">
			<textarea class="form-input" id="al-dl-alerta" placeholder="Digite o alerta a ser enviado"></textarea>
		</div>
	</div>

	<div class="dialog-hide" id="banir-dialog">
		<div class="form-group no-label no-border">
			<textarea class="form-input" id="ban-motivo" placeholder="Digite o motivo"></textarea>
		</div>

		<br><br>

		<div class="form-group no-label no-border">
			<input type="text" class="form-input" id="ban-tempo" placeholder="Até quando? DD/MM/AAAA HH:MM">
		</div>
	</div>

	<div class="dialog-hide" id="darids-dialog">
		<div class="form-group no-label no-border">
			<input type="text" class="form-input" id="moedas-ids" placeholder="Quantidade de moedas (números)">
		</div>
	</div>

	<div class="dialog-hide" id="daralt-dialog">
		<div class="form-group no-label no-border">
			<input type="text" class="form-input" id="moedas-alt" placeholder="Quantidade de moedas alternativas (números)">
		</div>
	</div>

	<div class="dialog-hide" id="daresm-dialog">
		<div class="form-group no-label no-border">
			<input type="text" class="form-input" id="moedas-esm" placeholder="Quantidade de esmeraldas (números)">
		</div>
	</div>

	<script src="assets/js/jquery.js"></script>
	<script src="assets/js/jquery-ui.js"></script>
	<script src="assets/ckeditor/ckeditor.js"></script>
	<script src="assets/js/general.js"></script>
	<script src="assets/js/main.js"></script>

	<script src="assets/js/moment.min.js"></script>
	<script src="assets/js/bootstrap-datetimepicker.min.js"></script>

	<? if($mdl_tabela == 'acp_chat') { ?><script src="assets/js/chat.js"></script><? } ?>
	<? if($mdl_tabela == 'acp_usuarios' || $mdl_tabela == 'usuarios') { ?><script src="assets/js/accounts.js"></script><? } ?>
	<? if($mdl_tabela == 'menu' && $_GET['a'] == 5) { ?><script src="assets/js/menu.js"></script><? } ?>

	<? if(!empty($script_js)) { ?>
	<script type="text/javascript">
	<?=$script_js;?>
	</script>
	<? } ?>

	<script type="text/javascript">
		$(function () {
			$('.datetimepicker .form-input').datetimepicker({
				 locale: 'pt-BR',
				 sideBySide: true,
				 minDate: moment().add(60, 'm').toDate()
			});
		});
	</script>
</body>
</html>