<? if($_GET['a'] == 2) {
	$id = $_GET['id'];

	$_ex = $conn->prepare("SELECT * FROM $mdl_tabela WHERE id = ? LIMIT 1");
	$_ex->bindValue(1, $id);
	$_ex->execute();
	$ex = $_ex->fetch();

	if(strtolower($ex['nick']) == strtolower($_SESSION['login']) && !$core->allAccess() && $permissoes[76] != 's') {
		echo '<div class="box-content"><div class="title-section">Erro</div> Você não pode editar a si mesmo.</div>';
	} else {
		if($_POST['form'] == 'form') {
			$nick = $core->clear($_POST['nick']);
			$email = $core->clear($_POST['email']);
			$senha = $core->clear($_POST['senha']);
			$moedas = $core->clear($_POST['moedas']);
			$moedas_alt = $core->clear($_POST['alternativas']);
			$moedas_esmeralda = $core->clear($_POST['esmeraldas']);
			$avatar_padrao = $core->clear($_POST['avatar-padrao']);
			$assinatura = $core->clear($_POST['assinatura']);
			$capa_padrao = $core->clear($_POST['capa-padrao']);
			$prosseguir = true;

			if(empty($nick) || empty($email)) {
				$form_return .= aviso_red("Digite um nick e um e-mail.");
				$prosseguir = false;
			}

			if($prosseguir && !empty($senha)) {
				if($permissoes[50] == 's' || $core->allAccess()) {
					$update_data['senha'] = md5($senha);
					//$update_data['senha_md5'] = md5($senha);

					$form_return .= aviso_yellow("A senha do usuário foi trocada com sucesso.");
				}
			}

			$_ex = $conn->prepare("SELECT * FROM $mdl_tabela WHERE id = ? LIMIT 1");
			$_ex->bindValue(1, $id);
			$_ex->execute();
			$ex = $_ex->fetch();

			if($prosseguir && $nick != $ex['nick']) {
				$update_data['nick'] = $nick;

				$replace = array(
					array('forum_denuncias', 'autor'),
					array('forum_topicos', 'autor'),
					array('forum_posts', 'autor'),
					array('noticias_coment', 'autor'),
					array('noticias_votos', 'autor'),
					array('noticias_coment', 'autor'),
					array('pixel_artes', 'autor'),
					array('pixel_coment', 'autor'),
					array('recordes', 'autor'),
					array('recordes', 'usuario'),
					array('recordes_comentarios', 'autor'),
					array('videos', 'autor'),
					array('videos_canais', 'autor'),
					array('votos_locutor', 'autor'),

					);

				foreach ($replace as $atual) {
					$tabela = $atual[0];
					$coluna = $atual[1];

					$up_nick[$coluna] = $nick;
					$wh_nick[$coluna] = $ex['nick'];
					$up_nick_query = $sqlActions->update($tabela, $up_nick, $wh_nick);
					unset($up_nick);
					unset($wh_nick);
				}

				$in_nick['id_usuario'] = $ex['id'];
				$in_nick['nick_original'] = $ex['nick'];
				$in_nick['nick_novo'] = $nick;
				$in_nick['autor'] = $autor;
				$in_nick['data'] = $timestamp;
				$in_nick_q = $sqlActions->insert("acp_nicks_trocados", $in_nick);

				$form_return .= aviso_yellow("O nick do usuário foi trocado com sucesso.");
			}

			if($prosseguir) {
				$up_name = 'avatar';

				$up_gallery = $core->clear($_POST["gl-$up_name"]);
				$up_file = $_FILES["fl-$up_name"];
				$up_url = $core->clear($_POST["url-$up_name"]);

				$_ex = $conn->prepare("SELECT * FROM $mdl_tabela WHERE id = ? LIMIT 1");
				$_ex->bindValue(1, $id);
				$_ex->execute();
				$ex = $_ex->fetch();

				$upload = new Upload($conn, $up_gallery, $up_file, $up_url, 'user-', false, $ex['avatar']);

				if(!$upload->erro) {
					$caminho_img = $upload->caminho;
				} else {
					$form_return .= aviso_red($upload->erro);
					$prosseguir = false;
				}
			}


			if($avatar_padrao) {
				$caminho_img = '/media/images/avatar-padrao.png';
			}

			if($permissoes[51] == 's' || $core->allAccess()) {
				$update_data['moedas'] = $moedas;

				if($moedas != $ex['moedas']) {
					$moedasT = $moedas - $ex['moedas'];
					$sqlActions->insert("usuarios_ices", array('id_usuario' => $ex['id'], 'qtd_moedas' => $moedasT, 'autor' => $autor, 'data' => time()));
				}
			}

			if($prosseguir) {
				$update_data['email'] = $email;
				$update_data['avatar'] = $caminho_img;
				$update_data['assinatura'] = $assinatura;

				$where_data['id'] = $id;
				$update = $sqlActions->update($mdl_tabela, $update_data, $where_data);

				if($update) {
					$core->logger("O usuário editou o usuário [#$id].", "acao");

					$form_return .= aviso_green("Sucesso!");
					foreach($_POST as $nome_campo => $valor){ $_POST[$nome_campo] = '';}
				} else {
					$form_return .= aviso_red("Ocorreu um erro ao executar a ação. Código de erro: {$sqlActions->error}");
				}
			}
		}

		$_ex = $conn->prepare("SELECT * FROM $mdl_tabela WHERE id = ? LIMIT 1");
		$_ex->bindValue(1, $id);
		$_ex->execute();
		$ex = $_ex->fetch();

		if(!$ex) {
			$script_js .= register404();
		}
		?>
		<div class="box-content">
			<div class="title-section"><?=$mdl['nome'];?></div>

			<button class="btn btn-danger" onclick="deletar(this, 1);" rel="?p=<?=$p;?>&a=3&id=<?=$id;?>">Inativar</button>
			<? if($ex['banido'] == 'n') { ?><button class="btn btn-warning" onclick="banir(this);" rel="?p=<?=$p;?>&a=5&id=<?=$id;?>">Banir</button><? } ?>
			<? if($ex['banido'] == 's') { ?><button class="btn btn-warning" onclick="desbanir(this);" rel="?p=<?=$p;?>&a=6&id=<?=$id;?>">Desbanir</button><? } ?>
			<button class="btn btn-info" onclick="alertar(this);" rel="?p=<?=$p;?>&a=7&id=<?=$id;?>">Enviar alerta</button>
			<? if($permissoes[51] == 's' || $core->allAccess()) { ?><button id="ids" class="btn btn-primary" onclick="darIds(this);" rel="?p=<?=$p;?>&a=8&id=<?=$id;?>">Dar moedas</button><? } ?>
			<? if($ex['ativado'] == 'n') { ?><button class="btn btn-danger" onclick="ativarConta(this);" rel="?p=<?=$p;?>&a=11&id=<?=$id;?>">Ativar conta</button><? } ?>
			<br><br>

			<? if($ex['banido'] == 's') {
				echo aviso_red("Este usuário está banido.<br>Motivo: ".$ex['ban_motivo']."<br>Até: " . date('d/m/Y H:i', $ex['ban_termino']));
			} ?>

			<? if($ex['ativado'] == 'n') {
				echo aviso_red("Este usuário não possui conta ativada, portanto, não pode logar na IceHabbo.");
			} ?>

			<? echo $form_return;

			$form = new Form('form-submit', '', true);

			//$form->mostraAviso(well('<b>'.$sys->getForumMsgs($ex['nick']).'</b> mensagens no fórum e <b>'.$ex['pixel_pontos'].'</b> pontos no iDPixel.<br>Último acesso: <b>'.date("d/m/Y H:i:s", $ex['acesso_data']).'</b><br>Data de registro: <b>'.date("d/m/Y H:i:s", $ex['data_criacao']).'</b><br>IP: <b>'.$ex['acesso_ip'].'</b>'));

			$sql6 = $conn->prepare("SELECT * FROM acp_nicks_trocados WHERE id_usuario = ? ORDER BY id DESC");
			$sql6->bindValue(1, $ex['id']);
			$sql6->execute();
			$sql7 = $sql6->fetchAll();

			$nicks_trocados = array();

			foreach ($sql7 as $atual) {
				$nicks_trocados[] = 'Nick trocado de <b>'.$atual['nick_original'].'</b> para <b>'.$atual['nick_novo'].'</b> - Por <b>'.$atual['autor'].'</b> em <b>'.date('d/m/Y H:i', $atual['data']).'</b>';
			}

			if(!empty($nicks_trocados)) {
				$form->mostraAviso(well('Este usuário já possuiu outros nicks:<br><br>' . implode('<br>', $nicks_trocados) ));
			}

			$form->createInput('Nick', 'text', 'nick', $core->clear($ex['nick']));
			$form->createInput('E-mail', 'text', 'email', $core->clear($ex['email']));
			$form->createInput('Nova senha', 'password', 'senha', '', '','', 'Digite caso queira trocar a atual.');

			if($permissoes[51] == 's' || $core->allAccess()) { $form->createInput('Quantidade de moedas', 'text', 'moedas', $ex['moedas'], '', '', 'Somente números.'); }

			$form->createUpload('Avatar', 'avatar', $ex['avatar']);
			$form->createCheckbox('Restaurar o avatar padrão?', 'avatar-padrao');

			$form->createTextarea('Assinatura', 'assinatura', $core->clear($ex['assinatura']));

			$form->generateForm();
			echo $form->form; ?>
		</div>

		<div class="box-content">
			<div class="title-section">Alertas enviados para este usuário</div>

			<? $sql9 = $conn->prepare("SELECT * FROM usuarios_alertas WHERE id_usuario = ?");
			$sql9->bindValue(1, $ex['id']);
			$sql9->execute();
			$sql10 = $sql9->fetchAll();

			if(count($sql10) == 0) {
				echo aviso_blue("Este usuário não recebeu nenhum alerta.");
			}

			foreach ($sql10 as $atual) { ?>
			<div class="well">
				Alerta enviado por <b><?=$atual['autor'];?></b> em <b><?=date('d/m/Y H:i', $atual['data']);?></b>
				<? if($atual['lido'] == 'n') { ?><span class="label label-danger">O usuário ainda não leu este alerta.</span><? } ?>
				<? if($atual['lido'] == 's') { ?><span class="label label-success">O usuário já leu este alerta.</span><? } ?>
				<br><br>

				<?=$core->clear($atual['alerta']);?>
			</div>
			<? } ?>
		</div>
		<? } // editar a si mesmo ?>
		<? } ?>

<? if($_GET['a'] == 339955) {
	$id = $_GET['id'];

	$ids = explode(',', $id);
	$ids = array_filter($ids);

	if(count($ids) > 0) {
		$delete = $conn->prepare("DELETE FROM $mdl_tabela WHERE id = ? LIMIT 1");
		$delete->bindParam(1, $id_atual);

		foreach ($ids as $id_atual) {
			$delete->execute();

			$core->logger("O usuário deletou o registro [#$id_atual - $mdl_tabela]", "acao");
		}
	} else {
		$delete_where['id'] = $id;
		$delete = $sqlActions->delete($mdl_tabela, $delete_where);

		$core->logger("O usuário deletou o registro [#$id_atual - $mdl_tabela]", "acao");
	}
} ?>

<? if($_GET['a'] == 3) {
	$id = $_GET['id'];

	$ids = explode(',', $id);
	$ids = array_filter($ids);

	if(count($ids) > 0) {
		$delete = $conn->prepare("UPDATE $mdl_tabela SET status = 'inativo' WHERE id = ? LIMIT 1");
		$delete->bindParam(1, $id_atual);

		foreach ($ids as $id_atual) {
			$delete->execute();

			$core->logger("O usuário deletou o usuário [#$id_atual]", "acao");
		}
	} else {
		$delete_where['id'] = $id;
		$delete = $conn->prepare("UPDATE $mdl_tabela SET status = 'inativo' WHERE id = ? LIMIT 1");
		$delete->bindValue(1, $id);
		$delete->execute();
		unset($delete_where);

		$core->logger("O usuário deletou o usuário [#$id]", "acao");
	}
} ?>

<? if($_GET['a'] == 4) {
	$id = $_GET['id'];

	$reset = $conn->query("ALTER TABLE $mdl_tabela AUTO_INCREMENT = 1;");
	$core->logger("O usuário resetou o AI de $mdl_tabela", "acao");

	echo "<script>location.replace('?p=$p');</script>";
} ?>

<? if($_GET['a'] == 9) {
	$id = $_GET['id'];

	$ativar = $conn->prepare("UPDATE $mdl_tabela SET status = 'ativo' WHERE id = ?");
	$ativar->bindValue(1, $id);
	$ativar->execute();

	$core->logger("O usuário ativou o registro [#$id - $mdl_tabela]", "acao");
} ?>

<? if($_GET['a'] == 5) {
	$id = $_GET['id'];
	$motivo = $_POST['motivo'];
	$tempo = $_POST['tempo'];

	$data = explode('/', $tempo);
	$hora = explode(':', substr($tempo, 11));

	$ban_tempo = mktime($hora[0], $hora[1], 0, $data[1], $data[0], $data[2]);

	$up['banido'] = 's';
	$up['ban_motivo'] = $motivo;
	$up['ban_termino'] = $ban_tempo;
	$up['ban_autor'] = $core->autor;
	$wh['id'] = $id;

	$update = $sqlActions->update($mdl_tabela, $up, $wh);

	$core->logger("O usuário baniu o usuário #$id", "acao");
} ?>

<? if($_GET['a'] == 6) {
	$id = $_GET['id'];

	$up['banido'] = 'n';
	$up['ban_motivo'] = '';
	$up['ban_termino'] = 0;
	$up['ban_autor'] = '';
	$wh['id'] = $id;

	$update = $sqlActions->update($mdl_tabela, $up, $wh);

	$core->logger("O usuário baniu o usuário #$id", "acao");
} ?>

<? if($_GET['a'] == 7) {
	$id = $_GET['id'];
	$alerta = $core->clear($_POST['alerta']);

	$in_data['id_usuario'] = $id;
	$in_data['alerta'] = $alerta;
	$in_data['autor'] = $autor;
	$in_data['data'] = $timestamp;

	$update = $sqlActions->insert("usuarios_alertas", $in_data);
	$core->logger("O usuário enviou um alerta para o usuário #$id", "acao");
} ?>

<? if($_GET['a'] == 8 && $permissoes[51] == 's' || $_GET['a'] == 8 && $core->allAccess()) {
	$id = $_GET['id'];
	$moedas = $core->clear($_POST['moedas']);

	$sql4 = $conn->prepare("SELECT moedas FROM usuarios WHERE id = ?");
	$sql4->bindValue(1, $id);
	$sql4->execute();
	$sql5 = $sql4->fetch();

	$up['moedas'] = $moedas + $sql5['moedas'];
	$wh['id'] = $id;

	$update = $sqlActions->update($mdl_tabela, $up, $wh);
	$core->logger("O usuário deu $moedas pixels para o usuário #$id", "acao");
	$core->sendNtfUser($id, "Você recebeu <b>$moedas</b> pixels.", "#", "win-coins");
} ?>

<? if($_GET['a'] == 9 && $permissoes[63] == 's' || $_GET['a'] == 9 && $core->allAccess()) {
	$id = $_GET['id'];
	$moedas = $core->clear($_POST['moedas']);

	$sql4 = $conn->prepare("SELECT moedas_alt FROM usuarios WHERE id = ?");
	$sql4->bindValue(1, $id);
	$sql4->execute();
	$sql5 = $sql4->fetch();

	$up['moedas_alt'] = $moedas + $sql5['moedas_alt'];
	$wh['id'] = $id;

	$update = $sqlActions->update($mdl_tabela, $up, $wh);
	$core->logger("O usuário deu $moedas moedas alternativas para o usuário #$id", "acao");
	$core->sendNtfUser($id, "Você recebeu <b>$moedas</b> ".$config['moedas_alt_nome'].".", "#", "win-coins-alt");
} ?>

<? if($_GET['a'] == 10 && $permissoes[64] == 's' || $_GET['a'] == 10 && $core->allAccess()) {
	$id = $_GET['id'];
	$moedas = $core->clear($_POST['moedas']);

	$sql4 = $conn->prepare("SELECT moedas_esmeralda FROM usuarios WHERE id = ?");
	$sql4->bindValue(1, $id);
	$sql4->execute();
	$sql5 = $sql4->fetch();

	$up['moedas_esmeralda'] = $moedas + $sql5['moedas_esmeralda'];
	$wh['id'] = $id;

	$update = $sqlActions->update($mdl_tabela, $up, $wh);
	$core->logger("O usuário deu $moedas esmeraldas para o usuário #$id", "acao");
	$core->sendNtfUser($id, "Você recebeu <b>$moedas</b> esmeraldas.", "#", "win-coins-emerald");
} ?>

<? if($_GET['a'] == 11) {
	$id = $_GET['id'];

	$up['ativado'] = 's';
	$up['ativado_data'] = $core->timestamp;
	$wh['id'] = $id;

	$update = $sqlActions->update($mdl_tabela, $up, $wh);
	$core->logger("O usuário ativou a conta do usuário #$id", "acao");
	$core->sendNtfUser($id, "Sua conta foi ativada manualmente por <b>{$core->autor}</b>", "#", "ticket-reply");
} ?>

<? if($_GET['a'] == '') { ?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>
	<? if($core->allAccess()) { ?><a href="?p=<?=$_GET['p'];?>&a=4"><button class="btn btn-danger">Resetar AI [DEV]</button></a><? } ?>
	<button class="btn btn-info" onclick="searchShow();">Pesquisar</button>
	<? if($_POST['search'] == 'search') { ?><a href="?p=<?=$_GET['p'];?>"><button class="btn btn-warning">Limpar busca</button></a><? } ?>
	<br><br>

	<?php

	$search = getSearchForm();
	echo $search;

	?>

	<?
	$table = new Table('', true, $core->allAccess());
	$table->head(array('#', 'Nick', 'Informações', 'Último acesso', 'Ações'));

	$table->startBody();

	$limite = 15;
	$pagina = $_GET['pag'];
	((!$pagina)) ? $pagina = 1 : '';
	$inicio = ($pagina * $limite) - $limite;

	$query = "$mdl_tabela ORDER BY id DESC";

	if($_POST['search'] == 'search') {
		$busca = $core->clear($_POST['busca']);
		$limite = 5000;

		$campo = "nick";

		$query = "$mdl_tabela WHERE nick LIKE ? OR acesso_ip LIKE ? OR email LIKE ? ORDER BY id DESC";
		$sql = $conn->prepare("SELECT * FROM $query LIMIT $inicio,$limite");
		$sql->bindValue(1, '%'.$busca.'%');
		$sql->bindValue(2, '%'.$busca.'%');
		$sql->bindValue(3, '%'.$busca.'%');
		$sql->execute();

		$_rows = $conn->prepare("SELECT count(id) FROM $query");
		$_rows->bindValue(1, '%'.$busca.'%');
		$_rows->bindValue(2, '%'.$busca.'%');
		$_rows->bindValue(3, '%'.$busca.'%');
		$_rows->execute();
		$total_registros = $_rows->fetchColumn();

		echo '<div class="searching">Pesquisando por: <b>'.$busca.'</b></div>';
	} else {
		$sql = $conn->query("SELECT * FROM $query LIMIT $inicio,$limite");
		$total_registros = $core->getRows("SELECT * FROM $query");
	}

	while($sql2 = $sql->fetch()) {
		$stats = '';

		if($sql2['banido'] == 's') { $stats .= '<span class="label label-danger">Banido</span> '; }
		if($sql2['vip'] == 's') { $stats .= '<span class="label label-primary">VIP</span> '; }
		if($sql2['ativado'] == 'n') { $stats .= '<span class="label label-info">Não ativado</span> '; }

		$sql3 = $conn->prepare("SELECT count(id) FROM acp_usuarios WHERE nick = ?");
		$sql3->bindValue(1, $sql2['nick']);
		$sql3->execute();
		$rows = $sql3->fetchColumn();

		if($rows > 0) { $stats .= '<span class="label label-success">Equipe</span> '; }

		$table->insertBody(array($sql2['id'], $core->clear($sql2['nick']), $stats, $core->clear(date('d/m/Y H:i', $sql2['acesso_data'])), 'actions'), $sql2['status']);
	}

	$table->closeTable();
	echo $table->table;

	if($total_registros == 0) {
		echo aviso_red("Nenhum registro encontrado.");
	} else {
		echo '<ul class="pagination">';

		$total_paginas = ceil($total_registros / $limite);

		$links_laterais = ceil($limite / 2);

		$inicio = $pagina - $links_laterais;
		$limite = $pagina + $links_laterais;

		for ($i = $inicio; $i <= $limite; $i++){
			if ($i == $pagina) {
				echo '<li class="active"><a href="#">'.$i.'</a></li>';
			} else {
				if ($i >= 1 && $i <= $total_paginas){
					$link = '?' . $_SERVER["QUERY_STRING"];
					$link = preg_replace('/(\\?|&)pag=.*?(&|$)/','',$link);
					echo '<li><a href="'.$link.'&pag='.$i.'">'.$i.'</a></li>';
				}
			}
		}

		echo '</ul>';
	} ?>

	<?php

	if($total_registros > 0) {
		$marked = getMarkedSelect($p, 3);
		echo $marked;
	}

	?>
</div>
<? } ?>