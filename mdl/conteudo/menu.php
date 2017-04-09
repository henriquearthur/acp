<? if($_GET['a'] == 1) {
	if($_POST['form'] == 'form') {
		$nome = $core->clear($_POST['nome']);
		$link = $core->clear($_POST['link']);
		//$icone = $core->clear($_POST['icone']);
		$prosseguir = true;

		if(empty($nome)) {
			$form_return .= aviso_red("Preencha todos os campos.");
			$prosseguir = false;
		}

		$sql5 = $conn->query("SELECT ordem FROM menu ORDER BY ordem DESC LIMIT 1");
		$sql6 = $sql5->fetch();
		$ordem = $sql6['ordem'] + 1;

		if($prosseguir) {
			$up_name = 'imagem';

			$up_gallery = $core->clear($_POST["gl-$up_name"]);
			$up_file = $_FILES["fl-$up_name"];
			$up_url = $core->clear($_POST["url-$up_name"]);

			$upload = new Upload($conn, $up_gallery, $up_file, $up_url, 'menu-', true);

			if(!$upload->erro) {
				$caminho_img = $upload->caminho;
			} else {
				$form_return .= aviso_red($upload->erro);
				$prosseguir = false;
			}
		}

		if($prosseguir) {
			$insert_data['nome'] = $nome;
			$insert_data['link'] = $link;
			$insert_data['icone'] = $caminho_img;
			$insert_data['ordem'] = $ordem;
			$insert_data['autor'] = $autor;
			$insert_data['data'] = $timestamp;

			$insert = $sqlActions->insert($mdl_tabela, $insert_data);

			if($insert) {
				$core->logger("O usuário adicionou um novo link no menu.", "acao");

				$form_return .= aviso_green("Sucesso!");
				foreach($_POST as $nome_campo => $valor){ $_POST[$nome_campo] = '';}
			} else {
				$form_return .= aviso_red("Ocorreu um erro ao executar a ação. Código de erro: {$sqlActions->error}");
			}
		}
	}
?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>

	<? echo $form_return;

	$form = new Form('form-submit', '');

	$form->createInput('Nome', 'text', 'nome');
	$form->createInput('Link', 'text', 'link', '', '', '', 'Evite utilizar caminhos absolutos. Utilize caminhos relativos.<br>Caminho absoluto - <pre>http://www.icehabbo.com.br/icehabbo/equipe</pre><br>Caminho relativo - <pre>/icehabbo/equipe</pre><br><br>Se este menu possuirá sub-menus ao invés de link direto, deixe este campo em branco.');
	$form->createUpload('Ícone', 'imagem');


	$form->generateForm();
	echo $form->form; ?>
</div>
<? } ?>

<? if($_GET['a'] == 2) {
	$id = $_GET['id'];

	if($_POST['form'] == 'form') {
		$nome = $core->clear($_POST['nome']);
		$link = $core->clear($_POST['link']);
		//$icone = $core->clear($_POST['icone']);
		$prosseguir = true;

		if(empty($nome)) {
			$form_return .= aviso_red("Preencha todos os campos.");
			$prosseguir = false;
		}

		if($prosseguir) {
			$up_name = 'imagem';

			$up_gallery = $core->clear($_POST["gl-$up_name"]);
			$up_file = $_FILES["fl-$up_name"];
			$up_url = $core->clear($_POST["url-$up_name"]);

			$_ex = $conn->prepare("SELECT * FROM $mdl_tabela WHERE id = ? LIMIT 1");
			$_ex->bindValue(1, $id);
			$_ex->execute();
			$ex = $_ex->fetch();

			$upload = new Upload($conn, $up_gallery, $up_file, $up_url, 'menu-', false, $ex['icone']);

			if(!$upload->erro) {
				$caminho_img = $upload->caminho;
			} else {
				$form_return .= aviso_red($upload->erro);
				$prosseguir = false;
			}
		}

		if($prosseguir) {
			$update_data['nome'] = $nome;
			$update_data['link'] = $link;
			$update_data['icone'] = $caminho_img;

			$where_data['id'] = $id;
			$update = $sqlActions->update($mdl_tabela, $update_data, $where_data);

			if($update) {
				$core->logger("O usuário editou o link do menu [#$id].", "acao");

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

	<button class="btn btn-danger" onclick="deletar(this, 1);" rel="?p=<?=$p;?>&a=3&id=<?=$id;?>">Inativar</button><br><br>

	<? echo $form_return;

	$form = new Form('form-submit', '');

	$form->createInput('Nome', 'text', 'nome', $ex['nome']);
	$form->createInput('Link', 'text', 'link', $ex['link'], '', '', 'Evite utilizar caminhos absolutos. Utilize caminhos relativos.<br>Caminho absoluto - <pre>http://www.icehabbo.com.br/icehabbo/equipe</pre><br>Caminho relativo - <pre>/icehabbo/equipe</pre><br><br>Se este menu possuirá sub-menus ao invés de link direto, deixe este campo em branco.');
	$form->createUpload('Ícone', 'imagem', $ex['icone']);

	$form->generateForm();
	echo $form->form; ?>
</div>
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

			$core->logger("O usuário deletou o link do menu [#$id_atual]", "acao");
		}
	} else {
		$delete_where['id'] = $id;
		$delete = $conn->prepare("UPDATE $mdl_tabela SET status = 'inativo' WHERE id = ? LIMIT 1");
		$delete->bindValue(1, $id);
		$delete->execute();

		$core->logger("O usuário deletou o link do menu [#$id]", "acao");
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

<? if($_GET['a'] == 5) { ?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>

	<ul id="menu-sort" style="list-style-type: none;">
	<? $sql = $conn->query("SELECT * FROM menu ORDER BY ordem ASC");
	while($sql2 = $sql->fetch()) { ?>
		<li class="ui-state-default box-menu-order" style="list-style-type: none;padding:10px;margin-bottom:5px;" rel="<?=$sql2['id'];?>"><span class="ui-icon ui-icon-arrowthick-2-n-s" style="float:left;"></span><?=$sql2['nome'];?></li>
	<? } ?>
	</ul>

	<div class="form-group submit"><button type="submit" class="btn btn-primary" onclick="menu.submit();">Enviar</button></div>
</div>
<? } ?>

<? if($_GET['a'] == 6) {
	$ids = $_POST['ids'];
	$ordem = $_POST['ordem'];

	$i = 0;
	foreach ($ids as $atual) {
		$ordem_atual = $ordem[$i];

		$up['ordem'] = $ordem_atual;
		$wh['id'] = $atual;

		$update = $sqlActions->update("menu", $up, $wh);

		$i++;
	}
} ?>

<? if($_GET['a'] == '') { ?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>
	<a href="?p=<?=$_GET['p'];?>&a=1"><button class="btn btn-primary">Adicionar</button></a>
	<a href="?p=<?=$_GET['p'];?>&a=5"><button class="btn btn-warning">Ordem</button></a>
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
	$table->head(array('#', 'Nome', 'Link', 'Data', 'Ações'));

	$table->startBody();

	$limite = 15;
	$pagina = $_GET['pag'];
	((!$pagina)) ? $pagina = 1 : '';
	$inicio = ($pagina * $limite) - $limite;

	$query = "$mdl_tabela ORDER BY id DESC";

	if($_POST['search'] == 'search') {
		$busca = $core->clear($_POST['busca']);
		$limite = 5000;

		$campo = "nome";

		$query = "$mdl_tabela WHERE $campo LIKE ? ORDER BY id DESC";
		$sql = $conn->prepare("SELECT * FROM $query LIMIT $inicio,$limite");
		$sql->bindValue(1, '%'.$busca.'%');
		$sql->execute();

		$_rows = $conn->prepare("SELECT count(id) FROM $query");
		$_rows->bindValue(1, '%'.$busca.'%');
		$_rows->execute();
		$total_registros = $_rows->fetchColumn();

		echo '<div class="searching">Pesquisando por: <b>'.$busca.'</b></div>';
	} else {
		$sql = $conn->query("SELECT * FROM $query LIMIT $inicio,$limite");
		$total_registros = $core->getRows("SELECT * FROM $query");
	}

	while($sql2 = $sql->fetch()) {
		$table->insertBody(array($sql2['id'], $core->clear($sql2['nome']), $core->clear($sql2['link']), $core->clear(date('d/m/Y H:i', $sql2['data'])), 'actions'), $sql2['status']);
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