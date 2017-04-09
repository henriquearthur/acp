<?
if($_POST['form'] == 'form') {
	$senha = $_senha = $core->clear($_POST['senha']);
	$facebook = $core->clear($_POST['facebook']);
	$instagram  = $core->clear($_POST['instagram']);
	$twitter = $core->clear($_POST['twitter']);
	$skype = $core->clear($_POST['skype']);
	$link_quarto = $core->clear($_POST['link_quarto']);
	$programa = $core->clear($_POST['programa']);
	$prosseguir = true;

	if(strstr($facebook, "http://") || strstr($facebook, "www.") || strstr($facebook, "facebook.com") || strstr($twitter, "http://") || strstr($twitter, "www.") || strstr($twitter, "twitter.com") || strstr($instagram, "http://") || strstr($instagram, "www.") || strstr($instagram, "instagram.com")) {
		$form_return .= aviso_red("Não envie links nos campos Facebook, Instagram ou Twitter. Envie apenas seu nome de usuário.");
		$prosseguir = false;
	}

	if(!empty($_senha) && strlen($_senha) <= 6 || $_senha == '1234567' || $_senha == '12345678' || $_senha == '123456789' || $_senha == $dados['nick'] || $_senha == 'icehabbo') {
		$form_return .= aviso_red("Sua senha é muito fraca. Por favor, utilize outra.");
		$prosseguir = false;
	}

	if($prosseguir && !empty($_senha)) {
		//$senha_real = Bcrypt::hash($_senha);

		$update_data['senha'] = md5($senha);

		$where_data['nick'] = $dados['nick'];
		$update = $sqlActions->update($mdl_tabela, $update_data, $where_data);

		if($update) {
			$core->logger("O usuário editou sua senha.", "acao");

			$form_return .= aviso_yellow("Senha editada com sucesso. Você precisará relogar no painel de gerenciamento.");
		} else {
			$form_return .= aviso_red("Ocorreu um erro ao executar a ação. Código de erro: {$sqlActions->error}");
		}
	}

	if($prosseguir) {
		if($facebook != '') { $facebook = 'http://www.facebook.com/' . $facebook; } else {$facebook='';}
		if($instagram != '') { $instagram = 'http://www.instagram.com/' . $instagram; } else {$instagram='';}
		if($twitter != '') { $twitter = 'http://www.twitter.com/' . $twitter; } else {$twitter='';}

		$update_data['facebook'] = $facebook;
		$update_data['instagram'] = $instagram;
		$update_data['twitter'] = $twitter;
		$update_data['skype'] = $skype;
		$update_data['link_quarto'] = $link_quarto;
		$update_data['programa'] = $programa;

		$where_data['nick'] = $dados['nick'];
		$update = $sqlActions->update($mdl_tabela, $update_data, $where_data);

		if($update) {
			$core->logger("O usuário editou suas informações.", "acao");

			$form_return .= aviso_green("Sucesso!");
			foreach($_POST as $nome_campo => $valor){ $_POST[$nome_campo] = '';}
		} else {
			$form_return .= aviso_red("Ocorreu um erro ao executar a ação. Código de erro: {$sqlActions->error}");
		}
	}
}

$_ex = $conn->prepare("SELECT * FROM $mdl_tabela WHERE nick = ? LIMIT 1");
$_ex->bindValue(1, $autor);
$_ex->execute();
$dados = $_ex->fetch();
?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>

	<? echo $form_return;
	$form = new Form('form-submit', '');

	$form->createInput('Nick', 'text', 'nick', $dados['nick'], '', true);
	$form->createInput('Nova senha', 'password', 'senha', '', '','', 'Digite caso queira trocar a atual.');
	$form->createInput('Facebook', 'text', 'facebook', (($dados['facebook']!=''))?substr($dados['facebook'], 24):'', '', '', 'Digite apenas o nome de usuário (ex: FNXHenry)');
	$form->createInput('Instagram', 'text', 'instagram', (($dados['instagram']!=''))?substr($dados['instagram'], 25):'', '', '', 'Digite apenas o nome de usuário (ex: FNXHenry)');
	$form->createInput('Twitter', 'text', 'twitter', (($dados['twitter']!=''))?substr($dados['twitter'], 23):'', '', '', 'Digite apenas o nome de usuário (ex: FNXHenry)');
	$form->createInput('Skype', 'text', 'skype', $dados['skype']);
	$form->createInput('Link do quarto', 'text', 'link_quarto', $dados['link_quarto']);
	$form->createInput('Nome do programa', 'text', 'programa', $dados['programa']);

	$form->generateForm();
	echo $form->form; ?>

	<? $a = explode('|', $dados['cargos_e']);
	$cargos_e = '';
	$i = 0;
	foreach($a as $atual) {
		$i++;

		if($a[$i] == '') {
			$cargos_e .= $atual . '.';
		} else {
			$cargos_e .= $atual . ', ';
		}
	}

	$cargos_e = str_replace('.', '', $cargos_e); ?>

	<br>

	<div class="well">
		Você possui <b><?=$dados['advert'];?></b> advertências.<br>
		Último login ao painel de controle: <b><?=date('d/m/y H:i:s', $_SESSION['acp_acesso_data']);?></b>.
	</div>
</div>
