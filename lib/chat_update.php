<?php

/**
 * IceHabbo
 * by Henrique Arthur <eu@henriquearthur.me>
 * Não use sem autorização.
 */

session_start();
session_write_close();

if(!isset($_SESSION['login'])) { die(); }

include "config.php";
include "functions.php";

$sql = $conn->query("UPDATE acp_usuarios SET chat_activity='$timestamp' WHERE nick='$autor' LIMIT 1");

$last = $_GET['last'];
$msgs = array();
$retorno['cleaned'] = false;

$query = "SELECT * FROM acp_chat";

if($core->getRows($query) == 0) {
	$retorno['cleaned'] = true;
}

$sql2 = $conn->prepare("SELECT * FROM acp_chat WHERE id > ? ORDER BY id DESC");
$sql2->bindValue(1, $last);
$sql2->execute();
while($sql3 = $sql2->fetch()) {
	$msgs[] = array(
		"id" => $sql3['id'],
		"nick" => $core->clear($sql3['nick']),
		"msg" => $core->linksClickable($core->clear($sql3['msg'])),
		"data" => date('d/m/Y H:i:s', $sql3['data'])
	);
}

$retorno['msgs'] = $msgs;
echo json_encode($retorno);