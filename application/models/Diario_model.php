<?php

class Diario_model extends CI_Model
{
	public function salvar($dados)
	{
		$retorno = true;
		
		$turma_disciplina_professor = $this->buscarTurmaDisciplinaProfessor($dados);

		$tdp_id = $turma_disciplina_professor[0]->tdp_id;
		$auxDia = explode('/', $dados['dia_letivo']);
		$dia_letivo = $auxDia[2] . '-' . $auxDia[1] . '-' . $auxDia[0];

		$sql = "INSERT INTO conteudo (tdp_id, con_dia, con_conteudo, con_bimestre)
				VALUES ($tdp_id, '$dia_letivo', '" . $dados['conteudo'] . "', ". $dados['fal_bimestre'] .")";

		if(!$this->db->simple_query($sql))
			$retorno = false;

		$sql = "INSERT INTO observacao (tdp_id, obs_dia, obs_observacao, obs_bimestre)
				VALUES ($tdp_id, '$dia_letivo', '" . $dados['observacao'] . "', ". $dados['fal_bimestre'] .")";

		if(!$this->db->simple_query($sql))
			$retorno = false;

		$sql = "INSERT INTO tarefa (tdp_id, tar_dia, tar_tarefa, tar_bimestre)
				VALUES ($tdp_id, '$dia_letivo', '" . $dados['tarefa'] . "', ". $dados['fal_bimestre'] .")";

		if(!$this->db->simple_query($sql))
			$retorno = false;

		foreach($dados['faltas'] as $alu_id => $faltas){
			$turma_disciplina_professor = $this->buscarAlunoTurmaDisciplinaProfessor($tdp_id, $alu_id);
			$sql = "SELECT * 
					FROM aluno_turma_disciplina_professor atd
					WHERE atd.tdp_id = $tdp_id
					AND atd.alu_id = $alu_id";

			$query = $this->db->query($sql);

			foreach ($query->result() as $row){
			    $turma_disciplina_professor[] = $row;
			}

			$atd_id = $turma_disciplina_professor[0]->atd_id;

			$sql = "INSERT INTO falta (atd_id, fal_dia, fal_falta, fal_bimestre, fal_quantidade_aulas)
					VALUES ($atd_id, '$dia_letivo', $faltas, ". $dados['fal_bimestre'] .", ". $dados['fal_quantidade_aulas'] .")";

			if(!$this->db->simple_query($sql))
				$retorno = false;
		}

		return $retorno;
	}

	public function editar($dados)
	{
		$retorno = 'editado';
		
		if (!empty($dados['con_id'])){
			$sql = "UPDATE conteudo
					SET con_conteudo = '" . $dados['conteudo'] . "'
					WHERE con_id = " . $dados['con_id'];

			if(!$this->db->simple_query($sql))
				$retorno = false;
		}

		if (!empty($dados['obs_id'])){
			$sql = "UPDATE observacao
					SET obs_observacao = '" . $dados['observacao'] . "'
					WHERE obs_id = " . $dados['obs_id'];

			if(!$this->db->simple_query($sql))
				$retorno = false;
		}

		if (!empty($dados['tar_id'])){
			$sql = "UPDATE tarefa
					SET tar_tarefa = '" . $dados['tarefa'] . "'
					WHERE tar_id = " . $dados['tar_id'];

			if(!$this->db->simple_query($sql))
				$retorno = false;
		}

		if (!empty($dados['atds_id'])){
			$valores = explode('@', $dados['atds_id']);
			$valores = array_filter($valores);

			foreach ($valores as $valor){
				$ids = explode('/', $valor);

				$num_falta = $dados['faltas'][$ids[1]];
				$alu_id = $ids[0];

				$sql = "UPDATE falta
						SET fal_falta = $num_falta, fal_quantidade_aulas = ". $dados['fal_quantidade_aulas'] ."
						WHERE fal_id = " . $alu_id;

				if(!$this->db->simple_query($sql))
					$retorno = false;
			}
		}

		return $retorno;
	}

	public function buscarDiario($post)
	{
		$retorno = false;

		$turma_disciplina_professor = $this->buscarTurmaDisciplinaProfessor($post);

		if(!empty($turma_disciplina_professor)){
			$tdp_id = $turma_disciplina_professor[0]->tdp_id;

			$auxDia = explode('/', $post['dia']);
			$dia_letivo = $auxDia[2] . '-' . $auxDia[1] . '-' . $auxDia[0];
			$bimestre = $post['fal_bimestre'];

			$retorno['conteudo'] = current($this->buscarConteudo($tdp_id, $dia_letivo, $bimestre));
			$retorno['observacao'] = current($this->buscarObservacao($tdp_id, $dia_letivo, $bimestre));
			$retorno['tarefa'] = current($this->buscarTarefa($tdp_id, $dia_letivo, $bimestre));

			$atd_ids = array();

			$aluno_turma_disciplina_professor = $this->buscarAlunoTurmaDisciplinaProfessor($tdp_id);

			foreach($aluno_turma_disciplina_professor as $dado)
				$atd_ids[] = $dado->atd_id;

			$retorno['faltas'] = $this->buscarFalta($atd_ids, $dia_letivo, $bimestre);
		}

		return $retorno;
		
	}

	public function buscarFalta($atd_ids, $dia, $bimestre)
	{
		$resultado = array();

		$atd_id = implode(',', $atd_ids);

		$sql = "SELECT * 
				FROM falta fal
				INNER JOIN aluno_turma_disciplina_professor atd ON atd.atd_id = fal.atd_id
				WHERE fal.atd_id IN ($atd_id)
				AND fal.fal_dia = '$dia'
				AND fal.fal_bimestre = $bimestre";

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $resultado;
	}

	public function buscarConteudo($tdp_id, $dia, $bimestre)
	{
		$resultado = array();

		$sql = "SELECT * 
				FROM conteudo con
				WHERE con.tdp_id = $tdp_id
				AND con.con_dia = '$dia'
				AND con.con_bimestre = $bimestre";

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $resultado;
	}

	public function buscarObservacao($tdp_id, $dia, $bimestre)
	{
		$resultado = array();

		$sql = "SELECT * 
				FROM observacao obs
				WHERE obs.tdp_id = $tdp_id
				AND obs.obs_dia = '$dia'
				AND obs.obs_bimestre = $bimestre";

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $resultado;
	}

	public function buscarTarefa($tdp_id, $dia, $bimestre)
	{
		$resultado = array();

		$sql = "SELECT * 
				FROM tarefa tar
				WHERE tar.tdp_id = $tdp_id
				AND tar.tar_dia = '$dia'
				AND tar.tar_bimestre = $bimestre";

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $resultado;
	}

	private function buscarTurmaDisciplinaProfessor($dados)
	{
		$resultado = array();

		$sql = "SELECT * 
				FROM turma_disciplina_professor tdp
				WHERE tdp.tur_id = " . $dados['tur_id'] . "
				AND tdp.dis_id = " . $dados['dis_id'];

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $resultado;
	}

	private function buscarAlunoTurmaDisciplinaProfessor($tdp_id, $alu_id = null)
	{
		$resultado = array();

		$sql = "SELECT * 
				FROM aluno_turma_disciplina_professor atd
				WHERE atd.tdp_id = $tdp_id";

		if (!empty($alu_id))
			$sql .= " AND atd.alu_id = ". $alu_id;

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $resultado;
	}

	public function excluirDiario($dados)
	{
		$retorno = 'excluido';

		$this->load->library('session');

		if (!empty($dados['con_id'])){
			$sql = "UPDATE conteudo
					SET con_status = 'I', con_qdo_inativo = NOW(), con_qem_inativo = ". $this->session->usuario['usu_id'] ."
					WHERE con_id = " . $dados['con_id'];

			if(!$this->db->simple_query($sql))
				$retorno = false;
		}

		if (!empty($dados['tar_id'])){
			$sql = "UPDATE tarefa
					SET tar_status = 'I', tar_qdo_inativo = NOW(), tar_qem_inativo = ". $this->session->usuario['usu_id'] ."
					WHERE tar_id = " . $dados['tar_id'];

			if(!$this->db->simple_query($sql))
				$retorno = false;
		}

		if (!empty($dados['obs_id'])){
			$sql = "UPDATE observacao
					SET obs_status = 'I', obs_qdo_inativo = NOW(), obs_qem_inativo = ". $this->session->usuario['usu_id'] ."
					WHERE obs_id = " . $dados['obs_id'];

			if(!$this->db->simple_query($sql))
				$retorno = false;
		}

		$ids = explode('@', $dados['atds_id']);
		$atds_id = array_filter($ids);

		foreach($atds_id as $atd_id){
			$id = explode('/', $atd_id);

			$sql = "UPDATE falta
					SET fal_status = 'I', fal_qdo_inativo = NOW(), fal_qem_inativo = ". $this->session->usuario['usu_id'] ."
					WHERE fal_id = ".$id[0];

			if(!$this->db->simple_query($sql))
				$retorno = false;
		}

		return $retorno;
	}
}
