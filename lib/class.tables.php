<?php

/**
 * IceHabbo
 * by Henrique Arthur <eu@henriquearthur.me>
 * Não use sem autorização.
 */

class Table {
	private $table_open;
	private $table_checks;
	private $table_head;
	private $table_body;

	private $allAccess;
	private $contaspag;

	public $table;

	/**
	 * Construtor - Iniciar a tabela
	 */
	public function __construct($classes = '', $checks = true, $allAccess, $iscontaspag = false) {
		if(!empty($classes)) {
			$classes = ' ' . $classes;
		}

		$this->table_open = '<table class="table table-striped table-hover'.$classes.'">';
		$this->table_checks = $checks;

		$this->allAccess = $allAccess;
		$this->contaspag = $iscontaspag;
	}

	/**
	 * Criar o cabeçalho da tabela
	 * @param  array $nomes
	 */
	public function head($nomes) {
		$this->table_head = '<thead><tr>';

		if($this->table_checks) {
			$this->table_head .= '<th class="check"><input class="form-check" id="table-check-all" type="checkbox"></th>';
		}

		foreach ($nomes as $atual) {
			$this->table_head .= "<th>$atual</th>";
		}

		$this->table_head .= '</tr></thead>';
	}

	/**
	 * Iniciar o corpo da tabela
	 */
	public function startBody() {
		$this->table_body = '<tbody>';
	}

	/**
	 * Inserir uma linha
	 * @param  array $nomes
	 */
	public function insertBody($nomes, $status, $ignoreLink = false) {
		$p = $_GET['p'];
		$id = $nomes[0];
		$link = "?p=$p&a=2&id=$id";
		$delete = "?p=$p&a=3&id=$id";
		$delete2 = "?p=$p&a=339955&id=$id";
		$ativar = "?p=$p&a=9&id=$id";

		if($ignoreLink) {
			$link = '#';
		}

		$this->table_body .= '<tr>';

		if($this->table_checks) {
			$this->table_body .= '<td class="check"><input class="form-check table-check" name="table-check" id="table-check-'.$id.'" type="checkbox"></td>';
		}

		foreach ($nomes as $atual) {
			if($atual == 'actions') {
				$this->table_body .= '<td>';
				$this->table_body .= "<a href=\"$link\"><button class=\"btn btn-success btn-xsm\">Editar</button></a> ";

				if($this->contaspag) {
					if($this->allAccess) {
						if($status == 'inativo' || $status == 'rascunho') {
							$this->table_body .= '<button class="btn btn-warning btn-xsm" onclick="ativar(this, 0);" rel="'.$ativar.'">Ativar</button>';
						} else {
							$this->table_body .= '<button class="btn btn-danger btn-xsm" onclick="deletar(this, 0);" rel="'.$delete.'">Inativar</button>';
						}

					}
				} else {
					if($status == 'inativo' || $status == 'rascunho') {
						$this->table_body .= '<button class="btn btn-warning btn-xsm" onclick="ativar(this, 0);" rel="'.$ativar.'">Ativar</button>';
					} else {
						$this->table_body .= '<button class="btn btn-danger btn-xsm" onclick="deletar(this, 0);" rel="'.$delete.'">Inativar</button>';
					}

				}

				if($this->allAccess) {
					$this->table_body .= '<button class="btn btn-danger btn-xsm" onclick="deletar2(this, 0);" rel="'.$delete2.'">Deletar</button>';
				}

				$this->table_body .= '</td>';
			} else {
				if(!empty($id)) {
					$this->table_body .= "<td><a href=\"$link\">$atual</a></td>";
				} else {
					$this->table_body .= "<td>$atual</td>";
				}

			}
		}

		$this->table_body .= '</tr>';
	}

	/**
	 * Gera a tabela completa
	 */
	public function closeTable() {
		if($this->table_body == '<tbody>') {
			$this->table = '';
		} else {
			$this->table = $this->table_open . $this->table_head . $this->table_body . '</tbody></table>';
		}
	}
}