
<div class="box-content">
	<div class="title-section">Resultado das indicações (Etapa 1)</div>

	<? $sql = $conn->query("SELECT * FROM awd_categorias ORDER BY id ASC");
	$sql2 = $sql->fetchAll();

	foreach ($sql2 as $atual) { ?>
	<div class="well well-lg">
		<b><?=$atual['nome'];?></b><br><br>
		<? $sql3 = $conn->prepare("SELECT nick, count(nick) AS c FROM awd_indicados WHERE id_cat = ? AND valido = 's' GROUP BY nick ORDER BY c DESC");
		$sql3->bindValue(1, $atual['id']);
		$sql3->execute();
		$sql4 = $sql3->fetchAll();

		foreach ($sql4 as $atual) { ?>
			<b><?=$atual['nick'];?></b> (<?=$atual['c'];?>x)<br>
		<? } ?>
	</div>
	<? } ?>
</div>

<div class="box-content">
	<div class="title-section">Resultado das votações (Etapa 2)</div>

	<? $sql = $conn->query("SELECT * FROM awd_categorias ORDER BY id ASC");
	$sql2 = $sql->fetchAll();

	foreach ($sql2 as $atual) {
		$sql5 = $conn->prepare("SELECT count(id) FROM awd_votos WHERE id_cat = ? AND valido = 's'");
		$sql5->bindValue(1, $atual['id']);
		$sql5->execute();
		$total = $sql5->fetchColumn(); ?>
	<div class="well well-lg">
		<b><?=$atual['nome'];?></b> - <?=$total;?> votos<br><br>
		<? $sql3 = $conn->prepare("SELECT nick, count(nick) AS c FROM awd_votos WHERE id_cat = ? AND valido = 's' GROUP BY nick ORDER BY c DESC");
		$sql3->bindValue(1, $atual['id']);
		$sql3->execute();
		$sql4 = $sql3->fetchAll();

		foreach ($sql4 as $atual) { ?>
			<b><?=$atual['nick'];?></b> (<?=$atual['c'];?>x - <?=(100 * $atual['c']) / $total;?>% dos votos)<br>
		<? } ?>
	</div>
	<? } ?>
</div>

