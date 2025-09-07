<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductoModel extends Model
{

	public function lista()
	{
		$builder = $this->db->table('producto a');
		$builder->orderBy('a.producto');
		$query = $builder->get();
		$res = $query->getResultArray();
		return $res;
	}
	public function lista_stock()
	{
		$builder = $this->db->table('producto a');
		$builder->join('inventario b', 'a.id_producto=b.id_producto','innner');
		$builder->orderBy('a.producto');
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
