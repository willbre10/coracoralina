<?php

class Menu_model extends CI_Model{
	
	public function buscaMenu($id_usuario)
	{
		$this->load->database();

		$sql = "SELECT DISTINCT mnp.* , mnf.*
				FROM menu_pai mnp 
				LEFT JOIN menu_filha mnf ON mnp.mnp_id = mnf.mnf_id_pai 
				LEFT JOIN menu_perfil mpe1 ON mpe1.mnf_id = mnf.mnf_id
				LEFT JOIN menu_perfil mpe2 ON mpe2.mnp_id = mnp.mnp_id
				INNER JOIN perfil per ON per.per_id = mpe1.per_id
				INNER JOIN usuario usu ON usu.per_id = mpe2.per_id
				WHERE usu.usu_id = $id_usuario";

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $this->trataMenu($resultado);
	}

	private function trataMenu($menus)
	{

		$newMenu = array();
		foreach ($menus as $menu){
			if (empty($menu->mnf_id_pai)){
				$newMenu[$menu->mnp_descricao]['link'] = $menu->mnp_link;
				$newMenu[$menu->mnp_descricao]['icon'] = $menu->mnp_icon;
			} else {
				$newMenu[$menu->mnp_descricao]['filhas'][$menu->mnf_descricao]['link'] =  $menu->mnf_link;
				$newMenu[$menu->mnp_descricao]['icon'] = $menu->mnp_icon;
			}
		}

		return $newMenu;
	}
}