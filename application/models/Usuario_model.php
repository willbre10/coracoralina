<?php

class Usuario_model extends CI_Model
{
	
	public function validaLoginUsuario()
	{
		$resultado = array();

		$login = $this->input->post('login');
		$senha = $this->input->post('senha');

		
		log_message('info', 'Tentativa de Login => usuário ['. $login .']');

		$sql = "SELECT * FROM usuario WHERE usu_login = ? AND usu_senha = ? AND usu_status = ?";
		$query = $this->db->query($sql, array($login, MD5($senha), 'Ativo'));

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $resultado;
	}

	public function findAllGrid($search, $order, $dir)
	{
		$resultado = array();

		
		$this->load->library('session');
		log_message('info', 'Busca em Usuarios => usuário ['. $this->session->usuario['usu_login'] .']');

		$sql = 'SELECT (CONCAT("<a onclick=\'editarUsuario(this);\' title=\'Editar Usuário\' 
									data-toggle=\'modal\' data-target=\'#myModal\' 
									id=\'editar", usu_id ,"\' class=\'one-action-grid\' 
									href=\'#\' data-id=\'", usu_id, "\'>
										<p class=\'fa fa-14x edit fa-edit\'></p>
								</a>")) AS acao
						, usu_login
						, per_nome
						, usu_status
				FROM usuario usu
				INNER JOIN perfil per ON per.per_id = usu.per_id ';

		if (!empty($search)){
			$sql .= "WHERE usu_login LIKE '%$search%'
						OR usu_status LIKE '$search%' ";
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
		log_message('info', 'Tentativa de inserção de usuário ['. $dados['usu_login'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');

		if (!$this->validaUsuarioExistente(array('usu_login' => $dados['usu_login']))){

			$dados = array_filter($dados);
			$dados['usu_senha'] = md5($dados['usu_senha']);


			$keys = implode(', ', array_keys($dados));
			//array_map adiciona aspas simples nos dados
			$values = implode(', ', array_map(function($value){return "'" . $value . "'";}, $dados));

			$sql = "INSERT INTO usuario (". $keys . ")
					VALUES (". $values .")";

			if(!$this->db->simple_query($sql)){
				$retorno = false;
				log_message('info', 'Inserção de usuário não efetuada ['. $dados['usu_login'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
			} else {
				log_message('info', 'Inserção de usuário efetuada ['. $dados['usu_login'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
			}
		} else {
			$retorno = array('status' => 'duplicado');
			log_message('info', 'Usuário duplicado ['. $dados['usu_login'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
		}

		return $retorno;
	}

	function validaUsuarioExistente($login)
	{
		$retorno = false;

		$resultado = $this->findBy($login);
		if (!empty($resultado))
			$retorno = true;

		return $retorno;
	}

	function findBy($dados)
	{
		$where = '';
		$resultado = '';

		
		foreach($dados as $key => $dado){
			$where .= " AND $key = ";
			$where .= !is_numeric($dado) ? "'$dado'" : $dado;
		}

		$sql = "SELECT * FROM usuario WHERE 1=1 $where";

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
		log_message('info', 'Tentativa de atualização de usuário ['. $dados['usu_login'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');

		if ($this->validaUsuarioExistente(array('usu_id' => $dados['usu_id']))){

			//valida usuario duplicado, para aquele id PODE
			$buscaUsuarioEditar = $this->findBy(array('usu_login' => $dados['usu_login']));

			if(empty($buscaUsuarioEditar) || $buscaUsuarioEditar[0]->usu_id == $dados['usu_id']){
				$dados = array_filter($dados);
				$dados['usu_senha'] = md5($dados['usu_senha']);
				$usu_id = $dados['usu_id'];
				unset($dados['usu_id']);
				$keys = array_keys($dados);

				//array_map adiciona aspas simples nos dados
				$values = array_map(function($value){return "'" . $value . "'";}, $dados);

				$cont = count($dados);
				for($i = 0; $i < $cont; $i++)
					$auxSet[] = $keys[$i] . ' = ' . $values[$keys[$i]];

				$set = implode(', ', $auxSet);

				$sql = "UPDATE usuario SET $set
						WHERE usu_id = $usu_id";

				if(!$this->db->simple_query($sql)){
					$retorno = false;
					log_message('info', 'Atualização não efetuada ['. $dados['usu_login'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
				} else {
					log_message('info', 'Atualização efetuada com sucesso ['. $dados['usu_login'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
				}

			} else {
				log_message('info', 'Usuário duplicado ['. $dados['usu_login'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
				$retorno = array('status' => 'duplicado');
			}
		} else {
			log_message('info', 'Usuário não existe ['. $dados['usu_login'] .'] => usuário ['. $this->session->usuario['usu_login'] .']');
			$retorno = false;
		}

		return $retorno;
	}

	function buscarUsuarioProfessor($dados)
	{
		$resultado = '';

		$this->load->library('session');
		log_message('info', 'Busca de usuario por perfil => usuário ['. $this->session->usuario['usu_login'] .']');

		$sql = "SELECT *
				FROM usuario usu 
				WHERE per_id = 4 AND usu_status = 'Ativo' AND usu_login LIKE '%".$dados['usu_nome']."%'";

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $resultado;
	}

}
