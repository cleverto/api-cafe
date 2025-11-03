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

	public function lista_detalle($id)
	{
		$builder = $this->db->table('compra_detalle a');
		$builder->select('a.*');
		$builder->select('a.id_detalle as id_detalle, b.producto, b.id_categoria, d.id_secado');
		$builder->join('producto b', 'a.id_producto = b.id_producto', 'inner');
		$builder->join('secado_compra d', 'a.id_compra = d.id_compra', 'inner');
		$builder->where('d.id_secado', $id);

		$query = $builder->get();
		return  $query->getResultArray();;
	}

	public function buscar($post)
	{
		$builder = $this->db->table('secado a');
		$builder->select('a.*');

		// Concatenar todos los nro_comprobante en una sola celda
		$builder->select("GROUP_CONCAT(aa.nro_comprobante SEPARATOR ', ') as referencia");

		// Concatenar todos los proveedores en una sola celda
		$builder->select("GROUP_CONCAT(b.proveedor SEPARATOR ', ') as proveedores");

		$builder->join('secado_compra d', 'a.id_secado = d.id_secado', 'inner');
		$builder->join('compra aa', 'aa.id_compra = d.id_compra', 'inner');
		$builder->join('proveedor b', 'aa.id_proveedor = b.id_proveedor', 'inner');

		$builder->where("a.fecha BETWEEN '{$post['desde']}' AND '{$post['hasta']}'");

		// Agrupar por id_secado para que no repita filas
		$builder->groupBy('a.id_secado');
		$builder->orderBy('a.fecha');

		$query = $builder->get();
		return $query->getResultArray();
	}
	public function filtro_compras($post)
	{
		$db = $this->db;

		// --- Compras que NO están en secado_compra ---
		$builder1 = $db->table('compra_detalle cd');
		$builder1->select([
			'c.fecha',
			'p.producto',
			'"Compra" AS operacion',
			'cd.cantidad',
			'cd.rendimiento',
			'cd.cascara',
			'cd.humedad'
		]);
		$builder1->join('compra c', 'c.id_compra = cd.id_compra', 'inner');
		$builder1->join('producto p', 'p.id_producto = cd.id_producto', 'inner');
		$builder1->join('secado_compra sc', 'sc.id_compra = c.id_compra', 'left');
		$builder1->where('sc.id_compra  IS  NULL');
		if (!empty($post['id'])) {
			$builder1->where('cd.id_producto', $post['id']);
		}
		$builder1->where('DATE(c.fecha) >=', $post['desde']);
		$builder1->where('DATE(c.fecha) <=', $post['hasta']);

		// --- Productos que retornaron del secado ---
		$builder2 = $db->table('secado_detalle sd');
		$builder2->select([
			's.fecha',
			'p.producto',
			'"Secado" AS operacion',
			'sd.cantidad',
			'sd.rendimiento',
			'sd.cascara',
			'sd.humedad'
		]);
		$builder2->join('secado s', 's.id_secado = sd.id_secado', 'inner');
		$builder2->join('producto p', 'p.id_producto = sd.id_producto', 'inner');
		$builder2->where('s.operacion', 'S');
		if (!empty($post['id'])) {
			$builder2->where('sd.id_producto', $post['id']);
		}
		$builder2->where('DATE(s.fecha) >=', $post['desde']);
		$builder2->where('DATE(s.fecha) <=', $post['hasta']);

		// --- Unir ambas consultas ---
		$builder1->unionAll($builder2);

		// Ejecutar
		$query = $builder1->get();
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

	public function secado_compra($id, $id_kardex, $compras)
	{
		$batch = [];
		$idsAgregados = [];
		foreach ($compras as $row) {
			if (!in_array($row['id_compra'], $idsAgregados)) {
				$batch[] = [
					'id_secado' => $id,
					'id_compra' => $row['id_compra'],
					'id_kardex' => $id_kardex,
				];
				$idsAgregados[] = $row['id_compra']; // marca como agregado
			}
		}


		$builder = $this->db->table('secado_compra');
		$builder->insertBatch($batch);
	}
	public function guardar($datos)
	{
		$builder = $this->db->table('secado');
		$builder->insert($datos);
		$id = $this->db->insertID();

		return $id;
	}
	public function guardar_kardex($motivo, $datos, $compras)
	{
		$datos_kardex = [
			'id_empresa'          => $datos["id_empresa"],
			'id_sucursal'         => $datos["id_sucursal"],
			'id_almacen'          => $datos["id_almacen"],
			'id_usuario'          => $datos["id_usuario"],
			'id_tipo_comprobante' => $datos["id_tipo_comprobante"],
			'operacion'           => $datos["operacion"],   // entrada o salida
			'motivo'              => $motivo,
			'fecha'               => $datos["fecha"],
			'nro_comprobante'     => $datos["nro_comprobante"],
		];

		$this->db->table('kardex')->insert($datos_kardex);
		$id_kardex = $this->db->insertID();


		// 3. A partir de las compras activas, insertar en kardex_detalle
		$kardexBatch = [];
		$compraBatch = [];
		foreach ($compras as $detalle) {
			if ($datos["operacion"] == "S") {
				// Obtener detalle de cada compra
				$detallesCompra = $this->db->table('compra_detalle')
					->select('id_producto, cantidad, precio, total, rendimiento, cascara, humedad')
					->where('id_compra', $detalle['id_compra'])
					->get()
					->getResultArray();

				$db = $this->db->table('compra');
				$db->where('id_compra',  $detalle['id_compra']);
				$db->update(['estado' => '1']);

				foreach ($detallesCompra as $dc) {
					$kardexBatch[] = [
						'id_kardex'   => $id_kardex,
						'id_producto' => $dc['id_producto'],
						'cantidad'    => $dc['cantidad'],
						'precio'    =>  $dc['precio'],
						'total'    =>  $dc['total'],
					];
				}
				foreach ($detallesCompra as $dc) {
					$compraBatch[] = [
						'id_producto' => $dc['id_producto'],
						'cantidad'    => $dc['cantidad'],
						'precio'    => $dc['precio'],
						'total'    => $dc['total'],
						'rendimiento'    => $dc['rendimiento'],
						'cascara'    => $dc['cascara'],
						'humedad'    => $dc['humedad'],
					];
				}
			} else {

				$kardexBatch[] = [
					'id_kardex'   => $id_kardex,
					'id_producto' => $detalle['id_producto'],
					'cantidad'    => $detalle['cantidad'],
					'precio'    =>  $detalle['precio'],
					'total'    =>  $detalle['total'],
				];

				$compraBatch[] = [
					'id_producto' => $detalle['id_producto'],
					'cantidad'    => $detalle['cantidad'],
					'precio'    => $detalle['precio'],
					'total'    => $detalle['total'],
					'rendimiento'    => $detalle['rendimiento'],
					'cascara'    => $detalle['cascara'],
					'humedad'    => $detalle['humedad'],
				];
			}
		}

		if (!empty($kardexBatch)) {
			$this->db->table('kardex_detalle')->insertBatch($kardexBatch);
		}

		return [$id_kardex, $compraBatch];
	}
	public function guardar_detalle($id, $detalle)
	{
		$detalleBatch = [];
		foreach ($detalle as $dc) {
			$detalleBatch[] = [
				'id_secado'   => $id,
				'id_producto' => $dc['id_producto'],
				'cantidad'    => $dc['cantidad'],
				'precio'    => $dc['precio'],
				'total'    => $dc['total'],
				'rendimiento'    => $dc['rendimiento'],
				'cascara'    => $dc['cascara'],
				'humedad'    => $dc['humedad'],
			];
		}

		if (!empty($detalleBatch)) {
			$this->db->table('secado_detalle')->insertBatch($detalleBatch);
		}
	}
	public function modificar($id, $datos)
	{
		$db = $this->db->table('secado');
		$db->where('id_secado', $id);
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
		if ($data['operacion'] == "S") {
			$builder = $this->db->table('secado_compra a');
			$builder->select('id_kardex, id_compra');
			$builder->where('id_secado', $data["id"]);
			$query = $builder->get()->getRow();
			$id_kardex = $query->id_kardex ?? "";;
			$compras = $builder->get()->getResultArray();

			foreach ($compras as $detalle) {
				$db = $this->db->table('compra');
				$db->where('id_compra',  $detalle['id_compra']);
				$db->update(['estado' => '0']);
			}
		} else {
			$builder = $this->db->table('secado_retorno a');
			$builder->select('id_kardex, id_secado');
			$builder->where('id_retorno', $data["id"]);
			$query = $builder->get()->getRow();
			$compras = $builder->get()->getResultArray();
			$id_kardex = $query->id_kardex ?? "";;

			foreach ($compras as $detalle) {
				$db = $this->db->table('secado');
				$db->where('id_secado',  $detalle['id_secado']);
				$db->update(['estado' => '0']);
			}
		}

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
		$query = $this->db->table('secado_compra')->delete($datos);
		$query = $this->db->table('secado_detalle')->delete($datos);
		$query = $this->db->table('secado_retorno')->delete(['id_retorno' => $data["id"]]);


		$model_almacen = new AlmacenModel();
		$model_almacen->restaurar_stock($id_kardex, $productos);

		$query = $this->db->table('secado')->delete($datos);

		return $query;
	}
	public function secado_retorno($id, $id_secado, $id_kardex)
	{
		$datos = ['id_retorno' => $id, 'id_secado' => $id_secado, 'id_kardex' => $id_kardex];
		$builder = $this->db->table('secado_retorno');
		$builder->insert($datos);
		$id = $this->db->insertID();
	}
	public function guardar_retorno($id_secado, $datos, $detalle)
	{

		$builder = $this->db->table('secado');
		$builder->insert($datos);
		$id = $this->db->insertID();

		$db = $this->db->table('secado');
		$db->where('id_secado', $id_secado);
		$db->update(['estado' => '1']);


		$cantidadTotal = 0;
		$totalGeneral = 0;
		$detalleBatch = [];
		foreach ($detalle as $dc) {
			$detalleBatch[] = [
				'id_secado'   => $id,
				'id_producto' => $dc['id_producto'],
				'cantidad'    => $dc['cantidad'],
				'precio'    => $dc['precio'],
				'total'    => $dc['total'],
				'rendimiento'    => $dc['rendimiento'],
				'cascara'    => $dc['cascara'],
				'humedad'    => $dc['humedad'],
			];
			$cantidadTotal += $dc['cantidad'];
			$totalGeneral += $dc['total'];
		}

		// Redondear a 2 decimales
		$cantidadTotal = number_format($cantidadTotal, 2, '.', '');
		$totalGeneral  = number_format($totalGeneral, 2, '.', '');


		if (!empty($detalleBatch)) {
			$this->db->table('secado_detalle')->insertBatch($detalleBatch);

			$totales = array("cantidad" => $cantidadTotal, "total" => $totalGeneral);
			$db = $this->db->table('secado');
			$db->where('id_secado', $id);
			$db->update($totales);
		}
		return $id;
	}
}
