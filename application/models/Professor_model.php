<?php

class Professor_model extends CI_Model
{

	public function findAllGrid($search, $order, $dir)
	{
		$resultado = array();

		$this->load->library('session');
		log_message('info', 'Busca em Professores => usuário ['. $this->session->usuario['usu_login'] .']');
		
		$sql = 'SELECT (CONCAT("<a onclick=\'editarProfessor(this);\' title=\'Editar Professor\' 
									data-toggle=\'modal\' data-target=\'#myModal\' 
									id=\'editar", pro_id ,"\' class=\'one-action-grid\' 
									href=\'#\' data-id=\'", pro_id, "\'>
										<p class=\'fa fa-14x edit fa-edit\'></p>
								</a>")) AS acao
						, pro_nome
						, pro_rg
						, (CONCAT(SUBSTR(pro_cpf,1,3),".",SUBSTR(pro_cpf,4,3),".",SUBSTR(pro_cpf,7,3),"-",SUBSTR(pro_cpf,10,2))) AS pro_cpf
						, DATE_FORMAT(pro_data_nascimento, \'%d/%m/%Y\') as pro_data_nascimento
						, pro_status
				FROM professor ';

		if (!empty($search)){
			$sql .= "WHERE pro_nome LIKE '%$search%'
						OR pro_rg LIKE '$search%' 
						OR pro_cpf LIKE '$search%' 
						OR pro_status LIKE '$search%' ";
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
		log_message('info', 'Tentativa de inserção de professor ['. $dados['pro_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');

		$dados['pro_cpf'] = str_replace(array('.', '-'), '', $dados['pro_cpf']);

		if (!$this->validaProfessorExistente(array('pro_cpf' => $dados['pro_cpf']))){

			$auxData = explode('/', $dados['pro_data_nascimento']);
			$dados['pro_data_nascimento'] = $auxData[2] . '-' . $auxData[1] . '-' . $auxData[0];

			$dados = array_filter($dados);
			$keys = implode(', ', array_keys($dados));

			//array_map adiciona aspas simples nos dados
			$values = implode(', ', array_map(function($value){return "'" . $value . "'";}, $dados));

			$sql = "INSERT INTO professor (". $keys . ")
					VALUES (". $values .")";

			if(!$this->db->simple_query($sql)){
				log_message('info', 'Inserção de professor não efetuada ['. $dados['pro_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
				$retorno = false;
			} else {
				log_message('info', 'Inserção de professor efetuada ['. $dados['pro_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
			}
		} else {
			log_message('info', 'Professor duplicado ['. $dados['pro_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
			$retorno = array('status' => 'duplicado');
		}

		return $retorno;
	}

	function validaProfessorExistente($nome)
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
			$where .= " AND $key $type ";
			if(!is_numeric($dado))
				if ($type === 'like')
					$where .= "'%$dado%'";
				else
					$where .= "'$dado'";
			else
				$where .= $dado;
		}

		$sql = "SELECT * FROM professor pro LEFT JOIN usuario usu ON pro.usu_id = usu.usu_id WHERE 1=1 $where";

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
		log_message('info', 'Tentativa de atualização de professor ['. $dados['pro_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');

		$dados['pro_cpf'] = str_replace(array('.', '-'), '', $dados['pro_cpf']);

		if ($this->validaProfessorExistente(array('pro_id' => $dados['pro_id']))){

			//valida professor duplicado, para aquele id PODE
			$buscaProfessorEditar = $this->findBy(array('pro_cpf' => $dados['pro_cpf']));
			if(empty($buscaProfessorEditar) || $buscaProfessorEditar[0]->pro_id == $dados['pro_id']){

				$dados = array_filter($dados);
				$pro_id = $dados['pro_id'];
				unset($dados['pro_id']);

				$auxData = explode('/', $dados['pro_data_nascimento']);
				$dados['pro_data_nascimento'] = $auxData[2] . '-' . $auxData[1] . '-' . $auxData[0];

				$keys = array_keys($dados);

				//array_map adiciona aspas simples nos dados
				$values = array_map(function($value){return "'" . $value . "'";}, $dados);

				$cont = count($dados);
				for($i = 0; $i < $cont; $i++)
					$auxSet[] = $keys[$i] . ' = ' . $values[$keys[$i]];

				$set = implode(', ', $auxSet);

				$sql = "UPDATE professor SET $set
						WHERE pro_id = $pro_id";


				if(!$this->db->simple_query($sql)){
					log_message('info', 'Atualização não efetuada ['. $dados['pro_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
					$retorno = false;
				} else {
					log_message('info', 'Atualização efetuada com sucesso ['. $dados['pro_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
				}
			} else {
				log_message('info', 'Professor duplicado ['. $dados['pro_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
				$retorno = array('status' => 'duplicado');
			}
		} else {
			log_message('info', 'Professor não existe ['. $dados['pro_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
			$retorno = false;
		}

		return $retorno;
	}
}
