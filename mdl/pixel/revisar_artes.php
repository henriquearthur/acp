<? if($_GET['a'] == 1) {
	$id = $_GET['id'];

	$up['status'] = 'ativado';
	$wh['id'] = $id;
	$update = $sqlActions->update("pixel_artes", $up, $wh);

	$sys->calculatePixelPoint($id);
	$core->logger("O usuário aprovou a obra [#$id]", "acao");

	$data_obra = $conn->query("SELECT * FROM pixel_artes WHERE id = " . $id);
	$fetch = $data_obra->fetch();
	$link = sprintf('/pixelart/%u-%s', $fetch['id'], $core->trataURL($fetch['titulo']));

	if($fetch['status'] != 'ativado') {
		$core->sendPoints($fetch['autor'], 25);
	}

	$data_alert['conteudo'] = 'Um novo pixelart foi postado. <a href="'.$link.'">Clique aqui para acessa-lo.</a>';
	$data_alert['autor'] = 'FSIceHabbo';
	$data_alert['data'] = time();
	$insertAlert = $sqlActions->insert('alertas', $data_alert);
} ?>

<? if($_GET['a'] == 2) {
	$id = $_GET['id'];

	$up['status'] = 'reprovado';
	$wh['id'] = $id;
	$update = $sqlActions->update("pixel_artes", $up, $wh);

	$core->logger("O usuário reprovou a obra [#$id]", "acao");
} ?>

<? if($_GET['a'] == '') { ?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>

	<? $query = "SELECT * FROM pixel_artes WHERE status = 'aguardando' ORDER BY id DESC";
	$sql = $conn->query($query);
	while($sql2 = $sql->fetch()) { ?>
	<div class="thumbnail">
		<a href="<?=$sql2['imagem'];?>" target="_blank"><div id="img" style="background-image:url(<?=$sql2['imagem'];?>);"></div></a>
		<div id="infos">
			<button class="btn btn-primary" onclick="pxAceitar(this)" rel="?p=<?=$_GET['p'];?>&a=1&id=<?=$sql2['id'];?>">Aceitar</button>
			<button class="btn btn-danger" onclick="pxRecusar(this);" rel="?p=<?=$_GET['p'];?>&a=2&id=<?=$sql2['id'];?>">Recusar</button>
		</div>
	</div>
	<? }
	if($core->getRows($query) == 0) {
		echo '<center>Nenhuma obra está aguardando revisão.</center>';
	} ?>
</div>
<? } ?>