<? if($_GET['a'] == 1) {
	$_ex = $conn->query("SELECT * FROM $mdl_tabela LIMIT 1");
	$ex = $_ex->fetch();

	$scpass = $ex['radio_senha_kike'];
	$scfp = fsockopen($ex['radio_ip'], $ex['radio_porta'], $errno, $errstr, 30);

	if($scfp) {
		fputs($scfp,"GET /admin.cgi?pass=$scpass&mode=&mode=kicksrc HTTP/1.0\r\nUser-Agent: SHOUTcast Song Status (Mozilla Compatible)\r\n\r\n");

		while(!feof($scfp)) {
			$page .= fgets($scfp, 1000);
		}

		fclose($scfp);
	}
?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>

	O locutor foi kickado com sucesso. O AutoDJ entrará na rádio em alguns segundos.<br><br>
	Entre na rádio!
</div>
<? } ?>

<? if($_GET['a'] == '') {
	$_ex = $conn->query("SELECT * FROM $mdl_tabela LIMIT 1");
	$ex = $_ex->fetch(); ?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>
	<a href="?p=<?=$_GET['p'];?>&a=1"><button class="btn btn-primary">Iniciar locução (kickar locutor)</button></a>
	<br><br>

	Para iniciar sua locução, você deve kickar/expulsar o locutor atual (ou o AutoDJ) clicando no botão abaixo.<br><br>
	<b>IP:</b> <?=$ex['radio_ip'];?><br>
	<b>Porta:</b> <?=$ex['radio_porta'];?><br>
	<b>Senha:</b> <?=$ex['radio_senha'];?><br>
	<b>Tipo de transmissão:</b> <?=$ex['radio_transmissao'];?><br><br>

	Você deve colocar seu nome e o nome de sua programação.
</div>
<? } ?>