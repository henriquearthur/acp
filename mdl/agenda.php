<?php

$dia = $_GET['d'];

$meia_noite = mktime(0, 0, 0, date('n'), $dia, date('Y'));
$meia_noite_limite = strtotime('+24 hours', $meia_noite);

$sql = $conn->prepare("SELECT * FROM acp_agenda WHERE data_agendado > ? AND data_agendado < ?");
$sql->bindValue(1, $meia_noite);
$sql->bindValue(2, $meia_noite_limite);
$sql->execute();
$sql2 = $sql->fetchAll();

if(count($sql2) > 0) { ?>
	<div class="box-content">
		<div class="title-section">Agendamentos do dia <?=$dia;?>/<?=date('n');?></div>

		<? foreach ($sql2 as $atual) { ?>
		<div class="well">
			Nome: <b><?=$atual['nome'];?></b><br>
			Informações: <b><?=$atual['infos'];?></b><br><br>
			Agendado por: <b><?=$atual['autor'];?></b>
		</div>
		<? } ?>
	</div>
<? } else {
	echo aviso_red("Não há eventos agendados para este dia.");
}