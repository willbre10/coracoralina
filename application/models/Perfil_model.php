<?php

class Perfil_model extends CI_Model
{
	public function buscarPerfil()
	{
		$resultado = array();

		$sql = 'SELECT *
				FROM perfil
				ORDER BY per_nome';

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $resultado;
	}
}
