<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CajaModel;
use App\Models\CreditoModel;

class Credito extends BaseController
{

    public function lista()
    {
        $model = new CreditoModel();
        $data = $model->lista();

        $rpta = array('items' => $data);
        return $this->response->setJSON($rpta);
    }
    public function lista_pago()
    {
        $post = json_decode(file_get_contents('php://input'), true);

        $model = new CreditoModel();
        $items = $model->lista_pago($post);

        $rpta = array('items' => $items);
        return $this->response->setJSON($rpta);
    }
    public function modulo()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new CreditoModel();
        $items = $model->modulo($data->id);
        $rpta = array('rpta' => '1', 'items' => $items);
        return $this->response->setJSON($rpta);
    }
    public function modulo_origen()
    {
        $post = json_decode(file_get_contents('php://input'), true);
        $model = new CreditoModel();
        $items = $model->modulo_origen($post);
        $rpta = array('rpta' => '1', 'items' => $items);
        return $this->response->setJSON($rpta);
    }
    private function valores($data)
    {
        $datos = array(
            'movimiento' => $data["movimiento"],
        );

        return $datos;
    }
    private function valores_detalle($data)
    {
        $datos = array(
            'id_credito' => $data["idmodulo"],
            'id_tipo_caja' => $data["id_tipo_caja"],            
            'fecha' => $data["fecha"],
            'monto' => $data["monto"],
            'referencia' => $data["referencia"],
        );

        return $datos;
    }
    private function valores_caja($data)
    {
        $datos = array(
            'id_empresa' => $data["id_empresa"],
            'id_sucursal' => $data["id_sucursal"],
            'id_usuario' => session()->get("data")["id_usuario"],
            'id_concepto' => "3",
            'id_tipo_caja' => $data["id_tipo_caja"],
            'id_proveedor' => $data["id_proveedor"],
            'id_moneda' => $data["id_moneda"],
            'fecha' => date("Y-m-d H:i:s", strtotime($data["fecha"] . " " . date("H:i:s"))),
            'registro' => date('Y-m-d H:i:s'),
            'movimiento' => "I",
            'monto' => $data["monto"],
            
            'estado' => "0",
            'referencia' => "",
            'observaciones' => $data["referencia"],
        );

        return $datos;
    }
    public function guardar()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $datos = $this->valores($data);
        $model = new CreditoModel();
        if ($data["operacion"] == "0") {
            $id = $model->guardar($datos);



            $data = array('rpta' => '1', 'msg' => "Creado correctamente", 'id' => $id);
        } else {
            $id = $model->modificar($data["idmodulo"], $datos);
            $data = array('rpta' => '1', 'msg' => "Modificado correctamente", 'id' => $id);
        }

        return $this->response->setJSON($data);
    }
    private function validar_detalle($datos)
    {
        $errors = array();

        $model = new CreditoModel();
        $saldo = $model->get_saldo($datos["id_credito"]);

        if ($datos["monto"] > $saldo) {
            $errors[] = "El importe ({$datos['monto']}) es mayor al saldo pendiente de ({$saldo}), no se puede amortizar.";
            return $errors;
        }
    }
    public function guardar_detalle()
    {
        $post = json_decode(file_get_contents('php://input'), true);
        $datos = $this->valores_detalle($post);

        $errors = $this->validar_detalle($datos);
        $rpta = array('rpta' => '0', 'msg' => $errors);
        if (!empty($errors)) {
            return $this->response->setJSON($rpta);
        }

        $model = new CreditoModel();
        $id = $model->guardar_detalle($datos);

        // para caja
        $model_caja = new CajaModel();
        $datos_caja = $this->valores_caja($post );
        $id_caja = $model_caja->guardar($datos_caja);

        // para caja credito
        $model->guardar_caja_credito($id, $id_caja);

        //enviar el saldo
        $saldo = $model->get_saldo($datos["id_credito"]);

        $data = array('rpta' => '1', 'msg' => "Creado correctamente", 'id' => $id, 'saldo' => $saldo);


        return $this->response->setJSON($data);
    }
    public function eliminar_detalle()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $model = new CreditoModel();
        $t = $model->eliminar_detalle($data);
        $rpta = array('rpta' => '0', 'msg' => "El registro no se puede eliminar");
        if ($t) {
            //enviar el saldo
            $saldo = $model->get_saldo($data["idmodulo"]);

            $rpta = array('rpta' => '1', 'msg' => "Registro eliminado correctamente", "saldo" => $saldo);
        }
        return $this->response->setJSON($rpta);
    }
}
