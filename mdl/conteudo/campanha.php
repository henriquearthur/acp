<? if($_GET['a'] == 1) {
	if($_POST['form'] == 'form') {
		$nome           = $core->clear($_POST['nome']);
		$eq1_nome       = $core->clear($_POST['eq1_nome']);
		$eq1_id_emblema = $core->clear($_POST['eq1_id_emblema']);
		$eq2_nome       = $core->clear($_POST['eq2_nome']);
		$eq2_id_emblema = $core->clear($_POST['eq2_id_emblema']);
		$prosseguir     = true;

		if($prosseguir) {
			$up['nome'] = $nome;
			$up['eq1_nome'] = $eq1_nome;
			$up['eq1_id_emblema'] = $eq1_id_emblema;
			$up['eq2_nome'] = $eq2_nome;
			$up['eq2_id_emblema'] = $eq2_id_emblema;
			$up['autor'] = $autor;
			$up['data'] = $core->timestamp;

			$update = $sqlActions->update($mdl_tabela, $up);

			if($update) {
				$core->logger("O usuário editou as configurações da Campanha", "acao");

				$form_return .= aviso_green("Sucesso!");
				foreach($_POST as $nome_campo => $valor){ $_POST[$nome_campo] = '';}
			} else {
				$form_return .= aviso_red("Ocorreu um erro ao executar a ação. Código de erro: {$sqlActions->error}");
			}
		}
	}

	$_ex = $conn->prepare("SELECT * FROM $mdl_tabela LIMIT 1");
	$_ex->execute();
	$ex = $_ex->fetch(); ?>
	<div class="box-content">
		<div class="title-section"><?=$mdl['nome'];?></div>

		<? echo $form_return;

		$form = new Form('form-submit', '');

		$emblemas = array();
		$sql = $conn->query("SELECT * FROM emblemas ORDER BY id DESC");
		while($sql2 = $sql->fetch()) {
			$atual = array("label" => $core->clear($sql2['nome']), "value" => $sql2['id']);
			$emblemas[] = $atual;
		}

		$form->createInput('Nome da campanha', 'text', 'nome', $ex['nome']);
		$form->createInput('Nome da Equipe 1', 'text', 'eq1_nome', $ex['eq1_nome']);
		$form->createSelect('Emblema da Equipe 1', 'eq1_id_emblema', $emblemas, $ex['eq1_id_emblema']);

		$form->createInput('Nome da Equipe 2', 'text', 'eq2_nome', $ex['eq2_nome']);
		$form->createSelect('Emblema da Equipe 2', 'eq2_id_emblema', $emblemas, $ex['eq2_id_emblema']);


		$form->generateForm();
		echo $form->form;
		?>
	</div>
	<? } ?>

	<? if($_GET['a'] == 2) {
		$_ex = $conn->prepare("SELECT * FROM $mdl_tabela LIMIT 1");
		$_ex->execute();
		$ex = $_ex->fetch();

		$up['ativado'] = 's';
		$update = $sqlActions->update($mdl_tabela, $up);
		unset($up);

		$resetUsers = $conn->query("UPDATE usuarios SET equipe_campanha = 0, pontos_campanha = 0");
		$resetTeam = $conn->query("UPDATE campanha SET eq1_pontos = 0, eq2_pontos = 0");

		$core->logger("O usuário abriu a Campanha.", "acao");
		?>
		<div class="box-content">
			<div class="title-section"><?=$mdl['nome'];?></div>

			<a href="?p=<?=$_GET['p'];?>"><button class="btn btn-primary">Voltar</button></a><br>
			<br>
			<?=aviso_blue("A campanha foi aberta com sucesso e está disponível para os usuários.<br>Todos os usuários estão sem equipe e sem pontos de campanha.");?>
		</div>
		<? } ?>

		<? if($_GET['a'] == 3) {
		$_ex = $conn->prepare("SELECT * FROM $mdl_tabela LIMIT 1");
		$_ex->execute();
		$ex = $_ex->fetch();

		$up['ativado'] = 's';
		$update = $sqlActions->update($mdl_tabela, $up);
		unset($up);

		$core->logger("O usuário abriu a Campanha sem resetar equipe e pontuação de usuários.", "acao");
		?>
		<div class="box-content">
			<div class="title-section"><?=$mdl['nome'];?></div>

			<a href="?p=<?=$_GET['p'];?>"><button class="btn btn-primary">Voltar</button></a><br>
			<br>
			<?=aviso_blue("A campanha foi aberta com sucesso e está disponível para os usuários.<br>Nenhum usuário foi removido de sua equipe e seus pontos não foram alterados.");?>
		</div>
		<? } ?>

		<? if($_GET['a'] == 4) {
		$_ex = $conn->prepare("SELECT * FROM $mdl_tabela LIMIT 1");
		$_ex->execute();
		$ex = $_ex->fetch();

		$up['ativado'] = 'n';
		$update = $sqlActions->update($mdl_tabela, $up);
		unset($up);

		$core->logger("O usuário fechou a Campanha.", "acao");
		?>
		<div class="box-content">
			<div class="title-section"><?=$mdl['nome'];?></div>

			<a href="?p=<?=$_GET['p'];?>"><button class="btn btn-primary">Voltar</button></a><br>
			<br>
			<?=aviso_blue("A campanha foi fechada com sucesso.");?>
		</div>
		<? } ?>

	<?php

	if($_GET['a'] == '') {
		$_ex = $conn->prepare("SELECT * FROM $mdl_tabela LIMIT 1");
		$_ex->execute();
		$ex = $_ex->fetch();

		$_userEq1 = $conn->query("SELECT count(id) FROM usuarios WHERE equipe_campanha = 1");
		$userEq1 = $_userEq1->fetchColumn();

		$_userEq2 = $conn->query("SELECT count(id) FROM usuarios WHERE equipe_campanha = 2");
		$userEq2 = $_userEq2->fetchColumn();
		?>
		<div class="box-content">
			<div class="title-section"><?=$mdl['nome'];?></div>

			<div class="well well-lg">
				<b>Equipe 1</b> (<?= $ex['eq1_nome']; ?>)<br><br>
				Usuários participantes: <b><?= $userEq1; ?></b><br>
				Pontuação: <b><?= $ex['eq1_pontos']; ?></b>
			</div>

			<div class="well well-lg">
				<b>Equipe 2</b> (<?= $ex['eq2_nome']; ?>)<br><br>
				Usuários participantes: <b><?= $userEq2; ?></b><br>
				Pontuação: <b><?= $ex['eq2_pontos']; ?></b>
			</div>

			<a href="?p=<?=$_GET['p'];?>&a=1"><button class="btn btn-primary">Configurações da campanha</button></a>

			<br><br>

			<?php

			echo aviso_red("Ao ABRIR A CAMPANHA, todos os usuários irão ser automaticamente removidos de qualquer equipe em que eles estejam participando e seus pontos de campanha serão resetados. Caso por algum motivo especial você desejar abrir a campanha SEM RESETAR A EQUIPE DOS USUÁRIOS, clique no botão de Abrir campanha sem reset.");

			?>

			<div class="well well-lg">Status da campanha: <b><?= ($ex['ativado'] == 's') ? 'ABERTA' : 'FECHADA'; ?></b></div>

			<a href="?p=<?=$_GET['p'];?>&a=2"><button class="btn btn-danger">Abrir campanha</button></a>
			<a href="?p=<?=$_GET['p'];?>&a=3"><button class="btn btn-warning">Abrir campanha sem reset</button></a>
			<a href="?p=<?=$_GET['p'];?>&a=4"><button class="btn btn-info">Fechar campanha</button></a>
		</div>
		<? } ?>