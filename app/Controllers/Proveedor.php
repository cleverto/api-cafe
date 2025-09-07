<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProveedorModel;

class Proveedor extends BaseController
{

    public function lista()
    {
        $model = new ProveedorModel();
        $data = $model->lista();

        $rpta = array('items' => $data);
        return $this->response->setJSON($rpta);
    }
    public function modulo()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new ProveedorModel();
        $items = $model->modulo($data->id);
        $rpta = array('rpta' => '1', 'items' => $items);
        return $this->response->setJSON($rpta);
    }
    public function get_by_dni()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new ProveedorModel();
        $items = $model->get_by_dni($data->id);
        $rpta = array('items' => $items);
        return $this->response->setJSON($rpta);
    }
    private function valores($data)
    {
        $datos = array(
            'id_ubigeo' => $data["id_ubigeo"],
            'id_identidad' => "1",
            'tipo' => $data["tipo"],
            'nro' => $data["dni"],
            'proveedor' => $data["proveedor"],
            'direccion' => $data["direccion"],
            'telefono' => $data["telefono"],
        );

        return $datos;
    }
    private function validar($datos)
    {
        $errors = array();
        $model = new ProveedorModel();

        $t = $model->existe_dni($datos);
        if ($t > 0) {
            $errors[] =  "Este numero de dni ya esta registrado";
            return $errors;
        }
    }
    private function validar_modificar($datos)
    {
        $errors = array();
        $model = new ProveedorModel();

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
        $model = new ProveedorModel();
        if ($post["operacion"] == "0") {


            $errors = $this->validar($datos);
            $rpta = array('rpta' => '0', 'msg' => $errors);
            if (!empty($errors)) {
                return $this->response->setJSON($rpta);
            }


            $id = $model->guardar($datos);

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
        $model = new ProveedorModel();
        $t = $model->eliminar($post);
        $rpta = array('rpta' => '1', 'msg' => "Registro eliminado correctamente");
        if ($t <= 0) {
            $rpta = array('rpta' => '1', 'msg' => "El registro no se puede eliminar");
        }
        return $this->response->setJSON($rpta);
    }
}
