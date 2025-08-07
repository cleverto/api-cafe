<?php

namespace App\Models;

use CodeIgniter\Model;

class AdministracionModel extends Model
{
	public function lista($data)
	{
		$builder = $this->db->table($data->modulo);
		$builder->select("id_" . $data->modulo . " as id, " . $data->modulo . " as descripcion");
		$builder->orderBy($data->modulo);
		$query = $builder->get();

		return $query;
	}
	public function modulo($data)
	{
		$builder = $this->db->table($data->modulo);
		$builder->select($data->campo . " as id, " . $data->modulo . " as descripcion");
		$builder->where($data->campo, $data->id);
		$query = $builder->get();
		$data = $query->getRowArray();
		return $data;
	}

	public function eliminar($data)
	{
		$query = $this->db->table($data->modulo)->delete(array($data->campo => $data->id));
		return $query;
	}
	public function guardar($data)
	{
		$valores = array(
			$data->modulo => strtoupper($data->descripcion),
		);
		$builder = $this->db->table($data->modulo);
		$builder->insert($valores);

		$id = $this->db->insertID();
		return $id;
	}
	public function validar($data)
	{

		$errors = array();


		$builder = $this->db->table($data->modulo);
		$builder->where($data->modulo, $data->descripcion);
		$query = $builder->get();
		if (count($query->getResultArray()) > 0) {
			$errors[] = "Ya existe esta descripción";
			return $errors;
		}

		return $errors;
	}
	public function validar_modificar($data)
	{
		$errors = array();
		$builder =  $this->db->table($data->modulo);
		$builder->select("Count(*)");
		$builder->where($data->modulo, $data->descripcion);
		$builder->where("id_".$data->modulo, $data->id);
		$query = $builder->get();
		if (count($query->getResultArray()) > 0) {
			$errors[] = "Ya existe esta descripción";
		}
		return $errors;
	}
}
