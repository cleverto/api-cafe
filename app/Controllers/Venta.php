<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AlmacenModel;
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

    private function valores($post)
    {
        $model = new FuncionesModel();


        if ($post["operacion"] == "S") {
            $id_tipo_comprobante = "94";
            $nro_comprobante = $model->get_correlativo("1", "1",  $id_tipo_comprobante);
        } else {
            $id_tipo_comprobante = "95";
            $nro_comprobante = $model->get_correlativo("1", "1",  $id_tipo_comprobante);
        }


        $id_usuario = session()->get("data")["id_usuario"];

        $datos = array(
            'id_empresa' => "1",
            'id_sucursal' => "1",
            'id_almacen' => "1",
            'id_usuario' => $id_usuario,
            'operacion' => $post["operacion"],
            'id_tipo_comprobante' => $id_tipo_comprobante,
            'nro_comprobante' =>  $nro_comprobante,
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
            'operacion' => $datos["operacion"],
            'nro_comprobante' => $datos["nro_comprobante"],
            'motivo' => "Procesar",
            'fecha' => $datos["fecha"],
        );
        return $data;
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

        list($id_kardex, $detalleCompra) = $model->guardar_kardex("Procesar", $datos_kardex, $compras);

        $model->guardar_detalle($id, $detalleCompra);

        $model->proceso_compra_secado_salida($id, $id_kardex, $compras);

        //actualizar stock
        $model_almacen = new AlmacenModel();
        $model_almacen->actualizar_stock($id_kardex);

        // *********
        $rpta = array('rpta' => '1', 'msg' => "Creado correctamente", 'id' => $id);


        return $this->response->setJSON($rpta);
    }

    public function eliminar()
    {
        $post = json_decode(file_get_contents('php://input'), true);
        $model = new VentaModel();
        $t = $model->eliminar($post);
        $rpta = array('rpta' => '1', 'msg' => "Registro eliminado correctamente");
        if ($t <= 0) {

            $rpta = array('rpta' => '1', 'msg' => "El registro no se puede eliminar");
        }
        return $this->response->setJSON($rpta);
    }

    private function valores_retorno($post)
    {
        $model = new FuncionesModel();
        $nro_comprobante = $model->get_correlativo("1", "1", "95");
        $id_usuario = session()->get("data")["id_usuario"];

        $datos = array(
            'id_empresa' => "1",
            'id_sucursal' => "1",
            'id_almacen' => "1",
            'id_usuario' => $id_usuario,
            'operacion' => "I",
            'id_tipo_comprobante' => "95",
            'nro_comprobante' =>  $nro_comprobante,
            'fecha' =>  $post["fecha"],
            'cantidad' => "0",
            'total' => "0",
            'estado' => "0",
        );

        return $datos;
    }
    public function guardar_retorno()
    {
        $post = json_decode(file_get_contents('php://input'), true);
        $idModulo = $post['id'];
        // Validar que exista información
        if (!isset($post['rowdata']) || empty($post['rowdata'])) {
            return $this->response->setJSON([
                'rpta'  => '0',
                'msg' => 'No se recibió información válida.'
            ]);
        }
        $datos = $this->valores_retorno($post);

        $compras = $post['rowdata'];
        $model = new VentaModel();


        // Insertar todos los registros de una vez
        $id = $model->guardar_retorno($idModulo, $datos, $compras);
        if ($id) {
            //$compras = $post["rowdata"];
            // Actualizar correlativo
            $model_funciones = new FuncionesModel();
            $model_funciones->actualizar_correlativo("1", "1",  $datos["id_tipo_comprobante"]);

            //Guardar en kardex
            $datos_kardex = $this->valores_kardex($datos);
            list($id_kardex, $detalleCompra) = $model->guardar_kardex("Procesar", $datos_kardex, $compras);

            $model->proceso_retorno($id, $idModulo, $id_kardex);

            $model->guardar_detalle($id, $detalleCompra);


            $model->proceso_compra_secado_ingreso($id, $id_kardex, $idModulo);

            //actualizar stock
            $model_almacen = new AlmacenModel();
            $model_almacen->actualizar_stock($id_kardex);

            return $this->response->setJSON([
                'rpta'  => '1',
                'msg' => 'Datos guardados correctamente.'
            ]);
        } else {
            return $this->response->setJSON([
                'rpta'  => '0',
                'msg' => 'No se pudo guardar la información.'
            ]);
        }
    }
}
