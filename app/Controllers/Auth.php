<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AuthModel;
use CodeIgniter\RESTful\ResourceController;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth extends ResourceController
{

    protected $format = 'json';

    public function auth()
    {
        $data = json_decode(file_get_contents('php://input'));

        $msg = " El usuario o password son incorrectos";
        if ($data->usuario == '' or $data->password == '') {
            $data = array('rpta' => '0', 'icon' => "error", 'msg' => $msg);
            return $this->response->setJSON($data);
        }
        $model = new AuthModel();
        $row = $model->auth($data);

        if (isset($row)) {

            $datos = [
                'id_usuario' => $row->id_usuario,
                'usuario' => $row->usuario,
                'email' => $row->email,
                'perfil' => $row->perfil,
                'id_perfil' => $row->id_perfil,
            ];
            session()->set('data', (array) $datos);


            $time = time();
            $key = Services::getSecretKey();
            $payload = [
                'iat' => $time,
                'exp' => $time + 36000, //1hora
                'data' => $datos,
            ];


            $jwt = JWT::encode($payload, $key, 'HS256');
            //$model->setToken($row->persona, $jwt);
            $datos['token'] = $jwt;
            $rpta = array('rpta' => '1', 'icon' => "info", 'msg' => 'Bienvenido', 'data' => $datos);

            return $this->respond($rpta, 200);
        }
        $rpta = array('rpta' => '0', 'icon' => "info", 'msg' => 'Datos de login invalidos');
        return $this->respond($rpta, 200);
    }
    public function cambiar()
    {
        $data = json_decode(file_get_contents('php://input'));
        $msg = " El usuario o password son incorrectos";
        $array = [
            "usuario" => session()->get("data")["email"],
            "password" => $data->password,
            "newPassword" => $data->newPassword,
            "confirmPassword" => $data->confirmPassword,
        ];

        $data = (object) $array;
        if ($data->newPassword == '' or $data->confirmPassword == '' or $data->password == '') {
            $data = array('rpta' => '0', 'icon' => "error", 'msg' => $msg);
            return $this->response->setJSON($data);
        }

        $model = new AuthModel();
        $row = $model->auth($data);
        if (isset($row)) {
            $model->setCambiar($data);

            $rpta = array('rpta' => '1', 'icon' => "info", 'msg' => 'Su contraseña ha sido cambiada');
        } else {
            $rpta = array('rpta' => '0', 'icon' => "error", 'msg' => 'Su contraseña es incorrecta');
        }
        return $this->response->setJSON($rpta);
    }
    protected function validateToken($token)
    {
        try {
            $key = Services::getSecretKey();
            return JWT::decode($token, new Key($key, 'HS256'));
        } catch (\Exception $e) {
            return false;
        }
    }

    public function verifyToken()
    {
        $key = Services::getSecretKey();

        //$token=$this->request->getPost("token");
        $data = json_decode(file_get_contents('php://input'));

        if ($this->validateToken($data->token) == false) {
            return $this->respond(['msg' => 'Token invalido'], 401);
        } else {
            $data = JWT::decode($data->token, new Key($key, 'HS256'));
            return $this->respond(['msg' => $data], 200);
        }
    }
    public function version(){

    }
}
