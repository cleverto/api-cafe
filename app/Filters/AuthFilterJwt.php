<?php

namespace App\Filters;


use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthFilterJwt implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {

        $key = Services::getSecretKey();
        $authHeader=$request->getServer('HTTP_AUTHORIZATION');
		$parts        = explode(' ', $authHeader);
        try{
            if (isset($parts[0]) && isset($parts[1]) && $parts[0] === 'Bearer') {
                $token      = $parts[1];
            }else{
                $token      = $parts[0];
            }
            $data = JWT::decode($token, new Key($key, 'HS256'));
        
            session()->set('data',(array) $data->data);
        }catch(\Exception $e){
            return Services::response()->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {

        // Lógica después de la ejecución del controlador
    }
}
