<?php

class Dias_letivos_model extends CI_Model
{

	public function findAllGrid($search, $order, $dir)
	{
		$resultado = array();

		$this->load->database();
		
		$sql = 'SELECT (CONCAT("<a onclick=\'editarAnoLetivo(this);\' title=\'Editar Dias Letivos\' 
									data-toggle=\'modal\' data-target=\'#myModal\' 
									id=\'editar", dil_dia_letivo ,"\' class=\'one-action-grid\' 
									href=\'#\' data-id=\'", dil_dia_letivo, "\'>
										<p class=\'fa fa-14x fa-edit\'></p>
								</a>")) AS acao
						, dil_dia_letivo
						, dil_status
				FROM dia_letivo ';

		if (!empty($search)){
			$sql .= "WHERE dil_dia_letivo LIKE '%$search%'
						OR dil_status LIKE '$search%' ";
		}

		if (!empty($order)){
			$sql .= "ORDER BY $order $dir";
		}

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $resultado;
	}

	function inserir($dados)
	{
		echo "<pre>";print_r($dados);die;
		$retorno = true;
		$insert = '';

		$this->load->database();

		if (!$this->validaDisciplinaExistente(array('dis_nome' => $dados['dis_nome']))){

			$dados = array_filter($dados);
			$keys = implode(', ', array_keys($dados));
			//array_map adiciona aspas simples nos dados
			$values = implode(', ', array_map(function($value){return "'" . $value . "'";}, $dados));

			$sql = "INSERT INTO disciplina (". $keys . ")
					VALUES (". $values .")";

			if(!$this->db->simple_query($sql))
				$retorno = false;
		} else {
			$retorno = array('status' => 'duplicado');
		}

		return $retorno;
	}

	function validaDisciplinaExistente($nome)
	{
		$retorno = false;

		$resultado = $this->findBy($nome);
		if (!empty($resultado))
			$retorno = true;


		return $retorno;
	}

	function findBy($dados)
	{
		$type = '=';
		$where = '';
		$resultado = '';

		if (isset($dados['type_search'])){
			$type = $dados['type_search'];
			unset($dados['type_search']);
		}

		$this->load->database();

		foreach($dados as $key => $dado){
			$where .= " AND $key $type ";
			if(!is_numeric($dado))
				if ($type === 'like')
					$where .= "'%$dado%'";
				else
					$where .= "'$dado'";
			else
				$where .= $dado;
		}

		$sql = "SELECT * FROM disciplina WHERE 1=1 $where";

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $resultado;
	}

	function atualizar($dados)
	{
		$retorno = true;
		$set = '';
		$auxSet = array();

		$this->load->database();

		if ($this->validaDisciplinaExistente(array('dis_id' => $dados['dis_id']))){

			$dados = array_filter($dados);
			$dis_id = $dados['dis_id'];
			unset($dados['dis_id']);
			$keys = array_keys($dados);

			//array_map adiciona aspas simples nos dados
			$values = array_map(function($value){return "'" . $value . "'";}, $dados);

			$cont = count($dados);
			for($i = 0; $i < $cont; $i++)
				$auxSet[] = $keys[$i] . ' = ' . $values[$keys[$i]];

			$set = implode(', ', $auxSet);

			$sql = "UPDATE disciplina SET $set
					WHERE dis_id = $dis_id";


			if(!$this->db->simple_query($sql))
				$retorno = false;
		} else {
			$retorno = false;
		}

		return $retorno;
	}
}
