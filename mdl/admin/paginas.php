<? if($_GET['a'] == 1 || $_GET['a'] == 2) {
	$script_js = 'pgExitEnable();';
}

if($_GET['a'] == 1) {
	if($_POST['form'] == 'form') {
		$titulo = $core->clear($_POST['titulo']);
		$conteudo = $_POST['conteudo'];
		$prosseguir = true;

		if(empty($titulo) || empty($conteudo)) {
			$form_return .= aviso_red("Preencha todos os campos.");
			$prosseguir = false;
		}

		$cargos_ler = array();
		$sql7 = $conn->query("SELECT * FROM acp_cargos ORDER BY nome ASC");
		while($sql8 = $sql7->fetch()) {
			$campo = 'c-' . $sql8['id'];

			if(isset($_POST[$campo])) {
				$cargos_ler[] = $sql8['id'];
			}
		}

		$cargos_ler = array_filter($cargos_ler);

		if(empty($cargos_ler)) {
			$cargos_ler = 'all';
		} else {
			$cargos_ler = implode('|', $cargos_ler);
		}

		if($prosseguir) {
			$insert_data['titulo'] = $titulo;
			$insert_data['conteudo'] = $conteudo;
			$insert_data['cargos'] = $cargos_ler;
			$insert_data['autor'] = $autor;
			$insert_data['data'] = $timestamp;

			$insert = $sqlActions->insert($mdl_tabela, $insert_data);

			if($insert) {
				$core->logger("O usuário adicionou uma nova página (painel).", "acao");

				$sql3 = $conn->prepare("SELECT id FROM acp_paginas WHERE autor = ? ORDER BY id DESC LIMIT 1");
				$sql3->bindValue(1, $core->autor);
				$sql3->execute();
				$sql4 = $sql3->fetch();

				$link = DOMAIN . '/acp/admin.php?v=1&id=' . $sql4['id'];
				$form_return .= aviso_green("Sucesso!<br>Link para a página: <a href='$link' target='_blank'><b>$link</b></a>");

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

	$form = new Form('form-submit', '', true);

	$form->createInput('Título', 'text', 'titulo');
	$form->createTextarea('Conteúdo', 'conteudo', '', 'ckeditor','ckeditor');

	$form->mostraTitulo('Cargos que podem ver esta página');
	$form->mostraAviso(well('Caso queira que todos os cargos vejam esta página, não marque nenhum.'));

	$sql7 = $conn->query("SELECT * FROM acp_cargos ORDER BY nome ASC");
	while($sql8 = $sql7->fetch()) {
		$form->createCheckbox($sql8['nome'], 'c-' . $sql8['id'], '', 'check-side');
	}

	$form->generateForm();
	echo $form->form; ?>
</div>
<? } ?>

<? if($_GET['a'] == 2) {
	$id = $_GET['id'];

	if($_POST['form'] == 'form') {
		$titulo = $core->clear($_POST['titulo']);
		$conteudo = $_POST['conteudo'];
		$prosseguir = true;

		if(empty($titulo) || empty($conteudo)) {
			$form_return .= aviso_red("Preencha todos os campos.");
			$prosseguir = false;
		}

		$cargos_ler = array();
		$sql7 = $conn->query("SELECT * FROM acp_cargos ORDER BY nome ASC");
		while($sql8 = $sql7->fetch()) {
			$campo = 'c-' . $sql8['id'];

			if(isset($_POST[$campo])) {
				$cargos_ler[] = $sql8['id'];
			}
		}

		$cargos_ler = array_filter($cargos_ler);

		if(empty($cargos_ler)) {
			$cargos_ler = 'all';
		} else {
			$cargos_ler = implode('|', $cargos_ler);
		}

		if($prosseguir) {
			$update_data['titulo'] = $titulo;
			$update_data['conteudo'] = $conteudo;
			$update_data['cargos'] = $cargos_ler;

			$where_data['id'] = $id;
			$update = $sqlActions->update($mdl_tabela, $update_data, $where_data);

			if($update) {
				$core->logger("O usuário editou a página (painel) [#$id].", "acao");

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

	$form = new Form('form-submit', '', true);

	$link = DOMAIN . '/acp/admin.php?v=1&id=' . $ex['id'];
	$form->mostraAviso(well("Link para esta página: <a href='$link' target='_blank'><b>$link</b></a>"));

	$usuarios_leram = array();
	$sql3 = $conn->prepare("SELECT * FROM acp_paginas_visualizacoes WHERE id_pagina = ?");
	$sql3->bindValue(1, $id);
	$sql3->execute();
	while($sql4 = $sql3->fetch()) {
		$sql5 = $conn->query("SELECT nick FROM acp_usuarios WHERE id='".$sql4['id_usuario']."' LIMIT 1");
		$sql6 = $sql5->fetch();

		$usuarios_leram[] = $sql6['nick'];
	}

	$leram = implode(' - ', $usuarios_leram);

	$form->mostraAviso(well("Usuários que visualizaram esta página: <b>$leram</b>"));

	$form->createInput('Título', 'text', 'titulo', $core->clear($ex['titulo']));
	$form->createTextarea('Conteúdo', 'conteudo', $ex['conteudo'], 'ckeditor','ckeditor');

	$form->mostraTitulo('Cargos que podem ver esta página');
	$form->mostraAviso(well('Caso queira que todos os cargos vejam esta página, não marque nenhum.'));

	$sql7 = $conn->query("SELECT * FROM acp_cargos ORDER BY nome ASC");
	while($sql8 = $sql7->fetch()) {
		$cargos_ler = explode('|', $ex['cargos']);

		if(in_array($sql8['id'], $cargos_ler)) { $checked = true; } else { $checked = false; }
		$form->createCheckbox($sql8['nome'], 'c-' . $sql8['id'], $checked, 'check-side');
	}

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

			$core->logger("O usuário deletou a página (painel) [#$id_atual]", "acao");
		}
	} else {
		$delete_where['id'] = $id;
		$delete = $conn->prepare("UPDATE $mdl_tabela SET status = 'inativo' WHERE id = ? LIMIT 1");
		$delete->bindValue(1, $id);
		$delete->execute();
		unset($delete_where);

		$core->logger("O usuário deletou a página (painel) [#$id]", "acao");
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

<? if($_GET['a'] == '') { ?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>
	<a href="?p=<?=$_GET['p'];?>&a=1"><button class="btn btn-primary">Adicionar</button></a>
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
	$table->head(array('#', 'Título', 'Autor', 'Data', 'Ações'));

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
		$table->insertBody(array($sql2['id'], $core->clear($sql2['titulo']), $core->clear($sql2['autor']), $core->clear(date('d/m/Y H:i', $sql2['data'])), 'actions'), $sql2['status']);
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