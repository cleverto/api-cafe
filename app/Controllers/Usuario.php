<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;

class Usuario extends BaseController
{

    public function lista()
    {
        $model = new UsuarioModel();
        $data = $model->lista();
        $data = array('items' => $data);
        return $this->response->setJSON($data);
    }
    public function lista_activo()
    {
        // $model = new UsuarioModel();
        // $query = $model->lista_activo();
        // $data = array('items' => $data);
        // return $this->response->setJSON($data);
    }
    public function modulo()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new UsuarioModel();
        $data = $model->modulo($data);
        $data = array('rpta' => 'true', 'items' => $data);
        return $this->response->setJSON($data);
    }
    private function valores($data)
    {
        $datos = array(
            'usuario' => $data->usuario,
            'dni' => $data->dni,
            'nacimiento' => $data->nacimiento,
            'id_perfil' => $data->id_perfil,
            'email' => $data->email,
            'telefono' => $data->telefono,
            'direccion' => $data->direccion,
            'activo' => $data->activo,
            'password' => md5($data->dni),
        );
        return $datos;
    }
    private function validar($datos)
    {
        $errors = array();
        $model = new UsuarioModel();
        $t = $model->existe_email($datos);
        if ($t > 0) {
            $errors[] =  "Este correo ya existe";
            return $errors;
        }

        $t = $model->existe_dni($datos);
        if ($t > 0) {
            $errors[] =  "Este numero de dni ya existe";
            return $errors;
        }
    }
    private function validar_modificar($id, $datos)
    {
        $errors = array();
        $model = new UsuarioModel();
        $t = $model->existe_email_modificar($id, $datos);
        if ($t > 0) {
            $errors[] =  "Este correo ya existe";
            return $errors;
        }

        $t = $model->existe_dni_modificar($id, $datos);
        if ($t > 0) {
            $errors[] =  "Este numero de dni ya existe";
            return $errors;
        }
    }
    public function guardar()
    {
        $data = json_decode(file_get_contents('php://input'));
        $datos = $this->valores($data);
        

        $model = new UsuarioModel();
        if ($data->operacion == "0") {

            $errors = $this->validar($datos);
            $rpta = array('rpta' => '0', 'msg' => $errors);
            if (!empty($errors)) {
                return $this->response->setJSON($rpta);
            }

            $id = $model->guardar($datos);
            $data = array('rpta' => '1', 'msg' => "Creado correctamente", 'id' => $id);
        } else {
            $errors = $this->validar_modificar($data->id, $datos);
            $rpta = array('rpta' => '0', 'msg' => $errors);
            if (!empty($errors)) {
                return $this->response->setJSON($rpta);
            }

            $id = $model->modificar($data->id, $datos);
            $data = array('rpta' => '1', 'msg' => "Modificado correctamente", 'id' => $id);
        }

        return $this->response->setJSON($data);
    }
    public function eliminar()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new UsuarioModel();
        $t = $model->eliminar($data);
        $data = array('rpta' => '1', 'msg' => "Registro eliminado correctamente");
        if ($t <= 0) {
            $data = array('rpta' => '0', 'msg' => "El registro no se puede eliminar");
        }
        return $this->response->setJSON($data);
    }
}
