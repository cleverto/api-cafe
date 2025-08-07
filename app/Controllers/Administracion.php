<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AdministracionModel;
use App\Models\ProcesoModel;

class Administracion extends BaseController
{

    public function lista()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new AdministracionModel();
        $query = $model->lista($data);
        $data = $query->getResultArray();
        $data = array('rpta' => '1', 'items' => $data);
        return $this->response->setJSON($data);
    }
    public function modulo()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new AdministracionModel();
        $items = $model->modulo($data);
        $rpta = array('rpta' => '1', 'items' => $items);
        return $this->response->setJSON($rpta);
    }
    public function eliminar()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new AdministracionModel();
        $t = $model->eliminar($data);
        $data = array('rpta' => 'true', 'msg' => "Registro eliminado correctamente");
        if ($t <= 0) {
            $data = array('rpta' => 'false', 'msg' => "El registro no se puede eliminar");
        }

        return $this->response->setJSON($data);
    }
    public function guardar()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new AdministracionModel();
        if ($data->operacion == "0") {
            $errors = $model->validar($data);
        } else {
            $errors = $model->validar_modificar($data);
        }

        if (!empty($errors)) {
            $rpta = array('rpta' => '0', 'msg' => $errors);
            return $this->response->setJSON($rpta);
        }

        if ($data->operacion == "0") {
            $id = $model->guardar($data);
            $data = array('rpta' => '1', 'msg' => "Creado correctamente", 'id' => $id);
        } else {
            $id = $model->modificar($data->id, $data);
            $data = array('rpta' => '1', 'msg' => "Modificado correctamente");
        }

        return $this->response->setJSON($data);
    }
}
