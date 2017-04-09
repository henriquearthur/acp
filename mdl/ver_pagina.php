<?php

$id = $_GET['id'];

$sql = $conn->prepare("SELECT * FROM acp_paginas WHERE id = ?");
$sql->bindValue(1, $id);
$sql->execute();
$sql2 = $sql->fetch();

if($sql2) {
	$cargos_ler = explode('|', $sql2['cargos']);
	$result = array_intersect($cargos_ler, $cargos_user);
	$result = array_filter($result);

	if(count($result) > 0 || $sql2['cargos'] == 'all' || $core->allAccess()) {
		$_visus = $conn->prepare("SELECT count(id) FROM acp_paginas_visualizacoes WHERE id_pagina = ? AND ip = ?");
		$_visus->bindValue(1, $sql2['id']);
		$_visus->bindValue(2, $core->ip);
		$_visus->execute();
		$visus = $_visus->fetchColumn();

		if($visus == 0) {
			$in_data['id_pagina'] = $sql2['id'];
			$in_data['id_usuario'] = $dados['id'];
			$in_data['ip'] = $core->ip;
			$in_data['data'] = $core->timestamp;
			$in = $sqlActions->insert("acp_paginas_visualizacoes", $in_data);
			unset($in_data);
		} ?>
		<div class="box-content">
			<div class="title-section"><?=$core->clear($sql2['titulo']);?></div>
			<?=$sql2['conteudo'];?>
		</div>
<?	}
} else {
	echo aviso_red("Esta página não existe.");
}