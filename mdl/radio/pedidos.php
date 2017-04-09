<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>
	Estão sendo exibidos somente os pedidos das últimas 24 horas feitos enquanto você estava online na rádio.<br><br>
	<? $tempo = time() - 3600000;
	$sql = $conn->prepare("SELECT * FROM $mdl_tabela WHERE data > $tempo AND locutor = ? ORDER BY id DESC");
	$sql->bindValue(1, $dados['nick']);
	$sql->execute();
	while($sql2 = $sql->fetch()) { ?>
	<div class="well">
		Pedido enviado em <b><?=date('d/m/Y H:i:s', $sql2['data']);?></b> por <b><?=$sql2['nick'];?></b><br><br>
		Mensagem: <b><?=$sql2['msg'];?></b>
	</div>
	<? } ?>
</div>