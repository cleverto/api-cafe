<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DesignacionModel;
use App\Models\RotacionModel;
use App\Models\TrabajadorVinculoDocenteModel;
use App\Models\TrabajadorModel;

class Reporte extends BaseController
{

    public function trabajador()
    {

        $id = !empty($_GET) ? $_GET["id"] : "";

        $model = new TrabajadorModel();
        $vista["lista"] = $model->modulo($id);
        $vista["lista_academica"] = $model->modulo_academica_by_trabajador($id);
        $vista["lista_cuenta"] = $model->modulo_cuenta($id);
        $vista["lista_familiar"] = $model->modulo_familiar($id);
        $vista["lista_hijos"] = $model->modulo_hijos($id);
        $vista["lista_pension"] = $model->modulo_pension($id);
        $vista["lista_vinculo"] = $model->modulo_vinculo($id);


        if ($vista["lista"]["id_tipo_trabajador"] == "1") {
            $model = new TrabajadorVinculoDocenteModel();
            $vista["lista_vinculo"] = $model->modulo($id);
        }

        return view('trabajador', $vista);
    }
    public function escalafonario10()
    {

        $id = !empty($_GET) ? $_GET["id"] : "";

        $model = new TrabajadorModel();
        $vista["lista"] = $model->modulo($id);
        $vista["lista_academica"] = $model->modulo_academica_by_trabajador($id);
        $vista["lista_vinculo"] = $model->modulo_vinculo($id);

        if ($vista["lista"]["id_tipo_trabajador"] == "1") {
            $model = new TrabajadorVinculoDocenteModel();
            $vista["lista_vinculo"] = $model->modulo($id);
        }
        $model = new DesignacionModel();
        $vista["lista_designacion"] = $model->lista_by_trabajador($id);
        $model = new RotacionModel();
        $vista["lista_rotacion"] = $model->lista_by_trabajador($id);
        return view('escalafonario10', $vista);
    }
    public function escalafonario13()
    {

        $id = !empty($_GET) ? $_GET["id"] : "";

        $model = new TrabajadorModel();
        $vista["lista"] = $model->modulo($id);
        $vista["lista_academica"] = $model->modulo_academica_by_trabajador($id);
        $vista["lista_vinculo"] = $model->modulo_vinculo($id);

        if ($vista["lista"]["id_tipo_trabajador"] == "1") {
            $model = new TrabajadorVinculoDocenteModel();
            $vista["lista_vinculo"] = $model->modulo($id);
        }
        $model = new DesignacionModel();
        $vista["lista_designacion"] = $model->lista_by_trabajador($id);
        return view('escalafonario13', $vista);
    }
    public function escalafonario14()
    {

        $id = !empty($_GET) ? $_GET["id"] : "";

        $model = new TrabajadorModel();
        $vista["lista"] = $model->modulo($id);
        $vista["lista_academica"] = $model->modulo_academica_by_trabajador($id);
        $vista["lista_vinculo"] = $model->modulo_vinculo($id);

        if ($vista["lista"]["id_tipo_trabajador"] == "1") {
            $model = new TrabajadorVinculoDocenteModel();
            $vista["lista_vinculo"] = $model->modulo($id);
        }
        $model = new DesignacionModel();
        $vista["lista_designacion"] = $model->lista_by_trabajador($id);
        return view('escalafonario14', $vista);
    }
    public function escalafonario15()
    {

        $id = !empty($_GET) ? $_GET["id"] : "";

        $model = new TrabajadorModel();
        $vista["lista"] = $model->modulo($id);
        $vista["lista_academica"] = $model->modulo_academica_by_trabajador($id);
        $vista["lista_vinculo"] = $model->modulo_vinculo($id);

        if ($vista["lista"]["id_tipo_trabajador"] == "1") {
            $model = new TrabajadorVinculoDocenteModel();
            $vista["lista_vinculo"] = $model->modulo($id);
        }
        $model = new DesignacionModel();
        $vista["lista_designacion"] = $model->lista_by_trabajador($id);
        return view('escalafonario14', $vista);
    }
}
