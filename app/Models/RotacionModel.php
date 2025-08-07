<?php

namespace App\Models;

use CodeIgniter\Model;

class RotacionModel extends Model
{

	public function lista()
	{
		$builder = $this->db->table('rotacion a');
		$builder->select('a.*');
		$builder->select('c.dependencia, d.trabajador');
		$builder->join('dependencia c',  "c.id_dependencia = a.id_dependencia", 'inner');
		$builder->join('trabajador d',  "d.id_trabajador = a.id_trabajador", 'inner');
		$builder->orderBy('d.trabajador');
		$query = $builder->get();
		$res = $query->getResultArray();
		return $res;
	}


	public function modulo($id)
	{
		$builder = $this->db->table('rotacion a');
		$builder->where('a.id_rotacion', $id);

		$query = $builder->get();
		$data = $query->getRowArray();

		return $data;
	}
	public function lista_by_trabajador($id)
	{
		$builder = $this->db->table('rotacion a');
		$builder->select('a.*');
		$builder->select('c.dependencia');
		$builder->join('dependencia c',  "c.id_dependencia = a.id_dependencia", 'inner');
		$builder->where('a.id_trabajador', $id);

		$query = $builder->get();
		$data = $query->getResultArray();

		return $data;
	}
	public function guardar($data)
	{
		$builder = $this->db->table('rotacion');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}

	public function modificar($id, $datos)
	{
		$db = $this->db->table('rotacion');
		$db->where('id_rotacion', $id);
		$db->update($datos);

		return $this->db->affectedRows();
	}

	public function eliminar($data)
	{
		$datos = array('id_rotacion' => $data->id);
		$query = $this->db->table('rotacion')->delete($datos);

		return $query;
	}
}
