<?
if($_GET['a'] == 2) {
	$id = $_GET['id'];

	$update_data['retirado'] = 's';
	$update_data['retirado_autor'] = $autor;
	$where_data['id'] = $id;

	$update = $sqlActions->update($mdl_tabela, $update_data, $where_data);

	$core->logger("O usuário marcou como entregue o mobi da Pro Store [#$id]", "acao");

	echo aviso_green("Marcado como entregue!");
}
?>

<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>

	<?php
		$sql = $conn->query("
			SELECT A.*, B.nome FROM shop_itens_mobis AS A
			LEFT JOIN shop_itens AS B ON A.id_item = B.id
			ORDER BY A.id DESC");
		while($row = $sql->fetch()) {
	?>
		<div class="well well-sm">
			Item vendido: <a href="?p=97&a=2&id=<?=$row['id_item'];?>"><b><?=$row['nome'];?></b></a><br><br>
			Nick do comprador: <b><?=$row['comprador'];?></b><br>
			Data da compra: <b><?=date('d/m/Y H:i', $row['data']);?></b><br><br>
		</div>
		<? }

		$query = "SELECT * FROM pro_store_vendas WHERE retirado = 'n' ORDER BY id DESC";
		$rows = $core->getRows($query);

		if($rows == 0) {
			echo aviso_red("Nenhuma venda pendente.");
		} ?>
	</div>