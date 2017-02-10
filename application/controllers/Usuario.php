<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario extends MY_Controller {

	public function index()
	{
		$this->load->helper('custom_helper');
		loadInternalView('usuario/index');
	}

	public function buscarTodosUsuariosGrid()
	{
		$get = $this->input->get();

		$dir = $get['order'][0]['dir'];
		$order = $get['order'][0]['column'] + 1;

		$this->load->model('usuario_model');
		$resultado = $this->usuario_model->findAllGrid($get['search']['value'], $order, $dir);
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

		$this->load->model('usuario_model');

		if(empty($post['usu_id']))
			$resultado = $this->usuario_model->inserir($post);
		else 
			$resultado = $this->usuario_model->atualizar($post);

		echo json_encode($resultado);
	}

	function buscarUsuario()
	{
		$post = $this->input->post();

		$this->load->model('usuario_model');
		$resultado = $this->usuario_model->findBy($post);

		echo json_encode($resultado);
	}

	function buscarPerfil()
	{
		$this->load->model('perfil_model');
		$resultado = $this->perfil_model->buscarPerfil();
		
		if(count($resultado) == 1)
			$resultado = array($resultado);

		echo json_encode($resultado);
	}

	function buscarUsuarioProfessor()
	{
		$usuarios = array();

		$this->load->model('usuario_model');
		$resultado = $this->usuario_model->buscarUsuarioProfessor();

		$i = 0;
		if (!empty($resultado) && count($resultado) > 0 ){
			foreach($resultado as $usuario){
				$usuarios[$i]['id'] = $usuario->usu_id;
				$usuarios[$i]['label'] = $usuario->usu_login;
				$i++;
			}
		}

		echo json_encode($usuarios);
	}
}
