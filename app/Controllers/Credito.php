<?php


namespace App\Controllers;

use App\Controllers\BaseController;
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
    public function modulo()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new CreditoModel();
        $items = $model->modulo($data->id);
        $rpta = array('rpta' => '1', 'items' => $items);
        return $this->response->setJSON($rpta);
    }

    private function valores($data)
    {
        $datos = array(
            'producto' => $data["producto"],
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
    public function eliminar()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new CreditoModel();
        $t = $model->eliminar($data);
        $rpta = array('rpta' => '1', 'msg' => "Registro eliminado correctamente");
        if ($t <= 0) {
            $rpta = array('rpta' => '1', 'msg' => "El registro no se puede eliminar");
        }
        return $this->response->setJSON($rpta);
    }
}
