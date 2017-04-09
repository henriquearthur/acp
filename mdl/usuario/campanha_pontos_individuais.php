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
					$sql = $conn->prepare("SELECT id, pontos_campanha, equipe_campanha FROM usuarios WHERE nick = ? LIMIT 1");
					$sql->bindValue(1, $atual);
					$sql->execute();
					$resUser = $sql->fetch();
					$newPontos = $resUser['pontos_campanha'] + $pontos;

					$ii = 1;
					$prevI = 1;
					for ($i = 10; $i < pow(10, 8); $i = pow(10, $ii)) {
						$ii++;

						if($resUser['pontos_campanha'] < $i && $newPontos >= $i) {
							$teamPoint = $prevI;

							$sql = $conn->query("SELECT eq1_pontos, eq2_pontos, eq1_lastWin, eq2_lastWin FROM campanha");
							$res = $sql->fetch();

							if($resUser['equipe_campanha'] == 1) {
								if($res['eq1_lastWin'] != $teamPoint) {
									$upCampanha['eq1_pontos'] = $res['eq1_pontos'] + $teamPoint;
									$upCampanha['eq1_lastWin'] = $teamPoint;
									$upp = $sqlActions->update("campanha", $upCampanha);
								}
							}

							if($resUser['equipe_campanha'] == 2) {
								if($res['eq2_lastWin'] != $teamPoint) {
									$upCampanha['eq2_pontos'] = $res['eq2_pontos'] + $teamPoint;
									$upCampanha['eq2_lastWin'] = $teamPoint;
									$upp = $sqlActions->update("campanha", $upCampanha);
								}
							}
						}

						$prevI = $i;
					}

					$update = $conn->prepare("UPDATE usuarios SET pontos_campanha = ? WHERE nick = ?");
					$update->bindValue(1, $newPontos);
					$update->bindValue(2, $atual);
					$update->execute();
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