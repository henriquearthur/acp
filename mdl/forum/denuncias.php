<? if($_GET['a'] != 3) {
	if(isset($_POST['marcar'])) {
		$id = $_POST['marcar'];

		$update_data['resolvido'] = 's';
		$where_data['id'] = $id;

		$_ex = $conn->prepare("SELECT * FROM $mdl_tabela WHERE id = ? LIMIT 1");
		$_ex->bindValue(1, $id);
		$_ex->execute();
		$ex = $_ex->fetch();

		$sql3 = $conn->prepare("SELECT * FROM forum_posts WHERE id = ?");
		$sql3->bindValue(1, $ex['id_msg']);
		$sql3->execute();
		$sql4 = $sql3->fetch();
		$sql5 = $conn->prepare("SELECT * FROM forum_topicos WHERE id = ?");
		$sql5->bindValue(1, $sql4['id_topico']);
		$sql5->execute();
		$sql6 = $sql5->fetch();

		$update = $sqlActions->update($mdl_tabela, $update_data, $where_data);

		if(isset($_POST['descartar'])) {
			$core->logger("O usuário descartou a denúncia [#$id]", "acao");
			echo aviso_green("Denúncia descartada com sucesso!");
		} else {
			$core->logger("O usuário resolveu a denúncia [#$id]", "acao");
			$core->sendNtfUser($ex['autor'], "Sua denúncia do post de [home]{$sql4['autor']}[/home] foi revisada pela equipe.", "/forum/topicos/{$sql6['id']}/{$core->trataurl($sql6['titulo'])}/#post-{$ex['id_msg']}", "denuncia-reply");
			echo aviso_green("Denúncia resolvida com sucesso!");
		}
	}
	?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>

	<div class="well">Lembre-se que os usuários podem denunciar apenas os posts de um tópico.</div>

	<? $sql = $conn->query("SELECT * FROM $mdl_tabela WHERE resolvido = 'n' ORDER BY id DESC");
	$rows = 0;
	while($sql2 = $sql->fetch()) {
		$rows++;
		$sql3 = $conn->prepare("SELECT * FROM forum_posts WHERE id = ?");
		$sql3->bindValue(1, $sql2['id_msg']);
		$sql3->execute();
		$sql4 = $sql3->fetch();
		$sql5 = $conn->prepare("SELECT * FROM forum_topicos WHERE id = ?");
		$sql5->bindValue(1, $sql4['id_topico']);
		$sql5->execute();
		$sql6 = $sql5->fetch();
		?>
	<div class="well">
		Denúncia feita por <b><?=$sql2['autor'];?></b> em <b><?=date('d/m/Y H:i:s', $sql2['data']);?></b><br><br>
		O usuário denunciou o post <b><a href="admin?p=<?=$core->getMdlId('Postagens');?>&a=2&id=<?=$sql4['id'];?>" target="_blank">#<?=$sql2['id_msg'];?> (clique para ver)</a></b> do tópico <b><a href="/forum/topicos/<?=$sql6['id'];?>/<?=$core->trataurl($sql6['titulo']);?>"><?=$core->clear($sql6['titulo']);?></a></b><br><br>

		<? if(!$sql4) {
			echo aviso_red("O post ou tópico denunciado já foi deletado. Marque a denúncia como resolvida para notificar o denunciante que o problema foi resolvido.");
		} ?>

		<form action="<?=$_SERVER['REQUEST_URI'];?>" method="post">
			<input type="hidden" name="marcar" value="<?=$sql2['id'];?>">
			<button class="btn btn-primary" type="submit">Marcar como resolvido</button>
		</form>

		<form action="<?=$_SERVER['REQUEST_URI'];?>" method="post">
			<input type="hidden" name="marcar" value="<?=$sql2['id'];?>">
			<input type="hidden" name="descartar" value="descartar">
			<button class="btn btn-danger" type="submit">Descartar</button>
		</form>
	</div>
	<? }

	if($rows == 0) {
		echo aviso_blue("Não há nenhuma denúncia pendente.");
	} ?>
</div>
<? } ?>