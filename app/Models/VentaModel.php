<?php

namespace App\Models;

use CodeIgniter\Model;

class VentaModel extends Model
{

	public function lista($post)
	{
		// --- CONSULTA 1: COMPRA ---
		$builder1 = $this->db->table('compra a');
		$builder1->select('
    a.id_compra, 
    "Compra" AS modulo,
    a.fecha, 
    "I" AS operacion,
    a.nro_comprobante,
    a.referencia, 
    p.proveedor, 
    a.total, 
    SUM(d.cantidad) AS cantidad
');
		$builder1->join('compra_detalle d', 'a.id_compra = d.id_compra', 'inner');
		$builder1->join('proveedor p', 'p.id_proveedor = a.id_proveedor', 'inner');
		$builder1->where('a.estado', '0');
		$builder1->groupBy('a.id_compra, a.fecha, a.referencia, p.proveedor, a.total, a.nro_comprobante');
		$sql1 = $builder1->getCompiledSelect();


		// --- CONSULTA 2: SECADO ---
		$builder2 = $this->db->table('secado a');
		$builder2->distinct();
		$builder2->select('
    a.id_secado AS id_compra, 
    "Secado" AS modulo, 
    a.fecha, 
    a.operacion, 
    a.nro_comprobante, 
    GROUP_CONCAT(DISTINCT c.referencia ORDER BY c.referencia SEPARATOR ", ") AS referencia, 
    GROUP_CONCAT(DISTINCT p.proveedor ORDER BY p.proveedor SEPARATOR ", ") AS proveedor, 
    a.total, 
    SUM(d.cantidad) AS cantidad
');
		$builder2->join('secado_detalle d', 'a.id_secado = d.id_secado', 'inner');
		$builder2->join('secado_compra e', 'e.id_secado = a.id_secado', 'inner');
		$builder2->join('compra c', 'c.id_compra = e.id_compra', 'inner');
		$builder2->join('proveedor p', 'p.id_proveedor = c.id_proveedor', 'inner');
		$builder2->where('a.estado', '0');
		$builder2->groupBy('a.id_secado, a.fecha, a.nro_comprobante, a.total, a.operacion');
		$sql2 = $builder2->getCompiledSelect();


		// --- CONSULTA 3: PROCESO ---
		$builder3 = $this->db->table('proceso a');
		$builder3->select('
    a.id_proceso AS id_compra,
    "Proceso" AS modulo,
    a.fecha,
    a.operacion,
    a.nro_comprobante,
    GROUP_CONCAT(DISTINCT c.referencia ORDER BY c.referencia SEPARATOR ", ") AS referencia,
    GROUP_CONCAT(DISTINCT p.proveedor ORDER BY p.proveedor SEPARATOR ", ") AS proveedor,
    a.total,
    SUM(d.cantidad) AS cantidad
');
		$builder3->join('proceso_modulo pm', 'pm.id_proceso = a.id_proceso', 'inner');

		// 🔹 si el proceso está relacionado a Compra
		$builder3->join('compra c', 'c.id_compra = pm.id_modulo AND pm.modulo = "Compra"', 'left');

		// 🔹 si el proceso está relacionado a Secado
		$builder3->join('secado s', 's.id_secado = pm.id_modulo AND pm.modulo = "Secado"', 'left');
		$builder3->join('secado_compra sc', 'sc.id_secado = s.id_secado', 'left');
		$builder3->join('compra c2', 'c2.id_compra = sc.id_compra', 'left');

		// 🔹 tomar proveedor tanto de compra directa como de la compra asociada al secado
		$builder3->join('proveedor p', 'p.id_proveedor = COALESCE(c.id_proveedor, c2.id_proveedor)', 'left');

		$builder3->join('proceso_detalle d', 'd.id_proceso = a.id_proceso', 'inner');
		$builder3->where('a.estado', '0');
		$builder3->where('a.operacion', 'I');
		$builder3->groupBy('a.id_proceso, a.fecha, a.nro_comprobante, a.total, a.operacion');
		$sql3 = $builder3->getCompiledSelect();
		// --- UNIÓN FINAL ---
		$sql = "($sql1) UNION ALL ($sql2) UNION ALL ($sql3)";


		// Ejecutamos la consulta final
		$query = $this->db->query($sql);

		// Obtenemos el resultado
		return $query->getResult();
	}
	public function lista_detalle_guardar($data)
	{
		$resultados = [];

		foreach ($data as $row) {
			$modulo = $row['modulo'] ?? '';
			$id = null;
			$tabla = '';
			$tabla_detalle = '';
			$campo_id = '';

			// Identificar módulo y sus tablas
			switch ($modulo) {
				case 'Compra':
					$tabla = 'compra';
					$tabla_detalle = 'compra_detalle';
					$campo_id = 'id_compra';
					$id = $row['id_compra'] ?? null;
					break;

				case 'Secado':
					$tabla = 'secado';
					$tabla_detalle = 'secado_detalle';
					$campo_id = 'id_secado';
					$id = $row['id_compra'] ?? null;
					break;

				case 'Proceso':
					$tabla = 'proceso';
					$tabla_detalle = 'proceso_detalle';
					$campo_id = 'id_proceso';
					$id = $row['id_compra'] ?? null;
					break;

				default:
					continue 2; // módulo no reconocido
			}

			if ($id === null) continue;

			// Consulta con Query Builder
			$builder = $this->db->table("$tabla a");
			$builder->select("
			b.id_detalle,
			a.$campo_id as id_compra,
			'$modulo' as modulo,
            b.id_producto,
            p.producto,
            b.cantidad,
            b.precio as pa,
			'0' as precio,
            b.total
        ");
			$builder->join("$tabla_detalle b", "a.$campo_id = b.$campo_id");
			$builder->join("producto p", "b.id_producto = p.id_producto", "inner");
			$builder->where("a.$campo_id", $id);

			$query = $builder->get();
			$detalle = $query->getResultArray();

			// Agregar todos los detalles directamente al array plano
			foreach ($detalle as $d) {
				$resultados[] = $d;
			}
		}

		return $resultados;
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
		$builder = $this->db->table('venta a');
		$builder->select('a.*, cr.id_credito');

		// nro_comprobante según el módulo (Compra, Secado, Proceso)
		$builder->select("
        GROUP_CONCAT(
            CASE 
                WHEN d.modulo = 'Compra' THEN c.nro_comprobante
                WHEN d.modulo = 'Secado' THEN s.nro_comprobante
                WHEN d.modulo = 'Proceso' THEN pr.nro_comprobante
            END SEPARATOR ', '
        ) AS referencia
    ");

		// proveedor según el módulo
		// Compra → proveedor directo
		// Secado → proveedor de la compra asociada vía secado_compra
		// Proceso → no tiene proveedor
		$builder->select("
        GROUP_CONCAT(
            CASE 
                WHEN d.modulo = 'Compra' THEN p.proveedor
                WHEN d.modulo = 'Secado' THEN ps.proveedor
                WHEN d.modulo = 'Proceso' THEN ''
            END SEPARATOR ', '
        ) AS proveedores
    ");

		// Relaciones principales
		$builder->join('venta_modulo d', 'a.id_venta = d.id_venta', 'inner');

		// Compra
		$builder->join('compra c', 'c.id_compra = d.id_modulo AND d.modulo="Compra"', 'left');
		$builder->join('proveedor p', 'p.id_proveedor = c.id_proveedor', 'left');

		// Secado
		$builder->join('secado s', 's.id_secado = d.id_modulo AND d.modulo="Secado"', 'left');
		$builder->join('secado_compra sc', 'sc.id_secado = s.id_secado', 'left');
		$builder->join('compra cs', 'cs.id_compra = sc.id_compra', 'left');
		$builder->join('proveedor ps', 'ps.id_proveedor = cs.id_proveedor', 'left');

		// Proceso (sin proveedor)
		$builder->join('proceso pr', 'pr.id_proceso = d.id_modulo AND d.modulo="Proceso"', 'left');
		$builder->join('proceso_detalle prd', 'prd.id_proceso = pr.id_proceso', 'left'); // opcional, si necesitas acceder a productos
		//credito
		$builder->join('credito_venta cr', 'cr.id_venta = a.id_venta', 'inner');


		// Filtro de fechas
		$builder->where("a.fecha BETWEEN '{$post['desde']}' AND '{$post['hasta']}'");

		$builder->groupBy('a.id_venta');
		$builder->orderBy('a.fecha');

		$query = $builder->get();
		return $query->getResultArray();
	}
	public function filtro_trazabilidad($post)
	{
		$producto = $post['producto'] ?? 'TODOS';
		$desde = $post['desde'] ?? '1900-01-01';
		$hasta = $post['hasta'] ?? date('Y-m-d');
		$producto = $post['producto'] ?? 'TODOS';
		$filtroCompra  = ($producto !== 'TODOS') ? "AND cd.id_producto = '{$producto}'" : '';
		$filtroSecado  = ($producto !== 'TODOS') ? "AND sd.id_producto = '{$producto}'" : '';
		$filtroProceso = ($producto !== 'TODOS') ? "AND pd.id_producto = '{$producto}'" : '';
		$filtroVenta   = ($producto !== 'TODOS') ? "AND vd.id_producto = '{$producto}'" : '';


		// 🟢 Compras
		$sqlCompra = "
        SELECT 
            c.id_tipo_comprobante,
            c.nro_comprobante AS comprobante,
            c.fecha,
            'Compra' AS etapa,
            NULL AS referencia,
            p.producto,
            cd.cantidad,
            cd.precio,
            cd.total,
            cd.rendimiento,
            cd.cascara,
            cd.humedad
        FROM compra_detalle cd
        INNER JOIN compra c ON c.id_compra = cd.id_compra
        INNER JOIN producto p ON p.id_producto = cd.id_producto
        WHERE DATE(c.fecha) BETWEEN '{$desde}' AND '{$hasta}'
        {$filtroCompra}
        ORDER BY c.id_tipo_comprobante, c.fecha, c.nro_comprobante
    ";
		$compras = $this->db->query($sqlCompra)->getResultArray();

		// 🟡 Secados
		$sqlSecado = "
SELECT
    s.id_tipo_comprobante,
    s.nro_comprobante AS comprobante,
    -- Todos los nro_comprobante de compra asociados en un solo campo
    (SELECT GROUP_CONCAT(c.nro_comprobante SEPARATOR ', ')
     FROM secado_compra sc
     INNER JOIN compra c ON c.id_compra = sc.id_compra
     WHERE sc.id_secado = s.id_secado
    ) AS referencia,
    s.fecha,
    CASE WHEN s.operacion='S' THEN 'Secado salida' ELSE 'Secado ingreso' END AS etapa,
    p.producto,
    CASE WHEN s.operacion='S' THEN -sd.cantidad ELSE sd.cantidad END AS cantidad,
    sd.precio,
    sd.total,
    sd.rendimiento,
    sd.cascara,
    sd.humedad
FROM secado_detalle sd
INNER JOIN secado s ON s.id_secado = sd.id_secado
INNER JOIN producto p ON p.id_producto = sd.id_producto
WHERE DATE(s.fecha) BETWEEN '{$desde}' AND '{$hasta}'
{$filtroSecado}
GROUP BY s.id_secado, sd.id_producto
ORDER BY s.id_tipo_comprobante, s.fecha, s.nro_comprobante;

    ";
		$secados = $this->db->query($sqlSecado)->getResultArray();

		// 🔵 Procesos
		$sqlProceso = "
SELECT 
    pr.id_tipo_comprobante,
    pr.nro_comprobante AS comprobante,
    -- Traemos todas las referencias de secado o compra relacionadas al proceso, evitando duplicados
    (
        SELECT GROUP_CONCAT(DISTINCT
            CASE 
                WHEN s.nro_comprobante IS NOT NULL THEN s.nro_comprobante
                WHEN c.nro_comprobante IS NOT NULL THEN c.nro_comprobante
            END
            ORDER BY
                CASE 
                    WHEN s.nro_comprobante IS NOT NULL THEN s.nro_comprobante
                    WHEN c.nro_comprobante IS NOT NULL THEN c.nro_comprobante
                END
            SEPARATOR ', '
        )
        FROM proceso_detalle pd2
        LEFT JOIN secado_detalle sd2 ON sd2.id_producto = pd2.id_producto
        LEFT JOIN secado s ON s.id_secado = sd2.id_secado
        LEFT JOIN compra_detalle cd2 ON cd2.id_producto = pd2.id_producto
        LEFT JOIN compra c ON c.id_compra = cd2.id_compra
        WHERE pd2.id_proceso = pr.id_proceso
    ) AS referencia,
    pr.fecha,
    CASE WHEN pr.operacion='S' THEN 'Proceso salida' ELSE 'Proceso ingreso' END AS etapa,
    p.producto,
    SUM(CASE WHEN pr.operacion='S' THEN -pd.cantidad ELSE pd.cantidad END) AS cantidad,
    pd.precio,
    pd.total,
    pd.rendimiento,
    pd.cascara,
    pd.humedad
FROM proceso_detalle pd
INNER JOIN proceso pr ON pr.id_proceso = pd.id_proceso
INNER JOIN producto p ON p.id_producto = pd.id_producto
WHERE DATE(pr.fecha) BETWEEN '{$desde}' AND '{$hasta}'
{$filtroProceso}
GROUP BY pr.id_proceso, pd.id_producto
ORDER BY pr.id_tipo_comprobante, pr.fecha, pr.nro_comprobante;


    ";
		$procesos = $this->db->query($sqlProceso)->getResultArray();

		// 🔴 Ventas
		$sqlVenta = "
SELECT 
    v.id_tipo_comprobante,
    v.nro_comprobante AS comprobante,
    -- Todas las referencias relacionadas a la venta (compra, secado, proceso) concatenadas
    (
        SELECT GROUP_CONCAT(
            CASE 
                WHEN vm2.modulo = 'Compra' THEN c2.nro_comprobante
                WHEN vm2.modulo = 'Secado' THEN s2.nro_comprobante
                WHEN vm2.modulo = 'Proceso' THEN pr2.nro_comprobante
            END SEPARATOR ', '
        )
        FROM venta_modulo vm2
        LEFT JOIN compra c2 ON c2.id_compra = vm2.id_modulo AND vm2.modulo='Compra'
        LEFT JOIN secado s2 ON s2.id_secado = vm2.id_modulo AND vm2.modulo='Secado'
        LEFT JOIN proceso pr2 ON pr2.id_proceso = vm2.id_modulo AND vm2.modulo='Proceso'
        WHERE vm2.id_venta = v.id_venta
    ) AS referencia,
    v.fecha,
    'Venta' AS etapa,
    p.producto,
    -vd.cantidad AS cantidad,
    vd.precio,
    vd.total,
    NULL AS rendimiento,
    NULL AS cascara,
    NULL AS humedad
FROM venta_detalle vd
INNER JOIN venta v ON v.id_venta = vd.id_venta
INNER JOIN venta_modulo vm ON vm.id_venta = v.id_venta
INNER JOIN producto p ON p.id_producto = vd.id_producto
WHERE DATE(v.fecha) BETWEEN '{$desde}' AND '{$hasta}'
{$filtroVenta}
GROUP BY v.id_venta, vd.id_producto
ORDER BY v.id_tipo_comprobante, v.fecha, v.nro_comprobante;

    ";
		$ventas = $this->db->query($sqlVenta)->getResultArray();

		return [
			'compra'  => $compras,
			'secado'  => $secados,
			'proceso' => $procesos,
			'venta'   => $ventas
		];
	}

	public function venta_relacionados_salida($id, $id_kardex, $compras)
	{

		$batch = [];
		$ids_modulo_agregados = [];
		foreach ($compras as $row) {
			// Evitar duplicado de id_modulo
			if (!in_array($row['id_compra'], $ids_modulo_agregados)) {
				$batch[] = [
					'id_venta' => $id,
					'id_modulo' => $row['id_compra'],
					'id_kardex' => $id_kardex,
					'modulo' => $row['modulo'],
				];

				// Marcar este id_compra como agregado
				$ids_modulo_agregados[] = $row['id_compra'];
			}
		}

		$builder = $this->db->table('venta_modulo');
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
		$builder = $this->db->table('venta');
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
			'operacion'           => "S",
			'motivo'              => $motivo,
			'fecha'               => $datos["fecha"],
			'nro_comprobante'     => $datos["nro_comprobante"],
		];

		$this->db->table('kardex')->insert($datos_kardex);
		$id_kardex = $this->db->insertID();

		// 3. A partir de las compras activas, insertar en kardex_detalle


		$ids_modulo_agregados = [];
		foreach ($compras as $row) {
			// Evitar duplicado de id_modulo
			if (!in_array($row['id_compra'], $ids_modulo_agregados)) {
				if ($row["modulo"] = "compra") {
					$db = $this->db->table('compra');
					$db->where('id_compra',  $row['id_compra']);
					$db->update(['estado' => '1']);
				}
				if ($row["modulo"] = "secado") {
					$db = $this->db->table('secado');
					$db->where('id_secado',  $row['id_compra']);
					$db->update(['estado' => '1']);
				}
				if ($row["modulo"] = "proceso") {
					$db = $this->db->table('proceso');
					$db->where('id_proceso',  $row['id_compra']);
					$db->update(['estado' => '1']);
				}
				// Marcar este id_compra como agregado
				$ids_modulo_agregados[] = $row['id_compra'];
			}
		}


		$kardexBatch = [];
		$compraBatch = [];

		foreach ($compras as $dc) {
			$kardexBatch[] = [
				'id_kardex'   => $id_kardex,
				'id_producto' => $dc['id_producto'],
				'cantidad'    => $dc['cantidad'],
				'precio'    =>  $dc['precio'],
				'total'    =>  $dc['total'],
			];
		}
		foreach ($compras as $dc) {
			$compraBatch[] = [
				'id_producto' => $dc['id_producto'],
				'cantidad'    => $dc['cantidad'],
				'precio'    =>  $dc['precio'],
				'total'    =>  $dc['total'],
			];
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
				'id_venta'   => $id,
				'id_producto' => $dc['id_producto'],
				'cantidad'    => $dc['cantidad'],
				'precio'    => $dc['precio'],
				'total'    => $dc['total'],
				// 'rendimiento'    => $dc['rendimiento'],
				// 'cascara'    => $dc['cascara'],
				// 'humedad'    => $dc['humedad'],
			];
		}

		if (!empty($detalleBatch)) {
			$this->db->table('venta_detalle')->insertBatch($detalleBatch);
		}
	}


	public function eliminar($data)
	{
		$builder = $this->db->table('venta_modulo a');
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

		$builder = $this->db->table('venta_modulo a');
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

		$builder = $this->db->table('venta_modulo a');
		$builder->select('id_kardex, id_modulo');
		$builder->where('id_modulo', $data["id"]);
		$builder->where('modulo', "proceso");
		$query = $builder->get()->getRow();
		$id_kardex = $query->id_kardex ?? "";;
		$compras = $builder->get()->getResultArray();

		foreach ($compras as $detalle) {
			$db = $this->db->table('proceso');
			$db->where('id_proceso',  $detalle['id_modulo']);
			$db->update(['estado' => '0']);
		}

		$builder = $this->db->table('venta_modulo a');
		$builder->select('id_kardex');
		$builder->where('id_venta', $data["id"]);
		$query = $builder->get()->getRow();
		$id_kardex = $query->id_kardex ?? "";;


		$sql = "
    SELECT 
        k.id_empresa,
        k.id_sucursal,
        k.id_almacen,
        d.id_producto,
        d.cantidad
    FROM kardex k
    JOIN kardex_detalle d ON k.id_kardex = d.id_kardex
    WHERE k.id_kardex = ?
";
		$productos = $this->db->query($sql, [$id_kardex])->getResultArray();


		$datos_kardex = array('id_kardex' => $id_kardex);
		$datos = array('id_venta' => $data["id"]);
		$query = $this->db->table('kardex_detalle')->delete($datos_kardex);
		$query = $this->db->table('kardex')->delete($datos_kardex);
		$query = $this->db->table('venta_modulo')->delete($datos);
		$query = $this->db->table('venta_detalle')->delete($datos);

		$model_almacen = new AlmacenModel();
		$model_almacen->restaurar_stock($id_kardex, $productos);

		$query = $this->db->table('venta')->delete($datos);

		return $query;
	}
	public function guardar_credito_venta($id, $id_credito)
	{
		$data = array("id_venta" => $id, "id_credito" => $id_credito);

		$builder = $this->db->table('credito_venta');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}




	public function proceso_retorno($id, $id_proceso, $id_kardex)
	{
		// $datos = ['id_retorno' => $id, 'id_proceso' => $id_proceso, 'id_kardex' => $id_kardex];
		// $builder = $this->db->table('proceso_retorno');
		// $builder->insert($datos);
		// $id = $this->db->insertID();
	}
	public function guardar_retorno($idModulo, $datos, $detalle)
	{
		// $builder = $this->db->table('proceso');
		// $builder->insert($datos);
		// $id = $this->db->insertID();


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

		// $db = $this->db->table('proceso');
		// $db->where('id_proceso',  $idModulo);
		// $db->update(['estado' => '1']);
		// return $id;
	}
}
