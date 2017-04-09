<?
if($_POST['form'] == 'form') {
	$d1_nick = $core->clear($_POST['d1nick']);
	$d1_motivo = $core->clear($_POST['d1motivo']);
	$d1_facebook = $core->clear($_POST['d1facebook']);
	$d1_twitter = $core->clear($_POST['d1twitter']);
	$d2_nick = $core->clear($_POST['d2nick']);
	$d2_motivo = $core->clear($_POST['d2motivo']);
	$d2_facebook = $core->clear($_POST['d2facebook']);
	$d2_twitter = $core->clear($_POST['d2twitter']);
	$id_arte = $core->clear($_POST['arte']);
	$prosseguir = true;

	if(strstr($d1_facebook, "http://") || strstr($d1_facebook, "www.") || strstr($d1_facebook, "facebook.com") || strstr($d1_twitter, "http://") || strstr($d1_twitter, "www.") || strstr($d1_twitter, "twitter.com")) {
		$form_return .= aviso_red("Não envie links nos campos Facebook ou Twitter. Envie apenas seu nome de usuário.");
		$prosseguir = false;
	}

	if(strstr($d2_facebook, "http://") || strstr($d2_facebook, "www.") || strstr($d2_facebook, "facebook.com") || strstr($d1_twitter, "http://") || strstr($d1_twitter, "www.") || strstr($d1_twitter, "twitter.com")) {
		$form_return .= aviso_red("Não envie links nos campos Facebook ou Twitter. Envie apenas seu nome de usuário.");
		$prosseguir = false;
	}

	if($d1_facebook != '') { $d1_facebook = 'http://www.facebook.com/' . $d1_facebook; } else {$d1_facebook='';}
	if($d1_twitter != '') { $d1_twitter = 'http://www.twitter.com/' . $d1_twitter; } else {$d1_twitter='';}
	if($d2_facebook != '') { $d2_facebook = 'http://www.facebook.com/' . $d2_facebook; } else {$d2_facebook='';}
	if($d2_twitter != '') { $d2_twitter = 'http://www.twitter.com/' . $d2_twitter; } else {$d2_twitter='';}

	if($prosseguir) {
		$update_data['d1_nick'] = $d1_nick;
		$update_data['d1_motivo'] = $d1_motivo;
		$update_data['d1_facebook'] = $d1_facebook;
		$update_data['d1_twitter'] = $d1_twitter;
		$update_data['d2_nick'] = $d2_nick;
		$update_data['d2_motivo'] = $d2_motivo;
		$update_data['d2_facebook'] = $d2_facebook;
		$update_data['d2_twitter'] = $d2_twitter;
		$update_data['id_arte'] = $id_arte;

		$update_data['autor'] = $autor;
		$update_data['data'] = $timestamp;

		$update = $sqlActions->update($mdl_tabela, $update_data);

		if($update) {
			$core->logger("O usuário alterou os usuários destaque.", "acao");

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

	$form->mostraAviso(well("A última alteração dos usuários destaques foi feita por <b>".$ex['autor']."</b> em <b>".date('d/m/Y H:i', $ex['data'])."</b>."));

	$form->mostraTitulo('Usuário destaque');
	$form->createInput('Nick', 'text', 'd1nick', $ex['d1_nick']);
	$form->createTextarea('Motivo', 'd1motivo', $ex['d1_motivo']);
	$form->createInput('Facebook', 'text', 'd1facebook', (($ex['d1_facebook']!=''))?substr($ex['d1_facebook'], 24):'', '', '', 'Digite apenas o nome de usuário (ex: FNXHenry)');
	$form->createInput('Twitter', 'text', 'd1twitter', (($ex['d1_twitter']!=''))?substr($ex['d1_twitter'], 23):'', '', '', 'Digite apenas o nome de usuário (ex: FNXHenry)');

	$form->mostraTitulo('Membro da equipe destaque');
	$form->createInput('Nick', 'text', 'd2nick', $ex['d2_nick']);
	$form->createTextarea('Motivo', 'd2motivo', $ex['d2_motivo']);
	$form->createInput('Facebook', 'text', 'd2facebook', (($ex['d2_facebook']!=''))?substr($ex['d2_facebook'], 24):'', '', '', 'Digite apenas o nome de usuário (ex: FNXHenry)');
	$form->createInput('Twitter', 'text', 'd2twitter', (($ex['d2_twitter']!=''))?substr($ex['d2_twitter'], 23):'', '', '', 'Digite apenas o nome de usuário (ex: FNXHenry)');

	$form->mostraTitulo('Outros');

	$categorias = array();
	$sql = $conn->query("SELECT * FROM pixel_artes WHERE status = 'ativado' ORDER BY id ASC");
	while($sql2 = $sql->fetch()) {
		$atual = array("label" => $core->clear($sql2['titulo']), "value" => $sql2['id']);
		$categorias[] = $atual;
	}

	$form->createSelect('Arte destaque', 'arte', $categorias, $ex['id_arte']);

	$form->generateForm();
	echo $form->form; ?>
</div>
