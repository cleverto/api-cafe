<?php

namespace App\Models;

use CodeIgniter\Model;

class ProcesoModel extends Model
{

	public function lista($post)
	{
		// Primera consulta (compra)
		$builder1 = $this->db->table('compra a');
		$builder1->select('
    a.id_compra, "Compra" as modulo,
    a.fecha, 
	"" as operacion,
	a.nro_comprobante,
    a.referencia, 
    p.proveedor, 
    a.total, 
    SUM(d.cantidad) AS cantidad,
	"I" as operacion
');
		$builder1->join('compra_detalle d', 'a.id_compra = d.id_compra', 'inner');
		$builder1->join('proveedor p', 'p.id_proveedor = a.id_proveedor', 'inner');
		$builder1->where('a.estado', '0');
		$builder1->groupBy('a.id_compra, a.fecha, a.referencia, p.proveedor, a.total');
		// Obtenemos el SQL sin ejecutar
		$sql1 = $builder1->getCompiledSelect();

		// Segunda consulta (secado)
		$builder2 = $this->db->table('secado a');
		$builder2->distinct();
		$builder2->select(' a.id_secado AS id_compra, "Secado" AS modulo, a.fecha, a.operacion, a.nro_comprobante, GROUP_CONCAT(DISTINCT c.referencia ORDER BY c.referencia SEPARATOR ", ") AS referencia, GROUP_CONCAT(DISTINCT p.proveedor ORDER BY p.proveedor SEPARATOR ", ") AS proveedor, a.total, SUM(d.cantidad) AS cantidad, a.operacion ');
		$builder2->join('secado_detalle d', 'a.id_secado = d.id_secado', 'inner');
		$builder2->join('secado_compra e', 'e.id_secado = a.id_secado', 'inner');
		$builder2->join('compra c', 'c.id_compra = e.id_compra', 'inner');
		$builder2->join('proveedor p', 'p.id_proveedor = c.id_proveedor', 'inner');
		$builder2->where('a.estado', '0');

		$builder2->groupBy('a.id_secado, a.fecha, a.nro_comprobante, a.total');

		// Obtenemos el SQL sin ejecutar
		$sql2 = $builder2->getCompiledSelect();


		// Unimos ambas con UNION ALL
		$sql = "($sql1) UNION ALL ($sql2)";

		// Ejecutamos la consulta final
		$query = $this->db->query($sql);

		// Obtenemos el resultado
		return $query->getResult();
	}
	public function lista_detalle($id)
	{
		$builder = $this->db->table('compra_detalle a');
		$builder->select('a.*');
		$builder->select('a.id_compra_detalle as id_detalle, b.producto, b.id_categoria, d.id_secado');
		$builder->join('producto b', 'a.id_producto = b.id_producto', 'inner');
		$builder->join('secado_compra d', 'a.id_compra = d.id_compra', 'inner');
		$builder->where('d.id_secado', $id);

		$query = $builder->get();
		return  $query->getResultArray();;
	}
	public function lista_detalle_salida($id)
	{
		$builder = $this->db->table('proceso_detalle a');
		$builder->select('a.*');
		$builder->select('b.producto, b.id_categoria');
		$builder->join('producto b', 'a.id_producto = b.id_producto', 'inner');
		$builder->where('a.id_proceso', $id);

		$query = $builder->get();
		return  $query->getResultArray();;
	}
	public function buscar($post)
	{

		// Concatenar los nro_comprobante de compra o secado según el módulo
		$builder = $this->db->table('proceso a');
		$builder->select('a.*');

		// nro_comprobante según el módulo (Compra o Secado)
		$builder->select("
    GROUP_CONCAT(
        CASE 
            WHEN d.modulo = 'Compra' THEN c.nro_comprobante
            WHEN d.modulo = 'Secado' THEN s.nro_comprobante
        END SEPARATOR ', '
    ) AS referencia
");

		// proveedor según el módulo
		// Compra → proveedor directo
		// Secado → proveedor de la compra asociada vía secado_compra
		$builder->select("
    GROUP_CONCAT(
        CASE 
            WHEN d.modulo = 'Compra' THEN p.proveedor
            WHEN d.modulo = 'Secado' THEN ps.proveedor
        END SEPARATOR ', '
    ) AS proveedores
");

		$builder->join('proceso_modulo d', 'a.id_proceso = d.id_proceso', 'inner');

		// JOIN para Compra
		$builder->join('compra c', 'c.id_compra = d.id_modulo AND d.modulo="Compra"', 'left');
		$builder->join('proveedor p', 'p.id_proveedor = c.id_proveedor', 'left');

		// JOIN para Secado
		$builder->join('secado s', 's.id_secado = d.id_modulo AND d.modulo="Secado"', 'left');

		// Relación Secado → Compra → Proveedor
		$builder->join('secado_compra sc', 'sc.id_secado = s.id_secado', 'left');
		$builder->join('compra cs', 'cs.id_compra = sc.id_compra', 'left');
		$builder->join('proveedor ps', 'ps.id_proveedor = cs.id_proveedor', 'left');

		$builder->where("a.fecha BETWEEN '{$post['desde']}' AND '{$post['hasta']}'");

		$builder->groupBy('a.id_proceso');
		$builder->orderBy('a.fecha');



		$query = $builder->get();

		return $query->getResultArray();
	}
	public function filtro_compras($post)
	{
		$db = $this->db;

		// --- Compras que NO están en proceso_compra ---
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
		$builder1->join('proceso_compra sc', 'sc.id_compra = c.id_compra', 'left');
		$builder1->where('sc.id_compra  IS  NULL');
		if (!empty($post['id'])) {
			$builder1->where('cd.id_producto', $post['id']);
		}
		$builder1->where('DATE(c.fecha) >=', $post['desde']);
		$builder1->where('DATE(c.fecha) <=', $post['hasta']);

		// --- Productos que retornaron del proceso ---
		$builder2 = $db->table('proceso_detalle sd');
		$builder2->select([
			's.fecha',
			'p.producto',
			'"Secado" AS operacion',
			'sd.cantidad',
			'sd.rendimiento',
			'sd.cascara',
			'sd.humedad'
		]);
		$builder2->join('proceso s', 's.id_secado = sd.id_secado', 'inner');
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
		$builder = $this->db->table('proceso a');
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
	public function proceso_compra_secado_salida($id, $id_kardex, $compras)
	{

		$batch = [];
		foreach ($compras as $row) {
			$batch[] = [
				'id_proceso' => $id,
				'id_modulo' => $row['id_compra'],
				'id_kardex' => $id_kardex,
				'modulo' => $row['modulo'],
			];
		}


		$builder = $this->db->table('proceso_modulo');
		$builder->insertBatch($batch);
	}
	public function proceso_compra_secado_ingreso($id, $id_kardex, $id_proceso_salida)
	{
		$detalles = $this->db->table('proceso_modulo')
			->where('id_proceso', $id_proceso_salida)
			->get()
			->getResultArray();

		$batch = [];
		foreach ($detalles as $dc) {
			$batch[] = [
				'id_proceso'   => $id,
				'id_kardex'   => $id_kardex,
				'id_modulo' => $dc['id_modulo'],
				'modulo'    => $dc['modulo'],
			];
		}


		$builder = $this->db->table('proceso_modulo');
		$builder->insertBatch($batch);
	}
	public function guardar($datos)
	{
		$builder = $this->db->table('proceso');
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
				if ($detalle["modulo"] == "Compra") {
					$detallesCompra = $this->db->table('compra_detalle')
						->select('id_producto, cantidad, precio, total, rendimiento, cascara, humedad')
						->where('id_compra', $detalle['id_compra'])
						->get()
						->getResultArray();

					$db = $this->db->table('compra');
					$db->where('id_compra',  $detalle['id_compra']);
					$db->update(['estado' => '1']);
				} else {
					$detallesCompra = $this->db->table('secado_detalle')
						->select('id_producto, cantidad, precio, total, rendimiento, cascara, humedad')
						->where('id_secado', $detalle['id_compra'])
						->get()
						->getResultArray();

					$db = $this->db->table('secado');
					$db->where('id_secado',  $detalle['id_compra']);
					$db->update(['estado' => '1']);
				}

				if ($detalle["modulo"] == "Compra") {
					$db = $this->db->table('compra');
					$db->where('id_compra',  $detalle['id_compra']);
					$db->update(['estado' => '1']);
				} else {
					$db = $this->db->table('Secado');
					$db->where('id_secado',  $detalle['id_compra']);
					$db->update(['estado' => '1']);
				}

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
						'precio'    =>  $dc['precio'],
						'total'    =>  $dc['total'],
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
					'precio'    =>  $detalle['precio'],
					'total'    =>  $detalle['total'],
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
				'id_proceso'   => $id,
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
			$this->db->table('proceso_detalle')->insertBatch($detalleBatch);
		}
	}


	public function eliminar($data)
	{

		if ($data['operacion'] == "S") {
			$builder = $this->db->table('proceso_modulo a');
			$builder->select('id_kardex, id_modulo');
			$builder->where('id_modulo', $data["id"]);
			$builder->where('modulo', "Compra");
			$query = $builder->get()->getRow();
			$id_kardex = $query->id_kardex ?? "";;
			$compras = $builder->get()->getResultArray();

			foreach ($compras as $detalle) {
				$db = $this->db->table('compra');
				$db->where('id_compra',  $detalle['id_modulo']);
				$db->update(['estado' => '0']);
			}

			$builder = $this->db->table('proceso_modulo a');
			$builder->select('id_kardex, id_modulo');
			$builder->where('id_modulo', $data["id"]);
			$builder->where('modulo', "Secado");
			$query = $builder->get()->getRow();
			$id_kardex = $query->id_kardex ?? "";;
			$compras = $builder->get()->getResultArray();

			foreach ($compras as $detalle) {
				$db = $this->db->table('secado');
				$db->where('id_secado',  $detalle['id_modulo']);
				$db->update(['estado' => '0']);
			}
		} else {
			$builder = $this->db->table('proceso_retorno a');
			$builder->select('id_kardex, id_proceso');
			$builder->where('id_proceso', $data["id"]);
			$query = $builder->get()->getRow();
			$compras = $builder->get()->getResultArray();
			$id_kardex = $query->id_kardex ?? "";;

			foreach ($compras as $detalle) {
				$db = $this->db->table('proceso');
				$db->where('id_proceso',  $detalle['id_proceso']);
				$db->update(['estado' => '0']);
			}
		}


		$builder = $this->db->table('proceso_modulo a');
		$builder->select('id_kardex');
		$builder->where('id_proceso', $data["id"]);
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
		$datos = array('id_proceso' => $data["id"]);
		$query = $this->db->table('kardex_detalle')->delete($datos_kardex);
		$query = $this->db->table('kardex')->delete($datos_kardex);
		$query = $this->db->table('proceso_modulo')->delete($datos);
		$query = $this->db->table('proceso_detalle')->delete($datos);
		$query = $this->db->table('proceso_retorno')->delete(['id_retorno' => $data["id"]]);

		$model_almacen = new AlmacenModel();
		$model_almacen->restaurar_stock($id_kardex, $productos);

		$query = $this->db->table('proceso')->delete($datos);

		return $query;
	}
	public function proceso_retorno($id, $id_proceso, $id_kardex)
	{
		$datos = ['id_retorno' => $id, 'id_proceso' => $id_proceso, 'id_kardex' => $id_kardex];
		$builder = $this->db->table('proceso_retorno');
		$builder->insert($datos);
		$id = $this->db->insertID();
	}
	public function guardar_retorno($idModulo, $datos, $detalle)
	{
		$builder = $this->db->table('proceso');
		$builder->insert($datos);
		$id = $this->db->insertID();


		// $cantidadTotal = 0;
		// $totalGeneral = 0;
		// $detalleBatch = [];
		// foreach ($detalle as $dc) {
		// 	$detalleBatch[] = [
		// 		'id_proceso'   => $id,
		// 		'id_producto' => $dc['id_producto'],
		// 		'cantidad'    => $dc['cantidad'],
		// 		'precio'    => $dc['precio'],
		// 		'total'    => $dc['total'],
		// 		'rendimiento'    => $dc['rendimiento'],
		// 		'cascara'    => $dc['cascara'],
		// 		'humedad'    => $dc['humedad'],
		// 	];
		// 	$cantidadTotal += $dc['cantidad'];
		// 	$totalGeneral += $dc['total'];
		// }

		// // Redondear a 2 decimales
		// $cantidadTotal = number_format($cantidadTotal, 2, '.', '');
		// $totalGeneral  = number_format($totalGeneral, 2, '.', '');



		// if (!empty($detalleBatch)) {
		// 	$this->db->table('proceso_detalle')->insertBatch($detalleBatch);

		// 	$totales = array("cantidad" => $cantidadTotal, "total" => $totalGeneral);
		// 	$db = $this->db->table('proceso');
		// 	$db->where('id_proceso', $id);
		// 	$db->update($totales);
		// }

		$db = $this->db->table('proceso');
		$db->where('id_proceso',  $idModulo);
		$db->update(['estado' => '1']);
		return $id;
	}
}
