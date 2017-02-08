<?php

class Boletim_model extends CI_Model
{
	public function buscarBoletimAluno($post)
	{
		$notas = array();
		$faltas = array();

		$sql = "SELECT DISTINCT alu.alu_nome
					, alu.alu_ra
					, tur.tur_nome
					, tur.tur_ano
					, atd.atd_numero_aluno
					, 'tur.tur_ensino' AS tur_ensino
					, 'tdp.tdp_curso' AS tdp_curso
				FROM aluno_turma_disciplina_professor atd
				INNER JOIN turma_disciplina_professor tdp ON tdp.tdp_id = atd.tdp_id
				INNER JOIN aluno alu ON alu.alu_id = atd.alu_id
				INNER JOIN turma tur ON tur.tur_id = tdp.tur_id
				WHERE atd.alu_id = ". $post['alu_id'] ."
				AND tdp.tur_id = ". $post['tur_id'];

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $dadosBasicos[] = $row;
		}

		$sql = "SELECT dis.dis_id, dis.dis_nome, fal.fal_bimestre, COUNT(fal_id) AS aulas, SUM(fal_falta) AS faltas
				FROM aluno_turma_disciplina_professor atd 
				INNER JOIN turma_disciplina_professor tdp ON tdp.tdp_id = atd.tdp_id 
				INNER JOIN falta fal ON fal.atd_id = atd.atd_id 
				INNER JOIN disciplina dis ON dis.dis_id = tdp.dis_id
				WHERE atd.alu_id = ". $post['alu_id'] ."
				AND tdp.tur_id = ". $post['tur_id'] ."
				GROUP BY atd.atd_id, fal.fal_bimestre";

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $faltas[] = $row;
		}

		$sql = "SELECT dis.dis_id, dis.dis_nome, not1.not_bimestre, (not_prova_mensal + not_trabalho_mensal + not_prova_bimestral + not_trabalho_bimestral) / 4 nota
				FROM aluno_turma_disciplina_professor atd
				INNER JOIN turma_disciplina_professor tdp ON tdp.tdp_id = atd.tdp_id
				INNER JOIN nota not1 ON not1.atd_id = atd.atd_id
				INNER JOIN disciplina dis ON dis.dis_id = tdp.dis_id
				WHERE atd.alu_id = ". $post['alu_id'] ."
				AND tdp.tur_id = ". $post['tur_id'] ."
				GROUP BY atd.atd_id, not1.not_bimestre";

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $notas[] = $row;
		}

		$resultado = $this->trataDadosBoletim(current($dadosBasicos), $faltas, $notas);

		return $resultado;
	}

	private function trataDadosBoletim($dadosBasicos, $faltas, $notas)
	{
		$newDados = array();
		$newFaltas = array();

		$newDados['header'] = array(
			'alu_nome' => $dadosBasicos->alu_nome,
			'alu_ra' => $dadosBasicos->alu_ra,
			'tur_nome' => $dadosBasicos->tur_nome,
			'tur_ano' => $dadosBasicos->tur_ano,
			'atd_numero_aluno' => $dadosBasicos->atd_numero_aluno,
			'ensino' => $dadosBasicos->alu_ra,
			'tdp_curso' => $dadosBasicos->tdp_curso
		);

		foreach($faltas as $falta){
			$newDados['disciplinas'][ $falta->dis_id ]['dis_nome'] = $falta->dis_nome;
			$newDados['disciplinas'][ $falta->dis_id ]['aulas'. $falta->fal_bimestre .'bimestre'] = $falta->aulas;
			$newDados['disciplinas'][ $falta->dis_id ]['faltas'. $falta->fal_bimestre .'bimestre'] = $falta->faltas;
		}

		foreach($notas as $nota){
			$newDados['disciplinas'][ $nota->dis_id ]['nota'. $nota->not_bimestre .'bimestre'] = $nota->nota;
		}

		return $newDados;
	}

}
