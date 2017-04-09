<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>
	
	<? $sql = $conn->query("SELECT * FROM usuarios WHERE banido = 's' ORDER BY id DESC");
	while($sql2 = $sql->fetch()) { ?>
		<div class="well well-sm">
			Nick: <b><?=$sql2['nick'];?></b><br>
			Motivo do banimento: <b><?=$sql2['ban_motivo'];?></b><br>
			Banido até: <b><?=date('d/m/Y H:i', $sql2['ban_termino']);?></b><br>
			Quem baniu: <b><?=$sql2['ban_autor'];?></b>
		</div>
	<? } 

	$query = "SELECT * FROM usuarios WHERE banido = 's' ORDER BY id DESC";
	$rows = $core->getRows($query);

	if($rows == 0) {
		echo aviso_red("Nenhum usuário banido.");
	} ?>
</div>