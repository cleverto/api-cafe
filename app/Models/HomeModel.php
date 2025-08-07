<?php

namespace App\Models;

use CodeIgniter\Model;

class HomeModel extends Model
{
    public function lista_menu($id)
    {
        $builder = $this->db->table('men_menuprincipal a');
        $builder->select('a.dominio, a.menu');
        $builder->select('a.nombre as desc_menu');
        $builder->where('a.dominio', $id);
        $builder->orderBy('a.orden');
        $query = $builder->get();
        return $query->getResultArray();
    }
    public function lista_menu_opcion($id)
    {

        $sql = "SELECT DISTINCT  rp.perfil, d.dominio, d.nombre, mp.menu, mp.nombre, CONCAT(o.dominio,o.menu,o.opcion) as cod_opcion, o.nombre as desc_opcion,";
        $sql .= " o.url, o.url, o.url2, mp.orden, o.orden";
        $sql .= " FROM men_perfilopcion rp";
        $sql .= " INNER JOIN men_opciones o ON o.dominio=rp.dominio AND o.menu=rp.menu AND o.opcion=rp.opcion";
        $sql .= " INNER JOIN men_menuprincipal mp ON mp.dominio=o.dominio AND mp.menu=o.menu";
        $sql .= " INNER JOIN men_dominio d ON d.dominio=mp.dominio";
        $sql .= " WHERE rp.perfil = :perfil: ";
        $sql .= " AND d.dominio = :domninio: ";
        $sql .= " AND o.activo=1";
        $sql .= " AND mp.activo=1";
        $sql .= " AND d.activo=1";
        $sql .= " AND o.vigente=1";
        $sql .= " ORDER BY o.orden";

        $query = $this->db->query($sql, [
            'perfil' => session()->get("data")["perfil"],
            'domninio' =>  $id,
        ]);


        return $query->getResultArray();
    }
    public function lista_menu_opcion_by_perfil()
    {

        $sql = "SELECT DISTINCT  o.url2";
        $sql .= " FROM men_perfilopcion rp";
        $sql .= " INNER JOIN men_opciones o ON o.dominio=rp.dominio AND o.menu=rp.menu AND o.opcion=rp.opcion";
        $sql .= " INNER JOIN men_menuprincipal mp ON mp.dominio=o.dominio AND mp.menu=o.menu";
        $sql .= " INNER JOIN men_dominio d ON d.dominio=mp.dominio";
        $sql .= " WHERE rp.perfil = :perfil: ";
        $sql .= " AND o.activo=1";
        $sql .= " AND mp.activo=1";
        $sql .= " AND d.activo=1";
        $query = $this->db->query($sql, [
            'perfil' => session()->get("data")["perfil"],
        ]);


        return $query->getResultArray();
    }

    public function menu_by_perfil($perfil, $menu)
    {

        $sql = "SELECT DISTINCT  o.url2";
        $sql .= " FROM men_perfilopcion rp";
        $sql .= " INNER JOIN men_opciones o ON o.dominio=rp.dominio AND o.menu=rp.menu AND o.opcion=rp.opcion";
        $sql .= " INNER JOIN men_menuprincipal mp ON mp.dominio=o.dominio AND mp.menu=o.menu";
        $sql .= " INNER JOIN men_dominio d ON d.dominio=mp.dominio";
        $sql .= " WHERE rp.perfil = :perfil: ";
        $sql .= " and o.url2 = :url2: ";
        $sql .= " AND o.activo=1";
        $sql .= " AND mp.activo=1";
        $sql .= " AND d.activo=1";
        $query = $this->db->query($sql, [
            'perfil' => $perfil,
            'url2' => $menu,
        ]);


        return $query->getResultArray();

        
    }
    public function lista_modulo()
    {
        $builder = $this->db->table('men_dominio a');
        $builder->distinct();
        $builder->select(' a.dominio, a.nombre as desc_dominio');
        $builder->join('men_perfilopcion b', 'b.dominio = a.dominio', 'inner');
        $builder->where('b.perfil', session()->get("data")["perfil"]);
        $builder->where('a.activo', '1');
        $query = $builder->get();

        return $query->getResultArray();
    }
}
