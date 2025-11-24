<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TipoCambioModel;

class Tipocambio extends BaseController
{

    public function lista()
    {
        $model = new TipoCambioModel();
        $data = $model->lista();

        $rpta = array('items' => $data);
        return $this->response->setJSON($rpta);
    }

    private function valores($data)
    {
        $datos = array(
            'id_moneda' => $data["id_moneda"],
            'fecha' =>  $data["fecha"],
            'tipo_cambio' => $data["tipo_cambio"],
        );

        return $datos;
    }
    private function validar($datos)
    {
        $errors = array();
        $model = new TipoCambioModel();

        $t = $model->existe($datos);
        if ($t > 0) {
            $errors[] =  "Ya existe el tipo de cambio en esta fecha";
            return $errors;
        }
    }

    public function guardar()
    {
        $post = json_decode(file_get_contents('php://input'), true);

        $datos = $this->valores($post);
        $model = new TipoCambioModel();

        $errors = $this->validar($datos);
        $rpta = array('rpta' => '0', 'msg' => $errors);
        if (!empty($errors)) {
            return $this->response->setJSON($rpta);
        }

        $id = $model->guardar($datos);
        $rpta = array('rpta' => '1', 'msg' => "Creado correctamente", 'id' => $id);

        return $this->response->setJSON($rpta);
    }
    public function eliminar()
    {
        $post = json_decode(file_get_contents('php://input'));
        $model = new TipoCambioModel();
        $t = $model->eliminar($post);
        $rpta = array('rpta' => '1', 'msg' => "Registro eliminado correctamente");
        if ($t <= 0) {
            $rpta = array('rpta' => '1', 'msg' => "El registro no se puede eliminar");
        }
        return $this->response->setJSON($rpta);
    }
}
