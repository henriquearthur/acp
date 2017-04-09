<?
	if($_POST['form'] == 'form') {
		$comando = $_POST['comando'];
		$prosseguir = true;

		if(empty($comando)) {
			$form_return .= aviso_red("Digite um comando.");
			$prosseguir = false;
		}

		if($prosseguir) {
			$command = $conn->query($comando);

			$insert_data['comando'] = $comando;
			$insert_data['autor'] = $autor;
			$insert_data['ip'] = $ip;
			$insert_data['data'] = $timestamp;

			$insert = $sqlActions->insert($mdl_tabela, $insert_data);

			$core->logger("O usuário executou comandos no Console MySQL.", "acao");

			$form_return .= aviso_green("Sucesso!");
		}
	}
?>
<link href='//fonts.googleapis.com/css?family=Source+Code+Pro' rel='stylesheet' type='text/css'>

<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>
	<? echo $form_return;

	$form = new Form('form-submit form-horizontal', '');

	$form->mostraAviso(well('O PDO vai dar Fatal Error se não for uma query válida.'));
	$form->createTextarea('Comando', 'comando', '', 'console');

	$form->generateForm();
	echo $form->form; ?>
</div>