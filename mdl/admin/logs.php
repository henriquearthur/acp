<? if($_GET['a'] == 2) {
	$id = $_GET['id'];

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

	<div class="well">
		Usuário que executou a ação: <b><?=$ex['autor'];?></b><br>
		Ação: <b><?=$ex['ato'];?></b><br><br>
		IP: <b><?=$ex['ip'];?></b><br>
		Geolocalização (Cloudflare): <b><?=$ex['geoloc'];?></b><br><br>
		URL: <b><?=$ex['url'];?></b><br>
		Data em que a ação foi realizada: <b><?=date('d/m/y H:i:s', $ex['data']);?></b><br>
		User Agent: <b><?=$ex['u_agent'];?></b><br>
	</div>
</div>
<? } ?>

<? if($_GET['a'] == '') { ?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>
	<a href="?p=<?=$_GET['p'];?>"><button class="btn btn-success">Todos</button></a>
	<a href="?p=<?=$_GET['p'];?>&e=2"><button class="btn btn-danger">Ação</button></a>
	<a href="?p=<?=$_GET['p'];?>&e=1"><button class="btn btn-primary">Acesso</button></a>
	<button class="btn btn-info" onclick="searchShow();">Pesquisar</button>
	<? if($_POST['search'] == 'search') { ?><a href="?p=<?=$_GET['p'];?>"><button class="btn btn-warning">Limpar busca</button></a><? } ?>
	<br><br>

	<?php

	$search = getSearchForm();
	echo $search;

	?>

	<?
	$table = new Table('', false);
	$table->head(array('#', 'Ato', 'Autor', 'Data'));

	$table->startBody();

	$limite = 15;
	$pagina = $_GET['pag'];
	((!$pagina)) ? $pagina = 1 : '';
	$inicio = ($pagina * $limite) - $limite;

	$query = "$mdl_tabela ORDER BY id DESC";

	if($_POST['search'] == 'search') {
		$busca = $core->clear($_POST['busca']);
		$limite = 5000;

		//$campo = "titulo";

		$query = "$mdl_tabela WHERE autor LIKE ? OR ato LIKE ? ORDER BY id DESC";
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
		if($_GET['nick'] != '') {
			$nick = $core->clear($_GET['nick']);

			$query = "$mdl_tabela WHERE autor = ? ORDER BY id DESC";

			$sql = $conn->prepare("SELECT * FROM $query LIMIT $inicio,$limite");
			$sql->bindValue(1, $nick);
			$sql->execute();

			$_rows = $conn->prepare("SELECT count(id) FROM $query");
			$_rows->bindValue(1, $nick);
			$_rows->execute();
			$total_registros = $_rows->fetchColumn();

			echo '<div class="searching">Exibindo somente atos do usuário <b>'.$nick.'</b></div>';
		} else {
			$sql = $conn->query("SELECT * FROM $query LIMIT $inicio,$limite");
			$total_registros = $core->getRows("SELECT * FROM $query");
		}
	}

	while($sql2 = $sql->fetch()) {
		$table->insertBody(array($sql2['id'], $core->clear($sql2['ato']), $core->clear($sql2['autor']), $core->clear(date('d/m/Y H:i', $sql2['data']))), $sql2['status']);
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
</div>
<? } ?>