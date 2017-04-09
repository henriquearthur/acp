<? if($_GET['a'] == 1) {
	if($_POST['form'] == 'form') {
		$nome = $core->clear($_POST['nome']);
		$descricao = $core->clear($_POST['descricao']);
		$gratis = $core->clear($_POST['gratis']);
		$prosseguir = true;

		if(empty($nome) || empty($descricao)) {
			$form_return .= aviso_red("Preencha todos os campos.");
			$prosseguir = false;
		}

		if($prosseguir) {
			$up_name = 'imagem';

			$up_gallery = $core->clear($_POST["gl-$up_name"]);
			$up_file = $_FILES["fl-$up_name"];
			$up_url = $core->clear($_POST["url-$up_name"]);

			$upload = new Upload($conn, $up_gallery, $up_file, $up_url, 'badges-', true);

			if(!$upload->erro) {
				$caminho_img = $upload->caminho;
			} else {
				$form_return .= aviso_red($upload->erro);
				$prosseguir = false;
			}
		}

		if($gratis) { $gratis = 's'; } else { $gratis = 'n'; }

		if($prosseguir) {
			$insert_data['nome'] = $nome;
			$insert_data['descricao'] = $descricao;
			$insert_data['imagem'] = $caminho_img;
			$insert_data['gratis'] = $gratis;
			$insert_data['autor'] = $autor;
			$insert_data['data'] = $timestamp;

			$insert = $sqlActions->insert($mdl_tabela, $insert_data);

			if($insert) {
				$core->logger("O usuário adicionou um novo emblema de usuário.", "acao");

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

	$form = new Form('form-submit', '', true);

	$form->createInput('Nome', 'text', 'nome');
	$form->createInput('Descrição', 'text', 'descricao');
	$form->createUpload('Imagem', 'imagem');
	$form->createCheckbox('Emblema grátis', 'gratis', '', 'check-side');

	$form->generateForm();
	echo $form->form; ?>
</div>
<? } ?>

<? if($_GET['a'] == 2) {
	$id = $_GET['id'];

	if($_POST['form'] == 'form') {
		$nome = $core->clear($_POST['nome']);
		$descricao = $core->clear($_POST['descricao']);
		$gratis = $core->clear($_POST['gratis']);
		$prosseguir = true;

		if(empty($nome) || empty($descricao)) {
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

			$upload = new Upload($conn, $up_gallery, $up_file, $up_url, 'partners-', false, $ex['imagem']);

			if(!$upload->erro) {
				$caminho_img = $upload->caminho;
			} else {
				$form_return .= aviso_red($upload->erro);
				$prosseguir = false;
			}
		}

		if($gratis) { $gratis = 's'; } else { $gratis = 'n'; }

		if($prosseguir) {
			$update_data['nome'] = $nome;
			$update_data['descricao'] = $descricao;
			$update_data['imagem'] = $caminho_img;
			$update_data['gratis'] = $gratis;

			$where_data['id'] = $id;
			$update = $sqlActions->update($mdl_tabela, $update_data, $where_data);

			if($update) {
				$core->logger("O usuário editou o emblema de usuário [#$id].", "acao");

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

	$form->createInput('Nome', 'text', 'nome', $ex['nome']);
	$form->createInput('Descrição', 'text', 'descricao', $ex['descricao']);
	$form->createUpload('Imagem', 'imagem', $ex['imagem']);
	$form->createCheckbox('Emblema grátis', 'gratis', (($ex['gratis'] == 's')) ? true : false, 'check-side');

	$form->generateForm();
	echo $form->form; ?>
</div>

<div class="box-content">
	<div class="title-section">Usuários que possuem este emblema</div>
	<?
	$table = new Table('', true, $core->allAccess());
	$table->head(array('#', 'Nick', 'Quem deu?', 'Data', 'Remover emblema'));

	$table->startBody();

	$sql = $conn->prepare("SELECT * FROM emblemas_usuario WHERE id_emblema = ?");
	$sql->bindValue(1, $id);
	$sql->execute();
	$sql2 = $sql->fetchAll();

	foreach ($sql2 as $atual) {
		$sql3 = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
		$sql3->bindValue(1, $atual['id_usuario']);
		$sql3->execute();
		$sql4 = $sql3->fetch();
		$table->insertBody(array('', $core->clear($sql4['nick']), $core->clear($atual['autor']), $core->clear(date('d/m/Y H:i', $atual['data'])), '<button class="btn btn-danger btn-xsm" onclick="removerEmblema(this);" rel="?p='.$_GET['p'].'&a=6&id='.$atual['id'].'">Remover</button>'), $sql2['status']);
	}

	$table->closeTable();
	echo $table->table;

	if(count($sql2) == 0) {
		echo aviso_red("Nenhum usuário encontrado.");
	} ?>
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

		$delete2 = $conn->prepare("DELETE FROM emblemas_usuario WHERE id_emblema = ?");
		$delete2->bindParam(1, $id_atual);

		foreach ($ids as $id_atual) {
			$delete->execute();
			$delete2->execute();

			$core->logger("O usuário deletou o emblema de usuário [#$id_atual]", "acao");
		}
	} else {
		$delete_where['id'] = $id;
		$delete = $conn->prepare("UPDATE $mdl_tabela SET status = 'inativo' WHERE id = ? LIMIT 1");
		$delete->bindValue(1, $id);
		$delete->execute();
		unset($delete_where);

		$delete_where['id_emblema'] = $id;
		$delete = $sqlActions->delete("emblemas_usuario", $delete_where);
		unset($delete_where);

		$core->logger("O usuário deletou o emblema de usuário [#$id]", "acao");
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
	if($_POST['form'] == 'form') {
		$nick = $_POST['nick'];
		$emblema = $core->clear($_POST['emblema']);
		$prosseguir = true;

		if(empty($nick)) {
			$form_return .= aviso_red("Preencha todos os campos.");
			$prosseguir = false;
		}

		if($prosseguir) {
			$nicks = explode("\n", $nick);
			$nicks = array_filter($nicks);

			foreach ($nicks as $atual) {
				$atual = trim(preg_replace('/\s\s+/', ' ', $atual));
				$sql3 = $conn->prepare("SELECT * FROM usuarios WHERE nick = ?");
				$sql3->bindParam(1, $atual);
				$sql3->execute();
				$sql4 = $sql3->fetch();

				if($sql4) {
					$sql5 = $conn->prepare("SELECT count(id) FROM emblemas_usuario WHERE id_usuario = ? AND id_emblema = ?");
					$sql5->bindValue(1, $sql4['id']);
					$sql5->bindValue(2, $emblema);
					$sql5->execute();
					$rows = $sql5->fetchColumn();

					if($rows > 0) {
						$form_return .= aviso_red("O emblema não foi dado ao usuário $atual pois ele já o possui.");
					} else {
						$insert_data['id_usuario'] = $sql4['id'];
						$insert_data['id_emblema'] = $emblema;
						$insert_data['autor_tipo'] = 'ganhado';
						$insert_data['autor'] = $autor;
						$insert_data['data'] = $timestamp;

						$insert = $sqlActions->insert("emblemas_usuario", $insert_data);

						$sql = $conn->prepare("SELECT id FROM emblemas_usuario WHERE id_usuario = ? AND autor = ? ORDER BY id DESC");
						$sql->bindValue(1, $sql4['id']);
						$sql->bindValue(2, $autor);
						$sql->execute();
						$id_emblema_usuario = $sql->fetchColumn();

						if($insert) {
							$_emblema = $conn->prepare("SELECT nome, imagem FROM emblemas WHERE id = ?");
							$_emblema->bindValue(1, $emblema);
							$_emblema->execute();
							$embl = $_emblema->fetch();

							$core->sendPoints($sql4['nick'], 70);
							$core->sendNtfUser($sql4['nick'], "Você ganhou o emblema <b>{$core->clear($embl['nome'])}</b>.", "#", $embl['imagem']);
							$core->logger("O usuário deu um emblema ao usuário $atual (e. #$emblema)", "acao");
						} else {
							$form_return .= aviso_red("Ocorreu um erro ao executar a ação. Código de erro: {$sqlActions->error}");
						}
					}
				} else {
					$form_return .= aviso_red("O emblema não foi dado ao usuário $atual pois ele não existe.");
				}
			}
		}

		if($prosseguir) {
			$form_return .= aviso_green("Sucesso!");
			foreach($_POST as $nome_campo => $valor){ $_POST[$nome_campo] = '';}
		}
	}
?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>

	<? echo $form_return;

	$form = new Form('form-submit', '', true);

	$form->createTextarea('Nick do(s) usuário(s)', 'nick', '', '', '', '', 'Separe os nicks dos usuários que receberão o emblema por linha.');

	$categorias = array();
	$sql = $conn->query("SELECT * FROM emblemas ORDER BY id DESC");
	while($sql2 = $sql->fetch()) {
		$atual = array("label" => $core->clear($sql2['nome']), "value" => $sql2['id']);
		$categorias[] = $atual;
	}

	$form->createSelect('Emblema', 'emblema', $categorias, $ex['cat_id']);

	$form->generateForm();
	echo $form->form; ?>
</div>
<? } ?>

<? if($_GET['a'] == 6) {
	$id = $_GET['id'];

	$sql = $conn->prepare("SELECT id_usuario FROM emblemas_usuario WHERE id = ?");
	$sql->bindValue(1, $id);
	$sql->execute();
	$id_user = $sql->fetchColumn();

	$delete_where['id'] = $id;
	$delete = $sqlActions->delete("emblemas_usuario", $delete_where);


	$core->logger("O usuário retirou o emblema do usuário [#$id (id do emblema-usuário)]", "acao");

} ?>

<? if($_GET['a'] == '') { ?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>
	<a href="?p=<?=$_GET['p'];?>&a=1"><button class="btn btn-primary">Adicionar</button></a>
	<a href="?p=<?=$_GET['p'];?>&a=5"><button class="btn btn-warning">Dar emblema para usuário(s)</button></a>
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
	$table->head(array('#', 'Nome', 'Emblema', 'Autor', 'Data', 'Ações'));

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
		$table->insertBody(array($sql2['id'], $core->clear($sql2['nome']), '<img src="'.$core->clear($sql2['imagem']).'">', $core->clear($sql2['autor']), $core->clear(date('d/m/Y H:i', $sql2['data'])), 'actions'), $sql2['status']);
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