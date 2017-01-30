<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Professor extends MY_Controller {

	public function index()
	{
		$this->load->helper('custom_helper');
		loadInternalView('professor/index');
	}

	public function buscarTodosProfessoresGrid()
	{
		$get = $this->input->get();

		$dir = $get['order'][0]['dir'];
		$order = $get['order'][0]['column'] + 1;

		$this->load->model('professor_model');
		$resultado = $this->professor_model->findAllGrid($get['search']['value'], $order, $dir);
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

		$this->load->model('professor_model');

		if(empty($post['pro_id']))
			$resultado = $this->professor_model->inserir($post);
		else 
			$resultado = $this->professor_model->atualizar($post);

		echo json_encode($resultado);
	}

	function buscarProfessor()
	{
		$post = $this->input->post();

		$this->load->model('professor_model');
		$resultado = $this->professor_model->findBy($post);

		echo json_encode($resultado);
	}

	function buscarProfessorAutocomplete()
	{
		$professores = array();
		$post = $this->input->post();

		$this->load->model('professor_model');
		$resultado = $this->professor_model->findBy($post);

		$i = 0;
		if (count($resultado) > 0 ){
			foreach($resultado as $professor){
				$professores[$i]['id'] = $professor->pro_id;
				$professores[$i]['label'] = $professor->pro_nome;
				$i++;
			}
		}

		echo json_encode($professores);
	}
}
