<?php

/**
 * IceHabbo - by Henrique Arthur <eu@henriquearthur.me>
 * Não use sem autorização.
 */

session_start();
session_write_close();

if(!isset($_SESSION['login'])) { die(); }

include "config.php";
include "functions.php";

$id = $_GET['id'];

$sql = $conn->prepare("SELECT count(id) FROM acp_avisos_lido WHERE id_aviso = ? AND id_usuario = ? LIMIT 1");
$sql->bindValue(1, $id);
$sql->bindValue(2, $dados['id']);
$sql->execute();
$rows = $sql->fetchColumn();

if($rows == 0) {
	$id_usuario = $dados['id'];

	$insert_data['id_aviso'] = $id;
	$insert_data['id_usuario'] = $id_usuario;
	$insert_data['ip'] = $ip;
	$insert_data['data'] = $timestamp;

	$sqlActions->insert("acp_avisos_lido", $insert_data);
}