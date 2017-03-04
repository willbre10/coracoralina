<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Aluno extends MY_Controller {

	public function index()
	{
		$this->load->helper('custom_helper');
		loadInternalView('aluno/index');
	}

	public function buscarTodosAlunosGrid()
	{
		$get = $this->input->get();

		$dir = $get['order'][0]['dir'];
		$order = $get['order'][0]['column'] + 1;

		$this->load->model('aluno_model');
		$resultado = $this->aluno_model->findAllGrid($get['search']['value'], $order, $dir);
		$total = count($resultado);

		$resultado = array_slice($resultado, $get['start'], $get['length']);

		echo json_encode(
			array(
				"data" => $resultado,
  				"recordsTotal" => $total,
  				"recordsFiltered" => $total
  			)
		);
	}

	function salvar()
	{
		$post = $this->input->post();

		$this->load->model('aluno_model');

		if(empty($post['alu_id']))
			$resultado = $this->aluno_model->inserir($post);
		else 
			$resultado = $this->aluno_model->atualizar($post);

		echo json_encode($resultado);
	}

	function buscarAluno()
	{
		$post = $this->input->post();

		$this->load->model('aluno_model');
		$resultado = $this->aluno_model->findBy($post);

		echo json_encode($resultado);
	}

	function buscarAlunoAutocomplete()
	{
		$alunos = array();
		$post = $this->input->post();
		$post['status'] = array('Ativo');

		$this->load->model('aluno_model');
		$resultado = $this->aluno_model->findBy($post);

		$i = 0;
		if (!empty($resultado) && count($resultado) > 0){
			foreach($resultado as $aluno){
				$alunos[$i]['id'] = $aluno->alu_id;
				$alunos[$i]['label'] = $aluno->alu_nome;
				$i++;
			}
		}

		echo json_encode($alunos);
	}

	public function importar()
	{
		$arquivo = $_FILES['importacao'];

		$this->load->helper('custom_helper');
		$this->load->model('aluno_model');

		$resultado = $this->aluno_model->importar($arquivo);

		loadInternalView('aluno/index', array('status' => $resultado));
	}
}
