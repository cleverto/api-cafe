<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AlmacenModel;
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
        $post = json_decode(file_get_contents('php://input'), true);
        $model = new CompraModel();
        $data = $model->lista($post);

        $total = $model->get_total($post["id"]);

        $rpta = array('items' => $data, 'total' => $total);
        return $this->response->setJSON($rpta);
    }
    public function lista_sin_secar()
    {
        $model = new CompraModel();
        $data = $model->lista_sin_secar();

        $rpta = array('items' => $data);
        return $this->response->setJSON($rpta);
    }
    public function buscar()
    {
        $post = json_decode(file_get_contents('php://input'), true);
        $model = new CompraModel();
        $items = $model->buscar($post);

        $rpta = array('items' => $items);
        return $this->response->setJSON($rpta);
    }
    public function lista_detalle()
    {
        $post = json_decode(file_get_contents('php://input'), true);
        $model = new CompraModel();
        $data = $model->lista_detalle($post["id"]);

        $rpta = array('items' => $data);
        return $this->response->setJSON($rpta);
    }
    public function lista_temp()
    {
        $post = json_decode(file_get_contents('php://input'), true);
        $id_usuario = session()->get("data")["id_usuario"];
        $model = new CompraModel();
        $data = $model->lista_temp($post["id"], $id_usuario);

        // suma total
        $total = $model->get_suma_total($post["id"], $id_usuario);

        $rpta = array('items' => $data, 'total' => $total);
        return $this->response->setJSON($rpta);
    }
    public function modulo()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $model = new CompraModel();
        $items = $model->modulo($data["id"]);

        $id_usuario = session()->get("data")["id_usuario"];
        $total = $model->get_suma_total($data["id"], $id_usuario);


        $rpta = array('rpta' => '1', 'items' => $items, 'total' => $total);
        return $this->response->setJSON($rpta);
    }

    private function valores($post)
    {
        $model = new FuncionesModel();
        $nro_comprobante = $model->get_correlativo("1", "1",  $post["id_tipo_comprobante"]);

        $id_usuario = session()->get("data")["id_usuario"];

        //$son = letras("123");
        $son = "";
        $datos = array(
            'id_empresa' => "1",
            'id_sucursal' => "1",
            'id_almacen' => "1",
            'id_usuario' => $id_usuario,
            'id_proveedor' => $post["id_proveedor"],
            'id_tipo_comprobante' => $post["id_tipo_comprobante"],
            'id_moneda' => $post["id_moneda"],
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
            'id_moneda' =>  $data["id_moneda"],
            'movimiento' => '0',
            'referencia' => $data["nro_comprobante"],
            'total' => $data["total"],
            'saldo' => $data["total"],
            'fecha' => date('Y-m-d'),
            'observaciones' => "",
            'estado' => "0",
        );

        return $datos;
    }
    private function valores_producto($data)
    {

        $id_usuario = session()->get("data")["id_usuario"];
        $datos = array(
            'id_modulo' => $data['idmodulo'],
            'id_empresa' => "1",
            'id_sucursal' => "1",
            'id_almacen' => "1",
            'id_usuario' => $id_usuario,
            'id_producto' => $data['id_producto'],
            'muestra' => $data['muestra'],
            'sacos' => $data['sacos'],
            'tara' => $data['tara'],
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
            'kg_bruto'    => $data['kg_bruto'],
            'kg_neto'    => $data['kg_neto'],
            'qq_bruto'    => $data['qq_bruto'],
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
    private function valores_kardex($datos)
    {
        $data = array(
            'id_empresa' => "1",
            'id_sucursal' => "1",
            'id_almacen' => "1",
            'id_usuario' => $datos["id_usuario"],
            'id_tipo_comprobante' => $datos["id_tipo_comprobante"],
            'operacion' => "I",
            'nro_comprobante' => $datos["referencia"],
            'motivo' => "Compra",
            'fecha' => $datos["fecha"],
        );
        return $data;
    }

    // private function validar_modificar($datos)
    // {
    //     $errors = array();
    //     $model = new CompraModel();

    //     $t = $model->existe_dni_modificar($datos);
    //     if ($t > 0) {
    //         $errors[] =  "Este numero de dni ya esta registrado";
    //         return $errors;
    //     }
    // }
    private function validar($datos)
    {
        $errors = array();
        if ($datos["id_moneda"] == "USD") {
            $model = new FuncionesModel();
            $id_tipo_cambio = $model->get_tipo_cambio($datos["id_moneda"], $datos["fecha"]);

            if (empty($id_tipo_cambio) || !isset($id_tipo_cambio)) {
                $errors[] =  "No existe el tipo de cambio, tiene que registrarlo";
                return $errors;
            }
        }
    }
    public function guardar()
    {
        $post = json_decode(file_get_contents('php://input'), true);

        $datos = $this->valores($post);
        $model = new CompraModel();

        $errors = $this->validar($datos);
        $rpta = array('rpta' => '0', 'msg' => $errors);
        if (!empty($errors)) {
            return $this->response->setJSON($rpta);
        }

        if ($post["operacion"] == "0") {
            $id = $model->guardar($datos);

            // Actualizar correlativo
            $model_funciones = new FuncionesModel();
            $model_funciones->actualizar_correlativo("1", "1",  $post["id_tipo_comprobante"]);

            //Guardar en kardex
            $datos_kardex = $this->valores_kardex($datos);

            $model_almacen = new AlmacenModel();
            $id_kardex = $model_almacen->guardar_kardex($datos_kardex, "compra_temp");

            // registra relación de almacen con kardex
            $model->guardar_kardex_compra($id, $id_kardex);

            //actualizar stock
            $model_almacen->actualizar_stock($id_kardex);

            // registra cuenta por cobrar
            $model_credito = new CreditoModel();
            $datos_credito = $this->valores_credito($datos);
            $id_credito = $model_credito->guardar($datos_credito);

            // registra relación de compra y credito
            $model->guardar_credito_compra($id, $id_credito);


            // elimina temp detalle
            $model->eliminar_temp($datos["id_usuario"]);


            // *********
            $rpta = array('rpta' => '1', 'msg' => "Creado correctamente", 'id' => $id, 'id_credito' => $id_credito);
        } else {

            $t = $model->modificar($post["idmodulo"], $datos);

            //id kardex de kardex_compra
            $id_kardex = $model->get_id_kardex($post["idmodulo"]);

            //Guardar en kardex
            $datos_kardex = $this->valores_kardex($datos);

            $model_almacen = new AlmacenModel();
            $model_almacen->modificar_kardex($id_kardex, $post["idmodulo"], $datos_kardex, "compra_temp");

            //actualizar stock 
            $model_almacen = new AlmacenModel();
            $model_almacen->actualizar_stock($id_kardex);

            // recupera id del credito
            $model_credito = new CreditoModel();
            $id_credito = $model_credito->get_id_by_compra($post["idmodulo"]);

            // registra cuenta por cobrar

            $datos_credito = $this->valores_credito($datos);
            $model_credito->modificar($id_credito, $datos_credito);

            // actualiza el saldo del credito porque total puede cambiar
            $model_credito->set_Saldo(["id_credito" => $id_credito]);

            // elimina temp detalle
            //$model->eliminar_temp_by_id_modulo($post["idmodulo"]);


            $rpta = array('rpta' => '1', 'msg' => "El registro no se puede modificar");
            if ($t > 0) {

                $rpta = array('rpta' => '1', 'msg' => "Modificado correctamente", 'id_credito' => $id_credito);
            }
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

            // suma total
            $total = $model->get_suma_total("", $datos["id_usuario"]);

            $rpta = array('rpta' => '1', 'msg' => "Creado correctamente", 'id' => $id, 'total' => $total);
        } else {
            // $errors = $this->validar_modificar($post);
            // $rpta = array('rpta' => '0', 'msg' => $errors);
            // if (!empty($errors)) {
            //     return $this->response->setJSON($rpta);
            // }

            $id = $model->guardar_producto($datos);
            // suma total
            $total = $model->get_suma_total($post["idmodulo"], $datos["id_usuario"]);

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
        $post = json_decode(file_get_contents('php://input'), true);
        $model = new CompraModel();
        $t = $model->eliminar_producto($post);
        $rpta = array('rpta' => '0', 'msg' => "El registro no se puede eliminar");
        if ($t > 0) {
            $id_usuario = session()->get("data")["id_usuario"];
            // suma total
            $total = $model->get_suma_total($post["idmodulo"], $id_usuario);

            $rpta = array('rpta' => '1', 'msg' => "El registro ha sido eliminado", "total" => $total);
        }
        return $this->response->setJSON($rpta);
    }
}
