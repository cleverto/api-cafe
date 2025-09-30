<?php

namespace App\Models;

use CodeIgniter\Model;

class AlmacenModel extends Model
{

	public function lista($post)
	{
		$datos = array('id_modulo' => $post["id"]);
		$this->db->table('nota_almacen_temp')->delete($datos);

		$id_usuario = session()->get("data")["id_usuario"];
		$this->db->query("
	  INSERT INTO nota_almacen_temp (
	    id_modulo, id_empresa, id_sucursal, id_almacen, id_producto, id_usuario, cantidad, precio, total
	  )
	  SELECT 
	    ? AS id_modulo, b.id_empresa, b.id_sucursal, b.id_almacen, a.id_producto,  ? as id_usuario, a.cantidad, a.precio, total
	  FROM nota_almacen_detalle a
	  INNER JOIN nota_almacen b on b.id_nota_almacen=a.id_nota_almacen
	  WHERE a.id_nota_almacen = ?", [$post["id"], $id_usuario, $post["id"]]);



		$builder = $this->db->table('nota_almacen_temp a');
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
		$builder = $this->db->table('nota_almacen a');
		$builder->join('kardex_almacen c', 'a.id_nota_almacen = c.id_nota_almacen', 'inner');
		$builder->join('usuario b', 'a.id_usuario = b.id_usuario', 'inner');
		$builder->where('a.fecha BETWEEN "' . $post['desde'] . '" AND "' . $post['hasta'] . '"');
		$builder->orderBy('a.fecha');
		$query = $builder->get();
		$data =  $query->getResultArray();



		return $data;
	}
	public function filtro($post)
	{
		$builder = $this->db->table('kardex a');
		$builder->select('a.fecha, a.motivo, a.operacion, c.cantidad, d.producto');


		$builder->join('kardex_detalle c', 'a.id_kardex = c.id_kardex', 'inner');
		$builder->join('producto d', 'd.id_producto = c.id_producto', 'inner');
		$builder->where('c.id_producto', $post['id']);
		$builder->where('DATE(a.fecha) BETWEEN "' . $post['desde'] . '" AND "' . $post['hasta'] . '"');
		$builder->orderBy('a.fecha, a.operacion, a.motivo', 'ASC');
		

		$query = $builder->get();
		return $query->getResultArray();
	}

	// 	public function lista_detalle($id)
	// 	{
	// 		$builder = $this->db->table('compra_detalle a');
	// 		$builder->select('a.*');
	// 		$builder->select('b.producto, b.id_categoria');
	// 		$builder->join('producto b', 'a.id_producto = b.id_producto', 'inner');
	// 		$builder->where('a.id_compra', $id);

	// 		$query = $builder->get();
	// 		return  $query->getResultArray();;
	// 	}
	public function lista_temp($id, $id_usuario)
	{
		$builder = $this->db->table('nota_almacen_temp a');
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
		$builder = $this->db->table('nota_almacen a');
		$builder->where('a.id_nota_almacen', $id);

		$query = $builder->get();
		return $query->getRowArray();
	}
	// public function get_total($id)
	// {
	// 	$builder = $this->db->table('compra a');
	// 	$builder->selectSum('total');
	// 	$builder->where('id_compra', $id);
	// 	$query = $builder->get()->getRow();

	// 	return $query->total ?? 0;;
	// }
	public function get_suma_total($id, $id_usuario)
	{
		$builder = $this->db->table('nota_almacen_temp a');
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
		$builder = $this->db->table('nota_almacen');
		$builder->insert($datos);
		$id = $this->db->insertID();


		$this->db->query("
	  INSERT INTO nota_almacen_detalle (
	    id_nota_almacen, id_producto, cantidad, precio, total
	  )
	  SELECT 
	    ? AS id_nota_almacen, id_producto, cantidad, precio, total
	  FROM nota_almacen_temp
	  WHERE id_usuario = ?", [$id, $datos["id_usuario"]]);

		return $id;
	}
	public function actualizar_stock($id)
	{
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
		$productos = $this->db->query($sql, [$id])->getResultArray();

		foreach ($productos as $m) {
			// Consulta para obtener ingresos y salidas del producto actual
			$sqlSaldo = "
        SELECT 
            SUM(CASE WHEN k.operacion = 'I' THEN d.cantidad ELSE 0 END) AS ingresos,
            SUM(CASE WHEN k.operacion = 'S' THEN d.cantidad ELSE 0 END) AS salidas
        FROM kardex k
        JOIN kardex_detalle d ON k.id_kardex = d.id_kardex
        WHERE k.id_empresa = ? AND k.id_sucursal = ? AND k.id_almacen = ? AND d.id_producto = ?
    ";

			$result = $this->db->query($sqlSaldo, [
				$m['id_empresa'],
				$m['id_sucursal'],
				$m['id_almacen'],
				$m['id_producto']
			])->getRowArray();

			$saldo = $result['ingresos'] - $result['salidas'];

			// Actualizar el stock en inventario
			$this->db->query("
        UPDATE inventario
        SET stock = ?
        WHERE id_empresa = ? AND id_sucursal = ? AND id_almacen = ? AND id_producto = ?
    ", [
				$saldo,
				$m['id_empresa'],
				$m['id_sucursal'],
				$m['id_almacen'],
				$m['id_producto']
			]);
		}
	}
	public function restaurar_stock($id, $productos)
	{


		foreach ($productos as $m) {
			// Consulta para obtener ingresos y salidas del producto actual
			$sqlSaldo = "
        SELECT 
            SUM(CASE WHEN k.operacion = 'I' THEN d.cantidad ELSE 0 END) AS ingresos,
            SUM(CASE WHEN k.operacion = 'S' THEN d.cantidad ELSE 0 END) AS salidas
        FROM kardex k
        JOIN kardex_detalle d ON k.id_kardex = d.id_kardex
        WHERE k.id_empresa = ? AND k.id_sucursal = ? AND k.id_almacen = ? AND d.id_producto = ?
    ";

			$result = $this->db->query($sqlSaldo, [
				$m['id_empresa'],
				$m['id_sucursal'],
				$m['id_almacen'],
				$m['id_producto']
			])->getRowArray();

			$saldo = $result['ingresos'] - $result['salidas'];

			// Actualizar el stock en inventario
			$this->db->query("
        UPDATE inventario
        SET stock = ?
        WHERE id_empresa = ? AND id_sucursal = ? AND id_almacen = ? AND id_producto = ?
    ", [
				$saldo,
				$m['id_empresa'],
				$m['id_sucursal'],
				$m['id_almacen'],
				$m['id_producto']
			]);
		}
	}
	public function guardar_kardex($datos, $tabla_temp)
	{
		$builder = $this->db->table('kardex');
		$builder->insert($datos);
		$id = $this->db->insertID();

		$this->db->query("
	  INSERT INTO kardex_detalle (
	    id_kardex, id_producto, cantidad, precio, total
	  )
	  SELECT 
	    ? AS id, id_producto, cantidad, precio, total
	  FROM " . $tabla_temp . "
	  WHERE id_usuario = ?", [$id, $datos["id_usuario"]]);

		return $id;
	}
	public function guardar_producto($data)
	{
		$builder = $this->db->table('nota_almacen_temp');
		$builder->insert($data);
		return $this->db->insertID();
	}
	public function guardar_kardex_almacen($id, $id_kardex)
	{
		$data = array("id_nota_almacen" => $id, "id_kardex" => $id_kardex);

		$builder = $this->db->table('kardex_almacen');
		$builder->insert($data);
		$id = $this->db->insertID();

		return $id;
	}

	// 	public function existe_dni($data)
	// 	{
	// 		$builder = $this->db->table('compra');
	// 		$builder->where('dni', $data["dni"]);
	// 		return $builder->countAllResults();
	// 	}
	// 	public function existe_dni_modificar($data)
	// 	{
	// 		$builder = $this->db->table('compra');
	// 		$builder->where('dni', $data["dni"]);
	// 		$builder->where('id_compra !=', $data["idmodulo"]);
	// 		return $builder->countAllResults();
	// 	}
	public function modificar($id, $datos)
	{
		$db = $this->db->table('nota_almacen');
		$db->where('id_nota_almacen', $id);
		$db->update($datos);
		$t = $this->db->affectedRows();

		$datos_compra = array('id_nota_almacen' => $id);
		$this->db->table('nota_almacen_detalle')->delete($datos_compra);

		$this->db->query("
	  INSERT INTO nota_almacen_detalle (
	    id_nota_almacen, id_producto, cantidad, precio, total
	  )
	  SELECT 
	    ? AS id_, id_producto, cantidad, precio, total
	  FROM nota_almacen_temp
	  WHERE id_modulo = ? AND id_usuario = ?", [$id, $id, $datos["id_usuario"]]);

		return $t;
	}
	public function modificar_kardex($id, $idmodulo, $datos, $tabla_temp)
	{
		$db = $this->db->table('kardex');
		$db->where('id_kardex', $id);
		$db->update($datos);

		$datos_kardex = array('id_kardex' => $id);
		$this->db->table('kardex_detalle')->delete($datos_kardex);

		$this->db->query("
	  INSERT INTO kardex_detalle (
	    id_kardex, id_producto, cantidad, precio, total
	  )
	  SELECT 
	    ? AS id_, id_producto, cantidad, precio, total
	  FROM " . $tabla_temp . "
	  WHERE id_modulo = ? AND id_usuario = ?", [$id, $idmodulo, $datos["id_usuario"]]);
	}
	public function eliminar($data)
	{
		$builder = $this->db->table('kardex_almacen a');
		$builder->select('id_kardex');
		$builder->where('id_nota_almacen', $data["id"]);
		$query = $builder->get()->getRow();
		$id_kardex = $query->id_kardex ?? "";;

		$datos_kardex = array('id_kardex' => $id_kardex);
		$query = $this->db->table('kardex_almacen')->delete($datos_kardex);
		$query = $this->db->table('kardex_detalle')->delete($datos_kardex);
		$query = $this->db->table('kardex')->delete($datos_kardex);

		$datos = array('id_nota_almacen' => $data["id"]);
		$query = $this->db->table('nota_almacen')->delete($datos);

		return $query;
	}
	public function eliminar_temp($id)
	{
		$datos = array('id_usuario' => $id);
		$query = $this->db->table('nota_almacen_temp')->delete($datos);

		return $query;
	}
	public function eliminar_kardex($id)
	{
		$datos = array('id_kardex' => $id);
		$this->db->table('kardex_detalle')->delete($datos);
		$this->db->table('kardex')->delete($datos);
	}
	// 	public function eliminar_temp_by_id_modulo($id)
	// 	{
	// 		$datos = array('id_modulo' => $id);
	// 		$query = $this->db->table('almacen_temp')->delete($datos);

	// 		return $query;
	// 	}
	public function eliminar_producto($post)
	{
		$datos = array('id_detalle' => $post["id"]);
		$query = $this->db->table('nota_almacen_temp')->delete($datos);

		return $query;
	}
}
