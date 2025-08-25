<?php

namespace App\Models;

use CodeIgniter\Model;

class CajaModel extends Model
{

	public function lista()
	{
		$builder = $this->db->table('producto a');
		$builder->orderBy('a.producto');
		$query = $builder->get();
		$res = $query->getResultArray();
		return $res;
	}

	public function lista_pago($id)
	{
		$builder = $this->db->table('caja_detalle a');
		$builder->join('caja_compra b', 'b.id_caja=a.id_caja', 'inner');
		$builder->where('b.id_compra', $id);
		$builder->orderBy('a.fecha desc');
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
		$builder = $this->db->table('caja');
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
