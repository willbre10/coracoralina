<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Turma extends MY_Controller {

	public function index()
	{
		$this->load->helper('custom_helper');
		loadInternalView('turma/index');
	}

	public function buscarTodasTurmasGrid()
	{
		$get = $this->input->get();

		$dir = $get['order'][0]['dir'];
		$order = $get['order'][0]['column'] + 1;

		$this->load->model('turma_model');
		$resultado = $this->turma_model->findAllGrid($get['search']['value'], $order, $dir);
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

		$this->load->model('turma_model');

		if(empty($post['tur_id']))
			$resultado = $this->turma_model->inserir($post);
		else 
			$resultado = $this->turma_model->atualizar($post);

		echo json_encode($resultado);
	}

	function buscarTurma()
	{
		$post = $this->input->post();

		$this->load->model('turma_model');
		$resultado = $this->turma_model->buscarAlunoTurmaDisciplina($post);

		echo json_encode($resultado);
	}

	function buscarTurmaPerfil()
	{
		$this->load->model('turma_model');
		$resultado = $this->turma_model->buscarTurma();

		echo json_encode($resultado);
	}
}
