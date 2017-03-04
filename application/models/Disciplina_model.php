<?php

class Disciplina_model extends CI_Model
{

	public function findAllGrid($search, $order, $dir)
	{
		$resultado = array();

		
		$this->load->library('session');
		log_message('info', 'Busca em Disciplinas => usuário ['. $this->session->usuario['usu_login'] .']');

		$sql = 'SELECT (CONCAT("<a onclick=\'editarDisciplina(this);\' title=\'Editar Disciplina\' 
									data-toggle=\'modal\' data-target=\'#myModal\' 
									id=\'editar", dis_id ,"\' class=\'one-action-grid\' 
									href=\'#\' data-id=\'", dis_id, "\'>
										<p class=\'fa fa-14x edit fa-edit\'></p>
								</a>")) AS acao
						, dis_nome
						, dis_status
				FROM disciplina ';

		if (!empty($search)){
			$sql .= "WHERE dis_nome LIKE '%$search%'
						OR dis_status LIKE '$search%' ";
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
		log_message('info', 'Tentativa de inserção de disciplina ['. $dados['dis_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');

		if (!$this->validaDisciplinaExistente(array('dis_nome' => $dados['dis_nome']))){

			$dados = array_filter($dados);
			$keys = implode(', ', array_keys($dados));
			//array_map adiciona aspas simples nos dados
			$values = implode(', ', array_map(function($value){return "'" . $value . "'";}, $dados));

			$sql = "INSERT INTO disciplina (". $keys . ")
					VALUES (". $values .")";

			if(!$this->db->simple_query($sql)){
				$retorno = false;
				log_message('info', 'Inserção de disciplina não efetuada ['. $dados['dis_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
			} else {
				log_message('info', 'Inserção de disciplina efetuada ['. $dados['dis_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
			}
		} else {
			log_message('info', 'Disciplina duplicada ['. $dados['dis_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
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

		$sql = "SELECT * FROM disciplina WHERE 1=1 $where";

		if (!empty($dados['status']))
			$sql .= " AND dis_status = '". $dados['status'][0]. "'";

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
		log_message('info', 'Tentativa de atualização de disciplina ['. $dados['dis_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');

		if ($this->validaDisciplinaExistente(array('dis_id' => $dados['dis_id']))){

			//valida disciplina duplicada, para aquele id PODE
			$buscaDisciplinaEditar = $this->findBy(array('dis_nome' => $dados['dis_nome']));

			if(empty($buscaDisciplinaEditar) || $buscaDisciplinaEditar[0]->dis_id == $dados['dis_id']){
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


				if(!$this->db->simple_query($sql)){
					$retorno = false;
					log_message('info', 'Atualização não efetuada ['. $dados['dis_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
				} else {
					log_message('info', 'Atualização efetuada com sucesso ['. $dados['dis_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
				}
			} else {
				log_message('info', 'Disciplina duplicada ['. $dados['dis_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
				$retorno = array('status' => 'duplicado');
			}
		} else {
			log_message('info', 'Disciplina não existe ['. $dados['dis_nome'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
			$retorno = false;
		}

		return $retorno;
	}

	function buscarDisciplinaPorTurmaProfessor($dados)
	{
		$resultado = array();

				$this->load->library('session');

		$perfil = $this->session->usuario['per_id'];
		$usuario = $this->session->usuario['usu_id'];

		log_message('info', 'Busca de turma_disciplina_professor => usuário ['. $this->session->usuario['usu_login'] .']');

		$sql = "SELECT * 
				FROM turma_disciplina_professor tud
				INNER JOIN disciplina dis ON dis.dis_id = tud.dis_id
				INNER JOIN professor pro ON pro.pro_id = tud.pro_id
				WHERE tud.tur_id = ". $dados['tur_id'];

		if($perfil == 4){
			$sql .= " AND pro.usu_id = $usuario";
		}

		$sql .= " ORDER BY dis_nome";

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $resultado;
	}
}
