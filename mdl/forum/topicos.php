<? if($_GET['a'] == 2) {
	$id = $_GET['id'];

	if($_POST['form'] == 'form') {
		$titulo = $core->clear($_POST['titulo']);
		$conteudo = $core->clear($_POST['conteudo']);
		$categoria = $core->clear($_POST['categoria']);
		$fixo = $core->clear($_POST['fixo']);
		$prosseguir = true;

		if(empty($titulo) || empty($conteudo)) {
			$form_return .= aviso_red("Preencha todos os campos.");
			$prosseguir = false;
		}

		if($fixo) { $fixo = 's'; } else { $fixo = 'n'; }

		if($prosseguir) {
			$update_data['titulo'] = $titulo;
			$update_data['conteudo'] = $conteudo;
			$update_data['cat_id'] = $categoria;
			$update_data['fixo'] = $fixo;
			$where_data['id'] = $id;
			$update = $sqlActions->update($mdl_tabela, $update_data, $where_data);

			$up['fixo'] = $fixo;
			$wh['id_topico'] = $id;
			$update = $sqlActions->update("forum_topicos_ativos", $up, $wh);

			if($update) {
				$core->logger("O usuário editou o tópico [#$id].", "acao");

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
	<? if($ex['moderado'] == 'n') { ?><button class="btn btn-warning" onclick="moderarTpc(this);" rel="?p=<?=$p;?>&a=5&id=<?=$id;?>">Marcar como moderado</button><? } ?>
	<? if($ex['fechado'] == 'n') { ?><button class="btn btn-info" onclick="fecharTpc(this);" rel="?p=<?=$p;?>&a=6&id=<?=$id;?>">Fechar tópico</button><? } ?>
	<? if($ex['fechado'] == 's') { ?><button class="btn btn-info" onclick="abrirTpc(this);" rel="?p=<?=$p;?>&a=7&id=<?=$id;?>">Abrir tópico</button><? } ?>
	<? if($ex['status'] == 'inativo') { ?><button class="btn btn-warning" onclick="ativarTopico(this);" rel="?p=<?=$p;?>&a=8&id=<?=$id;?>">Ativar tópico</button><? } ?>
	<? if($ex['status'] == 'ativo') { ?><button class="btn btn-warning" onclick="inativarTopico(this);" rel="?p=<?=$p;?>&a=9&id=<?=$id;?>">Inativar tópico</button><? } ?>
	<button class="btn btn-danger" onclick="deletar(this, 1);" rel="?p=<?=$p;?>&a=3&id=<?=$id;?>">Inativar</button><br><br>

	<? echo $form_return;

	$form = new Form('form-submit', '');

	$form->mostraAviso(well('Por enquanto, utilize as ferramentas no site para os recursos que não estão aqui.'));

	if($ex['moderado'] == 's') {
		$form->mostraAviso(aviso_blue("Este tópico foi moderado por <b>".$ex['mod_autor']."</b> em <b>".date('d/m/Y H:i', $ex['mod_data'])."</b>"));
	} else {
		$form->mostraAviso(aviso_red("Este tópico ainda não foi moderado."));
	}

	if($ex['fechado'] == 's') {
		$form->mostraAviso(aviso_yellow("Este tópico foi fechado por <b>".$ex['fechado_autor']."</b> em <b>".date('d/m/Y H:i', $ex['fechado_data'])."</b>"));
	}

	if($ex['status'] == 'inativo') {
		$form->mostraAviso(aviso_red("Este tópico foi inativado por <b>".$ex['status_autor']."</b> em <b>".date('d/m/Y H:i', $ex['status_data'])."</b>"));
	}

	$form->createInput('Título', 'text', 'titulo', $ex['titulo']);
	$form->createTextarea('Conteúdo', 'conteudo', $ex['conteudo']);

	$categorias = array();
	$sql = $conn->query("SELECT * FROM forum_cat ORDER BY id ASC");
	while($sql2 = $sql->fetch()) {
		$atual = array("label" => $core->clear($sql2['nome']), "value" => $sql2['id']);
		$categorias[] = $atual;
	}

	$form->createSelect('Categoria', 'categoria', $categorias, $ex['cat_id']);

	$categorias = array();
	$sql = $conn->query("SELECT * FROM forum_cat ORDER BY id ASC");
	while($sql2 = $sql->fetch()) {
		$atual = array("label" => $core->clear($sql2['nome']), "value" => $sql2['id']);
		$categorias[] = $atual;
	}

	$form->createCheckbox('Tópico fixo', 'fixo', ($ex['fixo'] == 's') ? true : false);

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
		$sql = $conn->prepare("SELECT autor FROM $mdl_tabela WHERE id = ?");
		$sql->bindParam(1, $id_atual);

		$delete = $conn->prepare("UPDATE $mdl_tabela SET status = 'inativo' WHERE id = ? LIMIT 1");
		$delete->bindParam(1, $id_atual);

		$delete2 = $conn->prepare("DELETE FROM forum_topicos_ativos WHERE id_topico = ? LIMIT 1");
		$delete2->bindParam(1, $id_atual);

		foreach ($ids as $id_atual) {
			$sql->execute();
			$sql2 = $sql->fetchAll();
			$sql2 = $sql2[0]['autor'];

			$delete->execute();
			$delete2->execute();

			$core->logger("O usuário deletou o tópico [#$id_atual]", "acao");
		}
	} else {
		$sql = $conn->prepare("SELECT autor FROM $mdl_tabela WHERE id = ?");
		$sql->bindValue(1, $id);
		$sql->execute();
		$sql2 = $sql->fetchAll();
		$sql2 = $sql2[0]['autor'];

		$delete_where['id'] = $id;
		$delete = $conn->prepare("UPDATE $mdl_tabela SET status = 'inativo' WHERE id = ? LIMIT 1");
		$delete->bindValue(1, $id);
		$delete->execute();

		$delete_where2['id_topico'] = $id;
		$delete2 = $sqlActions->delete("forum_topicos_ativos", $delete_where2);

		$core->logger("O usuário deletou o tópico [#$id]", "acao");
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

	$up['moderado'] = 's';
	$up['mod_autor'] = $core->autor;
	$up['mod_data'] = $core->timestamp;
	$wh['id'] = $id;
	$update = $sqlActions->update("forum_topicos", $up, $wh);

	$core->logger("O usuário marcou o tópico como moderado [#$id].", "acao");
} ?>

<? if($_GET['a'] == 6) {
	$id = $_GET['id'];

	$up['fechado'] = 's';
	$up['fechado_autor'] = $core->autor;
	$up['fechado_data'] = $core->timestamp;
	$wh['id'] = $id;
	$update = $sqlActions->update("forum_topicos", $up, $wh);

	$core->logger("O usuário fechou o tópico [#$id].", "acao");
} ?>

<? if($_GET['a'] == 7) {
	$id = $_GET['id'];

	$up['fechado'] = 'n';
	$up['fechado_autor'] = '';
	$up['fechado_data'] = 0;
	$wh['id'] = $id;
	$update = $sqlActions->update("forum_topicos", $up, $wh);

	$core->logger("O usuário abriu o tópico [#$id].", "acao");
} ?>

<? if($_GET['a'] == 8) {
	$id = $_GET['id'];

	$up['status'] = 'ativo';
	$up['status_autor'] = $core->autor;
	$up['status_data'] = $core->timestamp;
	$wh['id'] = $id;
	$update = $sqlActions->update("forum_topicos", $up, $wh);
	unset($up);
	unset($wh);

	$up['status'] = 'ativo';
	$wh['id_topico'] = $id;
	$update = $sqlActions->update("forum_topicos_ativos", $up, $wh);
	unset($up);
	unset($wh);

	$core->logger("O usuário ativou o tópico [#$id].", "acao");
} ?>

<? if($_GET['a'] == 9) {
	$id = $_GET['id'];

	$up['status'] = 'inativo';
	$up['status_autor'] = '';
	$up['status_data'] = 0;
	$wh['id'] = $id;
	$update = $sqlActions->update("forum_topicos", $up, $wh);
	unset($up);
	unset($wh);

	$up['status'] = 'inativo';
	$wh['id_topico'] = $id;
	$update = $sqlActions->update("forum_topicos_ativos", $up, $wh);
	unset($up);
	unset($wh);

	$core->logger("O usuário inativou o tópico [#$id].", "acao");
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
	$table->head(array('#', 'Título', 'Informações', 'Autor', 'Data', 'Ações'));

	$table->startBody();

	$limite = 15;
	$pagina = $_GET['pag'];
	((!$pagina)) ? $pagina = 1 : '';
	$inicio = ($pagina * $limite) - $limite;

	$query = "$mdl_tabela ORDER BY id DESC";

	if($_POST['search'] == 'search') {
		$busca = $core->clear($_POST['busca']);
		$limite = 5000;

		$campo = //titulo";

		$query = "$mdl_tabela WHERE autor LIKE ? OR conteudo LIKE ? ORDER BY id DESC";
		$sql = $conn->prepare("SELECT * FROM $query LIMIT $inicio,$limite");
		$sql->bindValue(1, '%'.$busca.'%');
		$sql->bindValue(2, '%'.$busca.'%');
		$sql->execute();

		$_rows = $conn->prepare("SELECT count(id) FROM $query");
		$_rows->bindValue(1, '%'.$busca.'%');
		$_rows->bindValue(2, '%'.$busca.'%');
		$_rows->execute();
		$total_registros = $_rows->fetchColumn();

		echo '<div class="searching">Pesquisando por: <b>'.$busca.'</b></div>';
	} else {
		$sql = $conn->query("SELECT * FROM $query LIMIT $inicio,$limite");
		$total_registros = $core->getRows("SELECT * FROM $query");
	}

	while($sql2 = $sql->fetch()) {
		$infos = '';

		if($sql2['status'] == 'ativo') { $infos .= '<span class="label label-primary">Ativo</span> '; }
		if($sql2['fechado'] == 's') { $infos .= '<span class="label label-warning">Fechado</span> '; }
		if($sql2['status'] == 'inativo') { $infos .= '<span class="label label-danger">Inativo</span> '; }
		if($sql2['fixo'] == 's') { $infos .= '<span class="label label-info">Fixo</span> '; }

		$table->insertBody(array($sql2['id'], $core->clear($sql2['titulo']), $infos, $core->clear($sql2['autor']), $core->clear(date('d/m/Y H:i', $sql2['data'])), 'actions'), $sql2['status']);
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