<? if($_GET['a'] == '') {
	if($_POST['form'] == 'form') {
		$nick = $_POST['nick'];
		$pontos = $core->clear($_POST['pontos']);
		$prosseguir = true;

		if(empty($nick) || empty($pontos)) {
			$form_return .= aviso_red("Preencha todos os campos.");
			$prosseguir = false;
		}

		if($prosseguir) {
			$nicks = explode("\n", $nick);
			$nicks = array_filter($nicks);

			foreach ($nicks as $atual) {
				$atual = trim(preg_replace('/\s\s+/', ' ', $atual));
				$sql3 = $conn->prepare("SELECT count(id) FROM usuarios WHERE nick = ?");
				$sql3->bindParam(1, $atual);
				$sql3->execute();
				$sql4 = $sql3->fetchColumn();

				if($sql4 == 0) {
					$form_return .= aviso_red("Os pontos não foram entregues ao usuário $atual pois ele não existe.");
				} else {
					$core->sendPoints($atual, $pontos);
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

		$form->createTextarea('Nick do(s) usuário(s)', 'nick', '', '', '', '', 'Separe os nicks dos usuários que receberão a quantidade de pontos por linha.');
		$form->createInput('Quantidade de pontos', 'text', 'pontos', '', 'w-md', '', 'Apenas números.<br>A quantidade de pontos que os usuários receberão.');

		$form->generateForm();
		echo $form->form; ?>
	</div>
	<? } ?>