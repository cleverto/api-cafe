<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CompraModel;
use App\Models\CreditoModel;
use App\Models\FuncionesModel;

class Compra extends BaseController
{
    public function __construct()
    {
        helper('util_helper');
    }
    public function lista()
    {
        $model = new CompraModel();
        $data = $model->lista();

        $rpta = array('items' => $data);
        return $this->response->setJSON($rpta);
    }
    public function lista_temp()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $model = new CompraModel();
        $data = $model->lista_temp($data["id"]);

        $rpta = array('items' => $data);
        return $this->response->setJSON($rpta);
    }
    public function modulo()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new CompraModel();
        $items = $model->modulo($data->id);
        $rpta = array('rpta' => '1', 'items' => $items);
        return $this->response->setJSON($rpta);
    }

    private function valores($post)
    {
        $model = new FuncionesModel();
        $nro_comprobante = $model->get_correlativo("1", "1",  $post["id_tipo_comprobante"]);
        $id_tipo_cambio = $model->get_tipo_cambio($post["id_moneda"], $post["fecha"]);

        $son = letras("123");
        $datos = array(
            'id_empresa' => "1",
            'id_sucursal' => "1",
            'id_almacen' => "1",
            'id_usuario' => session()->get("data")["id_usuario"],
            'id_proveedor' => $post["id_proveedor"],
            'id_tipo_comprobante' => $post["id_tipo_comprobante"],
            'id_tipo_cambio' => $id_tipo_cambio,
            'referencia' => $post["referencia"],
            'total' => $post["total"],
            'son' => $son,
            'fecha' => date('Y-m-d H:i:s'),
            'nro_comprobante' =>  $nro_comprobante,
        );

        return $datos;
    }
    private function valores_credito($data)
    {

        $datos = array(
            'id_empresa' => "1",
            'id_sucursal' => "1",
            'id_proveedor' => $data["id_proveedor"],
            'id_tipo_cambio' =>  $data["id_tipo_cambio"],
            'movimiento' => '0',
            'referencia' => $data["nro_comprobante"],
            'total' => $data["total"],
            'fecha' => date('Y-m-d'),
            'observaciones' => "",
            'estado' => "0",
        );

        return $datos;
    }
    private function valores_producto($data)
    {

        $datos = array(
            'id_detalle' => $data['id_detalle'],
            'id_usuario' => $data['id_usuario'],
            'id_producto' => $data['id_producto'],
            'muestra' => $data['muestra'],
            'rendimiento' => $data['rendimiento'],
            'segunda'  => "0",
            'bola'        => "0",
            'cascara'     => "0",
            'humedad'     => $data['humedad'],
            'descarte'    => "0",
            'pasilla'     => "0",
            'negro'       => "0",
            'ripio'       => "0",
            'impureza'    => "0",
            'defectos'    => "0",
            'taza'        => "0",
            'cantidad'    => $data['cantidad'],
            'precio'      => $data['precio'],
            'total'       => $data['total'],

        );

        if ($data["id_categoria"] == "1") {
            $datos['segunda'] = $data["segunda"];
            $datos['bola'] = $data["bola"];
            $datos['cascara'] = $data["cascara"];
        }
        if ($data["id_categoria"] == "2") {
            $datos['descarte'] = $data["descarte"];
            if ($data["id_producto"] == "8") {
                $datos['impureza'] = $data["impureza"];
            }
        }
        if ($data["id_categoria"] == "3") {
            $datos['pasilla'] = $data["pasilla"];
            $datos['negro'] = $data["negro"];
            $datos['ripio'] = $data["ripio"];
            $datos['impureza'] = $data["impureza"];
            $datos['defectos'] = $data["defectos"];
            $datos['taza'] = $data["taza"];
        }

        return $datos;
    }

    private function validar_modificar($datos)
    {
        $errors = array();
        $model = new CompraModel();

        $t = $model->existe_dni_modificar($datos);
        if ($t > 0) {
            $errors[] =  "Este numero de dni ya esta registrado";
            return $errors;
        }
    }
    public function guardar()
    {
        $post = json_decode(file_get_contents('php://input'), true);

        $datos = $this->valores($post);
        $model = new CompraModel();

        if ($post["operacion"] == "0") {
            $id = $model->guardar($datos);

            // Actualizar correlativo
            $model_funciones = new FuncionesModel();
            $model_funciones->actualizar_correlativo("1", "1",  $post["id_tipo_comprobante"]);

            // registra cuenta por cobrar
            $model_credito = new CreditoModel();
            $datos_credito = $this->valores_credito($datos);
            $id_credito = $model_credito->guardar($datos_credito);
            $id_credito = $model_credito->guardar_compra($id, $id_credito);

            // elimina temp detalle
            $model->eliminar_temp($datos["id_usuario"]);


            // *********

            $rpta = array('rpta' => '1', 'msg' => "Creado correctamente", 'id' => $id, 'id_credito' => $id_credito);
        } else {

            $id = $model->modificar($post["idmodulo"], $datos);
            $rpta = array('rpta' => '1', 'msg' => "Modificado correctamente", 'id' => $id);
        }

        return $this->response->setJSON($rpta);
    }
    public function guardar_producto()
    {
        $post = json_decode(file_get_contents('php://input'), true);

        $datos = $this->valores_producto($post);
        $model = new CompraModel();

        if ($post["operacion"] == "0") {
            $id = $model->guardar_producto($datos);

            $rpta = array('rpta' => '1', 'msg' => "Creado correctamente", 'id' => $id);
        } else {
            $errors = $this->validar_modificar($post);
            $rpta = array('rpta' => '0', 'msg' => $errors);
            if (!empty($errors)) {
                return $this->response->setJSON($rpta);
            }

            $id = $model->modificar($post["idmodulo"], $datos);
            $rpta = array('rpta' => '1', 'msg' => "Modificado correctamente", 'id' => $id);
        }

        return $this->response->setJSON($rpta);
    }
    public function eliminar()
    {
        $post = json_decode(file_get_contents('php://input'));
        $model = new CompraModel();
        $t = $model->eliminar($post);
        $rpta = array('rpta' => '1', 'msg' => "Registro eliminado correctamente");
        if ($t <= 0) {
            $rpta = array('rpta' => '1', 'msg' => "El registro no se puede eliminar");
        }
        return $this->response->setJSON($rpta);
    }
    public function eliminar_producto()
    {
        $post = json_decode(file_get_contents('php://input'));
        $model = new CompraModel();
        $t = $model->eliminar_producto($post);
        $rpta = array('rpta' => '1', 'msg' => "Registro eliminado correctamente");
        if ($t <= 0) {
            $rpta = array('rpta' => '1', 'msg' => "El registro no se puede eliminar");
        }
        return $this->response->setJSON($rpta);
    }
}
