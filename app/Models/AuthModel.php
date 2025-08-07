<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthModel extends Model
{

    public function auth($data)
    {
        //$token=sha1($data->password);
        //$sql = "CALL zoz_Val(?, ?, ?, ?, ?)";
        //$query = $this->db->query($sql, [$data->usuario, $data->password, $data->nivel,"",""]);
        //$row = $query->getRow();

        $builder = $this->db->table("usuario a");
        $builder->select("a.id_usuario, a.id_perfil, a.email");
        $builder->select("p.perfil, a.usuario, ");
        $builder->join('perfil p', 'p.id_perfil = p.id_perfil', 'inner');
        $builder->where('a.email', $data->usuario);
        $builder->where('a.password', md5($data->password));
        $builder->where('a.activo',  "1");
        $query = $builder->get();
        $row = $query->getRow(0);
        return  $row;
    }

    public function setToken($id, $jwt)
    {
        $datos = array(
            'token' => $jwt
        );
        $db = $this->db->table('mae_persona');
        $db->where('persona', $id);
        $db->update($datos);
    }
    public function setCambiar($data)
    {
        $datos = array(
            'password' => md5($data->newPassword)
        );
        $db = $this->db->table('usuario');
        $db->where('email', $data->usuario);
        $db->update($datos);
    }
}
