<?
if($_POST['form'] == 'form') {
	$prosseguir = true;

	if($prosseguir) {
		$up_name = 'imagem';

		$up_gallery = $core->clear($_POST["gl-$up_name"]);
		$up_file = $_FILES["fl-$up_name"];
		$up_multiple = $_FILES["flm-$up_name"];
		$up_url = $core->clear($_POST["url-$up_name"]);
		$up_watermark = $core->clear($_POST["options-watermark-$up_name"]);

		if($up_multiple['name'][0] == '') {
			$up_multiple = '';
		}

		$upload = new Upload($conn, $up_gallery, $up_file, $up_url, 'up-', true, '', true, $up_multiple, false, $up_watermark);

		if(!$upload->erro) {
			$caminho_img = $upload->caminho;
		} else {
			$form_return .= aviso_red($upload->erro);
			$prosseguir = false;
		}
	}

	if($prosseguir && empty($up_multiple)) {
		$form_return .= aviso_green("Imagem enviada com sucesso!<br><br>Link: <a href='$caminho_img' target='_blank'><b>http://www.icehabbo.com.br$caminho_img</b></a>");
	}

	if($prosseguir && !empty($up_multiple)) {
		$return  = "Imagens enviadas com sucesso!<br><br>Links:<br>";

		foreach ($caminho_img as $atual) {
			$return .= "<a href='$atual' target='_blank'><b>http://www.icehabbo.com.br$atual</b></a><br>";
		}

		$form_return .= aviso_green($return);
	}
}
?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?> - Upload de imagens</div>

	<? echo $form_return;
	$form = new Form('form-submit', '');

	$form->createUpload('Imagem', 'imagem', '', '', '', true);

	$form->generateForm();
	echo $form->form; ?>
</div>

<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?> - Imagens enviadas</div>

	<?
	$limite = 32;
	$pagina = $_GET['pag'];
	((!$pagina)) ? $pagina = 1 : '';
	$inicio = ($pagina * $limite) - $limite;

	$sql = $conn->query("SELECT DISTINCT(imagem) AS imagem FROM $mdl_tabela ORDER BY id DESC LIMIT $inicio,$limite");
	$total_registros = $conn->query("SELECT COUNT(DISTINCT imagem) FROM $mdl_tabela ORDER BY id DESC")->fetchColumn();

	while($sql2 = $sql->fetch()) { ?>
	<a href="<?=$sql2['imagem'];?>" target="_blank"><div class="thumbnail">
		<div id="img" style="background-image:url(<?=$sql2['imagem'];?>);"></div>
		<div id="infos"><?=basename($sql2['imagem']);?></div>
	</div></a>
	<? }

	if($total_registros == 0) {
		echo aviso_red("Nenhum registro encontrado.");
	} else {
		echo '<br><ul class="pagination">';

		$total_paginas = ceil($total_registros / $limite);

		$links_laterais = ceil($limite / 2);

		$inicio = $pagina - $links_laterais;
		$limite = $pagina + $links_laterais;

		for ($i = $inicio; $i <= $limite; $i++){
			if ($i == $pagina) {
				echo '<li class="active"><a href="#">'.$i.'</a></li>';
			} else {
				if ($i >= 1 && $i <= $total_paginas){
					$link = '?' . $_SERVER["QUERY_STRING"];
					$link = preg_replace('/(\\?|&)pag=.*?(&|$)/','',$link);
					echo '<li><a href="'.$link.'&pag='.$i.'">'.$i.'</a></li>';
				}
			}
		}

		echo '</ul>';
	} ?>
</div>