<?php

/**
 * IceHabbo
 * by Henrique Arthur <eu@henriquearthur.me>
 * Não use sem autorização.
 */

if(strpos($_SERVER['SERVER_NAME'], 'ice.localhost') !== false) {
	$conn_servidor  = '127.0.0.1';
	$conn_usuario   = 'root';
	$conn_senha     = 'root';

	$conn_db        = 'icehb';

	define("DOMAIN", "http://ice.localhost");
	define("LOCAL", "local");
} else {
	$conn_servidor  = '127.0.0.1';
	$conn_usuario   = 'root';
	$conn_senha     = 'TByRPJbepZkUybuyALQTRaZlcIXuhZtF';

	$conn_db        = 'icehabbo';

	define("DOMAIN", "http://icehabbo.com.br");
	define("LOCAL", "web");
}

try {
    $conn = new PDO("mysql:host=$conn_servidor;dbname=$conn_db", $conn_usuario, $conn_senha);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $conn->exec("set names utf8");
} catch (PDOException $e) {
    echo $e->getMessage();
}

define("MAIN_DIR", $_SERVER['DOCUMENT_ROOT'] . '/');

if (get_magic_quotes_gpc()) {
	function stripslashes_deep($value)
	{
		$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
		return $value;
	}

	$_POST = array_map('stripslashes_deep', $_POST);
	$_GET = array_map('stripslashes_deep', $_GET);
	$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
	$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}
