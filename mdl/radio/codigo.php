<?
if($_GET['a'] == 1) {
	$autorizado = false;

	if($permissoes[100] == 's' || $core->allAccess()) {
		$autorizado = true;
		$locutor = $dados['nick'];
	}

	if(!$autorizado) {
		$sql = $conn->query("SELECT radio_ip, radio_porta FROM config LIMIT 1");
		$sql2 = $sql->fetch();

		$page = 'http://' . $sql2['radio_ip'] . ':' . $sql2['radio_porta'];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $page);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds
		$return = curl_exec($ch);
		curl_close($ch);

		$title = @explode('Stream Title: </font></td><td><font class=default><b>', $return);
		$title = @explode('</b>', $title[1]);
		$title = $title[0];

		$locutor = (mb_check_encoding($title, 'UTF-8')) ? utf8_decode($title) : $title;

		if($locutor == $dados['nick']) {
			$autorizado = true;
		}
	}

	$sql4 = $conn->prepare("SELECT data FROM radio_presenca WHERE locutor = ? ORDER BY id DESC LIMIT 1");
	$sql4->bindValue(1, $core->autor);
	$sql4->execute();
	$sql5 = $sql4->fetch();

	if($sql5['data'] > time() - 1800) {
		$autorizado = false;

		echo aviso_red('Aguarde 30 minutos para gerar outro código de presença.');
	}

	function random_str()
	{
		$chars = "abcdefghijkmnopqrstuvwxyz023456789";
		srand((double)microtime()*1000000);
		$i = 0;
		$pass = '' ;

		while ($i <= 7) {
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}

		return $pass;
	}

	$codigo = random_str();

	if($autorizado) {
		$insert_data['codigo'] = $codigo;
		$insert_data['locutor'] = $locutor;
		$insert_data['valido'] = strtotime('+1 hour');
		$insert_data['data'] = $timestamp;

		$insert = $sqlActions->insert($mdl_tabela, $insert_data);

		if($insert) {
			echo aviso_green("Um código de presença válido por 1 hora foi gerado.<br>Código: <b>$codigo</b>");
			$core->logger("O usuário gerou um novo código de presença. [$codigo]", "acao");

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

	Ao gerar um código de presença, ele será válido por 1 hora a partir do momento que for gerado.<br>

	<? if($permissoes[100] == 's' || $core->allAccess()) {
		echo 'Se deseja gerar um código de presença, clique no botão abaixo.<br><br>';
	} else {
		echo 'Se deseja gerar um código de presença, clique no botão abaixo. O código será gerado apenas se você for o locutor online na rádio.<br><br>';
	} ?>

	<a href="?p=<?=$_GET['p'];?>&a=1"><button class="btn btn-primary">Gerar código</button></a>

	<? $sql4 = $conn->prepare("SELECT codigo, valido FROM radio_presenca WHERE locutor = ? ORDER BY id DESC LIMIT 1");
	$sql4->bindValue(1, $core->autor);
	$sql4->execute();
	$sql5 = $sql4->fetch();

	if($sql5 && time() + 3600 > $sql5['valido']) {
		echo '<br><br>';
		echo aviso_green('O último código de presença que você gerou ainda é válido.<br>Código: <b>'.$sql5['codigo'].'</b>');
	} ?>
</div>