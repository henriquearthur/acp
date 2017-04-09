<?php

/**
 * IceHabbo
 * by Henrique Arthur <eu@henriquearthur.me>
 * Não use sem autorização.
 */

date_default_timezone_set("America/Sao_Paulo");

if(LOCAL == 'local') {
	ini_set("display_errors", true);
	ini_set('html_errors', true);

	error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR);
} else {
	ini_set("display_errors", false);
	ini_set('html_errors', false);
}

if(isset($_GET['errors'])) {
	echo "<span style='color:red;'>O modo para visualizacao de erros está habilitado.</span><br><br>";

	ini_set("display_errors", true);
	ini_set('html_errors', true);

	error_reporting(E_ALL);
}

class Core {
	private $salt = '5/N!V+"r[\Y$t8n';

	private $conn;
	public $autor;
	public $timestamp;
	public $ip;
	public $user_agent;
	public $geo_loc;
	public $url_unica;

	/**
	 * Construtor
	 * @param object $conn conexão PDO com o banco de dados
	 */
	public function __construct($conn) {
		$this->conn = $conn;
		$this->timestamp = time();

		if(!empty($_SESSION['login'])) {
			$this->autor = $_SESSION['login'];
		} else {
			if(!empty($_SESSION['nick'])) {
				$this->autor = $_SESSION['nick'];
			} else {
				$this->autor = 'unlogged';
			}
		}

		$_SERVER['REMOTE_ADDR'] = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"];

		if(isset($_SERVER["HTTP_CF_IPCOUNTRY"])) {
			$this->geoloc = $_SERVER["HTTP_CF_IPCOUNTRY"];
		} else {
			$this->geoloc = 'not-CF';
		}

		$this->ip = $_SERVER['REMOTE_ADDR'];
		$this->user_agent = $_SERVER['HTTP_USER_AGENT'];

		// Caso esteja no painel
		if(strpos($_SERVER['REQUEST_URI'], 'acp') !== false) {
			$this->url_unica = 'p='.$_GET["p"].'&a='.$_GET["a"].'&id='.$_GET["id"];
		} else {
			$this->url_unica = $_SERVER['REQUEST_URI'];
		}
	}

	/**
	 * Escapar HTML
	 * @param  string $string string a ser limpa
	 * @return string         string limpa
	 */
	public function clear($string) {
		$string = preg_replace ('/<[^>]*>/', ' ', $string);


		return $string;
	}

	/**
	 * Escapar string para incorporar no Javascript
	 * @param  string $string string a ser escapada
	 * @return string         string escapada
	 */
	public function clearJS($string) {
		$string = addslashes($string);
		$string = trim(preg_replace('/\s+/', ' ', $string));
		return $string;
	}

	/**
	 * Limpar URL de imagem para entrar no background-image
	 * @param  string $string url da imagem
	 * @return string         url da imagem escapada
	 */
	public function clearImg($string) {
		$string = htmlspecialchars_decode($string);
		$string = strip_tags($string);
		$string = addslashes($string);

		return $string;
	}

	/**
	 * Encurtar e adicionar reticências a uma string
	 * @param  string $conteudo string a ser limpa
	 * @param  int    $max      número máximo de caracteres
	 * @return string           string encurtada e com reticências
	 */
	public function encurtar($conteudo, $max) {
		$conteudo = strlen($conteudo) > $max ? substr($conteudo,0,$max) . "..." : $conteudo;

		return $conteudo;
	}

	/**
	 * Transformar uma string para ser usada em URLs
	 * @param  string $string string a ser transformada
	 * @return string         string transformada
	 */
	public function trataURL($string, $delimiter = '-') {
		setlocale(LC_ALL, 'en_US.UTF8');

		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

		return $clean;
	}

	/**
	 * Distância de tempo entre um timestamp e outro por extenso
	 * @param  integer $fromTime            timestamp 1 (origem)
	 * @param  integer $toTime              timestamp 2 (final)
	 * @param  boolean $showLessThanAMinute se mostra menos de um minuto ou não
	 * @return string                       o tempo por extenso
	 */
	public function dTime($fromTime, $toTime = 0, $showLessThanAMinute = false) {
		$distanceInSeconds = round(abs($toTime - $fromTime));
		$distanceInMinutes = round($distanceInSeconds / 60);

		if ( $distanceInMinutes <= 1 ) {
			if ( !$showLessThanAMinute ) {
				return ($distanceInMinutes == 0) ? 'menos de 1m' : '1 min';
			} else {
				if ( $distanceInSeconds < 5 ) {
					return ($distanceInSeconds + 1).'s';
				}
				if ( $distanceInSeconds < 10 ) {
					return 'Menos de 10s';
				}
				if ( $distanceInSeconds < 20 ) {
					return 'Menos de 20s';
				}
				if ( $distanceInSeconds < 40 ) {
					return 'Meio min';
				}
				if ( $distanceInSeconds < 60 ) {
					return 'Menos de um min';
				}

				return '1 min';
			}
		}
		if ( $distanceInMinutes < 45 ) {
			return $distanceInMinutes . ' mins';
		}
		if ( $distanceInMinutes < 90 ) {
			return '1 hora';
		}
		if ( $distanceInMinutes < 1440 ) {
			return '' . round(floatval($distanceInMinutes) / 60.0) . ' horas';
		}
		if ( $distanceInMinutes < 2880 ) {
			return '1 dia';
		}
		if ( $distanceInMinutes < 43200 ) {
			return '' . round(floatval($distanceInMinutes) / 1440) . ' dias';
		}
		if ( $distanceInMinutes < 86400 ) {
			return '1 mês';
		}
		if ( $distanceInMinutes < 525600 ) {
			return round(floatval($distanceInMinutes) / 43200) . ' meses';
		}
		if ( $distanceInMinutes < 1051199 ) {
			return '1 ano';
		}

		return strtolower(round(floatval($distanceInMinutes) / 525600) . ' anos');
	}

	/**
	 * Deixar os links de uma string clicáveis
	 * @param  string $string string a ser verificada
	 * @return string         string com links clicáveis
	 */
	public function linksClickable($string) {
		$string = preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1">$1</a>', $string);

		return $string;
	}

	/**
	 * Registrar uma ação feita pelo usuário
	 * @param  string $ato  o que o usuário fez
	 * @param  string $tipo se é uma ação ou acesso
	 * @param  string $nick nick do usuário
	 * @return boolean      se foi concluído
	 */
	public function logger($ato, $tipo, $nick = '') {
		if($nick == '') {
			$autt = $this->autor;
		} else {
			$autt = $nick;
		}

		$sql = $this->conn->prepare("INSERT INTO acp_logs (ato, tipo, ip, u_agent, autor, data, geoloc, url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
		$sql->bindValue(1, $ato);
		$sql->bindValue(2, $tipo);
		$sql->bindValue(3, $this->ip);
		$sql->bindValue(4, $this->user_agent);
		$sql->bindValue(5, $autt);
		$sql->bindValue(6, $this->timestamp);
		$sql->bindValue(7, $this->geoloc);
		$sql->bindValue(8, $this->url_unica);

		if($sql->execute()) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Verifica se o usuário possui determinado cargo
	 * @param  string  $cargo o cargo a ser procurado
	 * @param  string  $nick  o usuário a ser procurado
	 * @return boolean        se tem o cargo ou não
	 */
	public function hasCargo($cargo, $nick) {
		if(is_numeric($nick)) {
			$sql = $this->conn->prepare("SELECT * FROM acp_usuarios WHERE id = ? LIMIT 1");
		} else {
			$sql = $this->conn->prepare("SELECT * FROM acp_usuarios WHERE nick = ? LIMIT 1");
		}

		$sql->bindValue(1, $nick);
		$sql->execute();

		$sql2 = $sql->fetch();
		$cargos = explode('|', $sql2['cargos']);

		if(in_array($cargo, $cargos)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Verifica se o usuário está na página da equipe em determinado cargo
	 * @param  string  $cargo o cargo a ser procurado
	 * @param  string  $nick  o usuário a ser procurado
	 * @return boolean        se está na página ou não
	 */
	public function hasCargoE($cargo, $nick) {
		if(is_numeric($nick)) {
			$sql = $this->conn->prepare("SELECT * FROM acp_usuarios WHERE id = ? LIMIT 1");
		} else {
			$sql = $this->conn->prepare("SELECT * FROM acp_usuarios WHERE nick = ? LIMIT 1");
		}

		$sql->bindValue(1, $nick);
		$sql->execute();

		$sql2 = $sql->fetch();
		$cargos = explode('|', $sql2['cargos_e']);

		if(in_array($cargo, $cargos)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Verifica se o usuário possui acesso total (cargo #1 ou #2)
	 * @return boolean se tem ou não
	 */
	public function allAccess() {
		$sql = $this->conn->query("SELECT * FROM acp_usuarios WHERE nick = '".$this->autor."' LIMIT 1");
		$sql2 = $sql->fetch();

		if($this->hasCargo(1, $sql2['nick'])) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Obter nome do módulo através do ID
	 * @param  integer $id id do módulo
	 * @return string      nome do módulo
	 */
	public function getMdlName($id) {
		$sql = $this->conn->query("SELECT nome FROM acp_modulos WHERE id='$id' LIMIT 1");
		$sql2 = $sql->fetch();

		return $sql2['nome'];
	}

	/**
	 * Obter id do módulo através do nome
	 * @param  string $nome nome do módulo
	 * @return integer      id do módulo
	 */
	public function getMdlId($nome) {
		$sql = $this->conn->query("SELECT id FROM acp_modulos WHERE nome='$nome' LIMIT 1");
		$sql2 = $sql->fetch();

		return $sql2['id'];
	}

	/**
	 * Obter nome do cargo através do ID
	 * @param  string $id id do cargo
	 * @return integer    nome do cargo
	 */
	public function getCargoName($id) {
		$sql = $this->conn->query("SELECT nome FROM acp_cargos WHERE id='$id' LIMIT 1");
		$sql2 = $sql->fetch();

		return $sql2['nome'];
	}


	/**
	 * Enviar notificação para o painel
	 * @param  string $texto notificação a ser enviada
	 * @param  string $tipo  tipo de notificação
	 * @return boolean       se enviou ou não
	 */
	public function sendNtf($texto, $tipo) {
		$texto = $this->clear($texto);

		$sql = $this->conn->prepare("INSERT INTO acp_notificacoes (texto, tipo, autor, data) VALUES (?, ?, ?, ?)");
		$sql->bindValue(1, $texto);
		$sql->bindValue(2, $tipo);
		$sql->bindValue(3, $this->autor);
		$sql->bindValue(4, $this->timestamp);

		if($sql->execute()) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Obtem o número de linhas de uma query SELECT
	 * @param  string $query a query
	 * @return boolean       o número de linhas ou FALSE se não for uma query SELECT
	 */
	public function getRows($query) {
		$arr = explode(" ", trim($query));

		if($arr[0] == 'SELECT') {
			$start = 'SELECT';
			$end =  'FROM';
			$replace_with = ' count(id) ';
			$query = preg_replace('#('.preg_quote($start).')(.*)('.preg_quote($end).')#si', '$1'.$replace_with.'$3', $query);

			$arr = explode(" ", trim($query));

			if($arr[count($arr)-2] == 'LIMIT') {
				array_splice($arr, -2);
				$query = implode(" ", $arr);
			}

			$rows = $this->conn->query($query)->fetchColumn();

			return $rows;
		} else {
			return false;
		}
	}

	/**
	 * Deslogar um usuário do site
	 * @return boolean sempre true
	 */
	public function deslogarSite() {
		unset($_SESSION['id']);
		unset($_SESSION['nick']);
		unset($_SESSION['senha']);
		unset($_SESSION['acesso_data']);
		unset($_SESSION['acesso_ip']);


		unset($_COOKIE['id-user-connected-1']);
		unset($_COOKIE['id-user-connected-2']);
		setcookie('id-user-connected-1', null, -1, '/');
		setcookie('id-user-connected-2', null, -1, '/');

		return true;
	}

	/**
	 * Deslogar um usuário do painel
	 * @return boolean sempre true
	 */
	public function deslogarPainel() {
		unset($_SESSION['acp_id']);
		unset($_SESSION['login']);
		unset($_SESSION['acp_senha']);
		unset($_SESSION['acp_acesso_data']);
		unset($_SESSION['acp_acesso_ip']);

		return true;
	}

	/**
	 * Obter a missão de um Habbo
	 * @param  string $nick nick do usuário
	 * @return string       missão do usuário
	 */
	public function getMotto($nick) {
		$url = 'https://www.habbo.com/api/public/users?name='.$nick.'&site=hhbr';

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.85 Safari/537.36');
		$data = curl_exec($ch);
		curl_close ($ch);

		$data = json_decode($data);
		$missao = $data->motto;

		echo 'Missão ($nick): ' . $missao . '<br>';

		if(empty($data) || empty($missao)) {
			$url = 'http://www.habbo.com.br/habblet/habbosearchcontent?searchString=' . $nick;
			$ip_req = '31.220.107.98';

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_COOKIE, "YPF8827340282Jdskjhfiw_928937459182JAX666=" . $ip_req);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$data = curl_exec($ch);
			curl_close ($ch);

			$missao = explode('<div class="item">', $data);
			$missao = explode('<br />', $missao[1]);
			$missao = explode('</div>', $missao[1]);
			$missao = trim($missao[0]);
		}

		return $missao;

		/*
		$url = 'http://www.habbo.com.br/habblet/habbosearchcontent?searchString=' . $nick;
		$ip_req = '177.55.108.130';  // Host apontada pelo Cloudflare

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_COOKIE, "YPF8827340282Jdskjhfiw_928937459182JAX666=" . $ip_req);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($ch);
		curl_close ($ch);

		$missao = explode('<div class="item">', $data);
		$missao = explode('<br />', $missao[1]);
		$missao = explode('</div>', $missao[1]);
		$missao = trim($missao[0]);

		return $missao; */

		/*

		$url = 'https://www.habbo.com/api/public/users?name='.$nick.'&site=hhbr';

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($ch);
		curl_close ($ch);
		return $data;

		$data = json_decode($data);
		$missao = $data->motto;

		return $missao;

		 */
	}

	/**
	 * Obter status de uma rádio shoutcast
	 * @param  string  $ip      ip da rádio
	 * @param  integer $porta   porta da rádio
	 * @param  integer $request o que está sendo pedido
	 * @return array            informações
	 */
	public function getShoutStatus($ip, $porta, $request) {
		/**
		 * Request
		 * 1 - Locutor e programa
		 * 2 - Ouvintes
		 * 3 - Locutor, programa e ouvintes
		 */

		if($request == 1 || $request == 3) {
			$fp = fsockopen($ip, $porta, $errno, $errstr, 15);

			if($fp) {
				fputs($fp,"GET /index.html HTTP/1.0\r\nUser-Agent: XML Getter (Mozilla Compatible)\r\n\r\n");
				while(!feof($fp)) { $pg .= fgets($fp, 1000); }
				fclose($fp);

				$locutor = preg_replace("(.*)<font class=default>Stream Title: </font></td><td><font class=default><b>", "", $pg);
				$locutor = preg_replace("</b></td></tr><tr><td width=100 nowrap>(.*)", "", $locutor);

				$programa = preg_replace("(.*)<font class=default>Stream Genre: </font></td><td><font class=default><b>", "", $pg);
				$programa = preg_replace("</b></td></tr><tr><td width=100 nowrap>(.*)", "", $programa);
			} else {
				$locutor = 'Erro';
				$programa = 'Erro';

				$retorno['erro_fp'] = $errstr;
			}

			$retorno['locutor'] = $locutor;
			$retorno['programa'] = $programa;
		}

		if($request == 2 || $request == 3) {
			$fp2 = fsockopen($ip, $porta, $errno, $errstr, 15);

			if($fp2) {
				fputs($fp2,"GET /7.html HTTP/1.0\r\nUser-Agent: XML Getter (Mozilla Compatible)\r\n\r\n");
				while(!feof($fp2)) { $data .= fgets($fp2, 1000); }
				fclose($fp2);

				$data              = ereg_replace(".*<body>", "", $data);
				$data              = ereg_replace("</body>.*", ",", $data);
				$data_array        = explode(",",$data);

				$ouvintes = $data_array[4];
			} else {
				$ouvintes = '-';
				$retorno['erro_fp2'] = $errstr;
			}

			$retorno['ouvintes'] = (int) $ouvintes;
		}

		return $retorno;
	}

	/**
	 * Verificar se um usuário é VIP
	 * @param  string  $nick nick do usuário
	 * @return boolean       se é vip ou não
	 */
	public function isVIP($nick) {
		$sql = $this->conn->prepare("SELECT vip FROM usuarios WHERE nick = ?");
		$sql->bindValue(1, $nick);
		$sql->execute();
		$sql2 = $sql->fetch();

		if($sql2['vip'] == 's') {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Verificar se o usuário existe ou não
	 * @param  string $nick nick do usuário
	 * @return boolean      se existe ou não
	 */
	public function userExists($nick, $ativado = false) {
		if($ativado) {
			$sql = $this->conn->prepare("SELECT count(id) FROM usuarios WHERE nick = ? AND ativado = 's'");
		} else {
			$sql = $this->conn->prepare("SELECT count(id) FROM usuarios WHERE nick = ?");
		}

		$sql->bindValue(1, $nick);
		$sql->execute();
		$rows = $sql->fetchColumn();

		if($rows > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function getUser($nick) {
		if(is_numeric($nick)) {
			$sql = $this->conn->prepare("SELECT * FROM usuarios WHERE id = ? LIMIT 1");
		} else {
			$sql = $this->conn->prepare("SELECT * FROM usuarios WHERE nick = ? LIMIT 1");
		}

		$sql->bindValue(1, $nick);
		$sql->execute();
		$sql2 = $sql->fetch();

		return $sql2;
	}

	/**
	 * Obter ranking do fórum
	 * @param  integer $msgs número de mensagens do usuário
	 * @return string        html gerado
	 */
	public function getRanking($msgs) {
		$ranking = 0;

		if($ranking == 0 && $msgs <= 50 - 1) { $ranking = 1; }
		if($ranking == 0 && $msgs <= 75 - 1) { $ranking = 2; }
		if($ranking == 0 && $msgs <= 150 - 1) { $ranking = 3; }
		if($ranking == 0 && $msgs <= 175 - 1) { $ranking = 4; }
		if($ranking == 0 && $msgs <= 300 - 1) { $ranking = 5; }
		if($ranking == 0 && $msgs <= 375 - 1) { $ranking = 6; }
		if($ranking == 0 && $msgs <= 600 - 1) { $ranking = 7; }
		if($ranking == 0 && $msgs <= 675 - 1) { $ranking = 8; }
		if($ranking == 0 && $msgs <= 1200 - 1) { $ranking = 9; }
		if($ranking == 0 && $msgs <= 1275 - 1) { $ranking = 10; }
		if($ranking == 0 && $msgs <= 2400 - 1) { $ranking = 11; }
		if($ranking == 0 && $msgs >= 2400) { $ranking = 12; }

		$html = '<div class="ranking ranks-rank-'.$ranking.' tip" title="<b>'.$msgs.'</b> mensagens"></div>';
		return $html;
	}

	/**
	 * Enviar notificação para um usuário
	 * @param  integer $id_usuario id do usuário
	 * @param  string  $ato        a notificação
	 * @param  string  $link       link
	 * @return boolean             se notificou ou não
	 */
	public function sendNtfUser($id_usuario, $ato, $link = '', $imagem = false) {
		$imagem = false;

		if(is_numeric($id_usuario)) {
			$sql = $this->conn->prepare("SELECT * FROM usuarios WHERE id = ? LIMIT 1");
		} else {
			$sql = $this->conn->prepare("SELECT * FROM usuarios WHERE nick = ? LIMIT 1");
		}

		$sql->bindValue(1, $id_usuario);
		$sql->execute();
		$sql2 = $sql->fetch();

		$ato = preg_replace('/(?<!\\\\)\[home(?::\w+)?\](.*?)\[\/home(?::\w+)?\]/si', '<a href="/home/\\1"><strong>\\1</strong></a>', $ato);

		if($sql2) {
			$sql = $this->conn->prepare("INSERT INTO usuarios_notificacoes (id_usuario, ato, link, data) VALUES (?, ?, ?, ?)");
			$sql->bindValue(1, $sql2['id']);
			$sql->bindValue(2, $ato);
			$sql->bindValue(3, $link);
			$sql->bindValue(4, $this->timestamp);

			if($sql->execute()) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function sendPoints($user, $pontos, $notify = true) {
		if(is_numeric($user)) {
			$sql = $this->conn->prepare("SELECT id, pontos FROM usuarios WHERE id = ? LIMIT 1");
		} else {
			$sql = $this->conn->prepare("SELECT id, pontos FROM usuarios WHERE nick = ? LIMIT 1");
		}

		$sql->bindValue(1, $user);
		$sql->execute();
		$usuario = $sql->fetch();

		$newPontos = $usuario['pontos'] + $pontos;

		$update = $this->conn->prepare("UPDATE usuarios SET pontos = ? WHERE id = ?");
		$update->bindValue(1, $newPontos);
		$update->bindValue(2, $usuario['id']);
		$update->execute();

		$this->sendCoins($usuario['id'], 1);

		if($notify) {
			$this->sendNtfUser($usuario['id'], "Você recebeu {$pontos} pontos de experiência por interagir com o site. Parabéns! Continue interagindo para ganhar + pontos e subir de Nível.");
		}
	}

	public function sendCoins($user, $moedas) {
		if(is_numeric($user)) {
			$sql = $this->conn->prepare("SELECT id, moedas FROM usuarios WHERE id = ? LIMIT 1");
		} else {
			$sql = $this->conn->prepare("SELECT id, moedas FROM usuarios WHERE nick = ? LIMIT 1");
		}

		$sql->bindValue(1, $user);
		$sql->execute();
		$usuario = $sql->fetch();

		$newMoedas = $usuario['moedas'] + $moedas;

		$update = $this->conn->prepare("UPDATE usuarios SET moedas = ? WHERE id = ?");
		$update->bindValue(1, $newMoedas);
		$update->bindValue(2, $usuario['id']);
		$update->execute();

		$sql = $this->conn->prepare("INSERT INTO usuarios_ices (id_usuario, qtd_moedas, autor, data) VALUES (?, ?, ?, ?)");
		$sql->bindValue(1, $usuario['id']);
		$sql->bindValue(2, $moedas);
		$sql->bindValue(3, 'FSIceHabbo');
		$sql->bindValue(4, time());
		$sql->execute();
	}

	/**
	 * Enviar alerta no site
	 * @param  string $conteudo conteúdo do alerta
	 * @param  string $autor    autor do alerta (nick)
	 */
	public function sendAlert($conteudo, $autor = 'FSIceHabbo') {
		$sql = $this->conn->prepare("INSERT INTO alertas (conteudo, autor, data) VALUES (?, ?, ?)");
		$sql->bindValue(1, $conteudo);
		$sql->bindValue(2, $autor);
		$sql->bindValue(3, $this->timestamp);

		if($sql->execute()) {
			return true;
		} else {
			return false;
		}
	}

	public function addHistory($nick, $cargos) {
		$sql = $this->conn->prepare("SELECT * FROM quem_passou WHERE nick = ?");
		$sql->bindValue(1, $nick);
		$sql->execute();
		$sql2 = $sql->fetchAll();
		$rows = count($sql2);

		if($rows == 0) {
			$cargos_h = array();
			foreach ($cargos as $atual) {
				$cargos_h[] = $this->getCargoName($atual);
			}

			$cargos_history = implode('|', $cargos_h);

			$insert = $this->conn->prepare("INSERT INTO quem_passou (nick, cargo, data) VALUES (?, ?, ?)");
			$insert->bindValue(1, $nick);
			$insert->bindValue(2, $cargos_history);
			$insert->bindValue(3, $this->timestamp);
			$insert->execute();
		} else {
			$data = $sql2[0];
			$c = explode('|', $data['cargo']);
			$c = array_filter($c);

			foreach ($cargos as $atual) {
				if(!empty($atual) && !in_array($atual, $c)) {
					$c[] = $atual;
				}
			}

			foreach ($c as $atual) {
				$c[] = $this->getCargoName($atual);
			}

			$cargos_history = implode('|', $c);
			$update = $this->conn->prepare("UPDATE quem_passou SET cargo = ?, data = ? WHERE nick = ?");
			$update->bindValue(1, $cargos_history);
			$update->bindValue(2, $this->timestamp);
			$update->bindValue(3, $nick);
			$update->execute();
		}
	}

	/**
	 * Verifica se o cliente é um bot/spider/crawler
	 * @return boolean            se é ou não
	 */
	public function isBOT() {
		$user_agent = $this->user_agent;

		$crawlers = array(
			'google' => 'GoogleBot|Google Web Preview|Mediapartners-Google|Wireless\s*Transcoder',
			'alexa' => 'ia_archiver',
			'yahoo' => 'compatible; Yahoo! Slurp;',
			'msn' => 'msnbot',
			'bing' => 'bingbot',
			'apache_bench' => 'ApacheBench',
			'baiduspider' => 'Baiduspider',
			'grapeshot' => 'GrapeshotCrawler',
			'archive.org' => 'archive.org_bot',
			'spider' => 'spider',
			'indexer' => 'indexer',
			'admantx' => 'admantx.com',
			'robot' => 'robot',
			'bot' => 'bot',
			'search' => 'search',
			'genieo' => 'Genieo'
			);

		foreach ($crawlers AS $key => $crawler) {
			if (preg_match('/\b' . $crawler . '\b/i', $user_agent) > 0)
				return $key;
		}

		return false;
	}

	/**
	 * Transformar UNIX timestamp em distância de tempo passado em palavras
	 * @param  integer $data o tempo em unix timestamp
	 * @return string        distância até o tempo
	 */
	public function getDateText($data) {
		if( date('d', $data) == date('d', $this->timestamp) ) {
			return 'Hoje';
		}

		if( date('d', $data) == (date('d', $this->timestamp) - 1) ) {
			return 'Ontem';
		}

		return date('d/m', $data);
	}

	/**
	 * Remover tags BBCode
	 * @param  string $texto texto a ter bbcode removido
	 * @return string        texto com tags removidas
	 */
	public function bbCodeRemove($texto) {
		$pattern = '|[[\/\!]*?[^\[\]]*?]|si';
		$replace = '';
		return preg_replace($pattern, $replace, $texto);
	}

	/**
	 * Transformar tags BBCode em HTML
	 * @param  string $texto texto a ser transformado
	 * @return string        texto transformado
	 */
	public function bbCode($texto, $simples = false) {
		if($simples == false) {
			$texto = str_replace("https://www.youtube", "http://www.youtube", $texto);
			$texto = str_replace("https://youtube", "http://youtube", $texto);
			$tags = array(
				'/\[size=(\d+)\](.*?)\[\/size\]/is' 			                   => "<span style=\"font-size:\\1%;\">\\2</span>",

				'/(?<!\\\\)\[left(?::\w+)?\](.*?)\[\/left(?::\w+)?\]/si'           => "<div style=\"text-align:left;\">\\1</div>",
				'/(?<!\\\\)\[right(?::\w+)?\](.*?)\[\/right(?::\w+)?\]/si'         => "<div style=\"text-align:right;\">\\1</div>",
				'/(?<!\\\\)\[center(?::\w+)?\](.*?)\[\/center(?::\w+)?\]/si'       => "<div style=\"text-align:center;\">\\1</div>",

				'/(?<!\\\\)\[code(?::\w+)?\](.*?)\[\/code(?::\w+)?\]/si'           => "<pre class=\"bbc-code\">\\1</pre>",

				'/(?<!\\\\)\[b(?::\w+)?\](.*?)\[\/b(?::\w+)?\]/si'                 => "<strong>\\1</strong>",
				'/(?<!\\\\)\[i(?::\w+)?\](.*?)\[\/i(?::\w+)?\]/si'                 => "<em>\\1</em>",
				'/(?<!\\\\)\[u(?::\w+)?\](.*?)\[\/u(?::\w+)?\]/si'                 => "<span style=\"text-decoration: underline;\">\\1</span>",
				'/(?<!\\\\)\[s(?::\w+)?\](.*?)\[\/s(?::\w+)?\]/si'                 => "<del>\\1</del>",
				'/\[color=([#a-z0-9]+)\](.*?)\[\/color\]/is'					   => "<span style=\"color: \\1;\">\\2</span>",

				'/(?<!\\\\)\[url(?::\w+)?\]www\.(.*?)\[\/url(?::\w+)?\]/si'        => "<a class=\"bbc-link\" href=\"http://www.\\1\" target=\"_blank\">\\1</a>",
				'/(?<!\\\\)\[url(?::\w+)?\](.*?)\[\/url(?::\w+)?\]/si'             => "<a class=\"bbc-link\" href=\"\\1\" target=\"_blank\">\\1</a>",
				'/(?<!\\\\)\[url(?::\w+)?=(.*?)?\](.*?)\[\/url(?::\w+)?\]/si'      => "<a class=\"bbc-link\" href=\"\\1\" target=\"_blank\">\\2</a>",

				'/(?<!\\\\)\[img(?::\w+)?\](.*?)\[\/img(?::\w+)?\]/si'             => "<img src=\"\\1\" class=\"bbc-img\" alt=\"Imagem\">",

				'/\[youtube\](?:http?:\/\/)?(?:www\.)?youtu(?:\.be\/|be\.com\/watch\?v=)([A-Z0-9\-_]+)(?:&(.*?))?\[\/youtube\]/i' => "<div class=\"bbc-video\"><iframe class=\"youtube-player\" type=\"text/html\" width=\"640\" height=\"385\" src=\"http://www.youtube.com/embed/\\1\" frameborder=\"0\"></iframe></div>",

				'/\\\\(\[\/?\w+(?::\w+)*\])/'                                      => "\\1",
				);
} else {
	$tags = array(
		'/(?<!\\\\)\[b(?::\w+)?\](.*?)\[\/b(?::\w+)?\]/si'                 => "<strong>\\1</strong>",
		'/(?<!\\\\)\[i(?::\w+)?\](.*?)\[\/i(?::\w+)?\]/si'                 => "<em>\\1</em>",
		'/(?<!\\\\)\[u(?::\w+)?\](.*?)\[\/u(?::\w+)?\]/si'                 => "<span style=\"text-decoration: underline;\">\\1</span>",
		'/(?<!\\\\)\[s(?::\w+)?\](.*?)\[\/s(?::\w+)?\]/si'                 => "<del>\\1</del>",
		'/\[color=([#a-z0-9]+)\](.*?)\[\/color\]/is'					   => "<span style=\"color: \\1;\">\\2</span>",

		'/(?<!\\\\)\[url(?::\w+)?\]www\.(.*?)\[\/url(?::\w+)?\]/si'        => "<a class=\"bbc-link\" href=\"http://www.\\1\" target=\"_blank\">\\1</a>",
		'/(?<!\\\\)\[url(?::\w+)?\](.*?)\[\/url(?::\w+)?\]/si'             => "<a class=\"bbc-link\" href=\"\\1\" target=\"_blank\">\\1</a>",
		'/(?<!\\\\)\[url(?::\w+)?=(.*?)?\](.*?)\[\/url(?::\w+)?\]/si'      => "<a class=\"bbc-link\" href=\"\\1\" target=\"_blank\">\\2</a>",

		'/\\\\(\[\/?\w+(?::\w+)*\])/'                                      => "\\1",
		);
}

$texto = preg_replace(array_keys($tags), array_values($tags), $texto);

$texto = str_replace('[e:1]', '<div class="emoticons emoticons-em-1"></div>', $texto);
$texto = str_replace('[e:2]', '<div class="emoticons emoticons-em-2"></div>', $texto);
$texto = str_replace('[e:3]', '<div class="emoticons emoticons-em-3"></div>', $texto);
$texto = str_replace('[e:4]', '<div class="emoticons emoticons-em-4"></div>', $texto);
$texto = str_replace('[e:5]', '<div class="emoticons emoticons-em-5"></div>', $texto);
$texto = str_replace('[e:6]', '<div class="emoticons emoticons-em-6"></div>', $texto);
$texto = str_replace('[e:7]', '<div class="emoticons emoticons-em-7"></div>', $texto);
$texto = str_replace('[e:8]', '<div class="emoticons emoticons-em-8"></div>', $texto);
$texto = str_replace('[e:9]', '<div class="emoticons emoticons-em-9"></div>', $texto);
$texto = str_replace('[e:10]', '<div class="emoticons emoticons-em-10"></div>', $texto);
$texto = str_replace('[e:11]', '<div class="emoticons emoticons-em-11"></div>', $texto);
$texto = str_replace('[e:12]', '<div class="emoticons emoticons-em-12"></div>', $texto);
$texto = str_replace('[e:13]', '<div class="emoticons emoticons-em-13"></div>', $texto);
$texto = str_replace('[e:14]', '<div class="emoticons emoticons-em-14"></div>', $texto);
$texto = str_replace('[e:15]', '<div class="emoticons emoticons-em-15"></div>', $texto);
$texto = str_replace('[e:16]', '<div class="emoticons emoticons-em-16"></div>', $texto);
$texto = str_replace('[e:17]', '<div class="emoticons emoticons-em-17"></div>', $texto);
$texto = str_replace('[e:18]', '<div class="emoticons emoticons-em-18"></div>', $texto);
$texto = str_replace('[e:19]', '<div class="emoticons emoticons-em-19"></div>', $texto);
$texto = str_replace('[e:20]', '<div class="emoticons emoticons-em-20"></div>', $texto);
$texto = str_replace('[e:21]', '<div class="emoticons emoticons-em-21"></div>', $texto);
$texto = str_replace('[e:22]', '<div class="emoticons emoticons-em-22"></div>', $texto);
$texto = str_replace('[e:23]', '<div class="emoticons emoticons-em-23"></div>', $texto);
$texto = str_replace('[e:24]', '<div class="emoticons emoticons-em-24"></div>', $texto);
$texto = str_replace('[e:25]', '<div class="emoticons emoticons-em-25"></div>', $texto);
$texto = str_replace('[e:26]', '<div class="emoticons emoticons-em-div"></div>', $texto);

$texto = str_replace('[quote]', '<div class="bbc-quote">', $texto);
$texto = str_replace('[/quote]', '</div>', $texto);

$texto = preg_replace("/@(([a-z0-9_,:=?@.!-])+)+/im", '<a class="bbc-link" href="http://www.icehabbo.com.br/home/$1">@$1</a>', $texto);

if(!$simples) {
	$texto = nl2br($texto);
}

return $texto;
}

}

$core = new Core($conn);