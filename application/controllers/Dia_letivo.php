<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dia_letivo extends MY_Controller {

	public function index()
	{
		$this->load->helper('custom_helper');
		loadInternalView('dia_letivo/index');
	}

	public function buscarTodosAnosLetivos()
	{
		$get = $this->input->get();

		$dir = $get['order'][0]['dir'];
		$order = $get['order'][0]['column'] + 1;

		$this->load->model('dia_letivo_model');
		$resultado = $this->dia_letivo_model->findAllGrid($get['search']['value'], $order, $dir);
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

		$this->load->model('dia_letivo_model');

		// if(empty($post['dil_id']))
			$resultado = $this->dia_letivo_model->inserir($post);
		// else 
			// $resultado = $this->dia_letivo_model->atualizar($post);

		echo json_encode($resultado);
	}

	function buscarAnoLetivo()
	{
		$post = $this->input->post();

		$this->load->model('dia_letivo_model');
		$resultado = $this->dia_letivo_model->buscarAnoLetivo($post);

		echo json_encode($resultado);
	}

	function buscarDiaLetivo()
	{
		$post = $this->input->post();

		$this->load->model('turma_model');
		$post['tur_curso'] = current($this->turma_model->findBy(array('tur_id' => $post['tur_id'])))->tur_curso;

		$this->load->model('dia_letivo_model');
		$resultado = $this->dia_letivo_model->buscarDiaLetivo($post);

		echo json_encode($resultado);
	}
}
