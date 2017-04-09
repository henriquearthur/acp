<? 
if($_POST['form'] == 'form') {
	$aviso = $_POST['conteudo'];
	$prosseguir = true;
	
	if($prosseguir) {
		$update_data['acp_aviso_fixo'] = $aviso;

		$update = $sqlActions->update($mdl_tabela, $update_data);
		
		if($update) {
			$core->logger("O usuário editou o aviso fixo do painel.", "acao");

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
	
	$form->createTextarea('Aviso fixo', 'conteudo', $ex['acp_aviso_fixo'], 'ckeditor','ckeditor');

	$form->generateForm();
	echo $form->form; ?>
</div>
