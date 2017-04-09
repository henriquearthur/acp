<? if($_GET['a'] == 1) {
	if($_POST['form'] == 'form') {
		$preco_jogo = $core->clear($_POST['preco_jogo']);
		$premio = $core->clear($_POST['premio']);
		$data_abertura = $core->clear($_POST['data_abertura']);
		$data_fechamento = $core->clear($_POST['data_fechamento']);
		$data_sorteio = $core->clear($_POST['data_sorteio']);
		$prosseguir = true;

		if(!is_numeric($preco_jogo) || $preco_jogo < 0) {
			$form_return .= aviso_red("Digite um preço/jogo válido.");
			$prosseguir = false;
		}

		if(empty($data_abertura)) {
			$data_abertura = 0;
		} else {
			$a = explode('/', $data_abertura);
			$b = substr($data_abertura, -5);
			$c = explode(':', $b);

			$dia = $a[0];
			$mes = $a[1];
			$ano = substr($a[2], 0, 4);
			$hora = $c[0];
			$minuto = $c[1];

			$data_abertura = mktime($hora, $minuto, 0, $mes, $dia, $ano);
		}

		if(empty($data_fechamento)) {
			$data_fechamento = 0;
		} else {
			$a = explode('/', $data_fechamento);
			$b = substr($data_fechamento, -5);
			$c = explode(':', $b);

			$dia = $a[0];
			$mes = $a[1];
			$ano = substr($a[2], 0, 4);
			$hora = $c[0];
			$minuto = $c[1];

			$data_fechamento = mktime($hora, $minuto, 0, $mes, $dia, $ano);
		}

		if(empty($data_sorteio)) {
			$data_sorteio = 0;
		} else {
			$a = explode('/', $data_sorteio);
			$b = substr($data_sorteio, -5);
			$c = explode(':', $b);

			$dia = $a[0];
			$mes = $a[1];
			$ano = substr($a[2], 0, 4);
			$hora = $c[0];
			$minuto = $c[1];

			$data_sorteio = mktime($hora, $minuto, 0, $mes, $dia, $ano);
		}

		if($prosseguir) {
			$up['preco_jogo'] = $preco_jogo;
			$up['premio'] = $premio;
			$up['data_abertura'] = $data_abertura;
			$up['data_fechamento'] = $data_fechamento;
			$up['data_sorteio'] = $data_sorteio;
			$up['autor'] = $autor;
			$up['data'] = $core->timestamp;

			$update = $sqlActions->update($mdl_tabela, $up);

			if($update) {
				$core->logger("O usuário editou as opções do Colorsorte", "acao");

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

		if($ex['etapa'] == 4) {
			echo "<div class=\"well well-lg\">O Colorsorte está sendo sorteada neste momento. Não é possível fazer edições neste momento, aguarde o término do sorteio.</div>";
		} else {
			$form = new Form('form-submit', '');

			$form->mostraAviso(well('A abertura, fechamento e sorteio do Colorsorte serão realizados automaticamente pelo sistema, na data e hora especificada abaixo.'));

			$form->createInput('Preço/cartela', 'text', 'preco_jogo', $ex['preco_jogo'], 'w-md', '', 'Apenas números.<br>Este é o preço em Pixels que o usuário pagará por cada cartela comprada.');
			$form->createInput('Prêmio', 'text', 'premio', $ex['premio']);

			$form->createInput('Data e hora de abertura', 'text', 'data_abertura', (($ex['data_abertura'] != 0)) ? date('d/m/Y H:i', $ex['data_abertura']) : '', '', '', 'Use o modelo: <pre>DD/MM/AAAA HH:MM</pre> - Ex: <pre>03/07/2015 15:00</pre><br>Esta é a data e hora que o Colorsorte será aberto para novas compras.<br>Deixe em branco para não definido.');
			$form->createInput('Data e hora de fechamento', 'text', 'data_fechamento', (($ex['data_abertura'] != 0)) ? date('d/m/Y H:i', $ex['data_fechamento']) : '', '', '', 'Use o modelo: <pre>DD/MM/AAAA HH:MM</pre> - Ex: <pre>03/07/2015 15:00</pre><br>Esta é a data e hora que o Colorsorte será fechado para novas compras.<br>Deixe em branco para não definido.');
			$form->createInput('Data e hora do sorteio', 'text', 'data_sorteio', (($ex['data_sorteio'] != 0)) ? date('d/m/Y H:i', $ex['data_sorteio']) : '', '', '', 'Use o modelo: <pre>DD/MM/AAAA HH:MM</pre> - Ex: <pre>03/07/2015 15:00</pre><br>Esta é a data e hora que o sorteio do Colorsorte começará.<br>Deixe em branco para não definido.');

			$form->generateForm();
			echo $form->form;
		} ?>
	</div>
	<? } ?>

	<? if($_GET['a'] == 2) {
		$_ex = $conn->prepare("SELECT * FROM $mdl_tabela LIMIT 1");
		$_ex->execute();
		$ex = $_ex->fetch();

		$sql = $conn->query("SELECT tipo, valor FROM colorsorte_sorteio");
		$res = $sql->fetchAll();

		$cor = 0;
		$numero = 0;

		foreach ($res as $atual) {
			if($atual->tipo == 'cor') {
				$cor = $atual->valor;
			}

			if($atual->tipo == 'numero') {
				$numero = $atual->valor;
			}
		}

		if(count($res) == 2) {
			$finished = true;
		}

		$sql = $conn->prepare("SELECT count(id) FROM colorsorte_cartelas WHERE cor = ? AND numero = ?");
		$sql->bindValue(1, $cor);
		$sql->bindValue(2, $numero);
		$sql->execute();
		$rows = $sql->fetchColumn();

		if($rows == 0 && $ex['etapa'] == 1) {
			$up['etapa'] = 3;
			$up['data_sorteio'] = strtotime('+30 seconds');
			$update = $sqlActions->update($mdl_tabela, $up);

			$core->logger("O usuário repetiu um sorteio do Colorsorte.", "acao");
			?>
			<div class="box-content">
				<div class="title-section"><?=$mdl['nome'];?></div>

				<a href="?p=<?=$_GET['p'];?>"><button class="btn btn-primary">Voltar</button></a><br>
				<br>
				<?=aviso_blue("O sorteio do Colorsorte iniciará em aproximadamente 60 segundos. Acompanhe através da <a href='http://icehabbo.com.br/colorsorte/sorteio'>página de sorteio</a>.");?>
			</div>
			<?php } else { ?>
			<?=aviso_blue("Não autorizado.");?>
			<?php } ?>
			<? } ?>


			<?php

			if($_GET['a'] == '') {
				$_ex = $conn->prepare("SELECT * FROM $mdl_tabela LIMIT 1");
				$_ex->execute();
				$ex = $_ex->fetch();

				if($ex['etapa'] == 1) {
					$status = 'Fechado para novas compras.';
				} else if($ex['etapa'] == 2) {
					$status = 'Aberto para novas compras.';
				} else if($ex['etapa'] == 3) {
					$status = 'Aguardando o início do sorteio.';
				} else if($ex['etapa'] == 4) {
					$status = 'Em sorteio. Não é possível fazer edições enquanto o Colorsorte está em sorteio, aguarde o término do sorteio. Ao término do sorteio, o status do Colosorte será alterado para Fechado.';
				}

				$_qtdJogos = $conn->query("SELECT count(id) FROM colorsorte_cartelas WHERE id_usuario != 0");
				$qtdJogos = $_qtdJogos->fetchColumn();
				?>
				<div class="box-content">
					<div class="title-section"><?=$mdl['nome'];?></div>

					<div class="well well-lg">Status do Colorsorte: <b><?=$status?></b></div>
					<div class="well well-lg">Cartelas compradas: <b><?=$qtdJogos;?></b></div>

					<?php if($ex['etapa'] != 4) { ?>
					<a href="?p=<?=$_GET['p'];?>&a=1"><button class="btn btn-primary">Definir datas e preços</button></a>
					<?php } ?>

					<?php

					$sql = $conn->query("SELECT tipo, valor FROM colorsorte_sorteio");
					$res = $sql->fetchAll();

					$cor = 0;
					$numero = 0;

					foreach ($res as $atual) {
						if($atual->tipo == 'cor') {
							$cor = $atual->valor;
						}

						if($atual->tipo == 'numero') {
							$numero = $atual->valor;
						}
					}

					if(count($res) == 2) {
						$finished = true;
					}

					$sql = $conn->prepare("SELECT count(id) FROM colorsorte_cartelas WHERE cor = ? AND numero = ?");
					$sql->bindValue(1, $cor);
					$sql->bindValue(2, $numero);
					$sql->execute();
					$rows = $sql->fetchColumn();

					if($rows == 0 && $ex['etapa'] == 1) { ?>
					<br><br>
					<div class="well well-lg">Não houve ganhador no último sorteio. Deseja realizar o sorteio novamente?</div>
					<a href="?p=<?=$_GET['p'];?>&a=2"><button class="btn btn-info">Repetir sorteio agora</button></a>
					<?php } ?>
				</div>
				<? } ?>