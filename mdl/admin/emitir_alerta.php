<? 
if($_POST['form'] == 'form') {
	$alerta = $_POST['alerta'];
	$prosseguir = true;

	if(empty($alerta)) {
		$form_return .= aviso_red("Digite um alerta.");
		$prosseguir = false;
	}

	if($prosseguir) {
		$insert_data['conteudo'] = $alerta;
		$insert_data['autor'] = $autor;
		$insert_data['data'] = $timestamp;

		$insert = $sqlActions->insert($mdl_tabela, $insert_data);

		if($insert) {
			$core->logger("O usuário emitiu um alerta no site.", "acao");

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

	$form->mostraAviso(well("O alerta enviado será mostrado para todos no site online em até 45 segundos e para todos que entrarem no site pelos próximos 5 minutos."));
	$form->createTextarea('Alerta', 'alerta', '', 'ckeditor', 'ckeditor');

	$form->generateForm();
	echo $form->form; ?>
</div>

<div class="box-content">
	<div class="title-section">Alertas emitidos</div>
	
	<? $sql = $conn->query("SELECT * FROM alertas ORDER BY id DESC");
	while($sql2 = $sql->fetch()) {
		$query = "SELECT * FROM alertas_lidos WHERE id_alerta='".$sql2['id']."'";
		$rows = $core->getRows($query); ?>
		<div class="well">
			<?=$sql2['conteudo'];?>
			<br>
			Enviado por <b><?=$sql2['autor'];?></b> em <b><?=date('d/m/Y H:i:s', $sql2['data']);?></b>.<br>
			Lido por <b><?=$rows;?></b> usuários.
		</div>
	<? } 

	$query = "SELECT * FROM alertas ORDER BY id DESC";
	$rows = $core->getRows($query);

	if($rows == 0) {
		echo aviso_red("Nenhum alerta foi emitido");
	} ?>
</div>