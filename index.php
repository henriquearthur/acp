<?php

/**
 * IceHabbo
 * by Henrique Arthur <eu@henriquearthur.me>
 * Não use sem autorização.
 */

session_start();

include "lib/config.php";
include "lib/functions.php";

if(isset($_SESSION['login'])) {
	$core->logger('O usuário entrou no painel de controle. (sessão já aberta)', 'acesso', $_SESSION['login']);
	header("Location: admin.php");
}

if(!empty($_POST)) {
	$usuario = $core->clear($_POST['usuario']);
	$senha = $core->clear($_POST['senha']);
	$prosseguir = true;

	if(empty($usuario) || empty($senha)) {
		$form_return = 'Preencha todos os campos.';
		$prosseguir = false;
	}

	if($prosseguir) {
		$sql = $conn->prepare("SELECT * FROM acp_usuarios WHERE nick = ?");
		$sql->bindValue(1, $usuario);
		$sql->execute();
		$sql2 = $sql->fetch();

		if(!$sql2) {
			$form_return = 'Conta não encontrada.';
			$prosseguir = false;
		} else {
			if(md5($senha) != $sql2['senha']) {
				$form_return = 'Nick ou senha inválidos.';
				$prosseguir = false;
			}

			if($sql2['ativado'] == 'n') {
				$form_return = 'Conta desativada.';
				$prosseguir = false;
			}
		}
	}

	if($prosseguir) {
		$_SESSION['acp_id'] = $sql2['id'];
		$_SESSION['login'] = $core->clear($sql2['nick']);
		$_SESSION['acp_senha'] = $sql2['senha'];
		$_SESSION['acp_acesso_data'] = $sql2['acesso_data'];
		$_SESSION['acp_acesso_ip'] = $sql2['acesso_ip'];

		$core->logger('O usuário entrou no painel de controle.', 'acesso', $sql2['nick']);
		$sql3 = $conn->query("UPDATE acp_usuarios SET acesso_data='$timestamp', acesso_ip='$ip' WHERE id='".$sql2['id']."' LIMIT 1");

		header("Location: admin.php");
	}
}

/*
$senha = "";
echo $hash = crypt($senha, '$2a$' . $core->crypt_custo . '$' . $core->crypt_salt . '$');
*/

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<meta name="language" content="pt-br">
	<meta name="robots" content="noindex, nofollow">

	<title>IceHabbo - Painel de gerenciamento</title>

	<link rel="icon" href="/favicon.png">
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600">
	<link rel="stylesheet" href="assets/css/login.css">
</head>

<body>
	<div id="logo"></div>

	<div class="page">
		<form action="<?=$_SERVER['REQUEST_URI'];?>" method="post">
			<div id="box-login">
				<?=((!empty($form_return))) ? "<div class='form-status'>$form_return</div>" : "";?>
				<input type="text" name="usuario"<?=((empty($_SESSION['nick']))) ? ' autofocus' : '';?> class="input user" placeholder="Usuário" value="<?=$_SESSION['nick'];?>">
				<input type="password" name="senha"<?=((!empty($_SESSION['nick']))) ? ' autofocus' : '';?> class="input pass" placeholder="Senha">
			</div>

			<button type="submit" id="submit">Conectar</button>
		</form>

		<footer class="copyright"><a href="/">icehabbo.com.br</a></footer>
	</div>
</body>
</html>
