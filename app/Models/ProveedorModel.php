<?php

namespace App\Models;

use CodeIgniter\Model;

class ProveedorModel extends Model
{

	public function lista()
	{
		$builder = $this->db->table('proveedor a');
		$builder->select('a.*');
		$builder->select('b.dis');
		$builder->join('ubigeo b' , 'a.id_ubigeo = b.id_ubigeo', 'inner');
		$builder->orderBy('a.proveedor');
		$query = $builder->get();
		$res = $query->getResultArray();
		return $res;
	}
	public function modulo($id)
	{
		$builder = $this->db->table('producto a');
		$builder->where('a.id_producto', $id);

		$query = $builder->get();
		$data = $query->getRowArray();

		return $data;
	}
	public function guardar($data)
	{
		$builder = $this->db->table('producto');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}

	public function modificar($id, $datos)
	{
		$db = $this->db->table('producto');
		$db->where('id_producto', $id);
		$db->update($datos);

		return $this->db->affectedRows();
	}

	public function eliminar($data)
	{
		$datos = array('id_producto' => $data->id);
		$query = $this->db->table('producto')->delete($datos);

		return $query;
	}
}
