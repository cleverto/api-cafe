<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AlmacenModel;
use App\Models\CreditoModel;
use App\Models\VentaModel;
use App\Models\FuncionesModel;

class Venta extends BaseController
{
    public function __construct()
    {
        helper('util_helper');
    }
    public function lista()
    {
        $post = json_decode(file_get_contents('php://input'), true);
        $model = new VentaModel();
        $data = $model->lista($post);

        $rpta = array('items' => $data);
        return $this->response->setJSON($rpta);
    }
    public function lista_detalle_guardar()
    {
        $post = json_decode(file_get_contents('php://input'), true);
        $model = new VentaModel();
        $data = $model->lista_detalle_guardar($post["rowData"]);

        $rpta = array('items' => $data);
        return $this->response->setJSON($rpta);
    }
    public function lista_detalle()
    {
        $post = json_decode(file_get_contents('php://input'), true);
        $model = new VentaModel();
        $data = $model->lista_detalle($post["id"]);

        $rpta = array('items' => $data);
        return $this->response->setJSON($rpta);
    }
    public function lista_detalle_salida()
    {
        $post = json_decode(file_get_contents('php://input'), true);
        $model = new VentaModel();
        $data = $model->lista_detalle_salida($post);

        $rpta = array('items' => $data);
        return $this->response->setJSON($rpta);
    }
    public function buscar()
    {
        $post = json_decode(file_get_contents('php://input'), true);
        $model = new VentaModel();
        $items = $model->buscar($post);

        $rpta = array('items' => $items);
        return $this->response->setJSON($rpta);
    }
    public function get_credito()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new VentaModel();
        $items = $model->get_credito($data->id);
        $rpta = array('rpta' => '1', 'items' => $items);
        return $this->response->setJSON($rpta);
    }
    private function valores($post)
    {
        $model = new FuncionesModel();

        $id_tipo_comprobante = "99";
        $nro_comprobante = $model->get_correlativo("1", "1",  $id_tipo_comprobante);


        $id_usuario = session()->get("data")["id_usuario"];
        $datos = array(
            'id_empresa' => "1",
            'id_sucursal' => "1",
            'id_almacen' => "1",
            'id_usuario' => $id_usuario,
            'id_usuario' => $id_usuario,
            // 'operacion' => $post["operacion"],
            'id_tipo_comprobante' => $id_tipo_comprobante,
            'id_proveedor' => $post["id_proveedor"],
            'id_moneda' => "PEN",
            'nro_comprobante' =>  $nro_comprobante,
            'referencia' =>  $post["referencia"],
            'fecha' => date('Y-m-d H:i:s'),
            'cantidad' => $post["qq"],
            'total' => $post["total"],
            'estado' => "0",
        );

        return [$datos];
    }

    private function valores_kardex($datos)
    {
        $data = array(
            'id_empresa'          => $datos["id_empresa"],
            'id_sucursal'         => $datos["id_sucursal"],
            'id_almacen'          => $datos["id_almacen"],
            'id_usuario'          => $datos["id_usuario"],
            'id_tipo_comprobante' => $datos["id_tipo_comprobante"],
            'operacion' => "S",
            'nro_comprobante' => $datos["nro_comprobante"],
            'motivo' => "Procesar",
            'fecha' => $datos["fecha"],
        );
        return $data;
    }
    private function valores_credito($data)
    {
        $datos = array(
            'id_empresa' => "1",
            'id_sucursal' => "1",
            'id_proveedor' => $data["id_proveedor"],
            'id_moneda' =>  "PEN",
            'movimiento' => '1',
            'referencia' => $data["nro_comprobante"],
            'total' => $data["total"],
            'saldo' => $data["total"],
            'fecha' => date('Y-m-d'),
            'observaciones' => "",
            'estado' => "0",
        );

        return $datos;
    }

    public function guardar()
    {
        $post = json_decode(file_get_contents('php://input'), true);

        $datos = $this->valores($post["form"]);
        $datos = $datos[0];

        $compras = $post["compras"];

        $model = new VentaModel();

        $id = $model->guardar($datos, $compras);

        // Actualizar correlativo
        $model_funciones = new FuncionesModel();
        $model_funciones->actualizar_correlativo("1", "1",  $datos["id_tipo_comprobante"]);

        //Guardar en kardex
        $datos_kardex = $this->valores_kardex($datos);

        list($id_kardex, $detalleCompra) = $model->guardar_kardex("Venta", $datos_kardex, $compras);

        $model->guardar_detalle($id, $detalleCompra);

        $model->venta_relacionados_salida($id, $id_kardex, $compras);


        // registra cuenta por cobrar
        $model_credito = new CreditoModel();
        $datos_credito = $this->valores_credito($datos);
        $id_credito = $model_credito->guardar($datos_credito);

        // registra relación de compra y credito
        $model->guardar_credito_venta($id, $id_credito);

        //actualizar stock
        $model_almacen = new AlmacenModel();
        $model_almacen->actualizar_stock($id_kardex);

        // *********

        $rpta = array('rpta' => '1', 'msg' => "Creado correctamente", 'id' => $id, 'id_credito' => $id_credito);


        return $this->response->setJSON($rpta);
    }

    public function eliminar()
    {
        $post = json_decode(file_get_contents('php://input'), true);
        $model = new VentaModel();


        $row = $model->get_credito_caja($post);
        if ($row !== null) {
            $rpta = array('rpta' => '0', 'msg' => "Para eliminar la venta, elimine primero los pagos");
            return $this->response->setJSON($rpta);
        }


        $t = $model->eliminar($post);
        $rpta = array('rpta' => '1', 'msg' => "Registro eliminado correctamente");
        if ($t <= 0) {

            $rpta = array('rpta' => '1', 'msg' => "El registro no se puede eliminar");
        }
        return $this->response->setJSON($rpta);
    }
}
