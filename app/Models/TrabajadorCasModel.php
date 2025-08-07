<?php

namespace App\Models;

use CodeIgniter\Model;

class TrabajadorCasModel extends Model
{
	public function modulo($id)
	{
		$builder = $this->db->table('trabajador');
		$builder->where('id_trabajador', $id);

		$query = $builder->get();
		$data = $query->getRowArray();

		return $data;
	}

	public function eliminar($id)
	{
		$datos = array('id_trabajador' => $id);
		$this->db->table('trabajador_vinculo_cas')->delete($datos);
	}
	public function guardar($data)
	{
		$builder = $this->db->table('trabajador_vinculo_cas');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}
}
