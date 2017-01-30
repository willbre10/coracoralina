<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dias_letivos extends CI_Controller {

	public function index()
	{
		$this->load->helper('custom_helper');
		loadInternalView('dias_letivos/index');
	}

	public function buscarTodosAnosLetivos()
	{
		$get = $this->input->get();

		$dir = $get['order'][0]['dir'];
		$order = $get['order'][0]['column'] + 1;

		$this->load->model('dias_letivos_model');
		$resultado = $this->dias_letivos_model->findAllGrid($get['search']['value'], $order, $dir);
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

		$this->load->model('dias_letivos_model');

		// if(empty($post['dil_id']))
			$resultado = $this->dias_letivos_model->inserir($post);
		// else 
			// $resultado = $this->dias_letivos_model->atualizar($post);

		echo json_encode($resultado);
	}

	function buscarDiaLetivo()
	{
		$post = $this->input->post();

		$this->load->model('dias_letivos_model');
		$resultado = $this->dias_letivos_model->findBy($post);

		echo json_encode($resultado);
	}
}
