<?
if($_POST['form'] == 'form') {
	$codigo = $core->clear($_POST['codigo']);
	$ids = $core->clear($_POST['moedas']);
	$usos = $core->clear($_POST['usos']);
	$prosseguir = true;

	if(empty($codigo) || empty($ids) || empty($usos)) {
		$form_return .= aviso_red("Preencha todos os campos.");
		$prosseguir = false;
	}

	$sql6 = $conn->prepare("SELECT count(id) FROM $mdl_tabela WHERE codigo = ?");
	$sql6->bindValue(1, $codigo);
	$sql6->execute();
	$rows = $sql6->fetchColumn();

	if($rows > 0) {
		$form_return .= aviso_red("Este código já existe.");
		$prosseguir = false;
	}

	if($prosseguir) {
		$insert_data['codigo'] = $codigo;
		$insert_data['qtd_moedas'] = $ids;
		$insert_data['qtd_uso'] = $usos;
		$insert_data['autor'] = $autor;
		$insert_data['data'] = $timestamp;

		$insert = $sqlActions->insert($mdl_tabela, $insert_data);

		if($insert) {
			$core->logger("O usuário gerou um código de iDs.", "acao");

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

	$form->mostraAviso('<center><button type="button" class="btn btn-success" onclick="geraCodigo();">Gerar código aleatório</button></center><br>');

	$form->createInput('Código', 'text', 'codigo');
	$form->createInput('Quantidade de moedas', 'text', 'moedas', '', 'w-md', '', 'Apenas números.<br>A quantidade de moedas que este código dará ao usuário quando gerado.');
	$form->createInput('Quantidade de usos', 'text', 'usos', '', 'w-md', '', 'Apenas números.<br>A quantidade de vezes que este código pode ser gerado por usuários diferentes.<br>Um mesmo usuário não pode gerar o mesmo código mais de uma vez');

	$form->generateForm();
	echo $form->form; ?>
</div>

<div class="box-content">
	<div class="title-section">Códigos gerados</div>

	<? $sql = $conn->query("SELECT * FROM $mdl_tabela ORDER BY id DESC");
	while($sql2 = $sql->fetch()) {
		$users_usaram = implode('|', $sql2['usos']);
		$usuarios = explode('|', $users_usaram);
		$usuarios = array_filter($usuarios);
		if(count($users_usaram) == 0) { $usuarios = array($sql2['usos']); } ?>
		<div class="well">
			<b>Código:</b> <?=$core->clear($sql2['codigo']);?><br>
			<b>Quantidade de moedas:</b> <?=$core->clear($sql2['qtd_moedas']);?><br>
			<b>Quantidade de usos:</b> <?=$core->clear($sql2['qtd_uso']);?><br>
			<b>Usuários que usaram:</b> <b><?=(count($usuarios) > 0) ? implode('</b> / <b>', $usuarios) : 'Nenhum usuário gerou o código.';?></b><br><br>
			<b>Autor:</b> <?=$core->clear($sql2['autor']);?><br>
			<b>Data:</b> <?=date('d/m/Y H:i', $core->clear($sql2['data']));?><br>
		</div>
		<? }

		$query = "SELECT * FROM $mdl_tabela ORDER BY id DESC";
		$rows = $core->getRows($query);

		if($rows == 0) {
			echo aviso_red("Nenhum código foi gerado.");
		} ?>
	</div>