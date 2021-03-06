<?php

class Nota_model extends CI_Model
{
	public function salvar($dados)
	{
		$retorno = true;
		
		$turma_disciplina_professor = $this->buscarTurmaDisciplinaProfessor($dados);

		$tdp_id = $turma_disciplina_professor[0]->tdp_id;

		foreach($dados['notas']['pm'] as $alu_id => $valor){

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

			$pm = $dados['notas']['pm'][$alu_id];
			$tm = $dados['notas']['tm'][$alu_id];
			$pb = $dados['notas']['pb'][$alu_id];
			$tb = $dados['notas']['tb'][$alu_id];
			$sm = $dados['notas']['sm'][$alu_id];

			$sql = "INSERT INTO nota (atd_id, not_bimestre, not_prova_mensal, not_trabalho_mensal, not_prova_bimestral, not_trabalho_bimestral, not_simulado)
					VALUES ($atd_id, " . $dados['bimestre'] . ", '" . $pm . "', '" . $tm . "', '" . $pb . "', '" . $tb . "', '" . $sm . "')";

			if(!$this->db->simple_query($sql))
				$retorno = false;
		}

		return $retorno;
	}

	public function editar($dados)
	{
		$retorno = 'editado';

		if (!empty($dados['atds_id'])){
			$valores = explode('@', $dados['atds_id']);
			$valores = array_filter($valores);

			foreach ($valores as $valor){
				$ids = explode('/', $valor);

				$nota_pm = $dados['notas']['pm'][$ids[1]];
				$nota_tm = $dados['notas']['tm'][$ids[1]];
				$nota_pb = $dados['notas']['pb'][$ids[1]];
				$nota_tb = $dados['notas']['tb'][$ids[1]];
				$nota_sm = $dados['notas']['sm'][$ids[1]];
				$not_id = $ids[0];

				$sql = "UPDATE nota
						SET not_prova_mensal = '$nota_pm',
							not_trabalho_mensal = '$nota_tm',
							not_prova_bimestral = '$nota_pb',
							not_trabalho_bimestral = '$nota_tb',
							not_simulado = '$nota_sm'
						WHERE not_id = " . $not_id;

				if(!$this->db->simple_query($sql))
					$retorno = false;
			}
		}

		return $retorno;
	}

	public function buscarNota($post)
	{
		$resultado = array();

		$turma_disciplina_professor = $this->buscarTurmaDisciplinaProfessor($post);

		if(!empty($turma_disciplina_professor)){
			$tdp_id = $turma_disciplina_professor[0]->tdp_id;

			$atd_ids = array();

			$aluno_turma_disciplina_professor = $this->buscarAlunoTurmaDisciplinaProfessor($tdp_id);

			foreach($aluno_turma_disciplina_professor as $dado)
				$atd_ids[] = $dado->atd_id;

			
			$atd_id = implode(',', $atd_ids);

			$sql = "SELECT * 
					FROM nota not1
					INNER JOIN aluno_turma_disciplina_professor atd ON atd.atd_id = not1.atd_id
					WHERE not1.atd_id IN ($atd_id)
					AND not1.not_bimestre = ". $post['bimestre'];

			$query = $this->db->query($sql);

			foreach ($query->result() as $row){
			    $resultado[] = $row;
			}
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
}
