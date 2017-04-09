<? if($_GET['a'] == 2) {
	$id = $_GET['id'];

	$_ex = $conn->prepare("SELECT * FROM $mdl_tabela WHERE id = ? LIMIT 1");
	$_ex->bindValue(1, $id);
	$_ex->execute();
	$ex = $_ex->fetch();

	if(!$ex) {
		$script_js .= register404();
	} ?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>

	<button class="btn btn-danger" onclick="deletar(this, 1);" rel="?p=<?=$p;?>&a=3&id=<?=$id;?>">Desbanir</button><br><br>

	<div class="well well-sm">
		Nick do usuário: <b><?=(!empty($ex['nick'])) ? $ex['nick'] : 'não informado';?></b><br>
		IP: <b><?=$ex['ip_ban'];?></b><br>
		Motivo do banimento: <b><?=$ex['ban_motivo'];?></b><br>
		Banido até: <b><?=date('d/m/Y H:i', $ex['ban_termino']);?></b><br>
		Quem baniu: <b><?=$ex['ban_autor'];?></b>
	</div>
</div>
<? } ?>

<? if($_GET['a'] == '') {
	if($_POST['form'] == 'form') {
		$nick = $core->clear($_POST['nick']);
		$ip_ban = $core->clear($_POST['ip']);
		$motivo = $core->clear($_POST['motivo']);
		$termino = $core->clear($_POST['termino']);
		$prosseguir = true;

		$data = explode('/', $termino);
		$hora = explode(':', substr($termino, 11));

		$ban_tempo = mktime($hora[0], $hora[1], 0, $data[1], $data[0], $data[2]);

		if(empty($ip_ban) || empty($motivo) || empty($termino)) {
			$form_return .= aviso_red("Preencha todos os campos.");
			$prosseguir = false;
		}

		if($prosseguir) {
			$insert_data['nick'] = $nick;
			$insert_data['ip_ban'] = $ip_ban;
			$insert_data['ban_motivo'] = $motivo;
			$insert_data['ban_termino'] = $ban_tempo;
			$insert_data['ban_autor'] = $autor;
			$insert_data['data'] = $timestamp;

			$insert = $sqlActions->insert($mdl_tabela, $insert_data);

			if($insert) {
				$core->logger("O usuário baniu um IP.", "acao");

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

		$form->createInput('Nick', 'text', 'nick', '', '', '', 'Este campo é opcional.');
		$form->createInput('IP', 'text', 'ip', '', '', '', 'Não é permitido banir por faixa de IP. Digite o IP exato.');
		$form->createTextarea('Motivo', 'motivo');
		$form->createInput('Término do banimento', 'text', 'termino', '', '', '', 'Banido até quando? Utilize o modelo <pre>DD/MM/AAAA HH:MM</pre>');

		$form->generateForm();
		echo $form->form; ?>
	</div>


	<div class="box-content">
		<div class="title-section"><?=$mdl['nome'];?></div>

		<button class="btn btn-info" onclick="searchShow();">Pesquisar</button>
		<? if($_POST['search'] == 'search') { ?><a href="?p=<?=$_GET['p'];?>"><button class="btn btn-warning">Limpar busca</button></a><? } ?>
		<br><br>

		<?php

		$search = getSearchForm();
		echo $search;

		?>

		<?
		$table = new Table('', true, $core->allAccess());
		$table->head(array('#', 'Nick', 'IP', 'Motivo', 'Término', 'Quem baniu?', 'Ações'));

		$table->startBody();

		$limite = 15;
		$pagina = $_GET['pag'];
		((!$pagina)) ? $pagina = 1 : '';
		$inicio = ($pagina * $limite) - $limite;

		$query = "$mdl_tabela ORDER BY id DESC";

		if($_POST['search'] == 'search') {
			$busca = $core->clear($_POST['busca']);
			$limite = 5000;

			$campo = "ip";

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
			$table->insertBody(array($sql2['id'], ((empty($sql2['nick']))) ? 'não informado' : $sql2['nick'], $core->clear($sql2['ip_ban']), $core->clear($sql2['ban_motivo']), $core->clear(date('d/m/Y H:i', $sql2['ban_termino'])), $core->clear($sql2['ban_autor']),'actions'), $sql2['status']);
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

				$core->logger("O usuário desbaniu o IP [#$id_atual]", "acao");
			}
		} else {
			$delete_where['id'] = $id;
			$delete = $conn->prepare("UPDATE $mdl_tabela SET status = 'inativo' WHERE id = ? LIMIT 1");
		$delete->bindValue(1, $id);
		$delete->execute();

			$core->logger("O usuário desbaniu o IP [#$id]", "acao");
		}
	} ?>