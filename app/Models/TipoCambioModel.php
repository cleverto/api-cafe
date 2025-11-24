<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoCambioModel extends Model
{

	public function lista()
	{
		$builder = $this->db->table('tipo_cambio a');
		$builder->select('a.*');
		$builder->select('b.moneda');
		$builder->join('moneda b', 'a.id_moneda = b.id_moneda', 'inner');
		$builder->orderBy('a.fecha desc');
		$query = $builder->get();
		$res = $query->getResultArray();
		return $res;
	}

	public function guardar($data)
	{
		$builder = $this->db->table('tipo_cambio');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}
	public function existe($data)
	{
		$builder = $this->db->table('tipo_cambio');
		$builder->where('fecha', $data["fecha"]);
		return $builder->countAllResults();
	}
	public function eliminar($data)
	{
		$datos = array('id_tipo_cambio' => $data->id);
		$query = $this->db->table('tipo_cambio')->delete($datos);

		return $query;
	}
}
