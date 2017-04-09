<?php

/**
 * IceHabbo
 * by Henrique Arthur <eu@henriquearthur.me>
 * Não use sem autorização.
 */

class SQLActions {
	public $conn;
	public $error;

	/**
	 * Construtor
	 * @param object $conn conexão PDO com o banco de dados
	 */
	public function __construct($conn) {
		$this->conn = $conn;
	}

	/**
	 * Inserir uma linha numa tabela
	 * @param  string $tabela nome da tabela
	 * @param  array  $dados  dados a serem inseridos e respectivas colunas
	 * @return boolean        se foi inserido ou não
	 */
	public function insert($tabela, $dados) {
		$colunas = array();
		$valores = array();

		foreach ($dados as $coluna => $valor) {
			$colunas[] = $coluna;
			$valores[] = $valor;
		}

		if(count($colunas) != count($valores)) {
			$this->error = 'DB01';
			return false;
		}

		$colunas_query = implode(",", $colunas);

		$valores_query = array();

		foreach ($colunas as $atual) {
			$valores_query[] = '?';
		}

		$valores_query = implode(",", $valores_query);

		$query = "INSERT INTO $tabela ($colunas_query) VALUES ($valores_query)";

		$sql = $this->conn->prepare($query);

		for ($i=0; $i < count($colunas); $i++) {
			$i_real = $i + 1;

			$sql->bindValue($i_real, $valores[$i]);
		}

		if($sql->execute()) {
			return true;
		} else {
			$this->error = 'DB02';
			return false;
		}
	}

	/**
	 * Atualizar linha de uma tabela
	 * @param  string $tabela nome da tabela
	 * @param  array  $dados  dados a serem atualizados e suas respectivas colunas
	 * @param  array  $where  condição para selecionar linha
	 * @return boolean        se atualizou ou não
	 */
	public function update($tabela, $dados, $where = array()) {
		$colunas = array();
		$valores = array();
		$condicoes_col = array();
		$condicoes_val = array();

		foreach ($dados as $coluna => $valor) {
			$colunas[] = $coluna;
			$valores[] = $valor;
		}

		foreach ($where as $coluna => $valor) {
			$condicoes_col[] = $coluna;
			$condicoes_val[] = $valor;
		}

		if(count($colunas) != count($valores)) {
			$this->error = 'DB01';
			return false;
		}

		$i = 0;
		$set_query = '';
		foreach ($colunas as $atual) {
			$set_query .= $atual . '=?,';

			$i++;
		}

		$set_query = substr($set_query, 0, -1);

		if(empty($where)) {
			$query = "UPDATE $tabela SET $set_query";
		} else {
			$where_query = '';
			foreach ($where as $coluna => $valor) {
				$where_query .= $coluna . '=? AND ';
			}

			$where_query = substr($where_query, 0, -strlen(' AND '));

			$query = "UPDATE $tabela SET $set_query WHERE $where_query";
		}

		$sql = $this->conn->prepare($query);

		for ($i=0; $i < count($colunas); $i++) {
			$i_real = $i + 1;

			$sql->bindValue($i_real, $valores[$i]);
		}

		for ($i=0; $i < count($condicoes_col); $i++) {
			$i_real = $i_real + 1;

			$sql->bindValue($i_real, $condicoes_val[$i]);
		}

		if($sql->execute()) {
			return true;
		} else {
			$this->error = 'DB02';
			return false;
		}
	}

	/**
	 * Inativar uma linha de uma tabela
	 * @param  string $tabela nome da tabela
	 * @param  array  $where   condição para selecionar linha
	 * @return boolean        se foi deletado ou não
	 */
	public function delete($tabela, $where) {
		$condicoes_col = array();
		$condicoes_val = array();

		foreach ($where as $coluna => $valor) {
			$condicoes_col[] = $coluna;
			$condicoes_val[] = $valor;
		}

		$where_query = '';
		foreach ($where as $coluna => $valor) {
			$where_query .= $coluna . '=? AND ';
		}

		$where_query = substr($where_query, 0, -strlen(' AND '));

		$query = "DELETE FROM $tabela WHERE $where_query";

		$sql = $this->conn->prepare($query);

		for ($i=0; $i < count($condicoes_col); $i++) {
			$i_real = $i + 1;

			$sql->bindValue($i_real, $condicoes_val[$i]);
		}

		if($sql->execute()) {
			return true;
		} else {
			$this->error = 'DB02';
			return false;
		}
	}
}

$sqlActions = new SQLActions($conn);