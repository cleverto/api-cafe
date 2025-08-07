<?php

namespace App\Models;

use CodeIgniter\Model;

class TrabajadorVinculoDocenteModel extends Model
{
	public function lista()
	{
		$builder = $this->db->table('trabajador_vinculo_docente a');
		$builder->select('a.id_trabajador_vinculo,  a.ingreso, a.documento');
		$builder->select('b.tipo_contrato, c.categoria, d.dedicacion');
		$builder->join('tipo_contrato b',  "b.id_tipo_contrato = a.id_tipo_contrato", 'inner');
		$builder->join('categoria c',  "c.id_categoria = a.id_categoria", 'inner');
		$builder->join('dedicacion d',  "d.id_dedicacion = a.id_dedicacion", 'inner');
		$builder->orderBy('a.ingreso');
		$query = $builder->get();
		$res = $query->getResultArray();
		return $res;
	}
	public function modulo($id)
	{
		$builder = $this->db->table('trabajador_vinculo_docente a');
		$builder->select('a.*');
		$builder->select('b.dedicacion,   e.departamento_academico, f.tipo_contrato,  r.regimen');
		$builder->join('dedicacion b', 'a.id_dedicacion = b.id_dedicacion', 'inner');
		$builder->join('departamento_academico e', 'a.id_departamento_academico = e.id_departamento_academico', 'inner');
		$builder->join('tipo_contrato f', 'a.id_tipo_contrato = f.id_tipo_contrato', 'inner');
		$builder->join('regimen r', 'a.id_regimen = r.id_regimen', 'inner');
		$builder->where('a.id_trabajador_vinculo', $id);

		$query = $builder->get();
		$data = $query->getRowArray();

		return $data;
	}

	public function eliminar($id)
	{
		$datos = array('id_trabajador_vinculo' => $id);
		$this->db->table('trabajador_vinculo_docente')->delete($datos);
		return $this->db->affectedRows();
	}

	public function guardar($data)
	{
		$builder = $this->db->table('trabajador_vinculo_docente');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}
	public function modificar($id, $datos)
	{

		$db = $this->db->table('trabajador_vinculo_docente');
		$db->where('id_trabajador_vinculo', $id);
		$db->update($datos);


		return $this->db->affectedRows();
	}
}
