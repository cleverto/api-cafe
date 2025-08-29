<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FuncionesModel;
use App\Models\ProveedorModel;

class Funciones extends BaseController
{

    public function get_nombre()
    {
        $post = json_decode(file_get_contents('php://input'));

        if ($post->tipo == "dni") {
            $model = new ProveedorModel();
            $row = $model->get_by_dni($post->nro);
            if (isset($row)) {
                $data = array(
                    'id_proveedor' => $row->id_proveedor,
                    'nombrecompleto' => $row->proveedor,
                );
                $rpta = array('rpta' => '1', 'items' => $data);
                return $this->response->setJSON($rpta);
            }
        }
        $model = new FuncionesModel();
        $data = $model->get_dni_externo($post);
        if (empty($data)) {
            $data = $model->get_dni_sunat($post);
        }

        if (!empty($data)) {
            $datos = array(
                'id_ubigeo' => "060801",
                'id_identidad' => "1",
                'dni' => $post->nro,
                'proveedor' => $data["nombrecompleto"],
                'tipo' => "0",
                'direccion' => "",
                'telefono' => "",
            );
            $model_proveedor = new ProveedorModel();
            $id = $model_proveedor->guardar($datos);

            $data["id_proveedor"] =  $id;
        }

        $rpta = array('rpta' => '1', 'items' => $data);
        return $this->response->setJSON($rpta);
    }
    public function get_solo_nombre()
    {
        $post = json_decode(file_get_contents('php://input'));

        if ($post->tipo == "dni") {
            $model = new ProveedorModel();
            $row = $model->get_by_dni($post->nro);
            if (isset($row)) {
                $data = array(
                    'id_proveedor' => $row->id_proveedor,
                    'nombrecompleto' => $row->proveedor,
                );
                $rpta = array('rpta' => '1', 'items' => $data);
                return $this->response->setJSON($rpta);
            }
        }
        $model = new FuncionesModel();
        $data = $model->get_dni_externo($post);
        if (empty($data)) {
            $data = $model->get_dni_sunat($post);
        }

        $rpta = array('rpta' => '1', 'items' => $data);
        return $this->response->setJSON($rpta);
    }
    public function get_dni_externo()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new FuncionesModel();
        $datos = $model->get_dni_externo($data);
        $data = array('rpta' => 'true', 'items' => $datos);
        echo json_encode($data);
    }
    public function get_ingreso_by_dni()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new FuncionesModel();
        $dato = $model->get_ingreso_by_dni($data);
        $datos = $model->get_inresos_by_dni($data);

        $t = "0";
        if ($dato !== null) {
            $t = $dato->cantidad;
        }
        $data = array('items' => $t, 'items2' => $datos);
        echo json_encode($data);
    }

    public function buscar_ubigeo()
    {
        $post = json_decode(file_get_contents('php://input'));
        $model = new FuncionesModel();
        $data = $model->buscar_ubigeo($post);
        $rpta = array('rpta' => '1', 'items' => $data);
        return $this->response->setJSON($rpta);
    }
    public function valida_url()
    {
        $data = json_decode(file_get_contents('php://input'));
        $model = new FuncionesModel();
        $t = $model->valida_url($data->url, $data->perfil);
        $data = array('data' => $t);
        echo json_encode($data);
    }
    public function get_ubigeo()
    {
        $data = json_decode(file_get_contents('php://input'));
        if (isset($data->ubigeo) && strlen($data->ubigeo) !== 6) {
            $rpta = array('rpta' => "0");
            echo json_encode($rpta);
            return false;
        }

        $model = new FuncionesModel();
        $items = $model->get_ubigeo($data);


        $data = array('rpta' => '1', 'items' => $items);
        echo json_encode($data);
    }


    public function lista_departamento()
    {
        $model = new FuncionesModel();
        $data = $model->lista_departamento();
        $rpta = array('rpta' => '1', 'items' => $data);
        return $this->response->setJSON($rpta);
    }
    public function lista_provincia()
    {
        $data = json_decode(file_get_contents('php://input'));
        $departamento = $data->departamento;

        $model = new FuncionesModel();
        $data = $model->lista_provincia($departamento);
        $rpta = array('rpta' => '1', 'items' => $data);
        return $this->response->setJSON($rpta);
    }
    public function lista_distrito()
    {
        $data = json_decode(file_get_contents('php://input'));
        $departamento = $data->departamento;
        $provincia = $data->provincia;

        $model = new FuncionesModel();
        $data = $model->lista_distrito($departamento, $provincia);

        $rpta = array('rpta' => '1', 'items' => $data);
        return $this->response->setJSON($rpta);
    }
    //     public function upload_pdf(&$post_file, $datos)
    //     {
    //           print_r($datos);
    //         print_r($post_file["file"]["name"]);
    // return false;

    //         $file = (isset($post_file["file"][$datos])) ? $post_file["file"][$datos] : null;
    //         if (!$file) {
    //             $data = array('rpta' => '0', 'msg' => 'No ha seleccionado el documento adjunto para casados');
    //             return $data;
    //         }
    //         if ($datos == "file_casado") {
    //             $ruta = WRITEPATH . "uploads/casado/";
    //         } else if ($datos == "file_conviviente") {
    //             $ruta = WRITEPATH . "uploads/conviviente/";
    //         }


    //         $ruta_nombre = $ruta . $nombre . ".pdf";
    //         $res = $this->copiar_pdf($ruta, $ruta_nombre, $file);
    //         if ($res === 0) {
    //             $data = array('rpta' => '0', 'msg' => "Error en " . $datos);
    //             return $data;
    //         }
    //         $data = array('rpta' => '1');
    //         return $data;
    //     }
    //     public function copiar_pdf($ruta, $ruta_nombre, $archivo_voucher)
    // 	{

    // 		$copied = copy($archivo_voucher['tmp_name'], $ruta_nombre);
    // 		if ($copied) {
    // 			$copied = 1;
    // 		} else {
    // 			$copied = 0;
    // 		}

    // 		return $copied;
    // 	}

    public function upload_pdf(&$post_file, $dato)
    {


        $file = (isset($post_file[$dato["id_requisito"]])) ? $post_file[$dato["id_requisito"]] : null;
        if (!$file) {
            $data = array('rpta' => '0', 'msg' => 'Por favor, no ha seleccionado  ' . $dato["id_requisito"]);
            echo json_encode($data);
            return false;
        }

        if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'unj.edu.pe') !== false) {
            $ruta = WRITEPATH . "uploads/docs/";
        } else {
            $ruta = WRITEPATH . "uploads//docs//";
        }

        $ruta_nombre = $ruta . $dato["dni"] . "-" . $dato["id_requisito"] . ".pdf";

        $res = $this->copiar_pdf($ruta_nombre, $file);
        if ($res === 0) {
            $data = array('rpta' => '0', 'msg' => "Error en " . $dato["id_requisito"]);
            return $data;
        }

        $data = array('rpta' => '1');
        return $data;
    }
    public function copiar_pdf($ruta_nombre, $archivo_voucher)
    {

        $copied = copy($archivo_voucher['tmp_name'], $ruta_nombre);
        if ($copied) {
            $copied = 1;
        } else {
            $copied = 0;
        }

        return $copied;
    }
}
