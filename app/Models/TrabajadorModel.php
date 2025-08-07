<?php

namespace App\Models;

use CodeIgniter\Model;

class TrabajadorModel extends Model
{

	public function lista()
	{
		$builder = $this->db->table('trabajador a');
		$builder->select('a.id_trabajador, a.trabajador, a.dni, a.correo, a.institucional, a.celular');
		$builder->select('A.id_tipo_trabajador, b.tipo_trabajador');
		$builder->join('tipo_trabajador b',  "b.id_tipo_trabajador = a.id_tipo_trabajador", 'inner');
		$builder->orderBy('a.trabajador');
		$query = $builder->get();
		$res = $query->getResultArray();
		return $res;
	}

	public function lista_academica()
	{
		$builder = $this->db->table('trabajador_academica a');
		$builder->select('a.id_trabajador_academica, a.id_trabajador, b.grado, c.institucion, d.carrera, a.egresado, a.colegiatura');
		$builder->join('grado b',  "b.id_grado = a.id_grado", 'inner');
		$builder->join('institucion c',  "c.id_institucion = a.id_institucion", 'inner');
		$builder->join('carrera d',  "d.id_carrera = a.id_carrera", 'inner');
		$builder->orderBy('a.egresado');
		$query = $builder->get();
		$res = $query->getResultArray();
		return $res;
	}

	public function lista_adjunto($data)
	{
		$builder = $this->db->table('trabajador_adjunto a');
		$builder->select('a.id_requisito, b.requisito');
		$builder->join('requisito b',  "b.id_requisito = a.id_requisito", 'inner');
		$builder->where('a.id_trabajador', $data["id"]);
		$builder->orderBy('b.requisito');
		$query = $builder->get();
		$res = $query->getResultArray();
		return $res;
	}
	public function get_dni($id)
	{
		$builder = $this->db->table('trabajador');
		$builder->select("dni");
		$builder->where('id_trabajador', $id);

		$query = $builder->get();
		$row = $query->getRow(0);

		$res = isset($row) ? $row->dni : "";
		return $res;
	}
	public function modulo($id)
	{
		$builder = $this->db->table('trabajador a');
		$builder->select("a.*");
		$builder->select("b.tipo_trabajador");
		$builder->join('tipo_trabajador b',  "b.id_tipo_trabajador = a.id_tipo_trabajador", 'inner');
		$builder->where('a.id_trabajador', $id);

		$query = $builder->get();
		$data = $query->getRowArray();

		return $data;
	}
	public function get_tipo_trabajador($id)
	{
		$builder = $this->db->table('trabajador');
		$builder->select("id_tipo_trabajador");
		$builder->where('id_trabajador', $id);

		$query = $builder->get();
		$data = $query->getRowArray();

		return $data;
	}
	public function modulo_hijos($id)
	{
		$builder = $this->db->table('trabajador_hijos');
		$builder->where('id_trabajador', $id);

		$query = $builder->get();
		$data = $query->getRowArray();

		return $data;
	}

	public function modulo_cuenta($id)
	{
		$builder = $this->db->table('trabajador_cuenta');
		$builder->where('id_trabajador', $id);

		$query = $builder->get();
		$data = $query->getRowArray();

		return $data;
	}
	public function modulo_academica($id)
	{
		$builder = $this->db->table('trabajador_academica');
		$builder->where('id_trabajador_academica', $id);

		$query = $builder->get();
		$data = $query->getRowArray();

		return $data;
	}
	public function modulo_vinculo($id)
	{
		$builder = $this->db->table('trabajador_vinculo a');
		$builder->select('a.*');
		$builder->select('a.cargo, "" as tipo_remuneracion, d.tipo_resolucion, e.regimen');
		$builder->join('tipo_resolucion d', 'a.id_tipo_resolucion = d.id_tipo_resolucion', 'inner');
		$builder->join('regimen e', 'a.id_regimen = e.id_regimen', 'inner');
		$builder->where('a.id_trabajador', $id);

		$query = $builder->get();
		$data = $query->getRowArray();

		return $data;
	}

	public function modulo_academica_by_trabajador($id)
	{
		$builder = $this->db->table('trabajador_academica a');
		$builder->select('a.*');
		$builder->select('b.grado, c.institucion, d.carrera');
		$builder->join('grado b',  "b.id_grado = a.id_grado", 'inner');
		$builder->join('institucion c',  "c.id_institucion = a.id_institucion", 'inner');
		$builder->join('carrera d',  "d.id_carrera = a.id_carrera", 'inner');
		$builder->where('a.id_trabajador', $id);

		$query = $builder->get();

		return $query->getResultArray();
	}
	public function modulo_familiar($id)
	{
		$builder = $this->db->table('trabajador_familiar a');
		$builder->select('a.*');
		$builder->select('b.parentezco');
		$builder->join('parentezco b',  "b.id_parentezco = a.id_parentezco", 'inner');
		$builder->where('a.id_trabajador', $id);

		$query = $builder->get();
		$data = $query->getRowArray();

		return $data;
	}
	public function modulo_pension($id)
	{
		$builder = $this->db->table('trabajador_pension a');
		$builder->select('a.*');
		$builder->select('b.pension');
		$builder->join('pension b',  "b.id_pension = a.id_pension", 'inner');
		$builder->where('a.id_trabajador', $id);

		$query = $builder->get();
		$data = $query->getRowArray();

		return $data;
	}
	public function guardar($data)
	{
		unset($data["id_trabajador"]);
		$builder = $this->db->table('trabajador');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}
	public function guardar_hijos($data)
	{
		unset($data["idmodulo"]);
		$builder = $this->db->table('trabajador_hijos');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}
	public function modificar($id, $datos)
	{

		unset($datos["id_trabajador"]);

		$db = $this->db->table('trabajador');
		$db->where('id_trabajador', $id);
		$db->update($datos);


		return $this->db->affectedRows();
	}
	public function eliminar_hijos($id)
	{
		$datos = array('id_trabajador' => $id);
		$this->db->table('trabajador_hijos')->delete($datos);
	}
	public function eliminar($data)
	{
		$datos = array('id_trabajador' => $data->id);

		$query = $this->db->table('trabajador_academica')->delete($datos);
		$query = $this->db->table('trabajador_adjunto')->delete($datos);
		$query = $this->db->table('trabajador_cuenta')->delete($datos);
		$query = $this->db->table('trabajador_familiar')->delete($datos);
		$query = $this->db->table('trabajador_formacion')->delete($datos);
		$query = $this->db->table('trabajador_hijos')->delete($datos);
		$query = $this->db->table('trabajador_pension')->delete($datos);
		$query = $this->db->table('trabajador_vinculo')->delete($datos);
		$query = $this->db->table('trabajador_vinculo_docente')->delete($datos);
		$query = $this->db->table('trabajador_vinculo_judicial')->delete($datos);
		$query = $this->db->table('trabajador')->delete($datos);

		return $query;
	}
	public function guardar_pension($data)
	{
		$builder = $this->db->table('trabajador_pension');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}
	public function eliminar_pension($id)
	{
		$datos = array('id_trabajador' => $id);
		$this->db->table('trabajador_pension')->delete($datos);
	}
	public function guardar_cuenta($data)
	{
		$builder = $this->db->table('trabajador_cuenta');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}
	public function eliminar_cuenta($id)
	{
		$datos = array('id_trabajador' => $id);
		$this->db->table('trabajador_cuenta')->delete($datos);
	}
	public function guardar_familiar($data)
	{
		$builder = $this->db->table('trabajador_familiar');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}
	public function eliminar_familiar($id)
	{
		$datos = array('id_trabajador' => $id);
		$this->db->table('trabajador_familiar')->delete($datos);
	}
	public function guardar_academica($data)
	{
		$builder = $this->db->table('trabajador_academica');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}
	public function guardar_vinculo($data)
	{
		$builder = $this->db->table('trabajador_vinculo');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}
	public function eliminar_vinculo($id)
	{
		$datos = array('id_trabajador' => $id);
		$this->db->table('trabajador_vinculo')->delete($datos);
	}
	public function guardar_vinculo_docente($data)
	{
		$builder = $this->db->table('trabajador_vinculo_docente');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}
	public function eliminar_vinculo_docente($id)
	{
		$datos = array('id_trabajador' => $id);
		$this->db->table('trabajador_vinculo_docente')->delete($datos);
	}
	public function eliminar_vinculo_cas($id)
	{
		$datos = array('id_trabajador' => $id);
		$this->db->table('trabajador')->delete($datos);
	}
	public function guardar_vinculo_cas($data)
	{
		$builder = $this->db->table('trabajador_vinculo_cas');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}
	public function modificar_academica($id, $data)
	{
		unset($data["id_trabajador_trabajador_academica"]);
		$db = $this->db->table('trabajador_academica');
		$db->where('id_trabajador_academica', $id);
		$db->update($data);

		return $this->db->affectedRows();
	}
	public function eliminar_academica($id)
	{
		$datos = array('id_trabajador_academica' => $id);
		$this->db->table('trabajador_academica')->delete($datos);
	}
	public function eliminar_adjunto($data)
	{
		$datos = array(
			'id_trabajador' => $data["id_trabajador"],
			'id_requisito' => $data["id_requisito"]
		);

		$this->db->table('trabajador_adjunto')->delete($datos);
		return $this->db->affectedRows();
	}

	public function guardar_adjunto($data)
	{
		unset($data["dni"]);

		$builder = $this->db->table('trabajador_adjunto');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}
	public function existe_adjunto($data)
	{
		$builder = $this->db->table('trabajador_adjunto');
		$builder->where('id_trabajador', $data["id_trabajador"]);
		$builder->where('id_requisito', $data["id_requisito"]);
		return $builder->countAllResults();
	}
}
