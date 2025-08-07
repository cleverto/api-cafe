<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FuncionesModel;
use App\Models\TrabajadorModel;

class Trabajador extends BaseController
{

    public function lista()
    {
        $model = new TrabajadorModel();
        $data = $model->lista();

        $rpta = array('items' => $data);
        return $this->response->setJSON($rpta);
    }
    public function lista_academica()
    {
        $model = new TrabajadorModel();
        $data = $model->lista_academica();

        $rpta = array('items' => $data);
        return $this->response->setJSON($rpta);
    }

    public function lista_activo()
    {
        $model = new TrabajadorModel();
        $query = $model->lista_activo();
        $data = $query->getResultArray();
        $data = array('items' => $data);
        return $this->response->setJSON($data);
    }
    public function lista_adjunto()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $model = new TrabajadorModel();
        $data = $model->lista_adjunto($data);
        $rpta = array('items' => $data);
        return $this->response->setJSON($rpta);
    }
    public function modulo()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new TrabajadorModel();
        $items = $model->modulo($data->id);
        $rpta = array('rpta' => '1', 'items' => $items);
        return $this->response->setJSON($rpta);
    }
    public function get_tipo_trabajador()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new TrabajadorModel();
        $items = $model->get_tipo_trabajador($data->id);
        $rpta = array('rpta' => '1', 'items' => $items);
        return $this->response->setJSON($rpta);
    }
    public function modulo_pension()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new TrabajadorModel();
        $items = $model->modulo_pension($data->id);
        if ($items) {
            $rpta = array('rpta' => '1', 'items' => $items);
        } else {
            $rpta = array('rpta' => '0',  'items' => $items);
        }

        return $this->response->setJSON($rpta);
    }
    public function modulo_hijos()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new TrabajadorModel();
        $items = $model->modulo_hijos($data->id);
        if ($items) {
            $rpta = array('rpta' => '1', 'items' => $items);
        } else {
            $rpta = array('rpta' => '0',  'items' => $items);
        }

        return $this->response->setJSON($rpta);
    }
    public function modulo_cuenta()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new TrabajadorModel();
        $items = $model->modulo_cuenta($data->id);
        if ($items) {
            $rpta = array('rpta' => '1', 'items' => $items);
        } else {
            $rpta = array('rpta' => '0',  'items' => $items);
        }

        return $this->response->setJSON($rpta);
    }
    public function modulo_academica()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new TrabajadorModel();
        $items = $model->modulo_academica($data->id);
        if ($items) {
            $rpta = array('rpta' => '1', 'items' => $items);
        } else {
            $rpta = array('rpta' => '0',  'items' => $items);
        }

        return $this->response->setJSON($rpta);
    }
    public function modulo_vinculo()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new TrabajadorModel();
        $items = $model->modulo_vinculo($data->id);
        if ($items) {
            $rpta = array('rpta' => '1', 'items' => $items);
        } else {
            $rpta = array('rpta' => '0',  'items' => $items);
        }

        return $this->response->setJSON($rpta);
    }
    public function modulo_familiar()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new TrabajadorModel();
        $items = $model->modulo_familiar($data->id);
        if ($items) {
            $rpta = array('rpta' => '1', 'items' => $items);
        } else {
            $rpta = array('rpta' => '0',  'items' => $items);
        }

        return $this->response->setJSON($rpta);
    }
    private function valores($data)
    {

        $datos = array(
            'id_trabajador' => $data["id_trabajador"],
            'id_tipo_via' => $data["id_tipo_via"],
            'id_estado_civil' => $data["id_estado_civil"],
            'id_tipo_trabajador' => $data["id_tipo_trabajador"],
            'id_ubigeo' => $data["departamento"] . $data["provincia"] . $data["distrito"],
            'dni' => $data["dni"],
            'paterno' => strtoupper(trim($data["paterno"])),
            'materno' => strtoupper(trim($data["materno"])),
            'nombres' => strtoupper(trim($data["nombres"])),
            'trabajador' => trim(
                strtoupper($data["paterno"]) . ' ' .
                    strtoupper($data["materno"]) . ' ' .
                    strtoupper($data["nombres"])
            ),
            'carne' => $data["carne"],
            'pasaporte' => $data["pasaporte"],
            'libreta' => $data["libreta"],
            'sexo' => $data["sexo"],
            'nacimiento' => $data["nacimiento"],
            'domicilio' => $data["domicilio"],
            'distrito' => $data["distrito"],
            'provincia' => $data["provincia"],
            'departamento' => $data["departamento"],
            'fijo' => $data["fijo"],
            'celular' => $data["celular"],
            'correo' => $data["correo"],
            'institucional' => $data["institucional"],
            'ruc' => $data["ruc"],
            'essalud' => $data["essalud"],
            'privada' => $data["privada"],
            'publica' => $data["publica"],
            'arirhsp' => $data["arirhsp"],
            'rdp' => $data["rdp"],
            'rdl' => $data["rdl"],
            'sisper' => $data["sisper"],
            'tregistro' => $data["tregistro"],
            'siga' => $data["siga"],
        );


        return $datos;
    }
    public function guardar()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $datos = $this->valores($data);
        $model = new TrabajadorModel();
        if ($data["operacion"] == "0") {
            $id = $model->guardar($datos);

            $data = array('rpta' => '1', 'msg' => "Creado correctamente", 'id' => $id);
        } else {
            $id = $model->modificar($data["id_trabajador"], $datos);
            $data = array('rpta' => '1', 'msg' => "Modificado correctamente", 'id' => $id);
        }

        return $this->response->setJSON($data);
    }
    public function eliminar()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new TrabajadorModel();
        $t = $model->eliminar($data);
        $rpta = array('rpta' => '1', 'msg' => "Registro eliminado correctamente");
        if ($t <= 0) {
            $rpta = array('rpta' => '1', 'msg' => "El registro no se puede eliminar");
        }
        return $this->response->setJSON($rpta);
    }
    private function valores_hijos($data)
    {
        $datos = array(
            'id_trabajador' => $data["idmodulo"],
            'cantidad' => $data["cantidad"],
            'discapacitado' => $data["discapacitado"],
            'mayor' => $data["mayor"],
        );

        if (empty($data["mayor"])) {
            unset($datos["mayor"]);
        }
        return $datos;
    }

    public function guardar_hijos()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $datos = $this->valores_hijos($data);
        $model = new TrabajadorModel();
        $model->eliminar_hijos($data["idmodulo"]);
        $model->guardar_hijos($datos);
        $data = array('rpta' => '1', 'msg' => "Su información se actualizo correctamente");
        return $this->response->setJSON($data);
    }
    private function valores_pension($data)
    {
        $datos = array(
            'id_trabajador' => $data["idmodulo"],
            'id_pension' => $data["id_pension"],
            'afiliado' => $data["afiliado"],
            'cuspp' => $data["cuspp"],
        );

        return $datos;
    }
    private function valores_vinculo($data)
    {
        $datos = array(
            'id_trabajador_vinculo' => $data["idmodulo"],
            'id_trabajador' => $data["id_trabajador"],
            'id_cargo' => $data["id_cargo"],
            'id_regimen' => $data["id_regimen"],
            'id_tipo_resolucion' => $data["id_tipo_resolucion"],
            'id_tipo_remuneracion' => $data["id_tipo_remuneracion"],
            'ingreso' => $data["ingreso"],
            'remuneracion' => $data["remuneracion"],
            'nro_resolucion' => $data["nro_resolucion"],
            'fecha_resolucion' => $data["fecha_resolucion"],
        );

        return $datos;
    }
    // private function valores_vinculo_docente($data)
    // {
    //     $datos = array(
    //         'id_trabajador' => $data["idmodulo"],
    //         'id_tipo_contrato' => $data["id_tipo_contrato"],
    //         'id_dedicacion' => $data["id_dedicacion"],
    //         'id_departamento_academico' => $data["id_departamento_academico"],
    //         'id_tipo_remuneracion' => $data["id_tipo_remuneracion"],
    //         'id_tipo_resolucion' => $data["id_tipo_resolucion"],
    //         'remuneracion' => $data["remuneracion"],
    //         'horas' => $data["horas"],
    //         'nro_resolucion' => $data["nro_resolucion"],
    //         'nro_plaza' => $data["nro_plaza"],
    //         'fecha_resolucion' => $data["fecha_resolucion"],
    //     );

    //     return $datos;
    // }
    // private function valores_vinculo_cas($data)
    // {
    //     $datos = array(
    //         'id_trabajador' => $data["idmodulo"],
    //         'id_cargo' => $data["id_cargo"],
    //         'id_tipo_remuneracion' => $data["id_tipo_remuneracion"],
    //         'id_tipo_resolucion' => $data["id_tipo_resolucion"],
    //         'remuneracion' => $data["remuneracion"],
    //         'nro_resolucion' => $data["nro_resolucion"],
    //         'fecha_resolucion' => $data["fecha_resolucion"],
    //     );

    //     return $datos;
    // }
    public function guardar_pension()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $datos = $this->valores_pension($data);
        $model = new TrabajadorModel();
        $model->eliminar_pension($data["idmodulo"]);
        $model->guardar_pension($datos);

        $data = array('rpta' => '1', 'msg' => "Su información se actualizo correctamente");


        return $this->response->setJSON($data);
    }
    private function valores_cuenta($data)
    {
        $datos = array(
            'id_trabajador' => $data["idmodulo"],
            'cuenta' => $data["cuenta"],
            'cci' => $data["cci"],
        );

        return $datos;
    }
    public function guardar_cuenta()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $datos = $this->valores_cuenta($data);
        $model = new TrabajadorModel();
        $model->eliminar_cuenta($data["idmodulo"]);
        $model->guardar_cuenta($datos);

        $data = array('rpta' => '1', 'msg' => "Su información se actualizo correctamente");


        return $this->response->setJSON($data);
    }
    public function guardar_vinculo()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $datos = $this->valores_vinculo($data);
        $model = new TrabajadorModel();
        $model->eliminar_vinculo($data["idmodulo"]);
        $model->guardar_vinculo($datos);

        $data = array('rpta' => '1', 'msg' => "Su información se actualizo correctamente");


        return $this->response->setJSON($data);
    }
    // public function guardar_vinculo_docente()
    // {
    //     $data = json_decode(file_get_contents('php://input'), true);

    //     $datos = $this->valores_vinculo_docente($data);
    //     $model = new TrabajadorModel();
    //     $model->eliminar_vinculo_docente($data["idmodulo"]);
    //     $model->guardar_vinculo_docente($datos);

    //     $data = array('rpta' => '1', 'msg' => "Su información se actualizo correctamente");
    //     return $this->response->setJSON($data);
    // }
    // public function guardar_vinculo_cas()
    // {
    //     $data = json_decode(file_get_contents('php://input'), true);

    //     $datos = $this->guardar_vinculo_cas($data);
    //     $model = new TrabajadorModel();
    //     $model->eliminar_vinculo_cas($data["idmodulo"]);
    //     $model->guardar_vinculo_cas($datos);

    //     $data = array('rpta' => '1', 'msg' => "Su información se actualizo correctamente");
    //     return $this->response->setJSON($data);
    // }
    private function valores_familiar($data)
    {
        $datos = array(
            'id_trabajador' => $data["idmodulo"],
            'id_parentezco' => $data["id_parentezco"],
            'familiar' => $data["familiar"],
            'direccion' => $data["direccion"],
            'celular' => $data["celular"],
        );

        return $datos;
    }
    public function guardar_familiar()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $datos = $this->valores_familiar($data);
        $model = new TrabajadorModel();
        $model->eliminar_familiar($data["idmodulo"]);
        $model->guardar_familiar($datos);

        $data = array('rpta' => '1', 'msg' => "Su información se actualizo correctamente");

        return $this->response->setJSON($data);
    }
    private function valores_academica($data)
    {
        $datos = array(
            'id_trabajador_academica' => $data["idmodulo"],
            'id_trabajador' => $data["id_trabajador"],
            'id_grado' => $data["id_grado"],
            'id_institucion' => $data["id_institucion"],
            'id_carrera' => $data["id_carrera"],
            'egresado' => $data["egresado"],
            'colegiatura' => $data["colegiatura"],
        );

        return $datos;
    }
    public function guardar_academica()
    {
        $data = json_decode(file_get_contents('php://input'), true);


        $datos = $this->valores_academica($data);
        $model = new TrabajadorModel();

        if ($data['operacion'] == '0') {
            // Nueva inserción
            $id = $model->guardar_academica($datos);

            if ($id) {
                $rpta = [
                    'rpta' => '1',
                    'id'   => $id,
                    'msg'  => 'La información académica fue añadida correctamente'
                ];
            } else {
                $rpta = [
                    'rpta' => '0',
                    'msg'  => 'Ocurrió un error al guardar la información académica'
                ];
            }
        } else {

            $exito = $model->modificar_academica($data["idmodulo"], $datos);

            $rpta = [
                'rpta' => $exito ? '1' : '0',
                'msg'  => $exito ? 'Su información se actualizó correctamente' : 'No se pudo actualizar la información'
            ];
        }


        return $this->response->setJSON($rpta);
    }
    public function eliminar_academica()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $model = new TrabajadorModel();
        $t = $model->eliminar_academica($data["id"]);
        $rpta = array('rpta' => '1', 'msg' => "Registro eliminado correctamente");
        if ($t <= 0) {
            $rpta = array('rpta' => '1', 'msg' => "El registro no se puede eliminar");
        }
        return $this->response->setJSON($rpta);
    }
    private function valores_adjunto($data)
    {
        $model = new TrabajadorModel();
        $dni = $model->get_dni($data["id_trabajador"]);

        $datos = array(
            'id_trabajador' => $data["id_trabajador"],
            'id_requisito' => $data["id_requisito"],
            'dni' => $dni,
        );

        return $datos;
    }
    public function eliminar_adjunto()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $model = new TrabajadorModel();

        $t = $model->eliminar_adjunto($data);
        $rpta = array('rpta' => '1', 'msg' => "Registro eliminado correctamente");
        if ($t <= 0) {
            $rpta = array('rpta' => '0', 'msg' => "El registro no se puede eliminar");
        }
        return $this->response->setJSON($rpta);
    }
    private function validar_adjunto($datos)
    {
        $errors = array();
        $model = new TrabajadorModel();
        $t = $model->existe_adjunto($datos);
        if ($t > 0) {
            $errors[] =  "Ya se encuentra el este documento como adjunto";
            return $errors;
        }

        $modulo_trabajador = $model->modulo($datos["id_trabajador"]);

        if ($modulo_trabajador["id_estado_civil"] == "1") {
            if (in_array($datos["id_requisito"], ["6", "8"])) {
                $errors[] =  "Este documento solo aplica para CASADOS Y CONVIVIENTES";
                return $errors;
            }
        }

        $modulo_cuenta = $model->modulo_cuenta($datos["id_trabajador"]);
        if (strlen(trim($modulo_cuenta["cuenta"]))  > 0) {
            if (in_array($datos["id_requisito"], ["1"])) {
                $errors[] =  "Este documento solo aplica para trabajadores que no tiene cuenta de ahorros";
                return $errors;
            }
        }

        $modulo_hijos = $model->modulo_hijos($datos["id_trabajador"]);
        if (isset($modulo_hijos)) {
            if (intval($modulo_hijos["cantidad"]) == 0) {
                if (in_array($datos["id_requisito"], ["9"])) {
                    $errors[] =  "Este documento solo aplica para trabajadores que tienen hijos";
                    return $errors;
                }
            }
        }
    }
    public function guardar_adjunto()
    {
        $data = json_decode($_POST['data'], true);
        $datos = $this->valores_adjunto($data);

        $errors = $this->validar_adjunto($datos);
        $rpta = array('rpta' => '0', 'msg' => $errors);
        if (!empty($errors)) {
            return $this->response->setJSON($rpta);
        }

        $model = new Funciones();
        $vali = $model->upload_pdf($_FILES, $datos);

        if ($vali["rpta"] === "0") {
            $rpta = array('rpta' => '0', 'msg' => $vali["msg"]);
            return $this->response->setJSON($rpta);
        }
        $model = new TrabajadorModel();
        $model->eliminar_adjunto($datos);
        $model->guardar_adjunto($datos);

        $rpta = array('rpta' => '1', 'msg' => "Archivo adjuntado correctamente");

        return $this->response->setJSON($rpta);
    }
}
