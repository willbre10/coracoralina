<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Boletim extends MY_Controller {

	public function index()
	{
		$this->load->library('session');
		$perfil = $this->session->usuario['per_id'];

		$this->load->helper('custom_helper');
		loadInternalView('boletim/index', array('perfil' => $perfil));
	}

	public function buscarBoletimAluno()
	{
		$post = $this->input->post();

		$this->load->model('boletim_model');
		$resultado = $this->boletim_model->buscarBoletimAluno($post);

		echo json_encode($resultado);
	}

}
