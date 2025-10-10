<?php


namespace App\Controllers;

use App\Controllers\BaseController;


class SecadoRetorno extends BaseController
{




    public function guardar()
    {
        $post = json_decode(file_get_contents('php://input'), true);

   print_r($post["rowdata"]);
        //return $this->response->setJSON($rpta);
    }

}
