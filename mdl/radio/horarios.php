<div class="box-content">
	<div class="title-section"><?=$mdl['nome'];?></div>

	<?

	if($_GET['a'] == 2) {
		$id = $_GET['id'];

		$hora = addslashes($_GET["hora"]);
		$dia = addslashes($_GET["dia"]);

		$sql = $conn->query("SELECT * FROM horarios WHERE hor_dia='$dia' AND hor_hora='$hora'");
		$res = $sql->fetch();


		if($res['usr_id'] == 0){
			$sql2 = $conn->query("UPDATE horarios SET usr_id='".$_SESSION['acp_id']."' WHERE hor_dia='$dia' AND hor_hora='$hora'");

			$core->logger("O usuário marcou um horário na rádio (dia: $dia, hora: $hora).", "acao");

			if($sql2){
				$form_return .= aviso_green("Horário marcado com sucesso!");
			}
		} else {
			$form_return .= aviso_red("Este horário já está marcado.");
		}
	}

	if($_GET['a'] == 3) {
		$id = $_GET['id'];

		$hora = addslashes($_GET["hora"]);
		$dia = addslashes($_GET["dia"]);

		$sql = $conn->query("SELECT * FROM horarios WHERE hor_dia='$dia' AND hor_hora='$hora'");
		$res = $sql->fetch();

		if( ($res) && ($permissoes[107] == 's' || $_SESSION['acp_id'] == $res['usr_id']) ) {

			if($res['usr_id'] > 0){
				$sql2 = $conn->query("UPDATE horarios SET usr_id='0' WHERE hor_dia='$dia' AND hor_hora='$hora'");

				$core->logger("O usuário desmarcou um horário na rádio (dia: $dia, hora: $hora).", "acao");

				if($sql2){
					$form_return .= aviso_green("Horário desmarcado com sucesso!");
				}
			} else {
				$form_return .= aviso_red("Este horário não está marcado.");
			}
		} else {
			$form_return .= aviso_red("Você não tem permissão para desmarcar este horário.");
		}
	}

	echo $form_return;

	?>

	<? $dia = ($_GET['dia'])?$_GET['dia']:"7";
	function traduz_dia($x){
		switch($x){
			case "7" : return "Domingo";
			break;
			case "1" : return "Segunda-Feira";
			break;
			case "2" : return "Ter&ccedil;a-Feira";
			break;
			case "3" : return "Quarta-Feira";
			break;
			case "4" : return "Quinta-Feira";
			break;
			case "5" : return "Sexta-Feira";
			break;
			case "6" : return "S&aacute;bado";
			break;
		}
	}

	echo "<center>";
	$i = 0;
	while($i<=7){
		if($i != 1){ echo " | ";}
		if($dia==$i){
			echo "<a href='?p={$_GET['p']}&dia=$i'><b><i>".traduz_dia($i)."</i></b></a>";
		}else{
			echo "<a href='?p={$_GET['p']}&dia=$i'>".traduz_dia($i)."</a>";
		}
		$i++;
	}
	echo "</center>";

	$query = "SELECT * FROM horarios h WHERE h.hor_dia='$dia' ORDER BY h.hor_hora";
	$sql = $conn->query($query);
	$res = $sql->fetchAll();

	$table = new Table('', true, $core->allAccess());
	$table->head(array('Horário', 'Locutor', 'Programa'));

	$table->startBody();

	foreach ($res as $atual) {
		$hora = str_pad($atual['hor_hora'], 2, "0", STR_PAD_LEFT);

		if($atual['usr_id']){
			$sql_u = $conn->prepare("SELECT * FROM acp_usuarios WHERE id = ?");
			$sql_u->bindValue(1, $atual['usr_id']);
			$sql_u->execute();
			$row_u = $sql_u->fetch();
		}
		if(!$row_u){
			$locutor = "<a href='?p={$_GET['p']}&a=2&dia=$dia&hora={$atual['hor_hora']}'>Marcar horário</a>";
			$programa = $locutor;
		}elseif($_SESSION['acp_id'] == $atual['usr_id']){
			$locutor = "<u>{$row_u['nick']}<u>";
			$programa = "<u>{$row_u['programa']}<u>";
		}else{
			$locutor = $row_u['nick'];
			$programa = $row_u['programa'];
		}

		if( ($atual['usr_id'] && $row_u) && ($permissoes[107] == 's' || $_SESSION['acp_id'] == $atual['usr_id']) ) {
			$locutor = "<a href='?p={$_GET['p']}&a=3&dia=$dia&hora={$atual['hor_hora']}'><u>{$row_u['nick']}<u> [Desmarcar horário]</a>";
			$programa = "<a href='?p={$_GET['p']}&a=3&dia=$dia&hora={$atual['hor_hora']}'>{$row_u['programa']}</a>";
		}

		$a = str_pad($atual['hor_hora'], 2, "0", STR_PAD_LEFT).":00 ~ ".(str_pad($atual['hor_hora']+1, 2, "0", STR_PAD_LEFT)) .":00";

		$table->insertBody(array($a, $locutor, $programa), 'ativo', true);

		unset($row_u);
		unset($locutor);
		unset($programa);
	}

	$table->closeTable();
	echo $table->table;

	?>
</div>