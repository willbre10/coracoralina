<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nota extends MY_Controller {

	public function index()
	{
		$this->load->library('session');
		$perfil = $this->session->usuario['per_id'];

		$this->load->helper('custom_helper');
		loadInternalView('nota/index', array('perfil' => $perfil));
	}

	public function salvar()
	{
		$post = $this->input->post();

		$this->load->model('nota_model');

		if (!empty($post['atds_id']))
			$resultado = $this->nota_model->editar($post);
		else
			$resultado = $this->nota_model->salvar($post);


		echo json_encode($resultado);
	}

	public function buscarNota()
	{
		$post = $this->input->post();

		$this->load->model('nota_model');
		$resultado = $this->nota_model->buscarNota($post);

		echo json_encode($resultado);
	}

}
