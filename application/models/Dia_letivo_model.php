<?php

class Dia_letivo_model extends CI_Model
{

	public function findAllGrid($search, $order, $dir)
	{
		$resultado = array();

		$this->load->database();
		
		$sql = 'SELECT (CONCAT("<a onclick=\'visualizarAnoLetivo(this);\' title=\'Visualizar Dias Letivos\' 
									data-toggle=\'modal\' data-target=\'#myModal\' 
									id=\'editar", dil_id ,"\' class=\'one-action-grid\' 
									href=\'#\' data-id=\'", dil_id, "\'>
										<p class=\'fa fa-14x fa-search\'></p>
								</a>")) AS acao
						, YEAR(dil_dia_letivo) AS ano
						, dil_status
				FROM dia_letivo ';

		if (!empty($search)){
			$sql .= "WHERE YEAR(dil_dia_letivo) LIKE '%$search%'
						OR dil_status LIKE '$search%' ";
		}

		$sql .= "GROUP BY YEAR(dil_dia_letivo) ";

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
		$retorno = true;
		$insert = '';

		$this->load->database();

		if (!$this->validaAnoLetivoExistente($dados['ano'])){

			foreach($dados['dias'] as $meses){

				if (!empty($meses[0])){
					$auxDias = array_filter(explode('~', $meses[0]));

					$dias = array_unique($auxDias);

					foreach($dias as $dia){

						$sql = "INSERT INTO dia_letivo (dil_dia_letivo)
								VALUES ('". $dados['ano'] . "-" . $dia ."')";

						if(!$this->db->simple_query($sql)){
							$retorno = false;

							$sql = "DELETE FROM dia_letivo WHERE YEAR(dil_dia_letivo) = ". $dados['ano'];
							$this->db->simple_query($sql);
						}
					}
				}
			}
		} else {
			$retorno = array('status' => 'duplicado');
		}

		return $retorno;
	}

	function buscarAnoLetivo($dados)
	{
		$resultado = array();

		$this->load->database();

		$sql = "SELECT *  FROM dia_letivo WHERE YEAR(dil_dia_letivo) =
					(SELECT YEAR(dil_dia_letivo)
					FROM dia_letivo 
					WHERE dil_id = ". $dados['dil_id'] .")";

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $resultado;
	}

	function validaAnoLetivoExistente($ano)
	{
		$retorno = false;

		$resultado = $this->findByAno($ano);

		if (!empty($resultado))
			$retorno = true;


		return $retorno;
	}

	function findByAno($ano)
	{
		$resultado = array();
		
		$this->load->database();

		$sql = "SELECT * FROM dia_letivo WHERE YEAR(dil_dia_letivo) = $ano";

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

	function buscarDiaLetivo($dia)
	{
		$resultado = array();
		$auxData = explode('/', $dia['dia_letivo']);
		$data = $auxData[2] . '-' . $auxData['1'] . '-' . $auxData[0];

		$this->load->database();

		$sql = "SELECT * FROM dia_letivo WHERE dil_dia_letivo = '$data'";

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $resultado;
	}
}
