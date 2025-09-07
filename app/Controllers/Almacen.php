<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AlmacenModel;
use App\Models\FuncionesModel;

class Almacen extends BaseController
{
    public function lista()
    {
        $post = json_decode(file_get_contents('php://input'), true);
        $model = new AlmacenModel();
        $data = $model->lista($post);

        $rpta = array('items' => $data);
        return $this->response->setJSON($rpta);
    }
    public function buscar()
    {
        $post = json_decode(file_get_contents('php://input'), true);
        $model = new AlmacenModel();
        $items = $model->buscar($post);

        $rpta = array('items' => $items);
        return $this->response->setJSON($rpta);
    }
    // public function lista_detalle()
    // {
    //     $post = json_decode(file_get_contents('php://input'), true);
    //     $model = new AlmacenModel();
    //     $data = $model->lista_detalle($post["id"]);

    //     $rpta = array('items' => $data);
    //     return $this->response->setJSON($rpta);
    // }
    public function lista_temp()
    {
        $post = json_decode(file_get_contents('php://input'), true);
        $id_usuario = session()->get("data")["id_usuario"];

        $model = new AlmacenModel();
        $data = $model->lista_temp($post["id"], $id_usuario);

        // suma total
        $total = $model->get_suma_total($post["id"], $id_usuario);

        $rpta = array('items' => $data, 'total' => $total);
        return $this->response->setJSON($rpta);
    }
    public function modulo()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $model = new AlmacenModel();
        $items = $model->modulo($data["id"]);

        $rpta = array('rpta' => '1', 'items' => $items);
        return $this->response->setJSON($rpta);
    }

    private function valores($post)
    {
        $model = new FuncionesModel();
        $nro_comprobante = $model->get_correlativo("1", "1",  $post["id_tipo_comprobante"]);
        $operacion = $post["id_tipo_comprobante"] == "90" ? "I" : "S";
        $id_usuario = session()->get("data")["id_usuario"];
        $datos = array(
            'id_empresa' => "1",
            'id_sucursal' => "1",
            'id_almacen' => "1",
            'id_usuario' => $id_usuario,
            'id_tipo_comprobante' => $post["id_tipo_comprobante"],
            'operacion' => $operacion,
            'motivo' => $post["motivo"],
            'fecha' => $post["fecha"],
            'nro_comprobante' =>  $nro_comprobante,
        );

        return $datos;
    }
    // private function valores_credito($data)
    // {

    //     $datos = array(
    //         'id_empresa' => "1",
    //         'id_sucursal' => "1",
    //         'id_proveedor' => $data["id_proveedor"],
    //         'id_moneda' =>  $data["id_moneda"],
    //         'movimiento' => '0',
    //         'referencia' => $data["nro_comprobante"],
    //         'total' => $data["total"],
    //         'saldo' => $data["total"],
    //         'fecha' => date('Y-m-d'),
    //         'observaciones' => "",
    //         'estado' => "0",
    //     );

    //     return $datos;
    // }
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
            'cantidad'    => $data['cantidad'],
            'precio'      => $data['precio'],
            'total'       => $data['total'],

        );
        return $datos;
    }

    // private function validar_modificar($datos)
    // {
    //     $errors = array();
    //     $model = new AlmacenModel();

    //     $t = $model->existe_dni_modificar($datos);
    //     if ($t > 0) {
    //         $errors[] =  "Este numero de dni ya esta registrado";
    //         return $errors;
    //     }
    // }

    public function guardar()
    {
        $post = json_decode(file_get_contents('php://input'), true);

        $datos = $this->valores($post);
        $model = new AlmacenModel();


        if ($post["operacion"] == "0") {
            $id = $model->guardar($datos);

            // Actualizar correlativo
            $model_funciones = new FuncionesModel();
            $model_funciones->actualizar_correlativo("1", "1",  $post["id_tipo_comprobante"]);

            //Guardar en kardex                        
            $id_kardex = $model->guardar_kardex($datos, "nota_almacen_temp");

            // registra relación de almacen con kardex
            $model->guardar_kardex_almacen($id, $id_kardex);

            //actualizar stock
            $model->actualizar_stock($id_kardex);

            // elimina temp detalle
            $model->eliminar_temp($datos["id_usuario"]);


            // *********
            $rpta = array('rpta' => '1', 'msg' => "Creado correctamente", 'id' => $id);
        } else {

            $t = $model->modificar($post["idmodulo"], $datos);


            $rpta = array('rpta' => '1', 'msg' => "El registro no se puede modificar");
            if ($t > 0) {

                $rpta = array('rpta' => '1', 'msg' => "Modificado correctamente");
            }
        }

        return $this->response->setJSON($rpta);
    }
    public function guardar_producto()
    {
        $post = json_decode(file_get_contents('php://input'), true);

        $datos = $this->valores_producto($post);
        $model = new AlmacenModel();

        if ($post["operacion"] == "0") {
            $id = $model->guardar_producto($datos);

            // suma total
            $total = $model->get_suma_total($id, $datos["id_usuario"]);

            $rpta = array('rpta' => '1', 'msg' => "Creado correctamente", 'id' => $id, 'total' => $total);
        } else {

            $id = $model->guardar_producto($datos);
            // suma total
            $total = $model->get_suma_total($post["idmodulo"], $datos["id_usuario"]);

            $rpta = array('rpta' => '1', 'msg' => "Modificado correctamente", 'id' => $id);
        }

        return $this->response->setJSON($rpta);
    }
    public function eliminar()
    {
        $post = json_decode(file_get_contents('php://input'), true);
        $model = new AlmacenModel();
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
        $model = new AlmacenModel();
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