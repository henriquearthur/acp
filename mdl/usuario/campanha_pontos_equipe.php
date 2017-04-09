<? if($_GET['a'] == '') {
	if($_POST['form'] == 'form') {
		$eq1_pontos = $core->clear($_POST['eq1_pontos']);
		$eq2_pontos = $core->clear($_POST['eq2_pontos']);
		$prosseguir = true;

		if($prosseguir) {
			$up['eq1_pontos'] = $eq1_pontos;
			$up['eq2_pontos'] = $eq2_pontos;

			$update = $sqlActions->update($mdl_tabela, $up);

			if($update) {
				$core->logger("O usuário editou os pontos de equipe da Campanha", "acao");

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


		$form->createInput('Pontos da Equipe 1', 'text', 'eq1_pontos', $ex['eq1_pontos'], 'w-md', '', 'Apenas números.<br>A quantidade de pontos que a Equipe 1 ('.$ex['eq1_nome'].') possui.');
		$form->createInput('Pontos da Equipe 2', 'text', 'eq2_pontos', $ex['eq2_pontos'], 'w-md', '', 'Apenas números.<br>A quantidade de pontos que a Equipe 2 ('.$ex['eq2_nome'].') possui.');


		$form->generateForm();
		echo $form->form;
		?>
	</div>
	<? } ?>