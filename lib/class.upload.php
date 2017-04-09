<?php



/**

 * IceHabbo

 * by Henrique Arthur <eu@henriquearthur.me>

 * Não use sem autorização.

 */



class Upload {

	private $pasta;

	private $main_dir;

	private $tipos = array('.jpg', '.jpeg', '.gif', '.png');

	private $conn;

	private $site;

	private $watermark = false;

	private $prefixo;



	public $caminho;

	public $erro = false;



	public function __construct($conn, $galeria, $arquivo, $url, $prefixo = 'acp-', $obrigatorio = false, $caminho_atual = '', $multiple = false, $up_multiple = '', $site = false, $watermark = false, $pasta = false) {

		$this->conn = $conn;

		$this->site = $site;



		if($pasta !== false) {

			$this->pasta = $pasta;

		} else {

			$this->pasta = 'uploads/';

		}



		$this->main_dir = $_SERVER['DOCUMENT_ROOT'] . '/';

		$up_tipo = 'empty';



		if($watermark) {

			$this->watermark = true;

		}



		// Prioridade: arquivo > multiplos > galeria > url

		if(is_uploaded_file($arquivo["tmp_name"])) {

			$up_tipo = 'arquivo';

			$this->prefixo = $prefixo;

		} else {

			if(!empty($up_multiple)) {

				$up_tipo = 'multiplo';

				$this->prefixo = $prefixo;

			} else {

				if(!empty($galeria)) {

					$up_tipo = 'galeria';

					$this->prefixo = $prefixo;

				} else {

					if(!empty($url)) {

						$up_tipo = 'url';

						$this->prefixo = $prefixo;

					}

				}

			}

		}



		if($up_tipo == 'galeria') {

			$this->upGaleria($galeria);

		}



		if($up_tipo == 'arquivo') {

			$this->upArquivo($arquivo);

		}



		if($up_tipo == 'multiplo') {

			$this->upMultiplo($up_multiple);

		}



		if($up_tipo == 'url') {

			$this->upUrl($url);

		}



		if($up_tipo == 'empty' && $obrigatorio) {

			$this->erro = 'Escolha ou envie uma imagem.';

		}



		if($up_tipo == 'empty' && !$obrigatorio) {

			$this->caminho = $caminho_atual;

		}

	}



	/**

	 * Fazer upload de arquivo escolhido da galeria

	 * @param  string $nome_arquivo nome do arquivo dentro da pasta /media/uploads

	 */

	public function upGaleria($nome_arquivo) {

		$caminho = $this->main_dir . $this->pasta . $nome_arquivo;



		if(file_exists($caminho)) {

			if($this->watermark) {

				$arquivo = $this->main_dir . $this->pasta . $nome_arquivo;

				$extensao = strtolower(strrchr($arquivo, '.'));



				switch(strtolower($extensao)) {

					case '.png':

					$image_resource =  imagecreatefrompng($arquivo);

					break;

					case '.gif':

					$image_resource =  imagecreatefromgif($arquivo);

					break;

					case '.jpeg': case '.jpg':

					$image_resource = imagecreatefromjpeg($arquivo);

					break;

					default:

					$image_resource = false;

				}



				if($image_resource) {

					$wm_width = 200;

					$wm_height = 106;



					list($img_width, $img_height) = getimagesize($arquivo);



					if($img_width < $wm_width) {

						$wm_width = 20;

						$wm_height = 20;



						$watermark = imagecreatefrompng($this->main_dir . 'media/images/watermark-2.png');

						$watermark_left = ($img_width - $wm_width) - 2;

						$watermark_bottom = ($img_height - $wm_height) - 2;

					} else {

						$watermark = imagecreatefrompng($this->main_dir . 'media/images/watermark.png');

						$watermark_left = ($img_width - $wm_width) - 10;

						$watermark_bottom = ($img_height - $wm_height) - 10;

					}



					imagecopy($image_resource, $watermark, $watermark_left, $watermark_bottom, 0, 0, $wm_width, $wm_height);



					$novo_nome = md5(uniqid(rand(), true));

					$novo_nome = substr($novo_nome, 0, 10);

					$novo_nome = $this->prefixo . $novo_nome . $extensao;

					$send = imagepng($image_resource, $this->main_dir . $this->pasta . $novo_nome);



					imagedestroy($image_resource);

					imagedestroy($watermark);



					$this->caminho = '/' . $this->pasta . $novo_nome;

					$this->insertMedia($this->caminho, 'galeria');

				}

			} else {

				$this->caminho = '/' . $this->pasta . $nome_arquivo;

				$this->insertMedia($this->caminho, 'galeria');

			}

		} else {

			$this->erro = 'O arquivo não existe na galeria de imagens.';

		}

	}



	/**

	 * Fazer upload de arquivo enviado do computador

	 * @param  array $arquivo arquivo enviado ($_FILES)

	 */

	public function upArquivo($arquivo) {

		$nome = $arquivo['name'];

		$extensao = strtolower(strrchr($nome, '.'));



		if(in_array($extensao, $this->tipos)) {

			$novo_nome = md5(uniqid(rand(), true));

			$novo_nome = substr($novo_nome, 0, 10);

			$novo_nome = $this->prefixo . $novo_nome . $extensao;



			$arquivo_tmp = $arquivo['tmp_name'];

			$arquivo_type = $arquivo['type'];

			$send = move_uploaded_file($arquivo_tmp, $this->main_dir . $this->pasta . $novo_nome);

			if($send != false) {

				chmod($this->main_dir . $this->pasta . $novo_nome, 0644);

				$this->caminho = '/' . $this->pasta . $novo_nome;



				if(!$this->site) {

					$this->insertMedia($this->caminho, 'arquivo');

				}

			} else {

				$infos = json_encode(array($arquivo_tmp, $this->main_dir . $this->pasta . $novo_nome));
				$this->erro = 'Não foi possível enviar a imagem. Tente novamente.<br>Código de erro: UP01'.$infos;

			}

		} else {

			$this->erro = 'Envie uma imagem válida (extensões permitidas: .jpg, .jpeg, .gif e .png).';

		}

	}



	/**

	 * Fazer upload de múltiplos arquivos enviados do computador

	 * @param  array $arquivos os arquivos enviados

	 */

	public function upMultiplo($arquivos) {

		$this->caminho = array();

		$count = 0;



		foreach ($arquivos['name'] as $f => $nome) {

			$extensao = strtolower(strrchr($nome, '.'));



			if($arquivos['error'][$f] == 4) {

				continue;

			}



			if ($arquivos['error'][$f] == 0) {

				if(!in_array($extensao, $this->tipos)) {

					continue;

				} else {

					$novo_nome = md5(uniqid(rand(), true));

					$novo_nome = substr($novo_nome, 0, 10);

					$novo_nome = $this->prefixo . $novo_nome . $extensao;



					if($this->watermark) {

						$arquivo_tmp = $arquivos["tmp_name"][$f];

						$arquivo_type = $arquivos["type"][$f];



						switch(strtolower($arquivo_type)) {

							case 'image/png':

							$image_resource =  imagecreatefrompng($arquivo_tmp);

							break;

							case 'image/gif':

							$image_resource =  imagecreatefromgif($arquivo_tmp);

							break;

							case 'image/jpeg': case 'image/pjpeg':

							$image_resource = imagecreatefromjpeg($arquivo_tmp);

							break;

							default:

							$image_resource = false;

						}



						if($image_resource) {

							$wm_width = 200;

							$wm_height = 106;



							list($img_width, $img_height) = getimagesize($arquivo_tmp);



							if($img_width < $wm_width) {

								$wm_width = 20;

								$wm_height = 20;



								$watermark = imagecreatefrompng($this->main_dir . 'media/images/watermark-2.png');

								$watermark_left = ($img_width - $wm_width) - 2;

								$watermark_bottom = ($img_height - $wm_height) - 2;

							} else {

								$watermark = imagecreatefrompng($this->main_dir . 'media/images/watermark.png');

								$watermark_left = ($img_width - $wm_width) - 10;

								$watermark_bottom = ($img_height - $wm_height) - 10;

							}



							imagecopy($image_resource, $watermark, $watermark_left, $watermark_bottom, 0, 0, $wm_width, $wm_height);

							$send = imagepng($image_resource, $this->main_dir . $this->pasta . $novo_nome);



							imagedestroy($image_resource);

							imagedestroy($watermark);

						}

					} else {

						$send = move_uploaded_file($arquivos["tmp_name"][$f], $this->main_dir . $this->pasta . $novo_nome);

					}



					if($send != false) {

						chmod($this->main_dir . $this->pasta . $novo_nome, 0644);

						$caminho = '/' . $this->pasta . $novo_nome;

						$this->caminho[] = $caminho;

						$this->insertMedia($caminho, 'multiplo');



						$count++;

					} else {

						$this->erro = 'Ocorreu um erro no envio de arquivos. <br>Código de erro: UP03';

						continue;

					}

				}

			}

		}

	}



	/**

	 * Fazer upload de imagem através da URL

	 * @param  string $url endereço da imagem

	 */

	public function upUrl($url) {

		$extensao = strtolower(strrchr($url, '.'));



		if(in_array($extensao, $this->tipos)) {

			$nome = basename($url);



			$novo_nome = $nome . time() . $_SESSION['login'];

			$novo_nome = md5($novo_nome);

			$novo_nome = substr($novo_nome, 0, 10);

			$novo_nome = $this->prefixo . $novo_nome . $extensao;





			if($this->watermark) {

				switch(strtolower($extensao)) {

					case '.png':

					$image_resource =  imagecreatefrompng($url);

					break;

					case '.gif':

					$image_resource =  imagecreatefromgif($url);

					break;

					case '.jpeg': case '.jpg':

					$image_resource = imagecreatefromjpeg($url);

					break;

					default:

					$image_resource = false;

				}



				if($image_resource) {

					$wm_width = 200;

					$wm_height = 106;



					list($img_width, $img_height) = getimagesize($url);



					if($img_width < $wm_width) {

						$wm_width = 20;

						$wm_height = 20;



						$watermark = imagecreatefrompng($this->main_dir . 'media/images/watermark-2.png');

						$watermark_left = ($img_width - $wm_width) - 2;

						$watermark_bottom = ($img_height - $wm_height) - 2;

					} else {

						$watermark = imagecreatefrompng($this->main_dir . 'media/images/watermark.png');

						$watermark_left = ($img_width - $wm_width) - 10;

						$watermark_bottom = ($img_height - $wm_height) - 10;

					}



					imagecopy($image_resource, $watermark, $watermark_left, $watermark_bottom, 0, 0, $wm_width, $wm_height);

					imagepng($image_resource, $this->main_dir . $this->pasta . $novo_nome);



					imagedestroy($image_resource);

					imagedestroy($watermark);



					$send = true;

				}

			} else {

				$send = file_put_contents($this->main_dir . $this->pasta . $novo_nome, file_get_contents($url));

			}



			if($send) {

				chmod($this->main_dir . $this->pasta . $novo_nome, 0644);

				$this->caminho = '/' . $this->pasta . $novo_nome;

				$this->insertMedia($this->caminho, 'url');

			} else {

				$this->erro = 'Não foi possível enviar a imagem. Tente novamente.<br>Código de erro: UP02';

			}

		} else {

			$this->erro = 'Envie uma imagem válida (extensões permitidas: .jpg, .jpeg, .gif e .png).';

		}

	}



	/**

	 * Inserir registro da imagem no banco de dados

	 * @param  string $caminho caminho da imagem

	 * @param  string $tipo    tipo de upload (galeria; arquivo; url)

	 */

	/**

	 * Inserir registro da imagem no banco de dados

	 * @param  string $caminho caminho da imagem

	 * @param  string $tipo    tipo de upload (galeria; arquivo; url)

	 * @return boolean         se inseriu ou não

	 */

	public function insertMedia($caminho, $tipo) {

		global $core;



		$caminho = str_replace(DOMAIN, '', $caminho);



		$pref = explode('-', basename($caminho));

		$prefixo = $pref[0];



		$autor = $_SESSION['login'];

		$timestamp = time();



		$sql = $this->conn->prepare("INSERT INTO acp_midia (imagem, tipo, prefixo, url, autor, data) VALUES (?,?,?,?,?,?)");

		$sql->bindValue(1, $caminho);

		$sql->bindValue(2, $tipo);

		$sql->bindValue(3, $prefixo);

		$sql->bindValue(4, $core->url_unica);

		$sql->bindValue(5, $autor);

		$sql->bindValue(6, $timestamp);



		if($sql->execute()) {

			return true;

		} else {

			return false;

		}

	}

}