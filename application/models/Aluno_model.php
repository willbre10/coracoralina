<?php

class Aluno_model extends CI_Model
{

	public function findAllGrid($search, $order, $dir)
	{
		$resultado = array();

		$this->load->library('session');
		log_message('info', 'Busca em Alunos => usuário ['. $this->session->usuario['usu_login'] .']');
		
		$sql = 'SELECT (CONCAT("<a onclick=\'editarAluno(this);\' title=\'Editar Aluno\' 
									data-toggle=\'modal\' data-target=\'#myModal\' 
									id=\'editar", alu_id ,"\' class=\'one-action-grid\' 
									href=\'#\' data-id=\'", alu_id, "\'>
										<p class=\'fa edit fa-14x fa-edit\'></p>
								</a>")) AS acao
						, alu_nome
						, alu_rg
						, alu_ra
						, DATE_FORMAT(alu_data_nascimento, \'%d/%m/%Y\') as alu_data_nascimento
						, alu_status
				FROM aluno ';

		if (!empty($search)){
			$sql .= "WHERE alu_nome LIKE '%$search%'
						OR alu_rg LIKE '%$search%'
						OR alu_ra LIKE '%$search%'
						OR alu_status LIKE '$search%' ";
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

		$this->load->library('session');
		log_message('info', 'Tentativa de inserção de aluno ['. $dados['alu_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');

		if (!$this->validaAlunoExistente(array('alu_nome' => $dados['alu_nome']))){
			$auxData = explode('/', $dados['alu_data_nascimento']);
			$dados['alu_data_nascimento'] = $auxData[2] . '-' . $auxData[1] . '-' . $auxData[0];

			if (array_key_exists('alu_cep', $dados))
				$dados['alu_cep'] = str_replace(array('.', '-'), '', $dados['alu_cep']);

			$dados = array_filter($dados);
			$keys = implode(', ', array_keys($dados));

			//array_map adiciona aspas simples nos dados
			$values = implode(', ', array_map(function($value){return "'" . $value . "'";}, $dados));

			$sql = "INSERT INTO aluno (". $keys . ")
					VALUES (". $values .")";

			if(!$this->db->simple_query($sql)){
				log_message('info', 'Inserção de aluno não efetuada ['. $dados['alu_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
				$retorno = false;
			} else {
				log_message('info', 'Inserção de aluno efetuada ['. $dados['alu_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
			}
		} else {
			log_message('info', 'Aluno duplicado ['. $dados['alu_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
			$retorno = array('status' => 'duplicado');
		}

		return $retorno;
	}

	function validaAlunoExistente($nome)
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

		foreach($dados as $key => $dado){
			if (!is_array($dado)){
				$where .= " AND $key $type ";
				if(!is_numeric($dado))
					if ($type === 'like')
						$where .= "'%$dado%'";
					else
						$where .= "'$dado'";
				else
					$where .= $dado;
			}
		}

		$sql = "SELECT * FROM aluno WHERE 1=1 $where";

		if (!empty($dados['status']))
			$sql .= " AND alu_status = '". $dados['status'][0]. "'";

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

		$this->load->library('session');
		log_message('info', 'Tentativa de atualização de aluno ['. $dados['alu_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');

		if ($this->validaAlunoExistente(array('alu_id' => $dados['alu_id']))){

			//valida aluno duplicado, para aquele id PODE
			$buscaAlunoEditar = $this->findBy(array('alu_nome' => $dados['alu_nome']));
			if(empty($buscaAlunoEditar) || $buscaAlunoEditar[0]->pro_id == $dados['alu_id']){
				$dados = array_filter($dados);
				$alu_id = $dados['alu_id'];
				unset($dados['alu_id']);

				$auxData = explode('/', $dados['alu_data_nascimento']);
				$dados['alu_data_nascimento'] = $auxData[2] . '-' . $auxData[1] . '-' . $auxData[0];

				$keys = array_keys($dados);

				//array_map adiciona aspas simples nos dados
				$values = array_map(function($value){return "'" . $value . "'";}, $dados);

				$cont = count($dados);
				for($i = 0; $i < $cont; $i++)
					$auxSet[] = $keys[$i] . ' = ' . $values[$keys[$i]];

				$set = implode(', ', $auxSet);

				$sql = "UPDATE aluno SET $set
						WHERE alu_id = $alu_id";


				if(!$this->db->simple_query($sql)){
					log_message('info', 'Atualização não efetuada ['. $dados['alu_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
					$retorno = false;
				} else {
					log_message('info', 'Atualização efetuada com sucesso ['. $dados['alu_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
				}
			} else {
				log_message('info', 'Aluno duplicado ['. $dados['alu_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
				$retorno = array('status' => 'duplicado');
			}
		} else {
			log_message('info', 'Aluno não existe ['. $dados['alu_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
			$retorno = false;
		}

		return $retorno;
	}

	public function importar($arquivo)
	{
		$retorno = true;
		$status['sucesso'] = 0;
		$status['erro'] = 0;
		$status['duplicado'] = 0;
		$row = 1;

		if (strrev(substr(strrev($arquivo['name']), 0, 3)) == 'csv'){
			if (($handle = fopen($arquivo['tmp_name'], "r")) !== FALSE) {
			    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

			        $num = count($data);
			        $row++;
			        $auxValor = explode(';', utf8_encode($data[0]));
			        if ($auxValor[0] != 'Nome' && $auxValor[3] != 'RA' && $auxValor[0] != 'Nome' && $auxValor[3] != 'RA')
			        	$valores[] = $auxValor;
			    }
			    fclose($handle);
			}

			$valores = array_filter(array_map('array_filter', $valores));

			foreach($valores as $valor){

				$newArray['alu_nome'] = !empty($valor[0]) ? $valor[0] : null;
				$newArray['alu_rg'] = !empty($valor[1]) ? $valor[1] : null;
				$newArray['alu_data_nascimento'] = !empty($valor[2]) ? $valor[2] : null;
				$newArray['alu_ra'] = !empty($valor[3]) ? $valor[3] : null;
				$newArray['alu_sexo'] = !empty($valor[4]) ? $valor[4] : null;
				$newArray['alu_estado'] = !empty($valor[5]) ? $valor[5] : null;
				$newArray['alu_endereco'] = !empty($valor[6]) ? $valor[6] : null;
				$newArray['alu_bairro'] = !empty($valor[7]) ? $valor[7] : null;
				$newArray['alu_cidade'] = !empty($valor[8]) ? $valor[8] : null;
				$newArray['alu_numero'] = !empty($valor[9]) ? $valor[9] : null;
				$newArray['alu_cep'] = !empty($valor[10]) ? $valor[10] : null;

				$retorno = $this->inserir($newArray);

				if(is_array($retorno) && $retorno['status'] == 'duplicado')
					$status['duplicado']++;
				else if($retorno)
					$status['sucesso']++;
				else
					$status['erro']++;
			}
		} else {
			$status['arquivoErro'] = "A extensão do arquivo deve ser .csv";
		}

		return $status;
	}
}
