<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('loadInternalView')){
	function loadInternalView($view, $fields = array())
	{
		$CI =& get_instance();

	    $CI->load->model('menu_model');

	    $CI->load->library('session');
	    $usuario = $CI->session->usuario;

		$menu = array('menu' => $CI->menu_model->buscaMenu($usuario['usu_id']));

		$CI->load->view('header', $menu);
		$CI->load->view($view, $fields);
		$CI->load->view('footer');
	}
}