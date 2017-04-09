<? if($_GET['a'] == 1) {
	$up['votacao'] = 'aberta';
	$update = $sqlActions->update("awd_opcoes", $up);
	$core->logger("O usuário abriu a votação do Awards.", "acao");
?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>

	<a href="?p=<?=$_GET['p'];?>"><button class="btn btn-primary">Voltar</button></a><br>

	<?=aviso_green("Votação aberta!");?>
</div>
<? } ?>

<? if($_GET['a'] == 2) {
	$up['votacao'] = 'fechada';
	$update = $sqlActions->update("awd_opcoes", $up);
	$core->logger("O usuário fechou a votação do Awards.", "acao");
?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>

	<a href="?p=<?=$_GET['p'];?>"><button class="btn btn-primary">Voltar</button></a><br>

	<?=aviso_red("Votação fechada!");?>
</div>
<? } ?>

<? if($_GET['a'] == 3) {
	if($_POST['form'] == 'form') {
		$titulo = $core->clear($_POST['titulo']);
		$etapa = $core->clear($_POST['etapa']);
		$visivel = $core->clear($_POST['visivel']);
		$prosseguir = true;

		if($visivel == 1) { $visivel = 's'; } else {  $visivel = 'n'; }

		if($prosseguir) {
			$up['titulo'] = $titulo;
			$up['etapa'] = $etapa;
			$up['visivel'] = $visivel;
			$up['autor'] = $autor;
			$up['data'] = $core->timestamp;

			$update = $sqlActions->update("awd_opcoes", $up);

			if($update) {
				$core->logger("O usuário editou as opções do Awards.", "acao");

				$form_return .= aviso_green("Sucesso!");
				foreach($_POST as $nome_campo => $valor){ $_POST[$nome_campo] = '';}
			} else {
				$form_return .= aviso_red("Ocorreu um erro ao executar a ação. Código de erro: {$sqlActions->error}");
			}
		}
	}

	$_ex = $conn->prepare("SELECT * FROM awd_opcoes LIMIT 1");
	$_ex->execute();
	$ex = $_ex->fetch(); ?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>

	<? echo $form_return;

	$form = new Form('form-submit', '');

	$form->createInput('Nome', 'text', 'titulo', $ex['titulo']);

	$etapas = array(
		array("label" => 'Primeira', "value" => 1),
		array("label" => 'Segunda', "value" => 2),
		);

	$form->createSelect('Etapa', 'etapa', $etapas, $ex['etapa']);

	$visivel = array(
		array("label" => 'Sim', "value" => 1),
		array("label" => 'Não', "value" => 2),
		);

	$form->createSelect('Visível aos usuários', 'visivel', $visivel, ($ex['visivel'] == 's') ? 1 : 2) ;

	$form->generateForm();
	echo $form->form; ?>
</div>
<? } ?>

<? if($_GET['a'] == 4) {
	if($_POST['form'] == 'form') {
		$del = $conn->query("DELETE FROM awd_candidatos");
		$reset = $conn->query("ALTER TABLE awd_candidatos AUTO_INCREMENT = 1;");

		$sql = $conn->query("SELECT * FROM awd_categorias ORDER BY id ASC");
		$sql2 = $sql->fetchAll();

		foreach ($sql2 as $atual) {
			$id = $atual['id'];
			$candidatos = $_POST["cand_$id"];

			$in['id_cat'] = $atual['id'];
			$in['candidatos'] = $candidatos;
			$in['autor'] = $autor;
			$in['data'] = $core->timestamp;
			$insert = $sqlActions->insert("awd_candidatos", $in);
		}

		$core->logger("O usuário lançou candidatos no Awards.", "acao");

		$form_return .= aviso_green("Sucesso!");
	}

	$_ex = $conn->prepare("SELECT * FROM awd_opcoes LIMIT 1");
	$_ex->execute();
	$ex = $_ex->fetch(); ?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>

	<? echo $form_return;

	$form = new Form('form-submit', '');

	$form->mostraAviso(well("Insira os nicks dos indicados em cada categoria para que eles concorram na segunda etapa do Awards."));
	$form->mostraAviso(well("Os campos foram preenchidos automaticamente com os 3 indicados mais votados de cada categoria. <b>Resolva os empates manualmente</b>."));
	$form->mostraAviso(well("Os nicks devem estar separados por um <b>espaço</b>. Não há limites de candidatos."));

	$sql = $conn->query("SELECT * FROM awd_categorias ORDER BY id ASC");
	$sql2 = $sql->fetchAll();

	foreach ($sql2 as $atual) {
		$sql3 = $conn->prepare("SELECT nick, count(nick) AS c FROM awd_indicados WHERE id_cat = ? AND valido = 's' GROUP BY nick ORDER BY c DESC LIMIT 3");
		$sql3->bindValue(1, $atual['id']);
		$sql3->execute();
		$sql4 = $sql3->fetchAll();
		$cand = '';

		foreach ($sql4 as $cand_atual) {
			$cand .= $cand_atual['nick'] . ' ';
		}
		$form->createInput($atual['nome'], 'text', 'cand_' . $atual['id'], $cand);
	}

	$form->generateForm();
	echo $form->form; ?>
</div>
<? } ?>

<? if($_GET['a'] == '') {
	$_ex = $conn->prepare("SELECT * FROM awd_opcoes LIMIT 1");
	$_ex->execute();
	$ex = $_ex->fetch(); ?>
<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>

	<div class="well well-lg">
	Status da votação: <b><?=$ex['votacao'];?></b><br>
	Etapa: <b><?=$ex['etapa'];?></b>
	</div>

	<a href="?p=<?=$_GET['p'];?>&a=1"><button class="btn btn-primary">Abrir votação</button></a>
	<a href="?p=<?=$_GET['p'];?>&a=2"><button class="btn btn-danger">Fechar votação</button></a>
	<a href="?p=<?=$_GET['p'];?>&a=3"><button class="btn btn-success">Editar opções</button></a>
	<a href="?p=<?=$_GET['p'];?>&a=4"><button class="btn btn-info">Lançar candidatos</button></a>
</div>
<? } ?>