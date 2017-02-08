<?php

class Perfil_model extends CI_Model
{
	public function BuscarPerfil()
	{
		$resultado = array();

				
		$sql = 'SELECT *
				FROM perfil ';

		$query = $this->db->query($sql);

		foreach ($query->result() as $row){
		    $resultado[] = $row;
		}

		return $resultado;
	}
}
