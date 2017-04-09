<? if($_GET['a'] == '') {
	if($_POST['form'] == 'form') {
		$nick = $_POST['nick'];
		$moedas = $core->clear($_POST['moedas']);
		$prosseguir = true;

		if(empty($nick) || empty($moedas)) {
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
					$update_data['moedas'] = $sql4['moedas'] + $moedas;

					$where_data['id'] = $sql4['id'];
					$update = $sqlActions->update("usuarios", $update_data, $where_data);

					$sqlActions->insert("usuarios_ices", array('id_usuario' => $sql4['id'], 'qtd_moedas' => $moedas, 'autor' => $autor, 'data' => time()));
				} else {
					$form_return .= aviso_red("As moedas não foram entregues ao usuário $atual pois ele não existe.");
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

		$form->createTextarea('Nick do(s) usuário(s)', 'nick', '', '', '', '', 'Separe os nicks dos usuários que receberão a quantidade de moedas por linha.');
		$form->createInput('Quantidade de moedas', 'text', 'moedas', '', 'w-md', '', 'Apenas números.<br>A quantidade de moedas que os usuários receberão.');

		$form->generateForm();
		echo $form->form; ?>
	</div>
	<? } ?>