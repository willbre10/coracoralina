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
		$resultado['resultado'] = $this->boletim_model->buscarBoletimAluno($post);

		require('./vendor/mpdf/mpdf.php');
		$mpdf = new mPDF('c', 'A4-L');

		$html = $this->load->view('boletim/boletim', $resultado, TRUE);

		$mpdf->writeHTML($html);

		$mpdf->Output();
	}

}
