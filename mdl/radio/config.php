<?
if($_POST['form'] == 'form') {
	$p_ip = $_POST['ip'];
	$p_porta = $_POST['porta'];
	$p_senha = $_POST['senha_kike'];
	$senha_radio = $_POST['senha_radio'];
	$tipo_transmissao = $_POST['tipo_transmissao'];
	$prosseguir = true;


	if($prosseguir) {
		$update_data['radio_ip'] = $p_ip;
		$update_data['radio_porta'] = $p_porta;
		$update_data['radio_senha_kike'] = $p_senha;
		$update_data['radio_senha'] = $senha_radio;
		$update_data['radio_transmissao'] = $tipo_transmissao;
		$update_data['autor'] = $autor;
		$update_data['data'] = $timestamp;

		$update = $sqlActions->update($mdl_tabela, $update_data);

		if($update) {
			$core->logger("O usuário alterou as configurações da rádio.", "acao");

			$form_return .= aviso_green("Sucesso!");
			foreach($_POST as $nome_campo => $valor){ $_POST[$nome_campo] = '';}
		} else {
			$form_return .= aviso_red("Ocorreu um erro ao executar a ação. Código de erro: {$sqlActions->error}");
		}
	}
}

$_ex = $conn->prepare("SELECT * FROM $mdl_tabela LIMIT 1");
$_ex->bindValue(1, $id);
$_ex->execute();
$ex = $_ex->fetch();

if(!$ex) {
	$script_js .= register404();
}
?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>
	<? echo $form_return;

	$form = new Form('form-submit', '');

	$form->createInput('IP do streaming', 'text', 'ip', $ex['radio_ip'], '', '', 'Não utilize <pre>http://</pre>');
	$form->createInput('Porta do streaming', 'text', 'porta', $ex['radio_porta']);
	$form->createInput('Senha de kike', 'text', 'senha_kike', $ex['radio_senha_kike'], '', '', 'Essa é a senha utilizada pelo sistema para conectar ao streaming.');
	$form->createInput('Senha de rádio', 'text', 'senha_radio', $ex['radio_senha'], '', '', 'Essa é a senha utilizada pelo locutor para entrar na rádio.');
	$form->createInput('Tipo de transmissão', 'text', 'tipo_transmissao', $ex['radio_transmissao']);

	$form->generateForm();
	echo $form->form; ?>
</div>
