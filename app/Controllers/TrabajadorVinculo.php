<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TrabajadorVinculoModel;

class TrabajadorVinculo extends BaseController
{
    public function lista()
    {
        $model = new TrabajadorVinculoModel();
        $data = $model->lista();

        $rpta = array('items' => $data);
        return $this->response->setJSON($rpta);
    }

    public function modulo()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new TrabajadorVinculoModel();
        $items = $model->modulo($data->id);
        $rpta = array('rpta' => '1', 'items' => $items);
        return $this->response->setJSON($rpta);
    }

    private function valores($data)
    {
        $datos = array(
            'id_trabajador' => $data["id_trabajador"],
            'ingreso' => $data["ingreso"],
            'cargo' => $data["cargo"],
            'id_regimen' => $data["id_regimen"],
            'id_tipo_resolucion' => $data["id_tipo_resolucion"],
            'dependencia' => $data["dependencia"],
            'documento' => $data["documento"],
            'remuneracion' => $data["remuneracion"],
            'fecha_resolucion' => $data["fecha_resolucion"],
            'fin' => !empty($data["fin"]) ? $data["fin"] : null,
        );

        return $datos;
    }
    public function guardar()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $datos = $this->valores($data);
        $model = new TrabajadorVinculoModel();
        $rpta = array('rpta' => '1', 'msg' => "Existe un error en esta operación");
        if ($data["operacion"] == "0") {
            $id = $model->guardar($datos);

            if ($id > 0) {
                $rpta = array('rpta' => '1', 'msg' => "Su información se registró correctamente", 'id' => $id);
            }
        } else {
            $t = $model->modificar($data["idmodulo"], $datos);
            if ($t > 0) {
                $rpta = array('rpta' => '1', 'msg' => "Su información se actualizó correctamente");
            }
        }


        return $this->response->setJSON($rpta);
    }
    public function eliminar()
    {
        $data = json_decode(file_get_contents('php://input'),true);
        $model = new TrabajadorVinculoModel();
        $t = $model->eliminar($data);
        $rpta = array('rpta' => '1', 'msg' => "Registro eliminado correctamente");
        if ($t <= 0) {
            $rpta = array('rpta' => '1', 'msg' => "El registro no se puede eliminar");
        }
        return $this->response->setJSON($rpta);
    }
}
