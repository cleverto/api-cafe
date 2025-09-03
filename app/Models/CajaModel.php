<?php

namespace App\Models;

use CodeIgniter\Model;

class CajaModel extends Model
{

	public function lista()
	{
		$builder = $this->db->table('producto a');
		$builder->orderBy('a.producto');
		$query = $builder->get();
		$res = $query->getResultArray();
		return $res;
	}
	public function lista_by_usuario($post)
	{
		$builder = $this->db->table('caja a');
		$builder->select("a.*, b.*, c.*, e.concepto,
    (CASE 
        WHEN d.id_caja IS NULL THEN 1 
        ELSE 0 
    END) AS es_caja", false);
		$builder->join('proveedor b', 'a.id_proveedor=b.id_proveedor');
		$builder->join('moneda c', 'a.id_moneda=c.id_moneda');
		$builder->join('caja_credito d', 'a.id_caja=d.id_caja', 'left');
		$builder->join('concepto e', 'a.id_concepto=e.id_concepto', 'inner');
		$builder->where('a.id_usuario', $post["id_usuario"]);
		$query = $builder->get();
		$res = $query->getResultArray();
		return $res;
	}
	public function apertura($moneda)
	{
		$builder =  $this->db->table('caja c');
		$builder->select("
    SUM(CASE WHEN c.movimiento = 'I' THEN c.monto ELSE 0 END) -
    SUM(CASE WHEN c.movimiento = 'S' THEN c.monto ELSE 0 END) AS saldo
", false);
		$builder->where('c.id_concepto', '1');
		$builder->where('c.id_moneda', $moneda);
		$builder->where('c.estado', 0);

		$query = $builder->get();
		$result = $query->getRow();

		return $result->saldo ?? 0;
	}
	public function ingresos($moneda)
	{
		$builder =  $this->db->table('caja c');
		$builder->select("SUM(c.monto)  AS saldo");
		$builder->where('c.id_concepto !=', '1');
		$builder->where('c.id_moneda', $moneda);
		$builder->where('c.movimiento', 'I');
		$builder->where('c.estado', 0);

		$query = $builder->get();
		$result = $query->getRow();

		return $result->saldo ?? 0;
	}
	public function egresos($moneda)
	{
		$builder =  $this->db->table('caja c');
		$builder->select("SUM(c.monto)  AS saldo");
		$builder->where('c.id_concepto !=', '1');
		$builder->where('c.id_moneda', $moneda);
		$builder->where('c.movimiento', 'S');
		$builder->where('c.estado', 0);

		$query = $builder->get();
		$result = $query->getRow();

		return $result->saldo ?? 0;
	}
	public function saldo_usuarios()
	{
		$builder = $this->db->table('caja c');
		$builder->select("
    c.id_usuario, b.usuario, c.id_moneda, 
    SUM(CASE WHEN c.movimiento = 'I' THEN c.monto ELSE 0 END) -
    SUM(CASE WHEN c.movimiento = 'S' THEN c.monto ELSE 0 END) AS saldo_apertura
", false);
		$builder->join('usuario b', 'c.id_usuario=b.id_usuario', 'inner');
		$builder->join('moneda e', 'c.id_moneda=e.id_moneda', 'inner');
		
		$builder->where('c.estado', 0);
		$builder->groupBy('c.id_usuario, c.id_moneda');

		$query = $builder->get();
		return $query->getResult();
	}
	public function lista_pago($id)
	{
		$builder = $this->db->table('caja_detalle a');
		$builder->join('caja_compra b', 'b.id_caja=a.id_caja', 'inner');
		$builder->where('b.id_compra', $id);
		$builder->orderBy('a.fecha desc');
		$query = $builder->get();

		return $query->getResultArray();
	}

	public function modulo($id)
	{
		$builder = $this->db->table('caja a');
		$builder->join('concepto b', 'a.id_concepto=b.id_concepto', 'inner');
		$builder->join('proveedor c', 'a.id_proveedor=c.id_proveedor', 'inner');
		$builder->join('moneda d', 'a.id_moneda=d.id_moneda', 'inner');
		$builder->where('a.id_caja', $id);

		$query = $builder->get();
		return $query->getRowArray();

	}
	public function guardar($data)
	{
		$builder = $this->db->table('caja');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}
	public function modificar($id, $datos)
	{
		$db = $this->db->table('caja');
		$db->where('id_caja', $id);
		$db->update($datos);

		return $this->db->affectedRows();
	}

	public function eliminar($data)
	{
		$datos = array('id_caja' => $data["id"]);
		return $this->db->table('caja')->delete($datos);

	}
}
