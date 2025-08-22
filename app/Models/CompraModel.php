<?php

namespace App\Models;

use CodeIgniter\Model;

class CompraModel extends Model
{

	public function lista()
	{
		$builder = $this->db->table('compra a');
		$builder->select('a.*');
		$builder->select('b.ubigeo');
		$builder->join('ubigeo b', 'a.id_ubigeo = b.id_ubigeo', 'inner');
		$builder->orderBy('a.compra');
		$query = $builder->get();
		$res = $query->getResultArray();
		return $res;
	}
	public function lista_temp($id)
	{
		$builder = $this->db->table('compra_temp a');
		$builder->select('a.*');
		$builder->select('b.producto, b.id_categoria');
		$builder->join('producto b', 'a.id_producto = b.id_producto', 'inner');
		$builder->where('a.id_usuario', $id);
		$query = $builder->get();
		$res = $query->getResultArray();
		return $res;
	}
	public function modulo($id)
	{
		$builder = $this->db->table('compra a');
		$builder->select('a.*');
		$builder->select('b.ubigeo');
		$builder->join('ubigeo b', 'a.id_ubigeo = b.id_ubigeo', 'inner');
		$builder->where('a.id_compra', $id);

		$query = $builder->get();
		$data = $query->getRowArray();

		return $data;
	}
	public function guardar($data)
	{
		$builder = $this->db->table('compra');
		$builder->insert($data);
		$id = $this->db->insertID();


		$this->db->query("
  INSERT INTO compra_detalle (
    id_compra, id_producto, muestra, rendimiento, segunda, bola, cascara, humedad,
    descarte, pasilla, negro, ripio, impureza, defectos, taza, cantidad, precio, total
  )
  SELECT 
    ? AS id_compra, id_producto, muestra, rendimiento, segunda, bola, cascara, humedad,
    descarte, pasilla, negro, ripio, impureza, defectos, taza, cantidad, precio, total
  FROM compra_temp
  WHERE id_usuario = ?", [$id, $data["id_usuario"]]);

		return $id;
	}
	public function guardar_producto($data)
	{
		$builder = $this->db->table('compra_temp');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}
	public function existe_dni($data)
	{
		$builder = $this->db->table('compra');
		$builder->where('dni', $data["dni"]);
		return $builder->countAllResults();
	}
	public function existe_dni_modificar($data)
	{
		$builder = $this->db->table('compra');
		$builder->where('dni', $data["dni"]);
		$builder->where('id_compra !=', $data["idmodulo"]);
		return $builder->countAllResults();
	}
	public function modificar($id, $datos)
	{
		$db = $this->db->table('compra');
		$db->where('id_compra', $id);
		$db->update($datos);

		return $this->db->affectedRows();
	}

	public function eliminar($data)
	{
		$datos = array('id_compra' => $data->id);
		$query = $this->db->table('compra')->delete($datos);

		return $query;
	}
	public function eliminar_temp($id)
	{
		$datos = array('id_usuario' => $id);
		$query = $this->db->table('compra_temp')->delete($datos);

		return $query;
	}
	public function eliminar_producto($data)
	{
		$datos = array('id_detalle' => $data->id);
		$query = $this->db->table('compra_temp')->delete($datos);

		return $query;
	}
}
