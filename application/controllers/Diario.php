<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Diario extends MY_Controller {

	public function index()
	{
		$this->load->library('session');
		$perfil = $this->session->usuario['per_id'];

		$this->load->helper('custom_helper');
		loadInternalView('diario/index', array('perfil' => $perfil));
	}

	public function impressao()
	{
		$this->load->helper('custom_helper');
		loadInternalView('diario/impressao', array('perfil' => $perfil));
	}

	public function imprimirDiario()
	{
		$post = $this->input->post();

		$this->load->model('diario_model');

		$resultado['resultado'] = $this->diario_model->buscaDadosImpressao($post);

		$this->load->view('diario/diarioImpresso', $resultado);
	}

	public function salvar()
	{
		$post = $this->input->post();

		$this->load->model('diario_model');

		if (!empty($post['con_id']) || !empty($post['atds_id']))
			$resultado = $this->diario_model->editar($post);
		else
			$resultado = $this->diario_model->salvar($post);


		echo json_encode($resultado);
	}

	public function buscarDiario()
	{
		$post = $this->input->post();

		$this->load->model('diario_model');
		$resultado = $this->diario_model->buscarDiario($post);

		echo json_encode($resultado);
	}

	public function excluirDiario()
	{
		$post = $this->input->post();

		$this->load->model('diario_model');

		$resultado = $this->diario_model->excluirDiario($post);

		echo json_encode($resultado);
	}
}
