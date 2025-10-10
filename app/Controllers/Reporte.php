<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AlmacenModel;
use App\Models\CompraModel;
use App\Models\SecadoModel;

class Reporte extends BaseController
{
    public function compras()
    {
        $desde = !empty($_GET) ? $_GET["desde"] : "";
        $hasta = !empty($_GET) ? $_GET["hasta"] : "";


        $datos = array(
            "desde" => $desde,
            "hasta" => $hasta
        );

        $model = new CompraModel();
        $vista["lista"] = $model->filtro($datos);
        $vista["filtro"] = $datos;

        return view('Compras', $vista);
    }
    public function almacen()
    {
        $id = !empty($_GET) ? $_GET["id"] : "";
        $desde = !empty($_GET) ? $_GET["desde"] : "";
        $hasta = !empty($_GET) ? $_GET["hasta"] : "";


        $datos = array(
            "id" => $id,
            "desde" => $desde,
            "hasta" => $hasta
        );

        $model = new AlmacenModel();
        $vista["lista"] = $model->filtro($datos);
        $vista["filtro"] = $datos;

        return view('Almacen', $vista);
    }

    public function compras_secado()
    {
        
        $desde = !empty($_GET) ? $_GET["desde"] : "";
        $hasta = !empty($_GET) ? $_GET["hasta"] : "";

        $datos = array(
            "desde" => $desde,
            "hasta" => $hasta
        );

        $model = new SecadoModel();
        $vista["lista"] = $model->filtro_compras($datos);
        $vista["filtro"] = $datos;

        return view('Comprassecado', $vista);
    }

}
