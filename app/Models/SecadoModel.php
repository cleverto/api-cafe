<?php

namespace App\Models;

use CodeIgniter\Model;

class SecadoModel extends Model
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
  INNER JOIN secado b ON a.id_compra=b.id_compra
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

	public function buscar($post)
	{
		$builder = $this->db->table('secado a');
		$builder->select('a.*');

		// Concatenar todos los nro_comprobante en una sola celda
		$builder->select("GROUP_CONCAT(aa.nro_comprobante SEPARATOR ', ') as referencia");

		// Concatenar todos los proveedores en una sola celda
		$builder->select("GROUP_CONCAT(b.proveedor SEPARATOR ', ') as proveedores");

		$builder->join('secado_detalle d', 'a.id_secado = d.id_secado', 'inner');
		$builder->join('compra aa', 'aa.id_compra = d.id_compra', 'inner');
		$builder->join('proveedor b', 'aa.id_proveedor = b.id_proveedor', 'inner');

		$builder->where('a.fecha', $post["desde"]);

		// Agrupar por id_secado para que no repita filas
		$builder->groupBy('a.id_secado');
		$builder->orderBy('a.fecha');

		$query = $builder->get();
		return $query->getResultArray();
	}


	public function modulo($id)
	{
		$builder = $this->db->table('secado a');
		$builder->select('a.*');
		$builder->select('b.proveedor, b.nro, c.simbolo');
		$builder->select('d.id_credito');

		$builder->join('proveedor b', 'b.id_proveedor = a.id_proveedor', 'inner');
		$builder->join('moneda c', 'c.id_moneda = a.id_moneda', 'inner');
		$builder->join('credito_compra d', 'd.id_compra = a.id_compra', 'inner');
		//$builder->join('tipo_cambio d', 'd.id_moneda = a.id_moneda and d.fecha=a.fecha', 'left');
		$builder->where('a.id_compra', $id);

		$query = $builder->get();
		return $query->getRowArray();
	}

	public function guardar_detalle($id, $id_kardex, $compras)
	{
		$batch = [];
		foreach ($compras as $row) {
			$batch[] = [
				'id_secado' => $id,
				'id_compra' => $row['id_compra'],
				'id_kardex' => $id_kardex,
			];
		}


		$builder = $this->db->table('secado_detalle');
		$builder->insertBatch($batch);
	}
	public function guardar($datos)
	{
		$builder = $this->db->table('secado');
		$builder->insert($datos);
		$id = $this->db->insertID();

		return $id;
	}
	public function guardar_kardex($datos, $compras)
	{
		$datos_kardex = [
			'id_empresa'          => $datos["id_empresa"],
			'id_sucursal'         => $datos["id_sucursal"],
			'id_almacen'          => $datos["id_almacen"],
			'id_usuario'          => $datos["id_usuario"],
			'id_tipo_comprobante' => $datos["id_tipo_comprobante"],
			'operacion'           => "S",   // entrada o salida
			'motivo'              => "SECADO",
			'fecha'               => $datos["fecha"],
			'nro_comprobante'     => $datos["nro_comprobante"],
		];

		$this->db->table('kardex')->insert($datos_kardex);
		$id_kardex = $this->db->insertID();


		// 3. A partir de las compras activas, insertar en kardex_detalle
		$kardexBatch = [];
		foreach ($compras as $detalle) {
			// Obtener detalle de cada compra
			$detallesCompra = $this->db->table('compra_detalle')
				->select('id_producto, cantidad')
				->where('id_compra', $detalle['id_compra'])
				->get()
				->getResultArray();

			foreach ($detallesCompra as $dc) {
				$kardexBatch[] = [
					'id_kardex'   => $id_kardex,        // opcional si quieres trazar
					'id_producto' => $dc['id_producto'],
					'cantidad'    => $dc['cantidad'],
					'precio'    => '0',
					'total'    => '0',
				];
			}
		}

		if (!empty($kardexBatch)) {
			$this->db->table('kardex_detalle')->insertBatch($kardexBatch);
		}
		return $id_kardex;
	}
	public function modificar($id, $datos)
	{
		$db = $this->db->table('secado');
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
		$builder = $this->db->table('secado_detalle a');
		$builder->select('id_kardex');
		$builder->where('id_secado', $data["id"]);
		$query = $builder->get()->getRow();
		$id_kardex = $query->id_kardex ?? "";;


		$sql = "
    SELECT 
        k.id_empresa,
        k.id_sucursal,
        k.id_almacen,
        d.id_producto,
        k.operacion,
        d.cantidad
    FROM kardex k
    JOIN kardex_detalle d ON k.id_kardex = d.id_kardex
    WHERE k.id_kardex = ?
";
		$productos = $this->db->query($sql, [$id_kardex])->getResultArray();



		$datos_kardex = array('id_kardex' => $id_kardex);
		$datos = array('id_secado' => $data["id"]);
		$query = $this->db->table('kardex_detalle')->delete($datos_kardex);
		$query = $this->db->table('kardex')->delete($datos_kardex);
		$query = $this->db->table('secado_detalle')->delete($datos);

		$model_almacen = new AlmacenModel();
		$model_almacen->restaurar_stock($id_kardex, $productos);

		$query = $this->db->table('secado')->delete($datos);

		return $query;
	}
}
