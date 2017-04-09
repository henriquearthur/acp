<? if($_GET['a'] == 2) {
	$id = $_GET['id'];

	if($_POST['form'] == 'form') {
		$_ex = $conn->prepare("SELECT * FROM $mdl_tabela WHERE id = ? LIMIT 1");
		$_ex->bindValue(1, $id);
		$_ex->execute();
		$ex = $_ex->fetch();

		$titulo = $core->clear($_POST['titulo']);
		$descricao = $core->clear($_POST['descricao']);
		$cat_id = $core->clear($_POST['categoria']);
		$status = $core->clear($_POST['status']);
		$prosseguir = true;

		if(empty($titulo) || empty($descricao) || $status > 3) {
			$form_return .= aviso_red("Preencha todos os campos.");
			$prosseguir = false;
		}

		if($status == 1) { $status = 'ativado'; }
		if($status == 2) { $status = 'aguardando'; }
		if($status == 3) { $status = 'reprovado'; }

		if($prosseguir) {
			$update_data['titulo'] = $titulo;
			$update_data['descricao'] = $descricao;
			$update_data['cat_id'] = $cat_id;
			$update_data['status'] = $status;

			$where_data['id'] = $id;
			$update = $sqlActions->update($mdl_tabela, $update_data, $where_data);

			if($update) {

				$core->logger("O usuário editou a obra [#$id].", "acao");
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

	<button class="btn btn-danger" onclick="deletar(this, 1);" rel="?p=<?=$p;?>&a=3&id=<?=$id;?>">Deletar</button><br><br>

	<? echo $form_return;

	$form = new Form('form-submit', '', true);

	$form->mostraAviso(well("<center><a href=\"".$ex['imagem']."\" target=\"_blank\"><img src=\"".$ex['imagem']."\"></a></center>"));

	$form->createInput('Título', 'text', 'titulo', $core->clear($ex['titulo']));
	$form->createInput('Descrição', 'text', 'descricao', $core->clear($ex['descricao']));

	$categorias = array();
	$sql = $conn->query("SELECT * FROM pixel_cat ORDER BY id ASC");
	while($sql2 = $sql->fetch()) {
		$atual = array("label" => $core->clear($sql2['nome']), "value" => $sql2['id']);
		$categorias[] = $atual;
	}

	$form->createSelect('Categoria', 'categoria', $categorias, $ex['cat_id']);


	$status = array(
		array("label" => 'Aprovado', "value" => 1),
		array("label" => 'Aguardando aprovação', "value" => 2),
		array("label" => 'Reprovado', "value" => 3)
		);

	if($ex['status'] == 'ativado') { $stats = 1; }
	if($ex['status'] == 'aguardando') { $stats = 2; }
	if($ex['status'] == 'reprovado') { $stats = 3; }

	$form->createSelect('Status', 'status', $status, $stats);

	$form->generateForm();
	echo $form->form; ?>
</div>
<? } ?>

<? if($_GET['a'] == 3) {
	$id = $_GET['id'];

	$ids = explode(',', $id);
	$ids = array_filter($ids);

	if(count($ids) > 0) {
		$sql = $conn->prepare("SELECT autor FROM $mdl_tabela WHERE id = ?");
		$sql->bindParam(1, $id_atual);
		$nicks = array();

		$delete = $conn->prepare("DELETE FROM $mdl_tabela WHERE id = ? LIMIT 1");
		$delete->bindParam(1, $id_atual);

		$delete2 = $conn->prepare("DELETE FROM pixel_coment WHERE id_arte = ?");
		$delete2->bindParam(1, $id_atual);

		$delete3 = $conn->prepare("DELETE FROM pixel_visualizacoes WHERE id_arte = ?");
		$delete3->bindParam(1, $id_atual);

		foreach ($ids as $id_atual) {
			$sql->execute();
			$nicks[] = $sql->fetchColumn();

			$delete->execute();
			$delete2->execute();
			$delete3->execute();

			$core->logger("O usuário deletou a obra [#$id_atual]", "acao");
		}
	} else {

		$delete_where['id'] = $id;
		$delete = $sqlActions->delete($mdl_tabela, $delete_where);
		unset($delete_where);

		$delete_where['id_arte'] = $id;
		$delete = $sqlActions->delete("pixel_coment", $delete_where);
		unset($delete_where);

		$delete_where['id_arte'] = $id;
		$delete = $sqlActions->delete("pixel_visualizacoes", $delete_where);
		unset($delete_where);


		$core->logger("O usuário deletou a obra [#$id]", "acao");
	}
} ?>

<? if($_GET['a'] == 4) {
	$id = $_GET['id'];

	$reset = $conn->query("ALTER TABLE $mdl_tabela AUTO_INCREMENT = 1;");
	$core->logger("O usuário resetou o AI de $mdl_tabela", "acao");

	echo "<script>location.replace('?p=$p');</script>";
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
	$table = new Table();
	$table->head(array('#', 'Título', 'Status', 'Autor', 'Data', 'Ações'));

	$table->startBody();

	$limite = 15;
	$pagina = $_GET['pag'];
	((!$pagina)) ? $pagina = 1 : '';
	$inicio = ($pagina * $limite) - $limite;

	$query = "$mdl_tabela ORDER BY id DESC";

	if($_POST['search'] == 'search') {
		$busca = $core->clear($_POST['busca']);
		$limite = 5000;

		$campo = "titulo";

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
		if($sql2['status'] == 'ativado') { $stats = '<span class="label label-primary">Aprovado</span> '; }
		if($sql2['status'] == 'aguardando') { $stats = '<span class="label label-warning">Aguardando aprovação</span> '; }
		if($sql2['status'] == 'reprovado') { $stats = '<span class="label label-danger">Reprovado</span> '; }

		$table->insertBody(array($sql2['id'], $core->clear($sql2['titulo']), $stats, $core->clear($sql2['autor']), $core->clear(date('d/m/Y H:i', $sql2['data'])), 'actions'));
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
					$link = ereg_replace('&pag=(.*)', '', $link);
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