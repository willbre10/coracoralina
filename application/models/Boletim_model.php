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
					, tur.tur_curso AS tur_curso
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

		$sql = "SELECT DISTINCT dis.dis_id, dis.dis_nome, fal.fal_bimestre, SUM(fal_quantidade_aulas) AS aulas, SUM(fal_falta) AS faltas
				FROM aluno_turma_disciplina_professor atd 
				INNER JOIN turma_disciplina_professor tdp ON tdp.tdp_id = atd.tdp_id 
				LEFT JOIN falta fal ON fal.atd_id = atd.atd_id 
				INNER JOIN disciplina dis ON dis.dis_id = tdp.dis_id
				INNER JOIN conteudo con ON con.tdp_id = tdp.tdp_id AND con.con_dia = fal.fal_dia
				WHERE atd.alu_id = ". $post['alu_id'] ."
				AND tdp.tur_id = ". $post['tur_id'] ."
				AND fal.fal_status = 'A'
				GROUP BY atd.atd_id, fal.fal_bimestre";

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $faltas[] = $row;
		}

		$sql = "SELECT dis.dis_id, dis.dis_nome, not1.not_bimestre, (not_prova_mensal + not_trabalho_mensal + not_prova_bimestral + not_trabalho_bimestral) / 2 nota, + not_simulado simulado
				FROM aluno_turma_disciplina_professor atd
				INNER JOIN turma_disciplina_professor tdp ON tdp.tdp_id = atd.tdp_id
				LEFT JOIN nota not1 ON not1.atd_id = atd.atd_id
				INNER JOIN disciplina dis ON dis.dis_id = tdp.dis_id
				WHERE atd.alu_id = ". $post['alu_id'] ."
				AND tdp.tur_id = ". $post['tur_id'] ."
				GROUP BY atd.atd_id, not1.not_bimestre";

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
			if ($row->simulado + $row->nota > '10.00')
				$row->nota = '10.00';
			else
				$row->nota = $row->simulado + $row->nota;
		    $notas[] = $row;
		}

		$resultado = $this->trataDadosBoletim(current($dadosBasicos), $faltas, $notas);

		return $resultado;
	}

	private function trataDadosBoletim($dadosBasicos, $faltas, $notas)
	{
		$newDados = array();
		$newFaltas = array();
		$todasNotas = false;

		$newDados['header'] = array(
			'alu_nome' => $dadosBasicos->alu_nome,
			'alu_ra' => $dadosBasicos->alu_ra,
			'tur_nome' => $dadosBasicos->tur_nome,
			'tur_ano' => $dadosBasicos->tur_ano,
			'atd_numero_aluno' => $dadosBasicos->atd_numero_aluno
		);

		switch($dadosBasicos->tur_curso){
			case 1:
				$newDados['header']['tur_curso'] = "Ensino Infantil";
				break;
			case 2:
				$newDados['header']['tur_curso'] = "Ensino Fundamental 1";
				break;
			case 3:
				$newDados['header']['tur_curso'] = "Ensino Fundamental 2";
				break;
		}

		foreach($faltas as $falta){
			$newDados['disciplinas'][ $falta->dis_id ]['dis_nome'] = $falta->dis_nome;

			for ($i = 1; $i <= 4; $i++)
				if (empty($newDados['disciplinas'][ $falta->dis_id ]['aulas'. $i .'bimestre']))
					$newDados['disciplinas'][ $falta->dis_id ]['aulas'. $i .'bimestre'] = 0;

			for ($i = 1; $i <= 4; $i++)
				if (empty($newDados['disciplinas'][ $falta->dis_id ]['faltas'. $i .'bimestre']))
					$newDados['disciplinas'][ $falta->dis_id ]['faltas'. $i .'bimestre'] = 0;
			
			if (!empty($falta->fal_bimestre)){
				$newDados['disciplinas'][ $falta->dis_id ]['aulas'. $falta->fal_bimestre .'bimestre'] = $falta->aulas;
				$newDados['disciplinas'][ $falta->dis_id ]['faltas'. $falta->fal_bimestre .'bimestre'] = $falta->faltas;

				// o if é gambeta retirar após inserção de inicio e fim dos bimestres
				if ($falta->fal_bimestre == 1)
					$newDados['disciplinas'][ $falta->dis_id ]['total_faltas'] += $falta->faltas;
			}

		}

		foreach($notas as $nota){
			$valor_nota = (str_replace($nota->nota, '.', '') > 1000) ? 10.00 : $nota->nota;

			if(empty($newDados['disciplinas'][ $nota->dis_id ]['dis_nome']))
				$newDados['disciplinas'][ $nota->dis_id ]['dis_nome'] = $nota->dis_nome;

			$valor_nota = round($nota->nota, 2);

			if(substr($valor_nota, 3) == 1 || substr($valor_nota, 3) == 6)
				$new_nota = $valor_nota - 0.01;
			elseif(substr($valor_nota, 3) == 2 || substr($valor_nota, 3) == 7)
				$new_nota = $valor_nota - 0.02;
			elseif(substr($valor_nota, 3) == 3 || substr($valor_nota, 3) == 8)
				$new_nota = $valor_nota + 0.02;
			elseif(substr($valor_nota, 3) == 4 || substr($valor_nota, 3) == 9)
				$new_nota = $valor_nota + 0.01;
			else
				$new_nota = $valor_nota;

			$newDados['disciplinas'][ $nota->dis_id ]['nota'. $nota->not_bimestre .'bimestre'] = number_format($new_nota, 2);
		}

		if (!empty($newDados['disciplinas'])){
			foreach($newDados['disciplinas'] as $dis_id => &$dado){
				$nota = ($dado['nota1bimestre'] + $dado['nota2bimestre'] + $dado['nota3bimestre'] + $dado['nota4bimestre']) / 4;
				
				if (!empty($dado['nota1bimestre']) && !empty($dado['nota2bimestre']) && !empty($dado['nota3bimestre']) && !empty($dado['nota4bimestre']))
					$todasNotas = true;

				$nota = round($nota, 2);
				$valor_nota = round($nota, 1);

				if(substr($valor_nota, 2) == 1 || substr($valor_nota, 2) == 6)
					$new_nota = $valor_nota - 0.1;
				elseif(substr($valor_nota, 2) == 2 || substr($valor_nota, 2) == 7)
					$new_nota = $valor_nota - 0.2;
				elseif(substr($valor_nota, 2) == 3 || substr($valor_nota, 2) == 8)
					$new_nota = $valor_nota + 0.2;
				elseif(substr($valor_nota, 2) == 4 || substr($valor_nota, 2) == 9)
					$new_nota = $valor_nota + 0.1;
				else
					$new_nota = $valor_nota;

				$newDados['disciplinas'][ $dis_id ]['recuperacao_final'] = '';
				$newDados['disciplinas'][ $dis_id ]['media_anual'] = ($todasNotas) ? number_format($new_nota, 1) : '0';

				$newDados['disciplinas'][ $dis_id ]['media_final'] = ($todasNotas) ? number_format($new_nota, 1) : '0';

				$situacao = '';

				$total_aulas = ($newDados['disciplinas'][ $dis_id ]['aulas1bimestre'] + 
								$newDados['disciplinas'][ $dis_id ]['aulas2bimestre'] + 
								$newDados['disciplinas'][ $dis_id ]['aulas3bimestre'] + 
								$newDados['disciplinas'][ $dis_id ]['aulas4bimestre']);

				$maximo_faltas = round($total_aulas * (25 / 100));

				if ($newDados['disciplinas'][ $dis_id ]['total_faltas'] >= $maximo_faltas || 
						$newDados['disciplinas'][ $dis_id ]['media_final'] < 5)
					$situacao = 'REPROVADA';
				else
					$situacao = 'APROVADA';

				$newDados['disciplinas'][ $dis_id ]['situacao'] = ($todasNotas) ? $situacao : '';
			}
		}

		$dadosOrdenados = $this->ordenacao($newDados);

		// gambeta retirar após inserção de inicio e fim dos bimestres
		foreach ($dadosOrdenados['disciplinas'] as &$dados) {
			unset($dados['aulas2bimestre']);
			unset($dados['aulas3bimestre']);
			unset($dados['aulas4bimestre']);
			unset($dados['faltas2bimestre']);
			unset($dados['faltas3bimestre']);
			unset($dados['faltas4bimestre']);
		}

		//põe total de faltas q n existe pra 0
		foreach($dadosOrdenados['disciplinas'] as &$disc){
			if (empty($disc['total_faltas']))
				$disc['total_faltas'] = 0;

			if (empty($disc['aulas1bimestre']))
				$disc['aulas1bimestre'] = 0;

			if (empty($disc['faltas1bimestre']))
				$disc['faltas1bimestre'] = 0;
		}

		return $dadosOrdenados;
	}

	private function ordenacao($dados)
	{
		$dadosOrdenados = array();

		$dadosOrdenados['header'] = $dados['header'];

		// Português
		if (isset($dados['disciplinas']['19']))
			$dadosOrdenados['disciplinas']['19'] = $dados['disciplinas']['19'];

		// Téc Redação
		if (isset($dados['disciplinas']['25']))
			$dadosOrdenados['disciplinas']['25'] = $dados['disciplinas']['25'];

		foreach ($dados['disciplinas'] as $key => $dado)
			if (!isset($dadosOrdenados['disciplinas'][$key]))
				$dadosOrdenados['disciplinas'][$key] = $dados['disciplinas'][$key];

		return $dadosOrdenados;
	}
}
