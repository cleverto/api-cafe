<?php

namespace App\Models;

use CodeIgniter\Model;

class CreditoModel extends Model
{

	public function lista()
	{
		$builder = $this->db->table('credito a');
		$builder->orderBy('a.credito');
		$query = $builder->get();
		$res = $query->getResultArray();
		return $res;
	}
	public function lista_pago($data)
	{
		if ($data["modulo"] == "compra") {

			$builder = $this->db->table('credito_detalle a');
			$builder->select('a.*');
			$builder->select('c.tipo_caja');
			$builder->join('credito_compra b', 'b.id_credito=a.id_credito', 'inner');
			$builder->join('tipo_caja c', 'c.id_tipo_caja=a.id_tipo_caja', 'inner');
			$builder->where('b.id_credito', $data["id"]);
			$builder->orderBy('a.fecha desc');
			$query = $builder->get();

			$res = $query->getResultArray();
			return $res;
		}
		if ($data["modulo"] == "venta") {

			$builder = $this->db->table('credito_detalle a');
			$builder->select('a.*');
			$builder->select('c.tipo_caja');
			$builder->join('credito_venta b', 'b.id_credito=a.id_credito', 'inner');
			$builder->join('tipo_caja c', 'c.id_tipo_caja=a.id_tipo_caja', 'inner');
			$builder->where('b.id_credito', $data["id"]);
			$builder->orderBy('a.fecha desc');
			$query = $builder->get();

			$res = $query->getResultArray();
			return $res;
		}
	}
	public function modulo($id)
	{
		$builder = $this->db->table('credito a');
		$builder->where('a.id_credito', $id);

		$query = $builder->get();
		$data = $query->getRowArray();

		return $data;
	}
	public function get_saldo($id)
	{
		$builder = $this->db->table('credito');
		$builder->select('saldo');
		$builder->where('id_credito', $id);
		$query = $builder->get()->getRow();
		$saldo = $query->saldo ?? 0;

		return $saldo;
	}
	public function get_id_by_compra($id)
	{
		$builder = $this->db->table('credito_compra');
		$builder->select('id_credito');
		$builder->where('id_compra', $id);
		$query = $builder->get()->getRow();
		$id = $query->id_credito ?? 0;

		return $id;
	}

	public function modulo_origen($post)
	{
		if ($post["modulo"] == "compra") {
			$builder = $this->db->table('compra a');
			$builder->select('a.*');
			$builder->select('b.proveedor, c.simbolo');
			$builder->select('d.id_credito, e.saldo, a.id_moneda');
			// 	$builder->select("CASE 
			//     WHEN a.id_moneda = 'USD' THEN ROUND(a.total * d.tipo_cambio,2)
			//     ELSE a.total
			// END AS total_pen");
			$builder->join('proveedor b', 'b.id_proveedor = a.id_proveedor', 'inner');
			$builder->join('moneda c', 'c.id_moneda = a.id_moneda', 'inner');
			$builder->join('credito_compra d', 'd.id_compra = a.id_compra', 'inner');
			$builder->join('credito e', 'e.id_credito = d.id_credito', 'inner');
			//$builder->join('tipo_cambio d', 'd.id_moneda = a.id_moneda and d.fecha=a.fecha', 'left');
			$builder->where('d.id_credito', $post["id"]);

			$query = $builder->get();
			return $query->getRowArray();
		}
		if ($post["modulo"] == "venta") {
			$builder = $this->db->table('venta a');
			$builder->select('a.*');
			$builder->select('b.proveedor, c.simbolo');
			$builder->select('d.id_credito, e.saldo, a.id_moneda');
			// 	$builder->select("CASE 
			//     WHEN a.id_moneda = 'USD' THEN ROUND(a.total * d.tipo_cambio,2)
			//     ELSE a.total
			// END AS total_pen");
			$builder->join('proveedor b', 'b.id_proveedor = a.id_proveedor', 'inner');
			$builder->join('moneda c', 'c.id_moneda = a.id_moneda', 'inner');
			$builder->join('credito_venta d', 'd.id_venta = a.id_venta', 'inner');
			$builder->join('credito e', 'e.id_credito = d.id_credito', 'inner');
			//$builder->join('tipo_cambio d', 'd.id_moneda = a.id_moneda and d.fecha=a.fecha', 'left');
			$builder->where('d.id_credito', $post["id"]);

			$query = $builder->get();
			return $query->getRowArray();
		}
	}
	public function guardar($data)
	{
		$builder = $this->db->table('credito');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}
	public function guardar_detalle($data)
	{
		$builder = $this->db->table('credito_detalle');
		$builder->insert($data);
		$id = $this->db->insertID();

		$this->set_saldo($data);

		return $id;
	}
	public function set_saldo($data)
	{
		// Sumar montos del detalle
		$builder = $this->db->table('credito_detalle');
		$builder->selectSum('monto', 'total_monto');
		$builder->where('id_credito', $data["id_credito"]);
		$query = $builder->get()->getRow();
		$total_monto = $query->total_monto ?? 0;

		$this->db->table('credito')
			->set('saldo', 'total - ' . $total_monto, false)
			->where('id_credito', $data['id_credito'])
			->update();
	}
	public function guardar_caja_credito($id, $id_caja)
	{
		$data = array(
			"id_caja" => $id_caja,
			"id_detalle" => $id,
		);
		$builder = $this->db->table('caja_credito');
		$builder->insert($data);
	}

	public function modificar($id, $datos)
	{
		$db = $this->db->table('credito');
		$db->where('id_credito', $id);
		$db->update($datos);

		return $this->db->affectedRows();
	}

	public function eliminar_detalle($data)
	{
		//$datos = array('id' => $data["id"]);
		// 1. Buscar el id_caja relacionado
		$row = $this->db->table('caja_credito')
			->select('id_caja')
			->where('id_detalle', $data["id"])
			->get()
			->getRow();



		// 3. Eliminar el registro en caja_credito
		$this->db->table('caja_credito')->where('id_detalle', $data["id"])->delete();

		if ($row) {
			$id_caja = $row->id_caja;

			// 4. Finalmente eliminar en caja usando el id_caja encontrado
			$this->db->table('caja')->where('id_caja', $id_caja)->delete();
		}

		// 2. Eliminar primero los detalles relacionados
		$this->db->table('credito_detalle')->where('id_detalle', $data["id"])->delete();

		$this->set_saldo(["id_credito" => $data["idmodulo"]]);

		return true;
	}
}
