<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TrabajadorVinculoDocenteModel;

class TrabajadorVinculoDocente extends BaseController
{

    public function lista()
    {
        $model = new TrabajadorVinculoDocenteModel();
        $data = $model->lista();

        $rpta = array('items' => $data);
        return $this->response->setJSON($rpta);
    }
    public function modulo()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new TrabajadorVinculoDocenteModel();
        $items = $model->modulo($data->id);
        $rpta = array('rpta' => '1', 'items' => $items);
        return $this->response->setJSON($rpta);
    }

    private function valores($data)
    {
        $datos = array(
            'id_trabajador' => $data["id_trabajador"],
            'ingreso' => $data["ingreso"],
            'id_tipo_contrato' => $data["id_tipo_contrato"],
            'id_categoria' => $data["id_categoria"],
            'id_dedicacion' => $data["id_dedicacion"],
            'id_departamento_academico' => $data["id_departamento_academico"],
            'id_regimen' => $data["id_regimen"],
            'documento' => $data["documento"],
            'remuneracion' => $data["remuneracion"],
            'horas' => $data["horas"],
            'nro_resolucion' => $data["nro_resolucion"],
            'nro_plaza' => $data["nro_plaza"],
            'fecha_resolucion' => $data["fecha_resolucion"],
            'fin' => !empty($data["fin"]) ? $data["fin"] : null,
        );

        return $datos;
    }
    public function guardar()
    {
        $data = json_decode(file_get_contents('php://input'), true);


        $datos = $this->valores($data);
        $model = new TrabajadorVinculoDocenteModel();
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
        $model = new TrabajadorVinculoDocenteModel();
        $t = $model->eliminar($data[]);
        $rpta = array('rpta' => '1', 'msg' => "Registro eliminado correctamente");
        if ($t <= 0) {
            $rpta = array('rpta' => '1', 'msg' => "El registro no se puede eliminar");
        }
        return $this->response->setJSON($rpta);
    }
}
