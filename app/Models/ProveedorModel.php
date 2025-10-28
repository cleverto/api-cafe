<?php

namespace App\Models;

use CodeIgniter\Model;

class ProveedorModel extends Model
{

	public function lista()
	{
		$builder = $this->db->table('proveedor a');
		$builder->select('a.*');
		$builder->select('b.ubigeo');
		$builder->join('ubigeo b', 'a.id_ubigeo = b.id_ubigeo', 'inner');
		$builder->orderBy('a.proveedor');
		$query = $builder->get();
		$res = $query->getResultArray();
		return $res;
	}
	public function modulo($id)
	{
		$builder = $this->db->table('proveedor a');
		$builder->select('a.*');
		$builder->select('b.ubigeo');
		$builder->join('ubigeo b', 'a.id_ubigeo = b.id_ubigeo', 'inner');
		$builder->where('a.id_proveedor', $id);

		$query = $builder->get();
		$data = $query->getRowArray();

		return $data;
	}
	public function get_by_dni($id)
	{
		$builder = $this->db->table('proveedor a');
		$builder->select('a.proveedor, a.id_proveedor, a.direccion');
		$builder->where('a.nro', $id);

		$query = $builder->get();
		$data = $query->getRow(0);

		return $data;
	}
	public function guardar($data)
	{
		$builder = $this->db->table('proveedor');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}
	public function existe_dni($data)
	{
		$builder = $this->db->table('proveedor');
		$builder->where('nro', $data["nro"]);
		return $builder->countAllResults();
	}
	public function existe_dni_modificar($data)
	{
		$builder = $this->db->table('proveedor');
		$builder->where('nro', $data["nro"]);
		$builder->where('id_proveedor !=', $data["idmodulo"]);
		return $builder->countAllResults();
	}
	public function modificar($id, $datos)
	{
		$db = $this->db->table('proveedor');
		$db->where('id_proveedor', $id);
		$db->update($datos);

		return $this->db->affectedRows();
	}

	public function eliminar($data)
	{
		$datos = array('id_proveedor' => $data->id);
		$query = $this->db->table('proveedor')->delete($datos);

		return $query;
	}
}
