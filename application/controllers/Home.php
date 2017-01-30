<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function index()
	{
		$this->load->library('session');
		$this->load->helper('custom_helper');

		if (empty($this->session->usuario)){
			if (!empty($this->input->post())){
				$this->load->model('usuario_model');

				$resultado = $this->usuario_model->validaLoginUsuario();

				if (count($resultado)){
					$this->session->usuario = (array) $resultado[0];
					log_message('info', 'Login com sucesso => usuário ['. $this->session->usuario['usu_login'] .']');
					loadInternalView('home/index');
				} else {
					$statusLogin = false;
					log_message('info', 'Login sem sucesso.');
					$this->load->view('login/login', array('statusLogin' => $statusLogin));
				}
			} else {
				$this->load->view('login/login');
			}
		} else {
			loadInternalView('home/index');
		}
	}

	public function logout()
	{
		$this->load->library('session');

		log_message('info', 'Logout com sucesso => usuário ['. $this->session->usuario['usu_login'] .']');

		$this->session->sess_destroy();
		$this->load->helper('url');
		redirect('home', 'refresh');
	}
}
