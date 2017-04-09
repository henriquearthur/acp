<?

$rows  = $conn->query("SELECT count(id) FROM noticias WHERE status = 'ativo'")->fetchColumn();
$rows2 = $conn->query("SELECT count(id) FROM forum_topicos WHERE status = 'ativo'")->fetchColumn();
$_rows3 = $conn->query("SELECT count(id) FROM noticias_coment")->fetchColumn();
$__rows3 = $conn->query("SELECT count(id) FROM pixel_coment")->fetchColumn();
$rows3 = $_rows3 + $__rows3;
$rows4 = $conn->query("SELECT count(id) FROM pixel_artes")->fetchColumn();
$rows5 = $conn->query("SELECT count(id) FROM emblemas")->fetchColumn();
$rows6 = $conn->query("SELECT count(id) FROM usuarios")->fetchColumn();

?>
<div id="charts"><center>
	<div class="chart" data-percent="66" data-bar-color="#F39C12">
		<div class="center"><center>
			<b><?=$rows;?></b><br><span>notícias</span>
		</center></div>
	</div>

	<div class="chart" data-percent="37" data-bar-color="#3498db">
		<div class="center"><center>
			<b><?=$rows2;?></b><br><span>tópicos</span>
		</center></div>
	</div>

	<div class="chart" data-percent="45" data-bar-color="#1BBC9B">
		<div class="center"><center>
			<b><?=$rows3;?></b><br><span>coment.</span>
		</center></div>
	</div>

	<div class="chart" data-percent="89" data-bar-color="#D91E18">
		<div class="center"><center>
			<b><?=$rows4;?></b><br><span>artes</span>
		</center></div>
	</div>

	<div class="chart" data-percent="54" data-bar-color="#663399">
		<div class="center"><center>
			<b><?=$rows5;?></b><br><span>embl.</span>
		</center></div>
	</div>

	<div class="chart" data-percent="33" data-bar-color="#F64747">
		<div class="center"><center>
			<b><?=$rows6;?></b><br><span>usuários</span>
		</center></div>
	</div>

	<br>
</center></div>
<br>

<? // caso tenha o gerenciar_contas
if($permissoes[4] == 's' || $core->allAccess()) {
	$usuarios_entra = array();

	$sql11 = $conn->query("SELECT advert, nick, acesso_data FROM acp_usuarios WHERE ativado = 's'");
	while($sql12 = $sql11->fetch()) {
		if($sql12['advert'] >= 3) {
			echo aviso_red("O usuário <b>".$sql12['nick']."</b> possui <b>".$sql12['advert']."</b> advertências.");
		}

		if($sql12['acesso_data'] < strtotime("-1 week") && $sql12['acesso_data'] > 0) {
			$usuarios_entra[] = $sql12['nick'];
		}
	}

	if(count($usuarios_entra) > 0) {
		$usuarios_entra = implode(', ', $usuarios_entra);
		echo aviso_red("Os usuários <b>{$usuarios_entra}</b> não entram no painel de gerenciamento há mais de uma semana.");
	}
}

// caso tenha o destaques
if($permissoes[17] == 's' || $core->allAccess()) {
	$sql22 = $conn->query("SELECT * FROM destaques");
	$sql23 = $sql22->fetch();

	if($sql23['data'] < strtotime("-1 week")) {
		echo aviso_red("Os usuários destaque foram trocados há mais de uma semana... Acho que está na hora de trocar de novo, não?");
	}
}

// caso tenha o notícias - postar sem revisão
if($permissoes[21] == 's' || $core->allAccess()) {
	$sql26 = $conn->query("SELECT count(id) FROM noticias WHERE status='revisao'");
	$rows = $sql26->fetchColumn();

	if($rows > 0) {
		if($rows == 1) {
			echo aviso_red("Existe <b>{$rows}</b> notícia aguardando revisão (colunas ou notícias).");
		} else {
			echo aviso_red("Existem <b>{$rows}</b> notícias aguardando revisão (colunas ou notícias).");
		}
	}
}

// caso tenha o revisar artes
if($permissoes[36] == 's' || $core->allAccess()) {
	$sql27 = $conn->query("SELECT count(id) FROM pixel_artes WHERE status = 'aguardando'");
	$rows = $sql27->fetchColumn();

	if($rows > 0) {
		if($rows == 1) {
			echo aviso_yellow("Existe <b>{$rows}</b> arte aguardando aprovação.");
		} else {
			echo aviso_yellow("Existem <b>{$rows}</b> artes aguardando aprovação.");
		}
	}
}

// caso tenha o revisar vídeos
if($permissoes[45] == 's' || $core->allAccess()) {
	$sql27 = $conn->query("SELECT count(id) FROM videos WHERE status = 'aguardando'");
	$rows = $sql27->fetchColumn();

	if($rows > 0) {
		if($rows == 1) {
			echo aviso_yellow("Existe <b>{$rows}</b> vídeo aguardando aprovação.");
		} else {
			echo aviso_yellow("Existem <b>{$rows}</b> vídeos aguardando aprovação.");
		}
	}
}

// caso tenha o moderar no site
if($permissoes[25] == 's' || $core->allAccess()) {
	$sql27 = $conn->query("SELECT * FROM forum_topicos WHERE moderado = 'n'");
	while($sql28 = $sql27->fetch()) {
		$link = 'http://icehabbo.com.br/forum/topicos/' . $sql28['id'] . '-' . $core->trataurl($sql28['titulo']);
		$nome = $sql28['titulo'];
		echo aviso_yellow("O tópico <b><a href=\"$link\">$nome</a></b> ainda não foi moderado.");
	}
}

// caso tenha o denúncias do fórum
if($permissoes[57] == 's' || $core->allAccess()) {
	$sql27 = $conn->query("SELECT count(id) FROM forum_denuncias WHERE resolvido = 'n'");
	$rows = $sql27->fetchColumn();

	if($rows > 0) {
		if($rows == 1) {
			echo aviso_yellow("Existe <b>{$rows}</b> denúncia do fórum aguardando resposta.");
		} else {
			echo aviso_yellow("Existem <b>{$rows}</b> denúncias do fórum aguardando resposta.");
		}
	}
}

?>

<div class="warnings">
	<div class="box-content">
		Bem-vindo(a), <b><?=$dados['nick'];?></b><br><br>
		Seu último acesso ao painel de gerenciamento foi em <b><?=date('d/m/Y H:i:s', $_SESSION['acp_acesso_data']);?></b>.<br>
		Você possui <b><?=$dados['advert'];?></b> advertências.

		<? $query = "SELECT * FROM acp_usuarios_alertas WHERE lido='n' AND id_usuario='".$dados['id']."'";
		$sql24 = $conn->query($query);

		if($core->getRows($query) > 0) {
			echo '<br><br>';
		}

		while($sql25 = $sql24->fetch()) {
			$up_data['lido'] = 's';
			$wh_data['id'] = $sql25['id'];

			$up = $sqlActions->update("acp_usuarios_alertas", $up_data, $wh_data);

			$alerta  = '<b>Alerta:</b><br><br>';
			$alerta .= $sql25['alerta'];
			$alerta .= '<br><br>';
			$alerta .= "Enviado por <b>{$sql25['autor']}</b> em <b>".date('d/m/Y H:i', $sql25['data'])."</b>.";

			echo aviso_red('<div class="txt-left">' . $alerta . '</div>');
		} ?>
	</div>

	<div class="box-content">
		<div class="title-section">Avisos</div>

		<? $_rows = 0;
		$sql = $conn->query("SELECT * FROM acp_avisos WHERE status = 'ativo' ORDER BY id DESC");
		while($sql2 = $sql->fetch()) {
			$cargos_ler = explode('|', $sql2['cargos']);
			$result = array_intersect($cargos_ler, $cargos_user);
			$result = array_filter($result);

			if(count($result) > 0 || $sql2['cargos'] == 'all' || $core->allAccess()) {
				$_rows++;
			?>
		<div class="box-warning">
			<div class="well">
				<div id="img" style="background:url(https://www.habbo.com.br/habbo-imaging/avatarimage?img_format=gif&user=<?=$sql2['autor'];?>&action=std&direction=2&head_direction=3&gesture=sml&size=b) -10px -14px;"></div>
				<div id="infos">Por <b><?=$sql2['autor'];?></b> há <b><?=strtolower($core->dTime($sql2['data'], time(), true));?></b><br><br></div>

				<br><hr>

				<center><b style="font-size:18px;"><?=$core->clear($sql2['titulo']);?></b></center>

				<?=$sql2['conteudo'];?>

				<br><br>

				<? $rows = $core->getRows("SELECT * FROM acp_avisos_lido WHERE id_usuario='".$dados['id']."' AND id_aviso='".$sql2['id']."'");
				if($rows == 0) { ?>
				<button id="warn-read-<?=$sql2['id'];?>" class="btn btn-primary f-right" onclick="warnRead(<?=$sql2['id'];?>)">Marcar como lido</button>
				<? } else { ?>
				<button class="btn btn-primary f-right" disabled="disabled">Você já leu este aviso.</button>
				<? } ?>

				<br>
				<? $usuarios_leram = array();
				$sql14 = $conn->prepare("SELECT * FROM acp_avisos_lido WHERE id_aviso = ?");
				$sql14->bindValue(1, $sql2['id']);
				$sql14->execute();
				while($sql15 = $sql14->fetch()) {
					$sql16 = $conn->prepare("SELECT nick FROM acp_usuarios WHERE id = ?");
					$sql16->bindValue(1, $sql15['id_usuario']);
					$sql16->execute();
					$sql17 = $sql16->fetch();

					if($sql17) {
						$usuarios_leram[] = $sql17['nick'];
					}
				}

				if(count($usuarios_leram) > 0) { echo '<br>'; }

				foreach ($usuarios_leram as $atual) {
					echo '<div id="img" class="tip-2" data-tip="'.$atual.'" style="background:url(https://www.habbo.com.br/habbo-imaging/avatarimage?img_format=gif&user='.$atual.'&action=std&direction=3&head_direction=3&gesture=sml&size=b)  -10px -14px;"></div>';
				}

				if(count($usuarios_leram) > 0) { echo '<br>'; }

				?>

			</div>
		</div>
		<? } }

		if($_rows == 0) {
			echo aviso_yellow("Nenhum aviso foi emitido pela administração.");
		} ?>
	</div>
</div>

<div class="notifications">
<div class="box-content">
		<div class="title-section">Agenda do mês</div>

		<?

		function build_calendar($month,$year,$dateArray,$conn) {
			$daysOfWeek = array('D','S','T','Q','Q','S','S');
			$firstDayOfMonth = mktime(0,0,0,$month,1,$year);
			$numberDays = date('t',$firstDayOfMonth);

			$dateComponents = getdate($firstDayOfMonth);
			$monthName = $dateComponents['month'];
			$dayOfWeek = $dateComponents['wday'];

			$calendar = "<table class='calendar table table-striped table-hover'>";
			$calendar .= "<tr>";

			foreach($daysOfWeek as $day) {
				$calendar .= "<th class='header' style='text-align:center;'>$day</th>";
			}

			$currentDay = 1;
			$calendar .= "</tr><tr>";

			if ($dayOfWeek > 0) {
				$calendar .= "<td colspan='$dayOfWeek'>&nbsp;</td>";
			}

			$month = str_pad($month, 2, "0", STR_PAD_LEFT);

			while ($currentDay <= $numberDays) {
				if ($dayOfWeek == 7) {
					$dayOfWeek = 0;
					$calendar .= "</tr><tr>";

				}

				$currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);

				$date = "$year-$month-$currentDayRel";

				($currentDay == date('d')) ? $classes = ' success' : $classes = '';

				$meia_noite = mktime(0, 0, 0, date('n'), $currentDay, date('Y'));
				$meia_noite_limite = strtotime('+24 hours', $meia_noite);

				$sql = $conn->prepare("SELECT count(id) FROM acp_agenda WHERE data_agendado > ? AND data_agendado < ?");
				$sql->bindValue(1, $meia_noite);
				$sql->bindValue(2, $meia_noite_limite);
				$sql->execute();
				$rows = $sql->fetchColumn();

				($currentDay == date('d')) ? $classes = ' success' : $classes = '';
				($rows > 0) ? $classes = ' info' : '';

				$calendar .= "<td class='day$classes' rel='$date' style='text-align:center;'><a href='?v=2&d=$currentDay'>$currentDay</a></td>";

				$currentDay++;
				$dayOfWeek++;
			}

			if ($dayOfWeek != 7) {

				$remainingDays = 7 - $dayOfWeek;
				$calendar .= "<td colspan='$remainingDays'>&nbsp;</td>";

			}

			$calendar .= "</tr>";
			$calendar .= "</table>";

			return $calendar;

		}

		$dateComponents = getdate();

		$month = $dateComponents['mon'];
		$year = $dateComponents['year'];

		echo build_calendar($month,$year,$dateArray,$conn); ?>
	</div>

	<div class="box-content">
		<div class="title-section">Importante!</div>

		<?=$config['acp_aviso_fixo'];?>
	</div>

	<div class="box-content">
		<div class="title-section">Notificações</div>

		<? $data_limite = strtotime("-3 days");
		$query = "SELECT * FROM acp_notificacoes WHERE data > $data_limite ORDER BY id DESC LIMIT 50";
		$sql9 = $conn->query($query);
		while($sql10 = $sql9->fetch()) {
			if($sql10['tipo'] == 'success') { $icon = 'check'; }
			if($sql10['tipo'] == 'info') { $icon = 'info-circled'; }
			if($sql10['tipo'] == 'warning') { $icon = 'attention-alt'; }
			if($sql10['tipo'] == 'danger') { $icon = 'attention'; }
			?>
		<div class="box-ntf <?=$sql10['tipo'];?>">
			<div id="icon"><i class="icon-<?=$icon;?>"></i></div>
			<div id="infos"><?=$core->clear($sql10['texto']);?> <div class="time"><?=strtolower($core->dTime($sql10['data'], time(), true));?></div></div>
			<br>
		</div>
		<? }

		if($core->getRows($query) == 0) {
			echo '<center>Não há notificações.</center>';
		} ?>
	</div>

	<div class="box-content">
		<div class="title-section">Black List</div>

		Os usuários da Black List são proibidos de participar de qualquer evento ou promoção na IceHabbo.<br><br>
		<? $query = "SELECT * FROM acp_blist WHERE status = 'ativo' ORDER BY id DESC";
		$sql18 = $conn->query($query);
		while($sql19 = $sql18->fetch()) { ?>
		<div class="box-warning">
			<div class="well">
				<div id="img" style="background:url(https://www.habbo.com.br/habbo-imaging/avatarimage?img_format=gif&user=<?=$sql19['nick'];?>&action=std&direction=2&head_direction=3&gesture=sml&size=b) -10px -14px;"></div>
				<div id="infos"><b><?=$sql19['nick'];?></b> - desde <b><?=strtolower($core->dTime($sql19['data'], time(), true));?></b><br><br></div>

				<br><hr>

				<?=$sql19['motivo'];?>
			</div>
		</div>
		<? }
		if($core->getRows($query) == 0) {
			echo aviso_blue("Não há usuários na Black List.");
		} ?>
	</div>

	<div class="box-content">
		<div class="title-section">Usuários online no painel</div>

		<? $sql5 = $conn->query("SELECT * FROM acp_online ORDER BY tempo DESC");
		while($sql6 = $sql5->fetch()) {
			$sql7 = $conn->query("SELECT nick FROM acp_usuarios WHERE id='".$sql6['id_usuario']."' LIMIT 1");
			$sql8 = $sql7->fetch();

			echo '<span class="label label-primary">' . $core->clear($sql8['nick']) . '</span>';
		} ?>
	</div>

	<div class="box-content">
		<div class="title-section">Últimos membros na equipe</div>

		<? $sql3 = $conn->query("SELECT id, nick, data FROM acp_usuarios ORDER BY id DESC LIMIT 10");
		while($sql4 = $sql3->fetch()) {
			if($sql4['id'] == 1 || $sql4['id'] == 2) {
				echo '<b>' . $core->clear($sql4['nick']) . '</b> - Entrou em: <b>-</b><br>';
			} else {
				echo '<b>' . $core->clear($sql4['nick']) . '</b> - Entrou em: <b>' . date('d/m/Y H:i', $sql4['data']) . '</b><br>';
			}
		} ?>
	</div>
</div>