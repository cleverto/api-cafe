<?php

namespace App\Filters;

use App\Models\FuncionesModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;

class UrlFilter implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {
        $currentUrl = $request->uri->getPath();
        $url =   str_replace('/admision/index.php', '', $currentUrl);

        if (session()->has('data')) {
           
            // $model = new FuncionesModel();
            // $t = $model->valida_url($url, session()->get("data")["perfil"]);
         
            // if ($t !== "1") {
            //     //return Services::response()->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            // }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {

        // Lógica después de la ejecución del controlador
    }
}
