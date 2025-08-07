<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TrabajadorCasModel;

class TrabajadorCas extends BaseController
{

    public function modulo()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new TrabajadorCasModel();
        $items = $model->modulo($data->id);
        $rpta = array('rpta' => '1', 'items' => $items);
        return $this->response->setJSON($rpta);
    }

    private function valores($data)
    {
        $datos = array(
            'id_trabajador' => $data["idmodulo"],
            'id_cargo' => $data["id_cargo"],
            'id_tipo_remuneracion' => $data["id_tipo_remuneracion"],
            'id_tipo_resolucion' => $data["id_tipo_resolucion"],
            'remuneracion' => $data["remuneracion"],
            'nro_resolucion' => $data["nro_resolucion"],
            'fecha_resolucion' => $data["fecha_resolucion"],
        );

        return $datos;
    }

    public function guardar()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $datos = $this->valores($data);
        $model = new TrabajadorCasModel();
        $model->eliminar($data["idmodulo"]);
        $model->guardar($datos);

        $data = array('rpta' => '1', 'msg' => "Su información se actualizo correctamente");
        return $this->response->setJSON($data);
    }
}
