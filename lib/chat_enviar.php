<?php

/**
 * IceHabbo
 * by Henrique Arthur <eu@henriquearthur.me>
 * Não use sem autorização.
 */

session_start();

if(!isset($_SESSION['login'])) { die(); }

include "config.php";
include "functions.php";

$msg = $core->clear($_POST['msg']);
$prosseguir = true;

if($msg == '') {
	$retorno = 'Pelo menos digita uma mensagem né, fera?';
	$prosseguir = false;
}

if($_SESSION['last_chat_msg'] > time() - 1) {
	$retorno = 'Ei mano, sem flood ai!' . $data_first;
	$prosseguir = false;
}

$first_word = explode(" ", $msg);

if($first_word[0] == "/limpar" && $permissoes[4] == 's') {
	$sql4 = $conn->query("TRUNCATE acp_chat");
	$sql5 = $conn->query("ALTER TABLE acp_chat AUTO_INCREMENT = 1;");

	$retorno = 'Chat limpo com sucesso!';
	$prosseguir = false;
}

if($first_word[0] == "/onlines") {
	$onlines = '';
	$number = 0;

	$sql6 = $conn->query("SELECT * FROM acp_usuarios ORDER BY id DESC");
	while($sql7 = $sql6->fetch()) {
		if(time() - $sql7['chat_activity'] < 40) {
			$onlines .= '<b>'.$sql7['nick'].'</b> - última atividade: há ' . strtolower($core->dTime($sql7['chat_activity'], time(), true)) . '<br>';
			$number++;
		}
	}

	$retorno = '<b>'.$number.'</b> usuários online: <br><br>' . $onlines;
	$prosseguir = false;
}

if($prosseguir) {
	$insert_data['nick'] = $autor;
	$insert_data['msg'] = $msg;
	$insert_data['data'] = $timestamp;
	$insert_data['ip'] = $ip;

	$insert = $sqlActions->insert("acp_chat", $insert_data);
	$retorno = 'success';

	$_SESSION['last_chat_msg'] = time();
}

echo $retorno;