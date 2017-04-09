<? if($_GET['a'] == 1) {
	$id = $_GET['id'];

	$up['status'] = 'ativado';
	$wh['id'] = $id;
	$update = $sqlActions->update("videos", $up, $wh);

	$sql = $conn->prepare("SELECT autor FROM videos WHERE id = ?");
	$sql->bindValue(1, $id);
	$sql->execute();
	$nick = $sql->fetchColumn();

	$core->logger("O usuário aprovou o vídeo [#$id]", "acao");
} ?>

<? if($_GET['a'] == 2) {
	$id = $_GET['id'];

	$up['status'] = 'reprovado';
	$wh['id'] = $id;
	$update = $sqlActions->update("videos", $up, $wh);

	$core->logger("O usuário reprovou o vídeo [#$id]", "acao");
} ?>

<? if($_GET['a'] == '') { ?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>

	<? $query = "SELECT * FROM videos WHERE status = 'aguardando' ORDER BY id DESC";
	$sql = $conn->query($query);
	while($sql2 = $sql->fetch()) {
		parse_str( parse_url( $sql2['link'], PHP_URL_QUERY ), $youtube);
		$codigo = $youtube['v'];?>
	<div class="thumbnail">
		<a href="http://<?=$sql2['link'];?>" target="_blank"><div id="img" style="background-image:url(http://img.youtube.com/vi/<?=$codigo;?>/default.jpg);"></div></a>
		<div id="infos">
			<button class="btn btn-primary" onclick="pxAceitar(this)" rel="?p=<?=$_GET['p'];?>&a=1&id=<?=$sql2['id'];?>">Aceitar</button>
			<button class="btn btn-danger" onclick="pxRecusar(this);" rel="?p=<?=$_GET['p'];?>&a=2&id=<?=$sql2['id'];?>">Recusar</button>
		</div>
	</div>
	<? }
	if($core->getRows($query) == 0) {
		echo '<center>Nenhum vídeo está aguardando revisão.</center>';
	} ?>
</div>
<? } ?>