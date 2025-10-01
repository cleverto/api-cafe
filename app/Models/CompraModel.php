<?php

namespace App\Models;

use CodeIgniter\Model;

class CompraModel extends Model
{

	public function lista($post)
	{



		$datos = array('id_modulo' => $post["id"]);
		$this->db->table('compra_temp')->delete($datos);

		$id_usuario = session()->get("data")["id_usuario"];
		$this->db->query("
  INSERT INTO compra_temp (
    id_modulo, id_empresa, id_sucursal, id_almacen, id_producto, id_usuario, muestra, rendimiento, segunda, bola, cascara, humedad,
    descarte, pasilla, negro, ripio, impureza, defectos, taza, cantidad, precio, total
  )
  SELECT 
    ? AS id_modulo, b.id_empresa, b.id_sucursal, b.id_almacen, a.id_producto,  ? as id_usuario, a.muestra, a.rendimiento, a.segunda, a.bola, a.cascara, a.humedad,
    a.descarte, a.pasilla, a.negro, a.ripio, a.impureza, a.defectos, a.taza, a.cantidad, a.precio, a.total
  FROM compra_detalle a
  INNER JOIN compra b ON a.id_compra=b.id_compra
  WHERE b.id_compra = ?", [$post["id"], $id_usuario, $post["id"]]);



		$builder = $this->db->table('compra_temp a');
		$builder->select('a.*');
		$builder->select('b.producto, b.id_categoria');
		$builder->join('producto b', 'a.id_producto = b.id_producto', 'inner');
		$builder->where('a.id_modulo', $post["id"]);
		$query = $builder->get();
		$data =  $query->getResultArray();



		return $data;
	}

public function lista_sin_secar()
{
    $builder = $this->db->table('compra a');
    $builder->select('
        a.id_compra, 
        a.fecha, 
        a.referencia, 
        p.proveedor, 
        a.total, 
        SUM(d.cantidad) as cantidad, 
        "0" as activo
    ');
    $builder->join('compra_detalle d', 'a.id_compra = d.id_compra', 'inner');		
	$builder->join('secado_compra sd', 'sd.id_compra = a.id_compra', 'left');
	$builder->join('secado s', 's.id_secado = sd.id_secado', 'left');
    $builder->join('proveedor p', 'p.id_proveedor = a.id_proveedor', 'inner');
    $builder->where('s.id_secado IS NULL');
    $builder->groupBy('a.id_compra, a.fecha, a.referencia, p.proveedor, a.total'); // 👈 agrupamos por compra
    $query = $builder->get();

    return $query->getResultArray();
}

	public function buscar($post)
	{
		$builder = $this->db->table('compra a');
		$builder->select('a.*');
		$builder->select('b.proveedor');
		$builder->select('c.id_credito');
		$builder->join('proveedor b', 'a.id_proveedor = b.id_proveedor', 'inner');
		$builder->join('credito_compra c', 'a.id_compra = c.id_compra', 'inner');
		$builder->where('a.fecha', $post["desde"]);
		$builder->orderBy('a.fecha');
		$query = $builder->get();
		return  $query->getResultArray();;
	}
	public function filtro($post)
	{
		$builder = $this->db->table('compra a');
		$builder->join('proveedor b', 'a.id_proveedor = b.id_proveedor', 'inner');
		$builder->join('compra_detalle c', 'a.id_compra = c.id_compra', 'inner');
		$builder->join('producto d', 'd.id_producto = c.id_producto', 'inner');
		$builder->where('a.fecha BETWEEN "' . $post['desde'] . '" AND "' . $post['hasta'] . '"');
		$builder->orderBy('a.fecha');
		$query = $builder->get();
		return  $query->getResultArray();;
	}
	public function lista_detalle($id)
	{
		$builder = $this->db->table('compra_detalle a');
		$builder->select('a.*');
		$builder->select('b.producto, b.id_categoria');
		$builder->join('producto b', 'a.id_producto = b.id_producto', 'inner');
		$builder->where('a.id_compra', $id);

		$query = $builder->get();
		return  $query->getResultArray();;
	}
	public function lista_temp($id, $id_usuario)
	{
		$builder = $this->db->table('compra_temp a');
		$builder->select('a.*');
		$builder->select('b.producto, b.id_categoria');
		$builder->join('producto b', 'a.id_producto = b.id_producto', 'inner');
		$builder->where("(a.id_modulo IS NULL OR a.id_modulo = '')");
		$builder->where('a.id_usuario', $id_usuario);

		$query = $builder->get();
		return  $query->getResultArray();;
	}

	public function modulo($id)
	{
		$builder = $this->db->table('compra a');
		$builder->select('a.*');
		$builder->select('b.proveedor, b.nro, c.simbolo');
		$builder->select('d.id_credito');
		// 	$builder->select("CASE 
		//     WHEN a.id_moneda = 'USD' THEN ROUND(a.total * d.tipo_cambio,2)
		//     ELSE a.total
		// END AS total_pen");
		$builder->join('proveedor b', 'b.id_proveedor = a.id_proveedor', 'inner');
		$builder->join('moneda c', 'c.id_moneda = a.id_moneda', 'inner');
		$builder->join('credito_compra d', 'd.id_compra = a.id_compra', 'inner');
		//$builder->join('tipo_cambio d', 'd.id_moneda = a.id_moneda and d.fecha=a.fecha', 'left');
		$builder->where('a.id_compra', $id);

		$query = $builder->get();
		return $query->getRowArray();
	}
	public function get_id_kardex($id)
	{
		$builder = $this->db->table('kardex_compra');
		$builder->select('id_kardex');
		$builder->where('id_compra', $id);
		$query = $builder->get()->getRow();

		return $query->id_kardex ?? 0;;
	}
	public function get_total($id)
	{
		$builder = $this->db->table('compra a');
		$builder->selectSum('total');
		$builder->where('id_compra', $id);
		$query = $builder->get()->getRow();

		return $query->total ?? 0;;
	}
	public function get_suma_total($id, $id_usuario)
	{
		$builder = $this->db->table('compra_temp a');
		$builder->selectSum('total');
		if (!empty($id)) {
			$builder->where('a.id_modulo', $id);
		}
		$builder->where('id_usuario', $id_usuario);
		$query = $builder->get()->getRow();

		return $query->total ?? 0;;
	}
	public function guardar($datos)
	{
		$builder = $this->db->table('compra');
		$builder->insert($datos);
		$id = $this->db->insertID();


		$this->db->query("
  INSERT INTO compra_detalle (
    id_compra, id_producto, muestra, sacos, rendimiento, segunda, bola, cascara, humedad,
    descarte, pasilla, negro, ripio, impureza, defectos, taza, kg_bruto, kg_neto, qq_bruto, cantidad, precio, total
  )
  SELECT 
    ? AS id_compra, id_producto, muestra, sacos, rendimiento, segunda, bola, cascara, humedad,
    descarte, pasilla, negro, ripio, impureza, defectos, taza, kg_bruto, kg_neto, qq_bruto, cantidad, precio, total
  FROM compra_temp
  WHERE id_usuario = ?", [$id, $datos["id_usuario"]]);

		return $id;
	}
	public function guardar_producto($data)
	{
		$builder = $this->db->table('compra_temp');
		$builder->insert($data);
		return $this->db->insertID();
	}
	public function guardar_credito_compra($id, $id_credito)
	{
		$data = array("id_compra" => $id, "id_credito" => $id_credito);

		$builder = $this->db->table('credito_compra');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}
	public function guardar_kardex_compra($id, $id_kardex)
	{
		$data = array("id_compra" => $id, "id_kardex" => $id_kardex);

		$builder = $this->db->table('kardex_compra');
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
		$t = $this->db->affectedRows();

		$datos_compra = array('id_compra' => $id);
		$this->db->table('compra_detalle')->delete($datos_compra);

		$this->db->query("
  INSERT INTO compra_detalle (
    id_compra, id_producto, muestra, rendimiento, segunda, bola, cascara, humedad,
    descarte, pasilla, negro, ripio, impureza, defectos, taza, cantidad, precio, total
  )
  SELECT 
    ? AS id_compra, id_producto, muestra, rendimiento, segunda, bola, cascara, humedad,
    descarte, pasilla, negro, ripio, impureza, defectos, taza, cantidad, precio, total
  FROM compra_temp
  WHERE id_modulo = ? AND id_usuario = ?", [$id, $id, $datos["id_usuario"]]);

		return $t;
	}

	public function eliminar($data)
	{
		$builder = $this->db->table('kardex_compra a');
		$builder->select('id_kardex');
		$builder->where('id_compra', $data["id"]);
		$query = $builder->get()->getRow();
		$id_kardex = $query->id_kardex ?? "";;

		$datos_kardex = array('id_kardex' => $id_kardex);
		$query = $this->db->table('kardex_compra')->delete($datos_kardex);
		$query = $this->db->table('kardex_detalle')->delete($datos_kardex);
		$query = $this->db->table('kardex')->delete($datos_kardex);


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
	public function eliminar_temp_by_id_modulo($id)
	{
		$datos = array('id_modulo' => $id);
		$query = $this->db->table('compra_temp')->delete($datos);

		return $query;
	}
	public function eliminar_producto($post)
	{
		$datos = array('id_detalle' => $post["id"]);
		$query = $this->db->table('compra_temp')->delete($datos);

		return $query;
	}
}
