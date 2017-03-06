<?php

class Turma_model extends CI_Model
{

	public function findAllGrid($search, $order, $dir)
	{
		$resultado = array();

				
		$sql = 'SELECT (CONCAT("<a onclick=\'editarTurma(this);\' title=\'Editar Turma\' 
									data-toggle=\'modal\' data-target=\'#myModal\' 
									id=\'editar", tur_id ,"\' class=\'one-action-grid\' 
									href=\'#\' data-id=\'", tur_id, "\'>
										<p class=\'fa fa-14x edit fa-edit\'></p>
								</a>")) AS acao
						, tur_nome
						, tur_ano
						, tur_status
				FROM turma ';

		if (!empty($search)){
			$sql .= "WHERE tur_nome LIKE '%$search%'
						OR tur_ano LIKE '$search%''
						OR tur_status LIKE '$search%' ";
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

		
		if (!$this->validaTurmaExistente(array('tur_nome' => $dados['tur_nome']))){

			$sql = "INSERT INTO turma (tur_nome, tur_ano)
					VALUES ('". $dados['tur_nome'] ."', ". $dados['tur_ano'] .")";

			if(!$this->db->simple_query($sql)){
				$retorno = false;
			} else {
				$this->inserirTurmaDisciplina($dados, $this->db->insert_id());
			}
		} else {
			$retorno = array('status' => 'duplicado');
		}

		return $retorno;
	}

	function validaTurmaExistente($nome)
	{
		$retorno = false;

		$resultado = $this->findBy($nome);
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

		$sql = "SELECT * FROM turma WHERE 1=1 $where";

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $resultado;
	}

	function atualizar($dados)
	{
		$retorno = true;

				if ($this->validaTurmaExistente(array('tur_id' => $dados['tur_id']))){

			$sql = "UPDATE turma 
					SET tur_ano = ". $dados['tur_ano'] .", tur_nome = '". $dados['tur_nome'] ."', tur_curso = '". $dados['tur_curso'] ."'
					WHERE tur_id = ". $dados['tur_id'];

			if(!$this->db->simple_query($sql))
				$retorno = false;

			$dados['alu_id'] = array_filter($dados['alu_id']);

			if (!empty($dados['alu_id']))
				$this->atualizarAlunos($dados);

			$dados['dis_id'] = array_filter($dados['dis_id']);
			$dados['pro_id'] = array_filter($dados['pro_id']);

			if (!empty($dados['dis_id']))
				$this->atualizarDisciplinas($dados);

		} else {
			$retorno = false;
		}

		return $retorno;
	}

	private function atualizarDisciplinas($dados)
	{
		$retorno = true;

		$cont = count($dados['dis_id']);

		for($i = 0; $i < $cont; $i++){
			$sql = "INSERT INTO turma_disciplina_professor (tur_id, dis_id)
					VALUES (". $dados['tur_id'] .", ". $dados['dis_id'][$i] .", ". $dados['pro_id'][$i] .")";

			if(!$this->db->simple_query($sql))
				$retorno = false;
			else
				//get ids inseridos
				$idsTurmasDiscProf[] = $this->db->insert_id();
		}

		$sql = "SELECT DISTINCT alu_id
				FROM turma_disciplina_professor tdp
				INNER JOIN aluno_turma_disciplina_professor atd ON atd.tdp_id = tdp.tdp_id
				WHERE tdp.tur_id = ". $dados['tur_id'];

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		foreach ($idsTurmasDiscProf as $tdp_id){
			foreach ($resultado as $aluno){

				$sql = "INSERT INTO aluno_turma_disciplina_professor (alu_id, tdp_id)
						VALUES ($aluno->alu_id, $tdp_id)";

				if(!$this->db->simple_query($sql))
					$retorno = false;
			}
		}

		return $retorno;
	}

	private function atualizarAlunos($dados)
	{
		$retorno = true;

		$sql = "SELECT tdp_id
				FROM turma_disciplina_professor
				WHERE tur_id = ". $dados['tur_id'];

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $turma_disciplina_professor[] = $row;
		}

		if(!empty($turma_disciplina_professor)){

			$contAlunos = count($dados['alu_id']);
			for($i = 0; $i < $contAlunos; $i++){
				foreach($turma_disciplina_professor as $tdp){
					$sql = "INSERT INTO aluno_turma_disciplina_professor (alu_id, tdp_id, atd_numero_aluno)
							VALUES (".$dados['alu_id'][$i].", $tdp->tdp_id, ".$dados['atd_numero_aluno'][$i].")";

					if(!$this->db->simple_query($sql))
						$retorno = false;
				}
			}

		} else {
			$retorno = false;
		}

		return $retorno;
	}

	private function inserirTurmaDisciplina($dados, $id)
	{
		$retorno = true;
		$dados['dis_id'] = array_filter($dados['dis_id']);
		$dados['pro_id'] = array_filter($dados['pro_id']);
		
		$i = 0;

		foreach ($dados['dis_id'] as $dis_id){

			$sql = "INSERT INTO turma_disciplina_professor (tur_id, dis_id, pro_id)
					VALUES (". $id .", ". $dis_id .", ". $dados['pro_id'][$i] .")";
		
			if(!$this->db->simple_query($sql))
				$retorno = false;
			else
				//get ids inseridos
				$idsTurmasDisc[] = $this->db->insert_id();
			$i++;
		}

		if ($retorno)
			$this->inserirAlunoTurmaDisciplina($dados, $idsTurmasDisc);

		return $retorno;
	}

	private function inserirAlunoTurmaDisciplina($dados, $ids)
	{
		$retorno = true;
		$dados['alu_id'] = array_filter($dados['alu_id']);

		foreach ($ids as $id){

			$cont = count($dados['alu_id']);
			for($i = 0; $i < $cont; $i++){
				$sql = "INSERT INTO aluno_turma_disciplina_professor (tdp_id, alu_id, atd_numero_aluno)
						VALUES (". $id .", ". $dados['alu_id'][$i] .", ". $dados['atd_numero_aluno'][$i] .")";

				if(!$this->db->simple_query($sql))
					$retorno = false;
			}
		}

		return $retorno;
	}

	function buscarAlunoTurmaDisciplina($idTurma)
	{
		$where = '';
		$disciplina_professor = array();
		
		$sql = "SELECT DISTINCT tur.tur_id, tur.tur_nome, tur.tur_ano, tur.tur_curso, dis.dis_id, dis.dis_nome, pro.pro_id, pro.pro_nome
				FROM turma tur
				INNER JOIN turma_disciplina_professor tdp ON tdp.tur_id = tur.tur_id
				INNER JOIN disciplina dis ON dis.dis_id = tdp.dis_id
				INNER JOIN professor pro ON pro.pro_id = tdp.pro_id
				WHERE tur.tur_id = ". $idTurma['tur_id'];

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $disciplina_professor[] = $row;
		}

		$sql = "SELECT DISTINCT alu.alu_id, alu.alu_nome, alu.alu_data_nascimento, atd.atd_numero_aluno
				FROM turma tur
				INNER JOIN turma_disciplina_professor tdp ON tdp.tur_id = tur.tur_id
				INNER JOIN aluno_turma_disciplina_professor atd ON atd.tdp_id = tdp.tdp_id
				INNER JOIN aluno alu ON alu.alu_id = atd.alu_id
				WHERE tur.tur_id = ". $idTurma['tur_id'];

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $aluno[] = $row;
		}

		$dadosTradados = $this->trataDadosTurmaDisciplina($disciplina_professor);
		$dadosTradados['alunos'] = $this->trataDadosAluno($aluno);

		return $dadosTradados;
	}

	function buscarTurma()
	{
		$resultado = array();

		$this->load->library('session');

		$perfil = $this->session->usuario['per_id'];
		$usuario = $this->session->usuario['usu_id'];

		$sql = "SELECT DISTINCT tur.*
				FROM turma tur
				INNER JOIN turma_disciplina_professor tdp ON tdp.tur_id = tur.tur_id
				INNER JOIN disciplina dis ON dis.dis_id = tdp.dis_id
				INNER JOIN professor pro ON pro.pro_id = tdp.pro_id
				WHERE 1=1 ";

		if($perfil == 4){
			$sql .= "AND pro.usu_id = $usuario";
		}

		$sql .= " ORDER BY tur_nome";

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $resultado;
	}

	private function trataDadosTurmaDisciplina($dados)
	{
		$newDados = array();

		$newDados['tur_id'] = $dados[0]->tur_id;
		$newDados['tur_nome'] = $dados[0]->tur_nome;
		$newDados['tur_ano'] = $dados[0]->tur_ano;
		$newDados['tur_curso'] = $dados[0]->tur_curso;

		$i = 0;
		foreach($dados as $dado){

			$newDados['disciplinas'][$i]['dis_id'] = $dado->dis_id;
			$newDados['disciplinas'][$i]['dis_nome'] = $dado->dis_nome;

			$newDados['professores'][$i]['pro_id'] = $dado->pro_id;
			$newDados['professores'][$i]['pro_nome'] = $dado->pro_nome;
			$i++;
		}

		return $newDados;
	}

	private function trataDadosAluno($dados)
	{
		$newDados = array();

		$i = 0;
		foreach($dados as $dado){
			$newDados[$i]['alu_id'] = $dado->alu_id;
			$newDados[$i]['alu_nome'] = $dado->alu_nome;
			$newDados[$i]['atd_numero_aluno'] = $dado->atd_numero_aluno;
			$newDados[$i]['alu_data_nascimento'] = $dado->alu_data_nascimento;

			$i++;
		}

		return $newDados;
	}
}
