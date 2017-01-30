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
				WHERE atd.alu_id = ". $post['alu_id'] ."
				AND tdp.tur_id = ". $post['tur_id'];

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
				WHERE atd.alu_id = ". $post['alu_id'] ."
				AND tdp.tur_id = ". $post['tur_id'];

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $notas[] = $row;
		}
echo "<pre>";print_r($notas);die;
		return $resultado;
	}

}
