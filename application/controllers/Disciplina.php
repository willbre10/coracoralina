<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Disciplina extends MY_Controller {

	public function index()
	{
		$this->load->helper('custom_helper');
		loadInternalView('disciplina/index');
	}

	public function buscarTodasDisciplinasGrid()
	{
		$get = $this->input->get();

		$dir = $get['order'][0]['dir'];
		$order = $get['order'][0]['column'] + 1;

		$this->load->model('disciplina_model');
		$resultado = $this->disciplina_model->findAllGrid($get['search']['value'], $order, $dir);
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

		$this->load->model('disciplina_model');

		if(empty($post['dis_id']))
			$resultado = $this->disciplina_model->inserir($post);
		else 
			$resultado = $this->disciplina_model->atualizar($post);

		echo json_encode($resultado);
	}

	function buscarDisciplina()
	{
		$post = $this->input->post();

		$this->load->model('disciplina_model');
		$resultado = $this->disciplina_model->findBy($post);

		echo json_encode($resultado);
	}

	function buscarDisciplinaAutocomplete()
	{
		$disciplinas = array();
		$post = $this->input->post();

		$this->load->model('disciplina_model');
		$resultado = $this->disciplina_model->findBy($post);

		$i = 0;
		if (!empty($resultado) && count($resultado) > 0 ){
			foreach($resultado as $disciplina){
				$disciplinas[$i]['id'] = $disciplina->dis_id;
				$disciplinas[$i]['label'] = $disciplina->dis_nome;
				$i++;
			}
		}

		echo json_encode($disciplinas);
	}

	function buscarDisciplinaPorTurmaProfessor()
	{
		$post = $this->input->post();

		$this->load->model('disciplina_model');
		$resultado = $this->disciplina_model->buscarDisciplinaPorTurmaProfessor($post);

		echo json_encode($resultado);
	}
}
