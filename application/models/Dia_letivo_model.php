<?php

class Dia_letivo_model extends CI_Model
{

	public function findAllGrid($search, $order, $dir)
	{
		$resultado = array();

		$sql = 'SELECT (CONCAT("<a onclick=\'visualizarAnoLetivo(this);\' title=\'Visualizar Dias Letivos\' 
									data-toggle=\'modal\' data-target=\'#myModal\' 
									id=\'editar", dil_id ,"\' class=\'one-action-grid\' 
									href=\'#\' data-id=\'", dil_id, "\'>
										<p class=\'fa fa-14x fa-search\'></p>
								</a>")) AS acao
						, YEAR(dil_dia_letivo) AS ano
						, (CASE 
							WHEN dil_tipo = 1
							THEN "Infantil/Fund 1"
						 		ELSE
						  		"Fund 2"
						  END) AS dil_tipo
						, dil_status
				FROM dia_letivo ';

		if (!empty($search)){
			$sql .= "WHERE YEAR(dil_dia_letivo) LIKE '%$search%'
						OR dil_status LIKE '$search%' ";
		}

		$sql .= "GROUP BY YEAR(dil_dia_letivo), dil_tipo ";

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

		if (!$this->validaAnoLetivoExistente($dados)){

			foreach($dados['dias'] as $meses){

				if (!empty($meses[0])){
					$auxDias = array_filter(explode('~', $meses[0]));

					$dias = array_unique($auxDias);

					foreach($dias as $dia){

						$sql = "INSERT INTO dia_letivo (dil_dia_letivo, dil_tipo)
								VALUES ('". $dados['ano'] . "-" . $dia ."', ".$dados['dil_tipo'].")";

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

		
		$sql = "SELECT *  FROM dia_letivo 
				WHERE YEAR(dil_dia_letivo) =
					(SELECT YEAR(dil_dia_letivo)
					FROM dia_letivo 
					WHERE dil_id = ". $dados['dil_id'] .")
				AND dil_tipo =
					(SELECT dil_tipo
					FROM dia_letivo 
					WHERE dil_id = ". $dados['dil_id'] .")";

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $resultado;
	}

	function validaAnoLetivoExistente($dados)
	{
		$retorno = false;
		$search = array('ano' => $dados['ano'], 'dil_tipo' => $dados['dil_tipo']);

		$resultado = $this->findByAno($search);

		if (!empty($resultado))
			$retorno = true;

		return $retorno;
	}

	function findByAno($search)
	{
		$resultado = array();
		
		$sql = "SELECT * FROM dia_letivo WHERE YEAR(dil_dia_letivo) = ".$search['ano']." AND dil_tipo = ".$search['dil_tipo'];

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

	function buscarDiaLetivo($dados)
	{
		$resultado = array();

		$dil_tipo = ($dados['tur_curso'] == 3) ? 2 : 1;
		$auxData = explode('/', $dados['dia_letivo']);
		$data = $auxData[2] . '-' . $auxData['1'] . '-' . $auxData[0];

		$sql = "SELECT * FROM dia_letivo WHERE dil_dia_letivo = '$data' AND dil_tipo = '$dil_tipo'";

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $resultado;
	}
}
