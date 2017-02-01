<?php

class Boletim_model extends CI_Model
{
	public function buscarBoletimAluno($post)
	{
		$notas = array();
		$faltas = array();

		$this->load->database();

		$sql = "SELECT *
				FROM aluno_turma_disciplina_professor atd
				INNER JOIN turma_disciplina_professor tdp ON tdp.tdp_id = atd.tdp_id
				INNER JOIN falta fal ON fal.atd_id = atd.atd_id
				INNER JOIN disciplina dis ON dis.dis_id = tdp.dis_id
				INNER JOIN aluno alu ON alu.alu_id = atd.alu_id
				INNER JOIN turma tur ON tur.tur_id = tdp.tur_id
				WHERE atd.alu_id = ". $post['alu_id'] ."
				AND tur.tur_id = ". $post['tur_id'];

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $faltas[] = $row;
		}

		$sql = "SELECT *
				FROM aluno_turma_disciplina_professor atd
				INNER JOIN turma_disciplina_professor tdp ON tdp.tdp_id = atd.tdp_id
				INNER JOIN nota not1 ON not1.atd_id = atd.atd_id
				INNER JOIN disciplina dis ON dis.dis_id = tdp.dis_id
				INNER JOIN aluno alu ON alu.alu_id = atd.alu_id
				INNER JOIN turma tur ON tur.tur_id = tdp.tur_id
				WHERE atd.alu_id = ". $post['alu_id'] ."
				AND tur.tur_id = ". $post['tur_id'];

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $notas[] = $row;
		}

		$this->trataDadosBoletim($faltas, $notas);

		// echo "<pre>";print_r($faltas);
		// echo "<pre>";print_r($notas);die;
		return true;
	}

	private function trataDadosBoletim($faltas, $notas)
	{
		$newDados = array();

		$newDados['tur_nome'] = $faltas[0]->tur_nome;
		$newDados['tur_ano'] = $faltas[0]->tur_ano;
	}

}
