<?php

namespace App\Controllers;

use App\Models\HomeModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }
    public function comments()
    {
        echo 'Look at this!';
    }
    function image($image = null)
    {


        if (!$image) {
            $image = $this->request->getGet('image');
        }

        // Verifica si los parámetros son válidos
        if ($image == '') {
            throw PageNotFoundException::forPageNotFound();
        }

        // Construye la ruta del archivo de imagen
        $name = WRITEPATH . 'uploads/' . $image;

        // Verifica si el archivo existe
        if (!file_exists($name)) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Obtiene la extensión del archivo
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        // Determina el tipo MIME en función de la extensión
        switch ($extension) {
            case 'jpg':

            case 'jpeg':
                $mimeType = 'image/jpeg';
                break;
            case 'png':
                $mimeType = 'image/png';
                break;
            default:
                throw PageNotFoundException::forPageNotFound(); // Tipo de archivo no soportado
        }

        // Abre el archivo en modo binario
        $fp = fopen($name, 'rb');

        // Envía las cabeceras correctas
        header("Content-Type: " . $mimeType);
        header("Content-Length: " . filesize($name));

        // Vuelca la imagen y detiene el script
        fpassthru($fp);
        exit;
    }
    function file($file = null)
    {

        if (!$file) {
            $file = $this->request->getGet('file');
        }

        // Verifica si los parámetros son válidos
        if ($file == '') {
            throw PageNotFoundException::forPageNotFound();
        }

        // Construye la ruta del archivo PDF
        $name = WRITEPATH . 'uploads/' . $file;


        // Verifica si el archivo existe
        if (!file_exists($name)) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Obtiene la extensión del archivo
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        // Determina el tipo MIME en función de la extensión
        if ($extension === 'pdf') {
            $mimeType = 'application/pdf';
        } else {
            throw PageNotFoundException::forPageNotFound(); // Tipo de archivo no soportado
        }

        // Abre el archivo en modo binario
        $fp = fopen($name, 'rb');

        // Envía las cabeceras correctas para PDF
        header("Content-Type: " . $mimeType);
        header("Content-Length: " . filesize($name));
        header("Content-Disposition: inline; filename=" . basename($name));

        // Vuelca el contenido del archivo PDF y detiene el script
        fpassthru($fp);
        exit;
    }
    public function lista_menu()
    {
        $do = "12";
        // if (!empty($_GET["do"])) {
        //     $do == $_GET["do"];
        // }
        $model = new HomeModel();
        $data = $model->lista_menu($do);
        $data_opcion = $model->lista_menu_opcion($do);

        $data = array('items' => $data, 'items_opcion'=> $data_opcion);
        return $this->response->setJSON($data);
    }
    public function lista_menu_opcion_by_perfil()
    {
        $data = json_decode(file_get_contents('php://input'));
       
        if (isset($data->perfil)) {
            $perfil = $data->perfil; 
           
            $model = new HomeModel();
            $permissions = $model->lista_menu_opcion_by_perfil($perfil); 

            $response = array('permissions' => $permissions);
            return $this->response->setJSON($response);
        } else {
            $response = array('error' => 'perfil no proporcionado');
            return $this->response->setJSON($response);
        }
    }
    public function menu_by_perfil()
    {
        //$data = json_decode(file_get_contents('php://input'));
        if (isset($_GET['m'])) {
            $menu = base64_decode($_GET['m']);

            if ($menu=="virtual"){                
                echo json_encode(array('permitido' => "true"));
                return false;
            }
        }
       
        if (isset(session()->get("data")["perfil"])) {
            $perfil =session()->get("data")["perfil"]; 
           
            $model = new HomeModel();
            $permiso = $model->menu_by_perfil($perfil, $menu); 
            $permitido = !empty($permiso); 

            $response = array('permitido' => $permitido);
            return $this->response->setJSON($response);
        } else {
            $response = array('error' => 'perfil no proporcionado');
            return $this->response->setJSON($response);
        }
    }
    public function lista_modulo()
    {
        $model = new HomeModel();
        $data = $model->lista_modulo();

        $data = array('items' => $data);
        return $this->response->setJSON($data);
    }
}
