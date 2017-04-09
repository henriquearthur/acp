<? if($_POST['form'] == 'form') {
	//$alerta = $_POST['alerta'];
	//$aviso_bottom = $_POST['aviso_bottom'];
	//$link = $core->clear($_POST['link']);
	$moedas = $core->clear($_POST['moedas']);
	//$vip = $core->clear($_POST['vip']);
	//$moedas_alt = $core->clear($_POST['moedas_alt']);
	//$moedas_alt_nome = $core->clear($_POST['moedas_alt_nome']);
	$prosseguir = true;

	$social_facebook = $_POST['social_facebook'];
	$social_twitter = $_POST['social_twitter'];
	$social_youtube = $_POST['social_youtube'];
	$social_rss = $_POST['social_rss'];
	$regras_timeline = $_POST['regras_timeline'];

	//(($moedas_alt == 'on')) ? $moedas_alt = 's' : $moedas_alt = 'n';

	if($prosseguir) {
		//$update_data['alerta'] = $alerta;
		//$update_data['aviso_bottom'] = $aviso_bottom;
		//$update_data['link_idetiqueta'] = $link;
		$update_data['moedas_inicial'] = $moedas;
		$update_data['social_facebook'] = $social_facebook;
		$update_data['social_twitter'] = $social_twitter;
		$update_data['social_youtube'] = $social_youtube;
		$update_data['social_rss'] = $social_rss;
		$update_data['regras_timeline'] = $regras_timeline;
		$update_data['autor'] = $autor;
		$update_data['data'] = $timestamp;

		$update = $sqlActions->update($mdl_tabela, $update_data);

		if($update) {
			$core->logger("O usuário alterou as configurações do site.", "acao");

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

	$form->mostraAviso(well("A última alteração dessas configurações foi feita por <b>".$ex['autor']."</b> em <b>".date('d/m/Y H:i', $ex['data'])."</b>."));
	$form->createInput('Moedas iniciais', 'text', 'moedas', $ex['moedas_inicial'], 'w-md', '', 'A quantidade de moedas que cada usuário possui ao se registrar.<br>Somente números.');

	$form->createInput('Link do Facebook (rodapé)', 'text', 'social_facebook', $ex['social_facebook']);
	$form->createInput('Link do Twitter (rodapé)', 'text', 'social_twitter', $ex['social_twitter']);
	$form->createInput('Link do YouTube (rodapé)', 'text', 'social_youtube', $ex['social_youtube']);
	$form->createInput('Link do Feed RSS (rodapé)', 'text', 'social_rss', $ex['social_rss']);

	$form->createTextarea('Regras da Timeline', 'regras_timeline', $ex['regras_timeline']);

	$form->generateForm();
	echo $form->form; ?>
</div>
