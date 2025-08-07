<?php

namespace App\Models;

use CodeIgniter\Model;

class TrabajadorVinculoModel extends Model
{
	public function lista()
	{
		$builder = $this->db->table('trabajador_vinculo a');
		$builder->select('a.*');
		$builder->select('b.regimen, c.tipo_resolucion');
		$builder->join('regimen b',  "b.id_regimen = a.id_regimen", 'inner');
		$builder->join('tipo_resolucion c',  "c.id_tipo_resolucion = a.id_tipo_resolucion", 'inner');

		$builder->orderBy('a.ingreso');
		$query = $builder->get();
		$res = $query->getResultArray();
		return $res;
	}

	public function modulo($id)
	{
		$builder = $this->db->table('trabajador_vinculo a');
		$builder->select('a.*');
		$builder->select('b.regimen, c.tipo_resolucion');
		$builder->join('regimen b',  "b.id_regimen = a.id_regimen", 'inner');
		$builder->join('tipo_resolucion c',  "c.id_tipo_resolucion = a.id_tipo_resolucion", 'inner');
		$builder->where('a.id_trabajador_vinculo', $id);

		$query = $builder->get();
		$data = $query->getRowArray();

		return $data;
	}

	public function eliminar($id)
	{
		$datos = array('id_trabajador_vinculo' => $id);
		$this->db->table('trabajador_vinculo')->delete($datos);
		return $this->db->affectedRows();
	}

	public function guardar($data)
	{
		$builder = $this->db->table('trabajador_vinculo');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}
	public function modificar($id, $datos)
	{

		$db = $this->db->table('trabajador_vinculo');
		$db->where('id_trabajador_vinculo', $id);
		$db->update($datos);


		return $this->db->affectedRows();
	}
}
