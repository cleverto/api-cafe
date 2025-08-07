<?php

namespace App\Models;

use CodeIgniter\Model;

class DesignacionModel extends Model
{

	public function lista()
	{
		$builder = $this->db->table('designacion a');
		$builder->select('a.*');
		$builder->select('b.cargo, c.dependencia, d.trabajador');
		$builder->join('cargo b',  "b.id_cargo = a.id_cargo", 'inner');
		$builder->join('dependencia c',  "c.id_dependencia = a.id_dependencia", 'inner');
		$builder->join('trabajador d',  "d.id_trabajador = a.id_trabajador", 'inner');
		$builder->orderBy('d.trabajador');
		$query = $builder->get();
		$res = $query->getResultArray();
		return $res;
	}


	public function modulo($id)
	{
		$builder = $this->db->table('designacion a');
		$builder->where('a.id_designacion', $id);

		$query = $builder->get();
		$data = $query->getRowArray();

		return $data;
	}
	public function lista_by_trabajador($id)
	{
		$builder = $this->db->table('designacion a');
		$builder->select('a.*');
		$builder->select('b.cargo, c.dependencia');
		$builder->join('cargo b',  "b.id_cargo = a.id_cargo", 'inner');
		$builder->join('dependencia c',  "c.id_dependencia = a.id_dependencia", 'inner');
		$builder->where('a.id_trabajador', $id);

		$query = $builder->get();
		$data = $query->getResultArray();

		return $data;
	}
	public function guardar($data)
	{
		$builder = $this->db->table('designacion');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}

	public function modificar($id, $datos)
	{
		$db = $this->db->table('designacion');
		$db->where('id_designacion', $id);
		$db->update($datos);

		return $this->db->affectedRows();
	}

	public function eliminar($data)
	{
		$datos = array('id_designacion' => $data->id);
		$query = $this->db->table('designacion')->delete($datos);

		return $query;
	}
}
