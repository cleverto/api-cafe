<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AlmacenModel;
use App\Models\CompraModel;
use App\Models\SecadoModel;
use App\Models\VentaModel;

class Reporte extends BaseController
{
    public function compras()
    {
        $desde = !empty($_GET) ? $_GET["desde"] : "";
        $hasta = !empty($_GET) ? $_GET["hasta"] : "";
        $h = $_GET["h"];

        $datos = array(
            "desde" => $desde,
            "hasta" => $hasta,
            "header" => $h
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
        $h = $_GET["h"];

        $datos = array(
            "id" => $id,
            "desde" => $desde,
            "hasta" => $hasta,
            "header" => $h
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

    public function trazabilidad()
    {

        $desde = !empty($_GET) ? $_GET["desde"] : "";
        $hasta = !empty($_GET) ? $_GET["hasta"] : "";
        $producto = !empty($_GET) ? $_GET["producto"] : "";
        $h = $_GET["h"];

        $datos = array(
            "producto" => $producto,
            "desde" => $desde,
            "hasta" => $hasta,
            "header" => $h
        );

        $model = new VentaModel();
        $vista["lista"] = $model->filtro_trazabilidad($datos);
        $vista["filtro"] = $datos;

        return view('Trazabilidad', $vista);
    }
    public function trazabilidad_consolidado()
    {

        $desde = !empty($_GET) ? $_GET["desde"] : "";
        $hasta = !empty($_GET) ? $_GET["hasta"] : "";
        $producto = !empty($_GET) ? $_GET["producto"] : "";
        $h = $_GET["h"];

        $datos = array(
            "producto" => $producto,
            "desde" => $desde,
            "hasta" => $hasta,
            "header" => $h
        );

        $model = new VentaModel();
        $vista["lista"] = $model->filtro_trazabilidad_consolidado($datos);
        $vista["filtro"] = $datos;

        return view('Trazabilidad_consolidado', $vista);
    }
    public function ventas()
    {
        $desde = !empty($_GET) ? $_GET["desde"] : "";
        $hasta = !empty($_GET) ? $_GET["hasta"] : "";
        $h = $_GET["h"];

        $datos = array(
            "desde" => $desde,
            "hasta" => $hasta,
            "header" => $h
        );

        $model = new VentaModel();
        $vista["lista"] = $model->filtro($datos);
        $vista["filtro"] = $datos;

        return view('Ventas', $vista);
    }
}
