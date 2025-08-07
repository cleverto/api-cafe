<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{

	public function lista()
	{
		$builder = $this->db->table('usuario a');
		$builder->select("a.*");
		$builder->select("pf.perfil");
		$builder->join("perfil pf", 'pf.id_perfil=a.id_perfil', 'inner');
		$builder->orderBy('a.activo', 'desc');
		$builder->orderBy('a.usuario');
		$query = $builder->get();
		$data = $query->getResultArray();

		return $data;
	}

	public function lista_activo()
	{
		// $builder = $this->db->table('usuario a');
		// $builder->select("a.nombrecompleto, pe.nombre as desc_perfil");
		// $builder->join("per_trabajador p", 'p.persona=a.persona', 'inner');
		// $builder->join("men_personaperfil pf", 'pf.persona=a.persona', 'inner');
		// $builder->join("men_perfil pe", 'pe.perfil=pf.perfil', 'inner');
		// $builder->where('p.activo', '1');
		// $builder->orderBy('a.nombrecompleto');
		// $result = $builder->get();

		// return $result;
	}

	public function modulo($data)
	{

		$builder = $this->db->table('usuario a');
		$builder->select("a.*");
		$builder->select("pf.perfil");
		$builder->join("perfil pf", 'pf.id_perfil=a.id_perfil', 'inner');
		$builder->orderBy('a.usuario');
		$builder->where('a.id_usuario', $data->id);
		$query = $builder->get();
		$data = $query->getRowArray();
		return $data;
	}


	public function guardar($data)
	{
		$builder = $this->db->table('usuario');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}
	public function modificar($id, $data)
	{
		unset($data['password']);
		$db = $this->db->table('usuario');
		$db->where('id_usuario', $id);
		$db->update($data);

		return $this->db->affectedRows();
	}
	public function eliminar($data)
	{
		$query = $this->db->table('usuario')->delete(array('id_usuario' => $data->id));
		return $query;
	}

	public function existe_email($data)
	{
		$builder = $this->db->table('usuario');
		$builder->where('email', $data["email"]);
		return $builder->countAllResults();
	}
	public function existe_dni($data)
	{
		$builder = $this->db->table('usuario');
		$builder->where('dni', $data["dni"]);
		return $builder->countAllResults();
	}

	public function existe_email_modificar($id, $data)
	{
		$builder = $this->db->table('usuario');
		$builder->where('email', $data["email"]);
		$builder->where('id_usuario !=', $id);
		return $builder->countAllResults();
	}
	public function existe_dni_modificar($id, $data)
	{
		$builder = $this->db->table('usuario');
		$builder->where('dni', $data["dni"]);
		$builder->where('id_usuario !=', $id);
		return $builder->countAllResults();
	}
}
