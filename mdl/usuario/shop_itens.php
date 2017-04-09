<? if($_GET['a'] == 1) {
	if($_POST['form'] == 'form' && isset($_GET['t'])) {
		$qtd_disponivel = $_POST['qtd_disponivel'];
		$preco_ids = $_POST['preco_ids'];
		$preco_esm = $_POST['preco_esm'];
		$preco_alt = $_POST['preco_alt'];
		$gratis = $_POST['gratis'];
		$prosseguir = true;

		if($gratis) {
			$preco_ids = 0;
			$preco_esm = 0;
			$preco_alt = 0;

			$gratis = 's';
		} else {
			$gratis = 'n';

			if(!is_numeric($preco_ids) || $preco_ids < 0) {
				$form_return .= aviso_red("O valor digitado no preço em iDs não é um valor númerico inteiro positivo.");
				$prosseguir = false;
			}

			if(!is_numeric($preco_esm) || $preco_esm < 0) {
				$form_return .= aviso_red("O valor digitado no preço em esmeralda não é um valor númerico inteiro positivo.");
				$prosseguir = false;
			}

			if(!is_numeric($preco_alt) || $preco_alt < 0) {
				$form_return .= aviso_red("O valor digitado no preço em moeda alternativa não é um valor númerico inteiro positivo.");
				$prosseguir = false;
			}
		}

		if($gratis != 's' && $gratis != 'n') { $gratis = 'n'; }

		if($_GET['t'] == 1) {
			$id_util = $_POST['id_util'];
			$insert_data['id_util'] = $id_util;
			$insert_data['nome'] = '[N.I] Colante';
		}

		if($_GET['t'] == 2) {
			$id_util = $_POST['id_util'];
			$insert_data['id_util'] = $id_util;
			$insert_data['nome'] = '[N.I] Fundo';
		}

		if($_GET['t'] == 3) {
			$id_util = $_POST['id_util'];
			$insert_data['id_util'] = $id_util;
			$insert_data['nome'] = '[N.I] Emblema';
		}

		if($_GET['t'] == 4) {
			$id_util = $_POST['id_util'];
			$insert_data['id_util'] = $id_util;
			$insert_data['nome'] = '[N.I] Capa do fórum';
		}

		if($_GET['t'] == 5) {
			$id_util = $_POST['id_util'];
			$insert_data['id_util'] = $id_util;
			$insert_data['nome'] = $_POST['nome'];
		}

		if($_GET['t'] == 6) {
			$id_util = $_POST['id_util'];
			$insert_data['id_util'] = $id_util;
			$insert_data['nome'] = $_POST['nome'];
		}

		if($_GET['t'] == 7) {
			$id_util = $_POST['id_util'];
			$insert_data['id_util'] = $id_util;
			$insert_data['nome'] = $_POST['nome'];
		}

		if($prosseguir) {
			$insert_data['tipo'] = $_GET['t'];
			$insert_data['qtd_disponivel'] = $qtd_disponivel;
			$insert_data['preco_ids'] = $preco_ids;
			$insert_data['preco_esm'] = $preco_esm;
			$insert_data['preco_alt'] = $preco_alt;
			$insert_data['gratis'] = $gratis;
			$insert_data['autor'] = $autor;
			$insert_data['data'] = $timestamp;

			$insert = $sqlActions->insert($mdl_tabela, $insert_data);

			if($insert) {
				$core->logger("O usuário adicionou um novo item no iD Shop.", "acao");

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

	<? if(!isset($_GET['t'])) { ?>
	O que você deseja adicionar?<br><br>
	<a href="?p=<?=$_GET['p'];?>&a=1&t=1"><button type="button" class="btn btn-primary">Colante</button></a>
	<a href="?p=<?=$_GET['p'];?>&a=1&t=2"><button type="button" class="btn btn-info">Fundo</button></a>
	<a href="?p=<?=$_GET['p'];?>&a=1&t=3"><button type="button" class="btn btn-warning">Emblema</button></a>
	<a href="?p=<?=$_GET['p'];?>&a=1&t=4"><button type="button" class="btn btn-danger">Capa do fórum</button></a>
	<a href="?p=<?=$_GET['p'];?>&a=1&t=5"><button type="button" class="btn btn-default">VIP</button></a>
	<a href="?p=<?=$_GET['p'];?>&a=1&t=6"><button type="button" class="btn btn-success">Esmeralda</button></a>
	<a href="?p=<?=$_GET['p'];?>&a=1&t=7"><button type="button" class="btn btn-info">Moeda alternativa</button></a>
	<? } else {
		echo '<a href="?p='.$_GET['p'].'&a=1"><button type="button" class="btn btn-primary">Voltar</button></a><br><br>';
		echo $form_return;

		$form = new Form('form-submit', '');

		if($_GET['t'] == 1) {
			$categorias = array();
			$sql = $conn->query("SELECT * FROM hm_colantes ORDER BY id DESC");
			while($sql2 = $sql->fetch()) {
				$atual = array("label" => $core->clear($sql2['nome']), "value" => $sql2['id']);
				$categorias[] = $atual;
			}

			$form->createSelect('Colante', 'id_util', $categorias);
		}

		if($_GET['t'] == 2) {
			$categorias = array();
			$sql = $conn->query("SELECT * FROM hm_fundos ORDER BY id DESC");
			while($sql2 = $sql->fetch()) {
				$atual = array("label" => $core->clear($sql2['nome']), "value" => $sql2['id']);
				$categorias[] = $atual;
			}

			$form->createSelect('Fundo', 'id_util', $categorias);
		}

		if($_GET['t'] == 3) {
			$categorias = array();
			$sql = $conn->query("SELECT * FROM emblemas ORDER BY id DESC");
			while($sql2 = $sql->fetch()) {
				$atual = array("label" => $core->clear($sql2['nome']), "value" => $sql2['id']);
				$categorias[] = $atual;
			}

			$form->createSelect('Emblema', 'id_util', $categorias);
		}

		if($_GET['t'] == 4) {
			$categorias = array();
			$sql = $conn->query("SELECT * FROM usuarios_capas WHERE paga = 's' ORDER BY id DESC");
			while($sql2 = $sql->fetch()) {
				$atual = array("label" => $core->clear($sql2['nome']), "value" => $sql2['id']);
				$categorias[] = $atual;
			}

			$form->createSelect('Capa', 'id_util', $categorias);
		}

		if($_GET['t'] == 5) {
			$form->createInput('Nome', 'text', 'nome');
			$form->createInput('Dias VIP', 'text', 'id_util', '', 'w-md', '', 'Somente números.');
		}

		if($_GET['t'] == 6) {
			$form->createInput('Nome', 'text', 'nome');
			$form->createInput('Quantidade de esmeraldas', 'text', 'id_util', '', 'w-md', '', 'Somente números.');
		}

		if($_GET['t'] == 7) {
			$form->createInput('Nome', 'text', 'nome');
			$form->createInput('Quantidade de moedas alternativas', 'text', 'id_util', '', 'w-md', '', 'Somente números.');
		}

		$form->createInput('Quantidade disponível', 'text', 'qtd_disponivel', '', 'w-md', '', 'Quando chegar ao limite de unidades, ninguém conseguirá comprar o item.<br>Somente números.');
		$form->createInput('Preço em iDs', 'text', 'preco_ids', '', 'w-md', '', 'Digite <b>0 (zero)</b> para não disponível em iDs.<br>Somente números.');
		$form->createInput('Preço em esmeralda', 'text', 'preco_esm', '', 'w-md', '', 'Digite <b>0 (zero)</b> para não disponível em esmeraldas.<br>Somente números.');
		$form->createInput('Preço em moeda alternativa', 'text', 'preco_alt', '', 'w-md', '', 'Digite <b>0 (zero)</b> para não disponível em moedas alternativas.<br>Somente números.');
		$form->mostraAviso(well('Para um item ser gratuito, você deve deixar os campos de <b>preço</b> em branco e marcar a opção abaixo.'));
		$form->createCheckbox('Grátis', 'gratis');

		$form->generateForm();
		echo $form->form;
	} ?>
</div>
<? } ?>

<? if($_GET['a'] == 2) {
	$id = $_GET['id'];

	if($_POST['form'] == 'form') {
		$qtd_disponivel = $_POST['qtd_disponivel'];
		$preco_ids = $_POST['preco_ids'];
		$preco_esm = $_POST['preco_esm'];
		$preco_alt = $_POST['preco_alt'];
		$gratis = $_POST['gratis'];
		$prosseguir = true;

		$_ex = $conn->prepare("SELECT * FROM $mdl_tabela WHERE id = ? LIMIT 1");
		$_ex->bindValue(1, $id);
		$_ex->execute();
		$ex = $_ex->fetch();

		if($gratis) {
			$preco_ids = 0;
			$preco_esm = 0;
			$preco_alt = 0;

			$gratis = 's';
		} else {
			$gratis = 'n';

			if(!is_numeric($preco_ids) || $preco_ids < 0) {
				$form_return .= aviso_red("O valor digitado no preço em iDs não é um valor númerico inteiro positivo.");
				$prosseguir = false;
			}

			if(!is_numeric($preco_esm) || $preco_esm < 0) {
				$form_return .= aviso_red("O valor digitado no preço em esmeralda não é um valor númerico inteiro positivo.");
				$prosseguir = false;
			}

			if(!is_numeric($preco_alt) || $preco_alt < 0) {
				$form_return .= aviso_red("O valor digitado no preço em moeda alternativa não é um valor númerico inteiro positivo.");
				$prosseguir = false;
			}
		}

		if($gratis != 's' && $gratis != 'n') { $gratis = 'n'; }

		if($ex['tipo'] == 1) {
			$id_util = $_POST['id_util'];
			$update_data['id_util'] = $id_util;
		}

		if($ex['tipo'] == 2) {
			$id_util = $_POST['id_util'];
			$update_data['id_util'] = $id_util;
		}

		if($ex['tipo'] == 3) {
			$id_util = $_POST['id_util'];
			$update_data['id_util'] = $id_util;
		}

		if($ex['tipo'] == 4) {
			$id_util = $_POST['id_util'];
			$update_data['id_util'] = $id_util;
		}

		if($ex['tipo'] == 5) {
			$id_util = $_POST['id_util'];
			$update_data['id_util'] = $id_util;
			$update_data['nome'] = $_POST['nome'];
		}

		if($ex['tipo'] == 6) {
			$id_util = $_POST['id_util'];
			$update_data['id_util'] = $id_util;
			$update_data['nome'] = $_POST['nome'];
		}

		if($ex['tipo'] == 7) {
			$id_util = $_POST['id_util'];
			$update_data['id_util'] = $id_util;
			$update_data['nome'] = $_POST['nome'];
		}

		if($prosseguir) {
			$update_data['qtd_disponivel'] = $qtd_disponivel;
			$update_data['preco_ids'] = $preco_ids;
			$update_data['preco_esm'] = $preco_esm;
			$update_data['preco_alt'] = $preco_alt;
			$update_data['gratis'] = $gratis;
			$where_data['id'] = $id;
			$update = $sqlActions->update($mdl_tabela, $update_data, $where_data);

			if($update) {
				$core->logger("O usuário editou o item do iD Shop [#$id].", "acao");

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

	<div class="well">
		<? $usuarios = explode('|', $ex['compradores']);
		$usuarios = array_filter($usuarios); ?>
		<b>Quantidade disponível inicialmente:</b> <?=$core->clear($ex['qtd_disponivel']);?><br>
		<b>Quantidade comprada pelos usuários:</b> <?=$core->clear($ex['qtd_comprado']);?><br>
		<b>Quantidade disponível no momento:</b> <?=$core->clear($ex['qtd_disponivel']) - $ex['qtd_comprado'];?><br>
		<b>Usuários que compraram:</b> <?=(count($usuarios) > 0) ? implode(' - ', $usuarios) : 'Nenhum usuário comprou este item.';?><br><br>
		<b>Autor:</b> <?=$core->clear($ex['autor']);?><br>
		<b>Data:</b> <?=date('d/m/Y H:i', $core->clear($ex['data']));?>
	</div>

	<? echo $form_return;

	$form = new Form('form-submit', '');

	if($ex['tipo'] == 1) {
		$categorias = array();
		$sql = $conn->query("SELECT * FROM hm_colantes ORDER BY id ASC");
		while($sql2 = $sql->fetch()) {
			$atual = array("label" => $core->clear($sql2['nome']), "value" => $sql2['id']);
			$categorias[] = $atual;
		}

		$form->createSelect('Colante', 'id_util', $categorias, $ex['id_util']);
	}

	if($ex['tipo'] == 2) {
		$categorias = array();
		$sql = $conn->query("SELECT * FROM hm_fundos ORDER BY id ASC");
		while($sql2 = $sql->fetch()) {
			$atual = array("label" => $core->clear($sql2['nome']), "value" => $sql2['id']);
			$categorias[] = $atual;
		}

		$form->createSelect('Fundo', 'id_util', $categorias, $ex['id_util']);
	}

	if($ex['tipo'] == 3) {
		$categorias = array();
		$sql = $conn->query("SELECT * FROM emblemas ORDER BY id ASC");
		while($sql2 = $sql->fetch()) {
			$atual = array("label" => $core->clear($sql2['nome']), "value" => $sql2['id']);
			$categorias[] = $atual;
		}

		$form->createSelect('Emblema', 'id_util', $categorias, $ex['id_util']);
	}

	if($ex['tipo'] == 4) {
		$categorias = array();
		$sql = $conn->query("SELECT * FROM usuarios_capas WHERE paga = 's' ORDER BY id ASC");
		while($sql2 = $sql->fetch()) {
			$atual = array("label" => $core->clear($sql2['nome']), "value" => $sql2['id']);
			$categorias[] = $atual;
		}

		$form->createSelect('Capa', 'id_util', $categorias, $ex['id_util']);
	}

	if($ex['tipo'] == 5) {
		$form->createInput('Nome', 'text', 'nome', $ex['nome']);
		$form->createInput('Dias VIP', 'text', 'id_util', $ex['id_util'], 'w-md', '', 'Somente números.');
	}

	if($ex['tipo'] == 6) {
		$form->createInput('Nome', 'text', 'nome', $ex['nome']);
		$form->createInput('Quantidade de esmeraldas', 'text', 'id_util', $ex['id_util'], 'w-md', '', 'Somente números.');
	}

	if($ex['tipo'] == 7) {
		$form->createInput('Nome', 'text', 'nome', $ex['nome']);
		$form->createInput('Quantidade de moedas alternativas', 'text', 'id_util', $ex['id_util'], 'w-md', '', 'Somente números.');
	}

	$form->createInput('Quantidade disponível', 'text', 'qtd_disponivel', $ex['qtd_disponivel'], 'w-md', '', 'Quando chegar ao limite de unidades, ninguém conseguirá comprar o item.<br>Somente números.');
	$form->createInput('Preço em iDs', 'text', 'preco_ids', $ex['preco_ids'], 'w-md', '', 'Digite <b>0 (zero)</b> para não disponível em iDs.<br>Somente números.');
	$form->createInput('Preço em esmeralda', 'text', 'preco_esm', $ex['preco_esm'], 'w-md', '', 'Digite <b>0 (zero)</b> para não disponível em esmeraldas.<br>Somente números.');
	$form->createInput('Preço em moeda alternativa', 'text', 'preco_alt', $ex['preco_alt'], 'w-md', '', 'Digite <b>0 (zero)</b> para não disponível em moedas alternativas.<br>Somente números.');
	$form->mostraAviso(well('Para um item ser gratuito, você deve deixar os campos de <b>preço</b> em branco e marcar a opção abaixo.'));
	$form->createCheckbox('Grátis', 'gratis', ($ex['gratis'] == 's') ? true : false);

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

			$core->logger("O usuário deletou o item do iD Shop [#$id_atual]", "acao");
		}
	} else {
		$delete_where['id'] = $id;
		$delete = $conn->prepare("UPDATE $mdl_tabela SET status = 'inativo' WHERE id = ? LIMIT 1");
		$delete->bindValue(1, $id);
		$delete->execute();

		$core->logger("O usuário deletou o item do iD Shop [#$id]", "acao");
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
	$table->head(array('#', 'Nome', 'Tipo', 'Quantidade', 'Data', 'Ações'));

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
		if($sql2['tipo'] == 1) {
			$tipo = 'Colante';

			$sql3 = $conn->prepare("SELECT nome FROM hm_colantes WHERE id = ?");
			$sql3->bindValue(1, $sql2['id_util']);
			$sql3->execute();
			$sql4 = $sql3->fetch();

			$nome = $sql4['nome'];
		}

		if($sql2['tipo'] == 2) {
			$tipo = 'Fundo';

			$sql3 = $conn->prepare("SELECT nome FROM hm_fundos WHERE id = ?");
			$sql3->bindValue(1, $sql2['id_util']);
			$sql3->execute();
			$sql4 = $sql3->fetch();

			$nome = $sql4['nome'];
		}

		if($sql2['tipo'] == 3) {
			$tipo = 'Emblema';

			$sql3 = $conn->prepare("SELECT nome FROM emblemas WHERE id = ?");
			$sql3->bindValue(1, $sql2['id_util']);
			$sql3->execute();
			$sql4 = $sql3->fetch();

			$nome = $sql4['nome'];
		}

		if($sql2['tipo'] == 4) {
			$tipo = 'Capa do fórum';

			$sql3 = $conn->prepare("SELECT nome FROM usuarios_capas WHERE id = ?");
			$sql3->bindValue(1, $sql2['id_util']);
			$sql3->execute();
			$sql4 = $sql3->fetch();

			$nome = $sql4['nome'];
		}

		if($sql2['tipo'] == 5) {
			$tipo = 'VIP';
			$nome = $sql2['nome'];
		}

		if($sql2['tipo'] == 6) {
			$tipo = 'Esmeralda';
			$nome = $sql2['nome'];
		}

		if($sql2['tipo'] == 7) {
			$tipo = 'Moeda alternativa';
			$nome = $sql2['nome'];
		}

		$table->insertBody(array($sql2['id'], $core->clear($nome), $tipo, 'Inicial: ' . $sql2['qtd_disponivel'] . ' - Comprada: ' .  $sql2['qtd_comprado'] . ' - Disponível: ' . ($sql2['qtd_disponivel'] - $sql2['qtd_comprado']), $core->clear(date('d/m/Y H:i', $sql2['data'])), 'actions'), $sql2['status']);
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