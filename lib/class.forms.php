<?php

/**
 * IceHabbo
 * by Henrique Arthur <eu@henriquearthur.me>
 * Não use sem autorização.
 */

class Form {
	private $form_classes;
	private $form_name;

	private $form_open;
	private $form_inputs;
	public $form;

	/**
	 * Construtor
	 * Setar as configurações do formulário
	 *
	 * @array options
	 */
	public function __construct($classes, $name, $confirmation = false) {
		global $core;
		$this->core = $core;

		$this->form_classes .= $classes;
		if(!empty($name)) { $this->form_name = $name; } else { $this->form_name = 'form'; }

		$this->createFormTag($confirmation);
	}

	/**
	 * Criar a tag de abertura do formulário
	 */
	public function createFormTag($confirmation) {
		if($confirmation) {
			$this->form_open  = '<form action="'.$_SERVER['REQUEST_URI'].'" method="post" class="'.$this->form_classes.'" enctype="multipart/form-data" onsubmit="pgExitDisable();">';
		} else {
			$this->form_open  = '<form action="'.$_SERVER['REQUEST_URI'].'" method="post" class="'.$this->form_classes.'" enctype="multipart/form-data">';
		}

		$this->form_open .= '<input type="hidden" name="'.$this->form_name.'" value="'.$this->form_name.'">';
	}

	/**
	 * Criar um input
	 * @param  string  $label       nome do campo
	 * @param  string  $tipo        tipo do input
	 * @param  string  $nome        nome do input
	 * @param  string  $value       valor pré-definido do input
	 * @param  string  $classes     classes do form-group
	 * @param  boolean $disabled    se está desabilitado ou não
	 * @param  string  $info        informações extras
	 */
	public function createInput($label, $tipo, $nome, $value = '', $classes = '', $disabled = '', $info = '') {
		(($disabled)) ? $disabled = ' disabled="disabled"' : '';
		((!empty($classes))) ? $classes = ' ' . $classes : '';

		if($tipo != 'password') {
			(($value == '')) ? $value = $this->core->clear($_POST[$nome]) : $value = $this->core->clear($value);
		}

		$this->form_inputs .= '<div class="form-group'.$classes.'">
		<label class="form-label" for="'.$nome.'">'.$label.'</label>
		<input class="form-input" type="'.$tipo.'" name="'.$nome.'" id="'.$nome.'" placeholder="'.$label.'" value="'.$value.'"'.$disabled.'>
		<br>';

		if(!empty($info)) { $this->form_inputs .= '<div class="form-info">'.$info.'</div>'; }

		$this->form_inputs .= '</div>';
	}

	/**
	 * Criar uma textarea
	 * @param  string  $label       nome do campo
	 * @param  string  $nome        nome do input
	 * @param  string  $value       valor pré-definido do input
	 * @param  string  $classes     classes do form-group
	 * @param  string  $classes2    classes da textarea
	 * @param  boolean $disabled    se está desabilitado ou não
	 * @param  string  $info        informações extras
	 */
	public function createTextarea($label, $nome, $value = '', $classes = '', $classes2 = '', $disabled = '', $info = '') {
		(($disabled)) ? $disabled = ' disabled="disabled"' : '';
		((!empty($classes))) ? $classes = ' ' . $classes : '';
		((!empty($classes2))) ? $classes2 = ' ' . $classes2 : '';

		if($classes == ' ckeditor') {
			(($value == '')) ? $value = $_POST[$nome] : $value = $value;
		} else {
			(($value == '')) ? $value = $this->core->clear($_POST[$nome]) : $value = $this->core->clear($value);
		}

		$this->form_inputs .= '<div class="form-group'.$classes.'">
		<label class="form-label" for="'.$nome.'">'.$label.'</label>
		<textarea style="height:250px;" class="form-input'.$classes2.'" name="'.$nome.'" id="'.$nome.'" placeholder="'.$label.'"'.$disabled.'>'.$value.'</textarea>
		<br>';

		if(!empty($info)) { $this->form_inputs .= '<div class="form-info">'.$info.'</div>'; }

		$this->form_inputs .= '</div>';
	}

	/**
	 * Criar um select
	 * @param  string  $label       nome do campo
	 * @param  string  $nome        nome do select
	 * @param  array   $options     opções disponíveis
	 * @param  string  $selected    valor pré-selecionado do select
	 * @param  string  $classes     classes do form-group
	 * @param  boolean $disabled    se está desabilitado ou não
	 */

	public function createSelect($label, $nome, $options, $selected = '', $classes = '', $disabled = '', $info = '') {
		(($disabled)) ? $disabled = ' disabled="disabled"' : '';
		((!empty($classes))) ? $classes = ' ' . $classes : '';
		(($selected == '')) ? $selected = $_POST[$nome] : '';

		$this->form_inputs .= '<div class="form-group'.$classes.'">
		<label class="form-label" for="'.$nome.'">'.$label.'</label>
		<select class="form-input" name="'.$nome.'" id="'.$nome.'">';

		foreach ($options as $atual) {
			(($selected == $atual['value'])) ? $sel = 'selected="selected"' : $sel = '';
			$this->form_inputs .= '<option value="'.$atual['value'].'"'.$sel.'>'.$atual['label'].'</option>';
		}

		$this->form_inputs .= '</select><br>';

		if(!empty($info)) { $this->form_inputs .= '<div class="form-info">'.$info.'</div>'; }

		$this->form_inputs .= '</div>';
	}

	/**
	 * Criar uma checkbox
	 * @param  string  $label       nome do campo
	 * @param  string  $nome        nome da checkbox
	 * @param  boolean $checked     se está selecionado ou não
	 * @param  string  $classes     classes do form-group
	 * @param  boolean $disabled    se está desabilitado ou não
	 */

	public function createCheckbox($label, $nome, $checked = '', $classes = '', $disabled = '') {
		(($disabled)) ? $disabled = ' disabled="disabled"' : '';
		(($checked)) ? $check = ' checked="checked"' : '';
		((!empty($classes))) ? $classes = ' ' . $classes : '';

		if($checked == '') {
			if($_POST[$nome]) {
				$check =  ' checked="checked"';
			}
		}

		$this->form_inputs .= '<div class="form-group'.$classes.'">
		<label class="form-label" for="'.$nome.'">'.$label.'</label>
		<input class="form-input" type="checkbox" name="'.$nome.'" id="'.$nome.'"'.$disabled.''.$check.'>
		<br></div>';
	}

	/**
	 * Criar um campo de upload de imagem
	 * @param  string  $label       nome do campo
	 * @param  string  $nome        nome do input
	 * @param  string  $value       valor do input
	 * @param  string  $classes     classes do form-group
	 * @param  string  $info        informações extras
	 */
	public function createUpload($label, $nome, $value = '', $classes = '', $info = '', $multiple = false) {
		((!empty($classes))) ? $classes = ' ' . $classes : '';
		(($value == '')) ? $value = $this->core->clearImg($_POST[$nome]) : $value = $this->core->clearImg($value);

		$this->form_inputs .= '<div class="form-group upload'.$classes.'">';
		$this->form_inputs .= '<label class="form-label" for="'.$nome.'">'.$label.'</label>';

		$this->form_inputs .= '<div class="form-upload tabs">';

		$this->form_inputs .= '<ul>';

		if(!empty($value)) {
			$this->form_inputs .= '<li><a href="#t-current-'.$nome.'">Imagem atual</a></li>';
		}

		$this->form_inputs .= '<li><a href="#t-gallery-'.$nome.'">Escolher da galeria</a></li>';
		$this->form_inputs .= '<li><a href="#t-file-'.$nome.'">Enviar arquivo</a></li>';

		if($multiple) {
			$this->form_inputs .= '<li><a href="#t-file-multiple-'.$nome.'">Enviar múltiplos arquivos</a></li>';
		}

		$this->form_inputs .= '<li><a href="#t-url-'.$nome.'">Digitar URL</a></li>';
		$this->form_inputs .= '<li><a href="#t-options-'.$nome.'">Opções</a></li>';
		$this->form_inputs .= '</ul>';

		if(!empty($value)) {
			$img_info = getimagesize($value);

			$this->form_inputs .= '<div id="t-current-'.$nome.'">';
			$this->form_inputs .= '<a href="'.$value.'" target="_blank"><img class="upl-current-img" src="'.$value.'"></a>';
			$this->form_inputs .= '<div class="img-infos">';
			$this->form_inputs .= '<a href="'.$value.'" target="_blank"><b>'.$value.'</b></a><br>';
			$this->form_inputs .= 'Largura: '.$img_info[0].' pixels<br>';
			$this->form_inputs .= 'Altura: '.$img_info[1].' pixels<br><br>';
			$this->form_inputs .= '<div class="txt-justify">Se você deseja alterar esta imagem, escolha uma das opções acima e envie a nova imagem.</div>';
			$this->form_inputs .= '</div>';
			$this->form_inputs .= '</div>';
		}

		$this->form_inputs .= '<div id="t-gallery-'.$nome.'">';
		$this->form_inputs .= '<select class="form-input upl-gallery-file" name="gl-'.$nome.'" id="gl-'.$nome.'">';
		$this->form_inputs .= '<option value="0" default>-- Selecione um arquivo</option>';

		$path = MAIN_DIR . 'uploads';
		$diretorio = dir($path);

		$i = 0;
		while($arquivo = $diretorio -> read()){
			if(strlen($arquivo) > 4) {
				$caminho = '/uploads/' . $arquivo;
				$files[$i]['caminho'] = $caminho;
				$files[$i]['arquivo'] = $arquivo;
				$i++;
			}

			if($i == 150) { break; }
		}

		$diretorio -> close();
		while($i >= 0) {
			$arquivo = $files[$i]['arquivo'];
			$i--;

			if($arquivo != '' && $arquivo != 'heads') {
				if($value == $arquivo) {
					$selected = 'selected';
				} else {
					$selected = '';
				}

				$this->form_inputs .= '<option value="'.$arquivo.'"'.$selected.'>'.$arquivo.'</option>';
			}
		}

		$this->form_inputs .= '</select>';
		$this->form_inputs .= '<img id="img-'.$nome.'" class="upl-gallery-img">';
		$this->form_inputs .= '<br>';
		$this->form_inputs .= '</div>';

		$this->form_inputs .= '<div id="t-file-'.$nome.'">';
		$this->form_inputs .= 'Extensões permitidas: <b>.jpg, .jpeg, .gif e .png</b><br><br>';
		$this->form_inputs .= '<input class="form-input upl-input-file" type="file" name="fl-'.$nome.'" id="fl-'.$nome.'">';
		$this->form_inputs .= '<br>';
		$this->form_inputs .= '<img id="img-'.$nome.'" class="upl-file-img">';
		$this->form_inputs .= '</div>';

		if($multiple) {
			$this->form_inputs .= '<div id="t-file-multiple-'.$nome.'">';
			$this->form_inputs .= 'Extensões permitidas: <b>.jpg, .jpeg, .gif e .png</b><br>Enviar várias imagens de uma só vez pode levar mais tempo que uma única imagem.<br><br>';
			$this->form_inputs .= '<input class="form-input upl-input-file" type="file" name="flm-'.$nome.'[]" id="fl-'.$nome.'" multiple>';
			$this->form_inputs .= '<br>';
			$this->form_inputs .= '</div>';
		}

		$this->form_inputs .= '<div id="t-url-'.$nome.'">';
		$this->form_inputs .= 'Extensões permitidas: <b>.jpg, .jpeg, .gif e .png</b><br><br>';
		$this->form_inputs .= '<input class="form-input upl-url-file" type="text" name="url-'.$nome.'" id="url-'.$nome.'" placeholder="Digite a URL da imagem">';
		$this->form_inputs .= '<br><br>Não se preocupe, iremos hospedar a imagem em nosso servidor.<br>';
		$this->form_inputs .= '<img id="img-'.$nome.'" class="upl-url-img">';
		$this->form_inputs .= '</div>';

		$this->form_inputs .= '<div id="t-options-'.$nome.'">';
		$this->form_inputs .= '<div class="form-group no-border check-side"><label class="form-label" for="options-watermark-'.$nome.'">Adicionar marca d\'água IceHabbo</label><input class="form-input" type="checkbox" id="options-watermark-'.$nome.'" name="options-watermark-'.$nome.'"><br></div>';
		$this->form_inputs .= '<br>A adição de marca d\'água pode não funcionar corretamente com imagens escolhidas da galeria.';
		$this->form_inputs .= '</div>';

		$this->form_inputs .= '</div>';

		if(!empty($info)) { $this->form_inputs .= '<div class="form-info">'.$info.'</div>'; }

		$this->form_inputs .= '<br></div>';

		/**
		 * input [name='gl-{$nome}']   => select da galeria
		 * input [name='fl-{$nome}']   => input file de enviar arquivo
		 * input [name='url-{$nome}']  => input text de digitar URL
		 */
	}

	/**
	 * Mostrar um aviso no formulário
	 * @param string $aviso
	 */
	public function mostraAviso($aviso) {
		$this->form_inputs .= $aviso;
	}

	/**
	 * Mostra um título no formulário
	 * @param  string $titulo
	 */
	public function mostraTitulo($titulo) {
		$this->form_inputs .= '<h1 class="form-title">' . $titulo . '</h1>';
	}

	/**
	 * Unificar todas as variáveis (tags), criar o submit e gerar o formulário completo
	 */
	public function generateForm() {
		$this->form = $this->form_open . $this->form_inputs;
		$this->form .= '<br><div class="form-group submit"><button type="submit" class="btn btn-primary">Enviar</button></div>';
		$this->form .= '</form>';
	}
}