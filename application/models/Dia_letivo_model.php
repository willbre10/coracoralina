<?php

class Dia_letivo_model extends CI_Model
{

	public function findAllGrid($search, $order, $dir)
	{
		$resultado = array();

		$sql = 'SELECT (CONCAT("<a onclick=\'visualizarAnoLetivo(this);\' title=\'Visualizar Dias Letivos\' 
									data-toggle=\'modal\' data-target=\'#myModal\' 
									id=\'editar", ano_id ,"\' class=\'one-action-grid\' 
									href=\'#\' data-id=\'", ano_id, "\'>
										<p class=\'fa fa-14x fa-search\'></p>
								</a>")) AS acao
						, ano_ano AS ano
						, (CASE 
							WHEN ano_tipo = 1
							THEN "Infantil/Fund 1"
						 		ELSE
						  		"Fund 2"
						  END) AS ano_tipo
						, ano_status
				FROM ano_letivo ';

		if (!empty($search)){
			$sql .= "WHERE ano_ano LIKE '%$search%'
						OR ano_status LIKE '$search%' ";
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
		$retorno = true;
		$insert = '';

		if (!$this->validaAnoLetivoExistente($dados)){

			$sql = "INSERT INTO ano_letivo (ano_ano, ano_tipo)
					VALUES ('". $dados['ano']."', ". $dados['ano_tipo'] .")";

			$this->db->simple_query($sql);

			$ano_id = $this->db->insert_id();

			$this->inserirBimestre($dados, $ano_id);

			foreach($dados['dias'] as $meses){

				if (!empty($meses[0])){
					$auxDias = array_filter(explode('~', $meses[0]));

					$dias = array_unique($auxDias);

					foreach($dias as $dia){

						$sql = "INSERT INTO dia_letivo (dil_dia_letivo, dil_id_ano)
								VALUES ('". $dados['ano'] . "-" . $dia ."', ". $ano_id .")";

						$this->db->simple_query($sql);
					}
				}
			}

		} else {
			$retorno = array('status' => 'duplicado');
		}

		return $retorno;
	}

	private function inserirBimestre($dados, $ano_id)
	{
		for ($i = 1; $i <= 4; $i++){
			$auxInicio = explode('/', $dados[$i.'b_inicio']);
			$inicio = $dados['ano'] . '-' . $auxInicio[1] . '-' . $auxInicio[0];

			$auxFim = explode('/', $dados[$i.'b_fim']);
			$fim = $dados['ano'] . '-' . $auxFim[1] . '-' . $auxFim[0];

			$sql = "INSERT INTO bimestre (bim_bimestre, bim_inicio, bim_fim, bim_id_ano)
					VALUES (".$i.", '$inicio', '$fim', '". $ano_id . "')";

			$this->db->simple_query($sql);
		}
	}

	function buscarAnoLetivo($dados)
	{
		$resultado = array();

		$sql = "SELECT *
				FROM dia_letivo
				WHERE dil_id_ano = ". $dados['ano_id'];

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado['dia_letivo'][] = $row;
		}

		$sql = "SELECT *
				FROM ano_letivo
				WHERE ano_id = ". $dados['ano_id'];

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado['ano'][] = $row;
		}

		$sql = "SELECT *
				FROM bimestre
				WHERE bim_id_ano = ". $dados['ano_id'];

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado['bimestre'][] = $row;
		}

		return $resultado;
	}

	function validaAnoLetivoExistente($dados)
	{
		$retorno = false;
		$search = array('ano' => $dados['ano'], 'ano_tipo' => $dados['ano_tipo']);

		$resultado = $this->findByAno($search);

		if (!empty($resultado))
			$retorno = true;

		return $retorno;
	}

	function findByAno($search)
	{
		$resultado = array();
		
		$sql = "SELECT * FROM ano_letivo WHERE ano_ano = ".$search['ano']." AND ano_tipo = ".$search['ano_tipo'];

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $resultado;
	}

	function buscarDiaLetivo($dados)
	{
		$resultado = array();

		$ano_tipo = ($dados['tur_curso'] == 3) ? 2 : 1;
		$auxData = explode('/', $dados['dia_letivo']);
		$data = $auxData[2] . '-' . $auxData['1'] . '-' . $auxData[0];

		$sql = "SELECT * 
				FROM dia_letivo dil
				INNER JOIN ano_letivo ano ON ano.ano_id = dil.dil_id_ano
				INNER JOIN bimestre bim ON bim.bim_id_ano = dil.dil_id_ano
				WHERE dil.dil_dia_letivo = '$data' 
				AND bim.bim_bimestre = '". $dados['fal_bimestre'] ."'
				AND bim.bim_inicio <= '$data'
				AND bim.bim_fim >= '$data'
				AND ano.ano_tipo = '$ano_tipo'";

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $resultado;
	}
}
