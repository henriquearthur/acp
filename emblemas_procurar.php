<? include "lib/config.php";
include 'lib/class.core.php';
include 'lib/class.sqlactions.php';



$sql = $conn->query("SELECT * FROM emblemas_habbo_siglas ORDER BY id ASC");
$sql2 = $sql->fetchAll();

foreach ($sql2 as $atual) {
	//echo '<br><br><b>'.$atual['sigla'].' : (ultimo descoberto) '.$atual['cod_ultimo'].'</b><br><br>';

	$last = '';
	$procurar = $atual['cod_ultimo'] + 15;
	$range = range($atual['cod_ultimo'], $procurar);

	foreach ($range as $numeracao) {
		if($numeracao > 0) {
			if($numeracao < 10 && strlen($atual['sigla'])  == 2) { $numeracao = '00' . $numeracao; }
			if($numeracao < 100 && $numeracao >= 10 && strlen($atual['sigla'])  == 2) { $numeracao = '0' . $numeracao; }

			if($numeracao < 10 && strlen($atual['sigla'])  == 3) { $numeracao = '0' . $numeracao; }


			$codigo = $atual['sigla'] . $numeracao;
			$link = 'http://images.habbo.com/c_images/album1584/'.$codigo.'.gif';

			$sigla = $atual['sigla'];

			//echo 'Emblema a ser procurado: <b>'.$codigo.'</b>';


				$ch = curl_init($link);
				curl_setopt($ch,  CURLOPT_RETURNTRANSFER, TRUE);
				$response = curl_exec($ch);
				$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);

				if($httpCode === 404) {
					//echo ' - NAO Encontrado: <b>'.$codigo.'</b>';
				} else {
					//echo ' - Encontrado: <b>'.$codigo.'</b>';
					$sql3 = $conn->query("SELECT count(id) FROM emblemas_habbo WHERE numeracao = '$numeracao' AND sigla = '$sigla'");
					$rows = $sql3->fetchColumn();

					if($rows == 0) {
						$last = $numeracao;

						$in['sigla'] = $atual['sigla'];
						$in['numeracao'] = $numeracao;
						$insert = $sqlActions->insert("emblemas_habbo", $in);

						$form_return .= 'Emblema encontrado: <b>'.$codigo.'</b> <img src="'.$link.'"><br>';
					}
				}

			//echo '<br>';
		}
	}

	if(is_numeric($last)) {
		$up['cod_ultimo'] = $last;
		$wh['id'] = $atual['id'];
		$update = $sqlActions->update("emblemas_habbo_siglas", $up, $wh);
	}

	//echo 'Ultimo descoberto da sigla: <b>'.$last.'</b><br>';
}
