<? if($_GET['a'] == 1) {
	if($_POST['form'] == 'form') {
		$nome = $core->clear($_POST['nome']);
		$ranking = $core->clear($_POST['ranking']);
		$oculto = $core->clear($_POST['oculto']);
		$cat = $_POST['categoria'];
		$prosseguir = true;

		if($oculto == 'on') { $oculto = 's'; } else { $oculto = 'n'; }
		if($oculto != 's' && $oculto != 'n') { $oculto = 'n'; }

		if(empty($nome)) {
			$form_return .= aviso_red("Preencha todos os campos.");
			$prosseguir = false;
		}

		$per = array();

		$sql7 = $conn->query("SELECT * FROM acp_modulos");
		while($sql8 = $sql7->fetch()) {
			$perm_mdl = (int) $sql8['permissao'];
			$campo = $_POST["p-$perm_mdl"];

			if($permissoes[$sql8['permissao']] == 's' || $core->allAccess()) {
				if($campo == 'on') { $marked = 's'; } else { $marked = 'n'; }
			} else {
				$marked = 'n';
			}

			$per[$perm_mdl] = $marked;
			$all_marked[] = $perm_mdl;
		}

		$per[0] = 's';

		rsort($all_marked);
		$limite = $all_marked[0];

		for ($i=0; $i < $limite; $i++) {
			if(!in_array($i, $all_marked)) {
				$per[$i] = 'n';
			}
		}

		ksort($per);
		$perms = implode('|', $per);

		if($prosseguir) {
			$insert_data['nome'] = $nome;
			$insert_data['ranking'] = $ranking;
			$insert_data['permissoes'] = $perms;
			$insert_data['cat'] = $cat;
			$insert_data['oculto'] = $oculto;
			$insert_data['autor'] = $autor;
			$insert_data['data'] = $timestamp;

			$insert = $sqlActions->insert($mdl_tabela, $insert_data);
			$lastId = $conn->lastInsertId();

			if($insert) {
				$novos_cargos = $dados['cargos'] . $lastId . '|';

				$up_data['cargos'] = $novos_cargos;
				$up_where['id'] = $dados['id'];
				$up_user = $sqlActions->update("acp_usuarios", $up_data, $up_where);

				$core->logger("O usuário adicionou um novo cargo no painel.", "acao");

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
	$form->createInput('Ranking', 'text', 'ranking', '', '', '', 'Este é o ranking que os membros deste cargo deve exibir no fórum. Deixe em branco para nenhum.');

	$categorias = array(
		array("label" => 'Administração', "value" => 1),
		array("label" => 'Supervisão', "value" => 2),
		array("label" => 'Coordenação', "value" => 3),
		array("label" => 'Gerencia', "value" => 4),
		array("label" => 'Outros', "value" => 5),
		);

	$form->createSelect('Categoria', 'categoria', $categorias);

	$form->createCheckbox('Cargo oculto', 'oculto');
	$form->mostraAviso(well('Selecione as permissões que este cargo possui. Lembre-se que essas permissões serão combinadas com as permissões de outros cargos do usuário.'));

	$sql3 = $conn->query("SELECT * FROM acp_modulos_cat WHERE id != 0 ORDER BY id ASC");
	while($sql4 = $sql3->fetch()) {
		$form->mostraTitulo($sql4['nome']);

		$sql5 = $conn->query("SELECT * FROM acp_modulos WHERE cat_id='".$sql4['id']."'");
		while($sql6 = $sql5->fetch()) {
			$check_name = 'p-' . $sql6['permissao'];

			if($permissoes[$sql6['permissao']] == 's' || $core->allAccess()) {
				$form->createCheckbox($sql6['nome'], $check_name, '', 'check-side');
			}
		}
	}

	$form->generateForm();
	echo $form->form; ?>
</div>
<? } ?>

<? if($_GET['a'] == 2) {
	$id = $_GET['id'];

	if($_POST['form'] == 'form') {
		$nome = $core->clear($_POST['nome']);
		$ranking = $core->clear($_POST['ranking']);
		$oculto = $core->clear($_POST['oculto']);
		$cat = $_POST['categoria'];
		$prosseguir = true;

		if($oculto) { $oculto = 's'; } else { $oculto = 'n'; }
		if($oculto != 's' && $oculto != 'n') { $oculto = 'n'; }

		if(empty($nome)) {
			$form_return .= aviso_red("Preencha todos os campos.");
			$prosseguir = false;
		}

		$per = array();
		$all_marked = array();
		$sql7 = $conn->query("SELECT * FROM acp_modulos");
		while($sql8 = $sql7->fetch()) {
			$perm_mdl = (int) $sql8['permissao'];
			$campo = $_POST["p-$perm_mdl"];

			if($permissoes[$sql8['permissao']] == 's' || $core->allAccess()) {
				if($campo == 'on') { $marked = 's'; } else { $marked = 'n'; }
			} else {
				$marked = 'n';
			}

			$per[$perm_mdl] = $marked;
			$all_marked[] = $perm_mdl;
		}

		$per[0] = 's';

		rsort($all_marked);
		$limite = $all_marked[0];

		for ($i=0; $i < $limite; $i++) {
			if(!in_array($i, $all_marked)) {
				$per[$i] = 'n';
			}
		}

		ksort($per);
		$perms = implode('|', $per);

		if($prosseguir) {
			$update_data['nome'] = $nome;
			$update_data['ranking'] = $ranking;
			$update_data['permissoes'] = $perms;
			$update_data['cat'] = $cat;
			$update_data['oculto'] = $oculto;

			$where_data['id'] = $id;
			$update = $sqlActions->update($mdl_tabela, $update_data, $where_data);

			if($update) {
				$core->logger("O usuário editou o cargo [#$id].", "acao");

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
	$form->createInput('Ranking', 'text', 'ranking', $ex['ranking'], '', '', 'Este é o ranking que os membros deste cargo deve exibir no fórum. Deixe em branco para nenhum.');

	$categorias = array(
		array("label" => 'Administração', "value" => 1),
		array("label" => 'Supervisão', "value" => 2),
		array("label" => 'Coordenação', "value" => 3),
		array("label" => 'Gerencia', "value" => 4),
		array("label" => 'Outros', "value" => 5),
		);

	$form->createSelect('Categoria', 'categoria', $categorias, $ex['cat']);

	$form->createCheckbox('Cargo oculto', 'oculto', (($ex['oculto'] == 's')) ? true : false);
	$form->mostraAviso(well('Selecione as permissões que este cargo possui. Lembre-se que essas permissões serão combinadas com as permissões de outros cargos do usuário.'));

	$sql3 = $conn->query("SELECT * FROM acp_modulos_cat WHERE id != 0 ORDER BY id ASC");
	while($sql4 = $sql3->fetch()) {
		$form->mostraTitulo($sql4['nome']);

		$sql5 = $conn->query("SELECT * FROM acp_modulos WHERE cat_id='".$sql4['id']."'");
		while($sql6 = $sql5->fetch()) {
			$check_name = 'p-' . $sql6['permissao'];
			$permissions = explode('|', $ex['permissoes']);

			if($permissions[$sql6['permissao']] == 's') { $checked = true; } else { $checked = false; }

			if($permissoes[$sql6['permissao']] == 's' || $core->allAccess()) {
				$form->createCheckbox($sql6['nome'], $check_name, $checked, 'check-side');
			}
		}
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

	if($id != 1 && $id != 2) {
		$ids = explode(',', $id);
		$ids = array_filter($ids);

		if(count($ids) > 0) {
			$delete = $conn->prepare("UPDATE $mdl_tabela SET status = 'inativo' WHERE id = ? LIMIT 1");
			$delete->bindParam(1, $id_atual);

			foreach ($ids as $id_atual) {
				$delete->execute();

				$core->logger("O usuário deletou o cargo [#$id_atual]", "acao");

				$sql11 = $conn->query("SELECT id, cargos, cargos_e FROM acp_usuarios");
				while($sql12 = $sql11->fetch()) {
					$cargos = explode('|', $sql12['cargos']);

					if(($key = array_search($id_atual, $cargos)) !== false) {
						unset($cargos[$key]);
					}

					$novos_cargos = implode('|', $cargos);

					$cargos_e = explode('|', $sql12['cargos_e']);

					if(($key = array_search($id, $cargos_e)) !== false) {
						unset($cargos_e[$key]);
					}

					$novos_cargos_e = implode('|', $cargos_e);

					$id_user = $sql12['id'];

					$up_data['cargos'] = $novos_cargos;
					$up_data['cargos_e'] = $novos_cargos_e;

					$wh_data['id'] = $id_user;
					$update_cargos = $sqlActions->update("acp_usuarios", $up_data, $wh_data);
				}
			}
		} else {
			$delete_where['id'] = $id;
			$delete = $conn->prepare("UPDATE $mdl_tabela SET status = 'inativo' WHERE id = ? LIMIT 1");
		$delete->bindValue(1, $id);
		$delete->execute();

			$core->logger("O usuário deletou o cargo [#$id]", "acao");

			$sql11 = $conn->query("SELECT id, cargos, cargos_e FROM acp_usuarios");
			while($sql12 = $sql11->fetch()) {
				$cargos = explode('|', $sql12['cargos']);

				if(($key = array_search($id, $cargos)) !== false) {
					unset($cargos[$key]);
				}

				$novos_cargos = implode('|', $cargos);

				$cargos_e = explode('|', $sql12['cargos_e']);

				if(($key = array_search($id, $cargos_e)) !== false) {
					unset($cargos_e[$key]);
				}

				$novos_cargos_e = implode('|', $cargos_e);

				$id_user = $sql12['id'];

				$up_data['cargos'] = $novos_cargos;
				$up_data['cargos_e'] = $novos_cargos_e;

				$wh_data['id'] = $id_user;
				$update_cargos = $sqlActions->update("acp_usuarios", $up_data, $wh_data);
			}
		}
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
	$table->head(array('#', 'Nome', 'Autor', 'Data', 'Ações'));

	$table->startBody();

	$limite = 30;
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
		if($core->hasCargo($sql2['id'], $dados['id']) || $core->allAccess()) {
			$table->insertBody(array($sql2['id'], $core->clear($sql2['nome']), $core->clear($sql2['autor']), $core->clear(date('d/m/Y H:i', $sql2['data'])), 'actions'), $sql2['status']);
		}
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