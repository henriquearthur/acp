<?php

/**
 * IceHabbo
 * by Henrique Arthur <eu@henriquearthur.me>
 * Não use sem autorização.
 */

$p = $_GET['p'];

$sql = $conn->prepare("SELECT * FROM acp_modulos WHERE id = ?");
$sql->bindValue(1, $p);
$sql->execute();
$mdl = $sql->fetch();

if(!empty($p)) {
	if(isset($_GET['debug'])) {
		echo '[DEBUG - DEV ONLY]' . '<br>';
		echo '<b>Nome:</b> ' . $mdl['nome'] . '<br>';
		echo '<b>Caminho do arquivo:</b> ' . $mdl['caminho'] . '<br>';
		echo '<b>Cat ID:</b> ' . $mdl['cat_id'] . '<br>';
		echo '<b>Permissão:</b> ' . ($mdl['permissao'] - 1);
	}

	if($mdl && $permissoes[$mdl['permissao']] == 's' || $mdl && $core->allAccess()) {
		$mdl_tabela = $mdl['tabela'];
		$path = 'mdl/' . $core->clear($mdl['caminho']);

		if(file_exists($path)) {
			$usuarios_on_mdl = array();

			$query = "SELECT id_usuario FROM acp_online WHERE url = ? AND id_usuario != ?";
			$sql3 = $conn->prepare($query);
			$sql3->bindValue(1, $core->url_unica);
			$sql3->bindValue(2, $dados['id']);
			$sql3->execute();

			while($sql4 = $sql3->fetch()) {
				$sql5 = $conn->query("SELECT nick FROM acp_usuarios WHERE id='".$sql4['id_usuario']."' LIMIT 1");
				$sql6 = $sql5->fetch();

				$usuarios_on_mdl[] = $sql6['nick'];
			}

			$query = "SELECT count(id) FROM acp_online WHERE url = ? AND id_usuario != ?";
			$sql3 = $conn->prepare($query);
			$sql3->bindValue(1, $core->url_unica);
			$sql3->bindValue(2, $dados['id']);
			$sql3->execute();

			$rows = $sql3->fetchColumn();
			if($rows > 0) {
				(($rows > 1)) ? $on_mdl = "Os usuários <b>" : $on_mdl = 'O usuário <b> ';
				$on_mdl .= implode(', ', $usuarios_on_mdl);
				(($rows > 1)) ? $on_mdl .= "</b> também estão navegando nesta página." : $on_mdl .= '</b> também está navegando nesta página.';
				echo aviso_yellow($on_mdl);
			}

			$sql7 = $conn->query("SELECT id, nome FROM acp_modulos_cat WHERE id='".$mdl['cat_id']."' LIMIT 1");
			$sql8 = $sql7->fetch();

			if($sql8['id'] > 1) {
				$bread  = '<div class="breadcumb">';
				$bread .= '<div class="item">'.$sql8['nome'].'</div>';
				$bread .= '<div class="sep">&bull;</div>';
				$bread .= '<a href="?p='.$mdl['id'].'"><div class="item">'.$mdl['nome'].'</div></a>';

				if($_GET['a'] == 1) {
					$bread .= '<div class="sep">&bull;</div>';
					$bread .= '<div class="item">Adicionar</div>';
				}

				if($_GET['a'] == 2) {
					$bread .= '<div class="sep">&bull;</div>';
					$bread .= '<div class="item">Editar item #'.$core->clear($_GET['id']).'</div>';
				}

				$bread .= '</div>';
				echo $bread;
			}

			include $path;
		} else {
			erro404();
		}
	} else {
		erro404();
	}
} else {
	if($_GET['v'] == 1) {
		include "mdl/ver_pagina.php";
	} else {
		if($_GET['v'] == 2) {
			include "mdl/agenda.php";
		} else {
			include "mdl/home.php";
		}
	}
}

/*

CÓDIGOS PADRÃO

=> FORMULÁRIO

<form action="<?=$_SERVER['REQUEST_URI'];?>" method="post" class="form-horizontal form-submit">
	<div class="form-group">
		<label class="form-label" for="nome">Nome:</label>
		<input class="form-input" type="text" name="nome" id="nome" placeholder="Nome" value="">
	</div>

	<div class="form-group">
		<label class="form-label" for="nome">Nome:</label>
		<input class="form-input" type="text" name="nome" id="nome" placeholder="Nome" value="">
	</div>

	<div class="form-group">
		<label class="form-label" for="nome">Nome:</label>
		<input class="form-input" type="text" name="nome" id="nome" placeholder="Nome" value="">
	</div>

	<br>

	<div class="form-group submit">
		<button type="submit" class="btn btn-primary">Enviar</button>
	</div>
</form>

========================================================================================

>>>>>>> 1a8af8995340bc2f4e52f82ae7bef9c5f3986de6
*/