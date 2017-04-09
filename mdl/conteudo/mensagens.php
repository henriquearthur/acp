<? if($_GET['a'] == 339955) {
	$id = $_GET['id'];

	$ids = explode(',', $id);
	$ids = array_filter($ids);

	if(count($ids) > 0) {
		$delete = $conn->prepare("DELETE FROM $mdl_tabela WHERE id = ? LIMIT 1");
		$delete->bindParam(1, $id_atual);

		foreach ($ids as $id_atual) {
			$delete->execute();

			$core->logger("O usuário deletou o registro [#$id_atual - $mdl_tabela]", "acao");
		}
	} else {
		$delete_where['id'] = $id;
		$delete = $sqlActions->delete($mdl_tabela, $delete_where);

		$core->logger("O usuário deletou o registro [#$id_atual - $mdl_tabela]", "acao");
	}
} ?>

<? if($_GET['a'] == 3) {
	$id = $_GET['id'];

	$delete_where['id'] = $id;
	$delete = $conn->prepare("UPDATE $mdl_tabela SET status = 'inativo' WHERE id = ? LIMIT 1");
		$delete->bindValue(1, $id);
		$delete->execute();

	$core->logger("O usuário deletou a mensagem [#$id]", "acao");
} ?>

<? if($_GET['a'] != 3) {
	if($_GET['a'] == 2) {
		$id = $_GET['id'];

		$update_data['status'] = 'aprovada';
		$where_data['id'] = $id;

		$update = $sqlActions->update($mdl_tabela, $update_data, $where_data);

		$core->logger("O usuário aprovou a mensagem [#$id]", "acao");

		$sql = $conn->prepare("SELECT nick, status FROM mensagens WHERE id = ?");
		$sql->bindValue(1, $id);
		$sql->execute();
		$res = $sql->fetch();

		if($res['status'] != 'aprovada') {
			$core->sendPoints($res['nick'], 10);
		}

		echo aviso_green("Mensagem aprovada com sucesso!");
	}
	?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>

	<? $sql = $conn->query("SELECT * FROM $mdl_tabela WHERE status = 'aguardando' ORDER BY id DESC");
	while($sql2 = $sql->fetch()) { ?>
	<div class="well">
		Mensagem enviada em <b><?=date('d/m/Y H:i:s', $sql2['data']);?></b> - IP: <b><?=$sql2['ip'];?></b><br><br>
		Nick: <b><?=$sql2['nick'];?></b><br>
		Mensagem: <b><?=$sql2['mensagem'];?></b><br><br>

		<? if($sql2['status'] == 'aguardando') { ?><a href="?p=<?=$p;?>&a=2&id=<?=$sql2['id'];?>"><button class="btn btn-primary">Aprovar</button></a><? } ?>
		<button class="btn btn-danger" onclick="deletar(this, 0);" rel="?p=<?=$p;?>&a=3&id=<?=$sql2['id'];?>">Inativar</button>
	</div>
	<? } ?>
</div>
<? } ?>