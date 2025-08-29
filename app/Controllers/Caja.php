<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CajaModel;

class Caja extends BaseController
{

    public function lista()
    {
        $model = new CajaModel();
        $data = $model->lista();

        $rpta = array('items' => $data);
        return $this->response->setJSON($rpta);
    }
    public function resumen()
    {
        $model = new CajaModel();
        $apertura = $model->apertura();
        $ingresos = $model->ingresos();
        $egresos = $model->egresos();
        $saldo_usuarios = $model->saldo_usuarios();

        $rpta = array(
            'apertura' => $apertura,
            'ingresos' => $ingresos,
            'egresos' => $egresos,
            'saldo' => $ingresos - $egresos,
            'saldo_usuarios' => $saldo_usuarios
        );
        return $this->response->setJSON($rpta);
    }
    public function lista_by_usuario()
    {
        $post = json_decode(file_get_contents('php://input'), true);
        $model = new CajaModel();
        $data = $model->lista_by_usuario($post);

        $rpta = array('items' => $data);
        return $this->response->setJSON($rpta);
    }
    public function modulo()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new CajaModel();
        $items = $model->modulo($data->id);
        $rpta = array('rpta' => '1', 'items' => $items);
        return $this->response->setJSON($rpta);
    }
    private function valores($data)
    {
        $datos = array(
            'id_empresa' => "1",
            'id_sucursal' => "1",
            'id_usuario' => session()->get("data")["id_usuario"],
            'id_concepto' => $data["id_concepto"],
            'id_tipo_caja' => $data["id_tipo_caja"],
            'id_proveedor' => $data["id_proveedor"],
            'fecha' => $data["fecha"],
            'movimiento' => $data["movimiento"],
            'observaciones' => $data["observaciones"],
        );

        return $datos;
    }
    public function guardar()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $model = new CajaModel();
        $datos = $this->valores($data);
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
        $model = new CajaModel();
        $t = $model->eliminar($data);
        $rpta = array('rpta' => '1', 'msg' => "Registro eliminado correctamente");
        if ($t <= 0) {
            $rpta = array('rpta' => '1', 'msg' => "El registro no se puede eliminar");
        }
        return $this->response->setJSON($rpta);
    }
}
