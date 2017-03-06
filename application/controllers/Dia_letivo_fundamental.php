<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dia_letivo_fundamental extends MY_Controller {

	public function index()
	{
		$this->load->helper('custom_helper');
		loadInternalView('dia_letivo_fundamental/index');
	}

	public function buscarTodosAnosLetivos()
	{
		$get = $this->input->get();

		$dir = $get['order'][0]['dir'];
		$order = $get['order'][0]['column'] + 1;

		$this->load->model('dia_letivo_fundamental_model');
		$resultado = $this->dia_letivo_fundamental_model->findAllGrid($get['search']['value'], $order, $dir);
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

		$this->load->model('dia_letivo_fundamental_model');

		// if(empty($post['dif_id']))
			$resultado = $this->dia_letivo_fundamental_model->inserir($post);
		// else 
			// $resultado = $this->dia_letivo_fundamental_model->atualizar($post);

		echo json_encode($resultado);
	}

	function buscarAnoLetivo()
	{
		$post = $this->input->post();

		$this->load->model('dia_letivo_fundamental_model');
		$resultado = $this->dia_letivo_fundamental_model->buscarAnoLetivo($post);

		echo json_encode($resultado);
	}

	function buscarDiaLetivo()
	{
		$post = $this->input->post();

		$this->load->model('dia_letivo_fundamental_model');
		$resultado = $this->dia_letivo_fundamental_model->buscarDiaLetivo($post);

		echo json_encode($resultado);
	}
}
