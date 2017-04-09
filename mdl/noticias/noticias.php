<? error_reporting(1); ?>
<? if($_GET['a'] == 1 || $_GET['a'] == 2) {
	$script_js = 'pgExitEnable();';
}

if($_GET['a'] == 1) {
	if($_POST['form'] == 'form') {
		$titulo = $core->clear($_POST['titulo']);
		$descricao = $core->clear($_POST['descricao']);
		$noticia = stripslashes($_POST['conteudo']);
		$cat_id = (int) $core->clear($_POST['categoria']);
		$status = $core->clear($_POST['status']);
		$comentarios = $core->clear($_POST['comentarios']);
		$fixo = $core->clear($_POST['fixo']);
		$evento_tempo = $core->clear($_POST['evento']);
		$prosseguir = true;

		if(empty($titulo) || empty($descricao) || empty($noticia)) {
			$form_return .= aviso_red("Preencha todos os campos.");
			$prosseguir = false;
		}

		$sql3 = $conn->prepare("SELECT COUNT(id) FROM noticias_cat WHERE id = ?");
		$sql3->bindValue(1, $cat_id);
		$sql3->execute();

		if($sql3->fetchColumn() == 0) {
			$form_return .= aviso_red("Ocorreu um erro ao executar a ação. Código de erro: CT01");
			$prosseguir = false;
		}

		$status_disp = array(2, 3);

		if($permissoes[21] == 's' || $core->allAccess()) { array_unshift($status_disp, 1); }

		if(in_array($status, $status_disp)) {
			if($status == 1) { $status = 'ativo'; }
			if($status == 2) { $status = 'rascunho'; }
			if($status == 3) { $status = 'revisao'; }
		} else {
			$form_return .= aviso_red("Ocorreu um erro ao executar a ação. Código de erro: ST01");
			$prosseguir = false;
		}

		if($comentarios) { $comentarios = 's'; } else { $comentarios = 'n'; }

		if($permissoes[22] == 's' || $core->allAccess()) {
			if($fixo) { $fixo = 's'; } else { $fixo = 'n'; }
		} else { $fixo = 'n'; }

		if($prosseguir) {
			$up_name = 'imagem';

			$up_gallery = $core->clear($_POST["gl-$up_name"]);
			$up_file = $_FILES["fl-$up_name"];
			$up_url = $core->clear($_POST["url-$up_name"]);

			$upload = new Upload($conn, $up_gallery, $up_file, $up_url, 'news-', true);

			if(!$upload->erro) {
				$caminho_img = $upload->caminho;
			} else {
				$form_return .= aviso_red($upload->erro);
				$prosseguir = false;
			}
		}

		if(empty($evento_tempo)) {
			$evento_tempo = 0;
			$evento = 'n';
		} else {
			$a = explode('/', $evento_tempo);
			$b = substr($evento_tempo, -5);
			$c = explode(':', $b);

			$dia = $a[0];
			$mes = $a[1];
			$ano = substr($a[2], 0, 4);
			$hora = $c[0];
			$minuto = $c[1];

			$evento_tempo = mktime($hora, $minuto, 0, $mes, $dia, $ano);
			$evento = 's';
		}

		if($permissoes[117] == 's' || $core->allAccess()) {
			$coluna = $core->clear($_POST['coluna']);
		} else { $coluna = 0; }

		if($prosseguir) {
			$insert_data['titulo'] = $titulo;
			$insert_data['descricao'] = $descricao;
			$insert_data['imagem'] = $caminho_img;
			$insert_data['noticia'] = $noticia;
			$insert_data['cat_id'] = $cat_id;
			$insert_data['comentarios'] = $comentarios;
			$insert_data['fixo'] = $fixo;
			$insert_data['status'] = $status;
			$insert_data['evento'] = $evento;
			$insert_data['evento_horario'] = $evento_tempo;
			$insert_data['blog_id'] = $coluna;
			$insert_data['autor'] = $autor;
			$insert_data['data'] = $timestamp;

			$insert = $sqlActions->insert($mdl_tabela, $insert_data);

			if($insert) {
				$core->logger("O usuário adicionou uma nova notícia. ($status)", "acao");

				if($status == 'revisao') {
					$core->sendNtf($core->autor . " postou uma notícia para revisão.", "info");
				}

				if($status == 'ativo') {
					$sql = $conn->prepare("SELECT id FROM noticias WHERE autor = ? ORDER BY id DESC LIMIT 1");
					$sql->bindValue(1, $autor);
					$sql->execute();
					$id_noticia = $sql->fetchColumn();
					$link_noticia = "/noticias/".$id_noticia."-" . $core->trataurl($titulo);

					$core->sendAlert("Uma nova notícia foi postada! <a href=\"$link_noticia\">Clique aqui</a> para ficar mais informado sobre o que acontece nesse mundo de pixel!");
				}

				$form_return .= aviso_green("Sucesso!");
				foreach($_POST as $nome_campo => $valor){ $_POST[$nome_campo] = '';}
			} else {
				$form_return .= aviso_red("Ocorreu um erro ao executar a ação. Código de erro: {$sqlActions->error}");
			}
		}
	}
	?>
	<div class="box-content">
		<div class="title-section"><?=$mdl['nome'];?></div>

		<? echo $form_return;

		$form = new Form('form-submit', '', true);

		$form->mostraAviso(well("Para pré-visualizar sua notícia, salve-a como rascunho primeiro."));

		$form->createInput('Título', 'text', 'titulo', '', 'no-label s-xlg no-border');
		$form->createInput('Descrição', 'text', 'descricao', '', 'no-label s-lg');
		$form->createTextarea('Notícia', 'conteudo', '', 'ckeditor','ckeditor');
		$form->createUpload('Imagem', 'imagem');

		$categorias = array();
		$sql = $conn->query("SELECT * FROM noticias_cat ORDER BY id ASC");
		while($sql2 = $sql->fetch()) {
			$atual = array("label" => $core->clear($sql2['nome']), "value" => $sql2['id']);
			$categorias[] = $atual;
		}

		$form->createSelect('Categoria', 'categoria', $categorias, '', 'w-md');

		$status_opt = array();
		$status_info = '';

		if($permissoes[21] == 's' || $core->allAccess()) {
			$status_opt[] = array("label" => "Ativo", "value" => 1);
			$status_info = '<b>Ativo</b> - A notícia aparecerá no site imediatamente.<br>';
		}

		$status_opt[] = array("label" => "Em rascunho", "value" => 2);
		$status_opt[] = array("label" => "Para revisão", "value" => 3);

		$status_info .= '<b>Em rascunho</b> - A notícia aparecerá apenas no painel.<br>';
		$status_info .= '<b>Para revisão</b> - Um membro superior revisará sua notícia antes de ser ativada.';

		$form->createSelect('Status', 'status', $status_opt, 3, 'w-md', '', $status_info);

		$form->createInput('Data e hora do evento', 'text', 'evento', '', '', '', 'Se esta notícia for de um evento, digite o dia e o horário do evento usando o modelo: <pre>DD/MM/AAAA HH:MM</pre> - Ex: <pre>03/07/2015 15:00</pre><br>Use o Horário de Brasília.<br>Se esta notícia não for de um evento, deixe este campo em branco.');

		if($permissoes[117] == 's' || $core->allAccess()) {

			$colunas = array(array("label" => '-- Não é uma coluna', "value" => 0));
			$sql = $conn->query("SELECT * FROM blogs ORDER BY id ASC");
			while($sql2 = $sql->fetch()) {
				$atual = array("label" => $core->clear($sql2['nome']), "value" => $sql2['id']);
				$colunas[] = $atual;
			}

			$form->createSelect('Colunas', 'coluna', $colunas, '', 'w-md', '', 'Se esta notícia for uma edição de uma coluna, selecione a coluna. Se não, deixe em branco.');
		}

		$form->createCheckbox('Comentários', 'comentarios', true, 'check-side');

		if($permissoes[22] == 's' || $core->allAccess()) {
			$form->createCheckbox('Notícia fixa', 'fixo', '', 'check-side');
		}

		$form->generateForm();
		echo $form->form; ?>
	</div>
	<? } ?>

	<? if($_GET['a'] == 2) {
		$id = $_GET['id'];
		$_ex = $conn->prepare("SELECT * FROM $mdl_tabela WHERE id = ? LIMIT 1");
		$_ex->bindValue(1, $id);
		$_ex->execute();
		$ex = $_ex->fetch();

		if($ex['autor'] != $_SESSION['login'] && $permissoes[23] != 's' && !$core->allAccess()) {
			$script_js .= noticia403();
		} else {
			if($_POST['form'] == 'form') {
				$titulo = $core->clear($_POST['titulo']);
				$descricao = $core->clear($_POST['descricao']);
				$noticia = stripslashes($_POST['conteudo']);
				$cat_id = (int) $core->clear($_POST['categoria']);
				$status = $core->clear($_POST['status']);
				$comentarios = $core->clear($_POST['comentarios']);
				$fixo = $core->clear($_POST['fixo']);
				$nova = $core->clear($_POST['nova']);
				$evento_tempo = $core->clear($_POST['evento']);
				$prosseguir = true;

				$_ex = $conn->prepare("SELECT * FROM $mdl_tabela WHERE id = ? LIMIT 1");
				$_ex->bindValue(1, $id);
				$_ex->execute();
				$ex = $_ex->fetch();

				if(empty($titulo) || empty($descricao) || empty($noticia)) {
					$form_return .= aviso_red("Preencha todos os campos.");
					$prosseguir = false;
				}

				$sql3 = $conn->prepare("SELECT COUNT(id) FROM noticias_cat WHERE id = ?");
				$sql3->bindValue(1, $cat_id);
				$sql3->execute();

				if($sql3->fetchColumn() == 0) {
					$form_return .= aviso_red("Ocorreu um erro ao executar a ação. Código de erro: CT01");
					$prosseguir = false;
				}

				$status_disp = array(2, 3);

				if($permissoes[21] == 's' || $core->allAccess()) { array_unshift($status_disp, 1); }

				if(in_array($status, $status_disp)) {
					if($status == 1) { $status = 'ativo'; }
					if($status == 2) { $status = 'rascunho'; }
					if($status == 3) { $status = 'revisao'; }
				} else {
					$form_return .= aviso_red("Ocorreu um erro ao executar a ação. Código de erro: ST01");
					$prosseguir = false;
				}

				if($nova) {
					$update_data['data'] = $core->timestamp;
				}

				if($comentarios) { $comentarios = 's'; } else { $comentarios = 'n'; }

				if($permissoes[22] == 's' || $core->allAccess()) {
					if($fixo) { $fixo = 's'; } else { $fixo = 'n'; }
				} else { $fixo = 'n'; }

				if($permissoes[117] == 's' || $core->allAccess()) {
					$coluna = $core->clear($_POST['coluna']);
				} else { $coluna = 0; }

				if($prosseguir) {
					$up_name = 'imagem';

					$up_gallery = $core->clear($_POST["gl-$up_name"]);
					$up_file = $_FILES["fl-$up_name"];
					$up_url = $core->clear($_POST["url-$up_name"]);

					$upload = new Upload($conn, $up_gallery, $up_file, $up_url, 'news-', false, $ex['imagem']);

					if(!$upload->erro) {
						$caminho_img = $upload->caminho;
					} else {
						$form_return .= aviso_red($upload->erro);
						$prosseguir = false;
					}
				}

				if(empty($evento_tempo)) {
					$evento_tempo = 0;
					$evento = 'n';
				} else {
					$a = explode('/', $evento_tempo);
					$b = substr($evento_tempo, -5);
					$c = explode(':', $b);

					$dia = $a[0];
					$mes = $a[1];
					$ano = substr($a[2], 0, 4);
					$hora = $c[0];
					$minuto = $c[1];

					$evento_tempo = mktime($hora, $minuto, 0, $mes, $dia, $ano);
					$evento = 's';
				}

				if($prosseguir) {
					$update_data['titulo'] = $titulo;
					$update_data['descricao'] = $descricao;
					$update_data['imagem'] = $caminho_img;
					$update_data['noticia'] = $noticia;
					$update_data['cat_id'] = $cat_id;
					$update_data['comentarios'] = $comentarios;
					$update_data['fixo'] = $fixo;
					$update_data['status'] = $status;
					$update_data['evento'] = $evento;
					$update_data['evento_horario'] = $evento_tempo;
					$update_data['blog_id'] = $coluna;

					$where_data['id'] = $id;
					$update = $sqlActions->update($mdl_tabela, $update_data, $where_data);

					if($update) {
						$core->logger("O usuário editou a notícia [#$id].", "acao");

						$inInt['id_noticia'] = $id;
						$inInt['acao'] = 'O usuário editou a notícia';

						if($ex['status'] != $status) {
							if($status == 'ativo') {
								$stats = 'Ativo';

								$link_noticia = "/noticias/".$ex['id']."-" . $core->trataurl($ex['titulo']);
								$core->sendAlert("Uma nova notícia foi postada! <a href=\"$link_noticia\">Clique aqui</a> para ficar mais informado sobre o que acontece nesse mundo de pixel!");
							}

							if($status == 'rascunho') { $stats = 'Rascunho'; }
							if($status == 'revisao') { $stats = 'Para revisão'; }

							$inInt['acao'] .= ' e alterou o status para <b>'.$stats.'</b>';
						}

						$inInt['acao'] .= '.';
						$inInt['autor'] = $core->autor;
						$inInt['data'] = $core->timestamp;

						$insInt = $sqlActions->insert("noticias_atividades", $inInt);

						$form_return .= aviso_green("Sucesso!");
						foreach($_POST as $nome_campo => $valor){ $_POST[$nome_campo] = '';}
					} else {
						$form_return .= aviso_red("Ocorreu um erro ao executar a ação. Código de erro: {$sqlActions->error}");
					}
				}
			}
		}

		$_ex = $conn->prepare("SELECT * FROM $mdl_tabela WHERE id = ? LIMIT 1");
		$_ex->bindValue(1, $id);
		$_ex->execute();
		$ex = $_ex->fetch();

		$not_allowed = false;

		if(!$ex || $not_allowed) {
			$script_js .= register404();
		}
		?>
		<div class="box-content">
			<div class="title-section"><?=$mdl['nome'];?></div>

			<button class="btn btn-danger" onclick="deletar(this, 1);" rel="?p=<?=$p;?>&a=3&id=<?=$id;?>">Inativar</button><br><br>

			<? echo $form_return;

			$form = new Form('form-submit', '', true);

			if($ex['status'] != 'ativo') {
				if($ex['autor'] == $_SESSION['login'] || $permissoes[59] == 's' || $core->allAccess()) {
					$form->mostraAviso(well("Sua notícia não está ativa no site, mas você pode visualizá-la a partir do link abaixo. Apenas você e a administração do site tem acesso a esse link (lembre-se de logar no site).<br>Link: <b><a href=\"http://www.icehabbo.com.br/noticias/".$ex['id']."-".$core->trataurl($ex['titulo'])."\" target=\"_blank\">http://www.icehabbo.com.br/noticias/".$ex['id']."-".$core->trataurl($ex['titulo'])."</a></b>"));
				}
			}

			$interacoes = array();

			$sql4 = $conn->prepare("SELECT * FROM noticias_atividades WHERE id_noticia = ? ORDER BY id DESC");
			$sql4->bindValue(1, $ex['id']);
			$sql4->execute();
			while($sql5 = $sql4->fetch()) {
				$interacoes[] = '<span class="label label-info">'.$sql5['autor'].'</span> <span class="label label-success">'.date('d/m/Y H:i:s', $sql5['data']).'</span> ' . $sql5['acao'];
			}

			if(!empty($interacoes)) {
				$form->mostraAviso(well(implode('<br>', $interacoes)));
			}

			$form->createInput('Título', 'text', 'titulo', $ex['titulo'], 'no-label s-xlg no-border');
			$form->createInput('Descrição', 'text', 'descricao', $ex['descricao'], 'no-label s-lg');
			$form->createTextarea('Notícia', 'conteudo', $ex['noticia'], 'ckeditor','ckeditor');
			$form->createUpload('Imagem', 'imagem', $ex['imagem']);

			$categorias = array();
			$sql = $conn->query("SELECT * FROM noticias_cat ORDER BY id ASC");
			while($sql2 = $sql->fetch()) {
				$atual = array("label" => $core->clear($sql2['nome']), "value" => $sql2['id']);
				$categorias[] = $atual;
			}

			$form->createSelect('Categoria', 'categoria', $categorias, $ex['cat_id'], 'w-md');

			$status_opt = array();
			$status_info = '';

			if($permissoes[21] == 's' || $core->allAccess()) {
				$status_opt[] = array("label" => "Ativo", "value" => 1);
				$status_info = '<b>Ativo</b> - A notícia aparecerá no site imediatamente.<br>';
			}

			$status_opt[] = array("label" => "Em rascunho", "value" => 2);
			$status_opt[] = array("label" => "Para revisão", "value" => 3);

			$status_info .= '<b>Em rascunho</b> - A notícia aparecerá apenas no painel.<br>';
			$status_info .= '<b>Para revisão</b> - Um membro superior revisará sua notícia antes de ser ativada.';

			if($ex['status'] == 'ativo') { $status_news = 1; }
			if($ex['status'] == 'rascunho') { $status_news = 2; }
			if($ex['status'] == 'revisao') { $status_news = 3; }

			$form->createSelect('Status', 'status', $status_opt, $status_news, 'w-md', '', $status_info);

			$form->createInput('Data e hora do evento', 'text', 'evento', (($ex['evento_horario'] != 0)) ? date('d/m/Y H:i', $ex['evento_horario']) : '', '', '', 'Se esta notícia for de um evento, digite o dia e o horário do evento usando o modelo: <pre>DD/MM/AAAA HH:MM</pre> - Ex: <pre>03/07/2015 15:00</pre><br>Use o Horário de Brasília.<br>Se esta notícia não for de um evento, deixe este campo em branco.');

			if($permissoes[117] == 's' || $core->allAccess()) {

				$colunas = array(array("label" => '-- Não é uma coluna', "value" => 0));
				$sql = $conn->query("SELECT * FROM blogs ORDER BY id ASC");
				while($sql2 = $sql->fetch()) {
					$atual = array("label" => $core->clear($sql2['nome']), "value" => $sql2['id']);
					$colunas[] = $atual;
				}

				$form->createSelect('Colunas', 'coluna', $colunas, $ex['blog_id'], 'w-md', '', 'Se esta notícia for uma edição de uma coluna, selecione a coluna. Se não, deixe em branco.');
			}

			$form->createCheckbox('Comentários', 'comentarios', (($ex['comentarios'] == 's')) ? true : false, 'check-side');

			if($permissoes[22] == 's' || $core->allAccess()) {
				$form->createCheckbox('Notícia fixa', 'fixo', (($ex['fixo'] == 's')) ? true : false, 'check-side');
			}

			$form->generateForm();
			echo $form->form; ?>
		</div>
		<? } ?>

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

			$_ex = $conn->prepare("SELECT autor FROM $mdl_tabela WHERE id = ? LIMIT 1");
			$_ex->bindValue(1, $id);
			$_ex->execute();
			$ex = $_ex->fetch();

	// Caso ele não seja o autor da notícia e ele não possa editar qualquer notícia...
			if($ex['autor'] != $_SESSION['nick'] && $permissoes[23] != 's' && !$core->allAccess()) {
				die('Acesso negado.');
			}

			$ids = explode(',', $id);
			$ids = array_filter($ids);

			if(count($ids) > 0) {
				$delete = $conn->prepare("UPDATE $mdl_tabela SET status = 'rascunho' WHERE id = ? LIMIT 1");
				$delete->bindParam(1, $id_atual);

				$delete2 = $conn->prepare("DELETE FROM noticias_coment WHERE id_noticia = ?");
				$delete2->bindParam(1, $id_atual);

				$delete4 = $conn->prepare("DELETE FROM noticias_votos WHERE id_noticia = ?");
				$delete4->bindParam(1, $id_atual);

				foreach ($ids as $id_atual) {
					$delete->execute();
					$delete2->execute();
					$delete4->execute();

					$core->logger("O usuário inativou a notícia [#$id_atual]", "acao");
				}
			} else {
				$delete_where['id'] = $id;
				$delete = $conn->prepare("UPDATE $mdl_tabela SET status = 'rascunho' WHERE id = ? LIMIT 1");
				$delete->bindValue(1, $id);
				$delete->execute();
				unset($delete_where);

				$delete_where['id_noticia'] = $id;
				$delete = $sqlActions->delete("noticias_coment", $delete_where);
				unset($delete_where);

				$delete_where['id_noticia'] = $id;
				$delete = $sqlActions->delete("noticias_votos", $delete_where);

				$core->logger("O usuário inativou a notícia [#$id]", "acao");
			}
		} ?>

		<? if($_GET['a'] == 4) {
			$id = $_GET['id'];

			$reset = $conn->query("ALTER TABLE $mdl_tabela AUTO_INCREMENT = 1;");
			$core->logger("O usuário resetou o AI de $mdl_tabela", "acao");

			echo "<script>location.replace('?p=$p');</script>";
		} ?>

		<? if($_GET['a'] == 9) {
			$id = $_GET['id'];

			$ativar = $conn->prepare("UPDATE $mdl_tabela SET status = 'ativo' WHERE id = ?");
			$ativar->bindValue(1, $id);
			$ativar->execute();

			$core->logger("O usuário ativou o registro [#$id - $mdl_tabela]", "acao");
		} ?>

		<? if($_GET['a'] == '') { ?>
		<div class="box-content">
			<div class="title-section"><?=$mdl['nome'];?></div>
			<a href="?p=<?=$_GET['p'];?>&a=1"><button class="btn btn-primary">Adicionar</button></a>
			<? if($core->allAccess()) { ?><a href="?p=<?=$_GET['p'];?>&a=4"><button class="btn btn-danger">Resetar AI [DEV]</button></a><? } ?>
			<button class="btn btn-info" onclick="searchShow();">Pesquisar</button>
			<? if($_POST['search'] == 'search') { ?><a href="?p=<?=$_GET['p'];?>"><button class="btn btn-warning">Limpar busca</button></a><? } ?>
			<br><br>

			<?php

			$search = getSearchForm();
			echo $search;

			?>

			<?
			$table = new Table('', true, $core->allAccess());
			$table->head(array('#', 'Título', 'Status', 'Autor', 'Data', 'Ações'));

			$table->startBody();

			$limite = 15;
			$pagina = $_GET['pag'];
			((!$pagina)) ? $pagina = 1 : '';
			$inicio = ($pagina * $limite) - $limite;

			$query = "$mdl_tabela ORDER BY id DESC";

			if($_POST['search'] == 'search') {
				$busca = $core->clear($_POST['busca']);
				$limite = 5000;

				$campo = "titulo";

				$query = "$mdl_tabela WHERE $campo LIKE ? ORDER BY id DESC";
				$sql = $conn->prepare("SELECT * FROM $query LIMIT $inicio,$limite");
				$sql->bindValue(1, '%'.$busca.'%');
				$sql->execute();

				$_rows = $conn->prepare("SELECT count(id) FROM $query");
				$_rows->bindValue(1, '%'.$busca.'%');
				$_rows->execute();
				$total_registros = $_rows->fetchColumn();

				echo '<div class="searching">Pesquisando por: <b>'.$busca.'</b></div>';
			} else {
				$sql = $conn->query("SELECT * FROM $query LIMIT $inicio,$limite");
				$total_registros = $core->getRows("SELECT * FROM $query");
			}

			while($sql2 = $sql->fetch()) {
				$stats = '';

				if($sql2['status'] == 'ativo') { $stats .= '<span class="label label-primary">Ativo</span> '; }
				if($sql2['status'] == 'rascunho') { $stats .= '<span class="label label-warning">Rascunho</span> '; }
				if($sql2['status'] == 'revisao') { $stats .= '<span class="label label-danger">Aguardando revisão</span> '; }

				if($sql2['fixo'] == 's') { $stats .= '<span class="label label-info">Fixo</span> '; }

				$not_allowed = false;

		// Caso ele não seja o autor da notícia e ele não possa editar qualquer notícia...
				if($sql2['autor'] != $_SESSION['login'] && $permissoes[23] != 's' && !$core->allAccess()) {
					$not_allowed = true;
				}

				$table->insertBody(array($sql2['id'], $core->clear($sql2['titulo']), $stats, $core->clear($sql2['autor']), $core->clear(date('d/m/Y H:i', $sql2['data'])), (($not_allowed)) ? '' : 'actions'), $sql2['status']);
			}

			$table->closeTable();
			echo $table->table;

			if($total_registros == 0) {
				echo aviso_red("Nenhum registro encontrado.");
			} else {
				echo '<ul class="pagination">';

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

			<?php

			if($total_registros > 0) {
				$marked = getMarkedSelect($p, 3);
				echo $marked;
			}

			?>
		</div>
		<? } ?>