<?php

namespace App\Models;

use CodeIgniter\Model;

class FuncionesModel extends Model
{
   public function get_dni_sunat($data)
   {

      $url = "https://ww1.sunat.gob.pe/ol-ti-itfisdenreg/itfisdenreg.htm?accion=obtenerDatosDni&numDocumento=" . $data->nro;

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE); //esto se añadio en relacion a los demas **
      curl_setopt($ch, CURLOPT_HEADER, 0); //esto se añadio en relacion a los demas
      $headers = array(
         'Content-Type: application/json'
      );
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);  //esto se añadio en relacion a los demas
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 3); //esto se añadio en relacion a los demas
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //esto se añadio en relacion a los demas (con esto solito funciona, se dejo con los demas, porque así se encontro el codigo)
      //https://www.it-swarm.dev/es/php/como-usar-curl-en-lugar-de-file-get-contents/941144620/
      $result = curl_exec($ch);

      curl_close($ch);


      $data = json_decode($result, true);

      if ($data && isset($data['lista'])) {
         foreach ($data['lista'] as $persona) {
            $nombre = $persona['nombresapellidos'];
         }
         $partes = explode(',', $nombre);
         if (count($partes) === 2) {
            $apellidos = trim($partes[0]);
            $nombres = trim($partes[1]);

            // Separar los apellidos
            $apellidosArray = explode(' ', $apellidos);
            $apellidoPaterno = $apellidosArray[0] ?? '';
            $apellidoMaterno = $apellidosArray[1] ?? '';


            $data = array(
               'origen' => "0",
               'nombres' => $nombres,
               'paterno' => $apellidoPaterno,
               'materno' => $apellidoMaterno,
            );
            return $data;
         } else {
            return "";
         }
      } else {
         return "";
      }
   }
   public function get_dni_externo($data)
   {
      //https://dniruc.apisperu.com/api/v1/dni/12345678?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6Im1hY2V2YTQyOEBnbWFpbC5jb20ifQ.F3tbDYl0PiXqSCKPpifrF0iEk5a86QPNoJcRsMel2Bc
      $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6Im1hY2V2YTQyOEBnbWFpbC5jb20ifQ.F3tbDYl0PiXqSCKPpifrF0iEk5a86QPNoJcRsMel2Bc";
      $link = 'https://dniruc.apisperu.com/api/v1/' . $data->tipo . '/';
      $nro = $data->nro;
      $url = $link . $nro . "?token=" . $token;
      //echo $url;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $json = curl_exec($ch);
      curl_close($ch);

      if ($json === false || empty($json)) {
         $data = [];
         return $data;
      } else {
         $datos = json_decode($json, true);
         if (json_last_error() !== JSON_ERROR_NONE) {
            // Manejar el error de decodificación JSON
            $data = [];
            return $data;
         } elseif (empty($datos['success']) || $datos['success'] === false) {
            $data = [];
            return $data;
         } else {
            $datos = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
            }
            $datos = json_decode($json, true);
            $nombres = $datos["nombres"];
            $paterno = $datos["apellidoPaterno"];
            $materno = $datos["apellidoMaterno"];

            $data = array(
               'origen' => "0",
               'nombres' => $nombres,
               'paterno' => $paterno,
               'materno' => $materno
            );

            return $data;
         }
      }
   }
   public function get_ingreso_by_dni($data)
   {
      $builder = $this->db->table("adm_proceso_postulante a");
      $builder->select("COUNT(*) AS cantidad");
      $builder->where('a.numerodocumento',  $data->nro);
      $builder->where('a.ingresante',  "2");
      $builder->orderBy('a.proceso');
      $builder->having('COUNT(*) >', 0);
      $query = $builder->get();
      $row = $query->getRow(0);
      return  $row;
   }
   public function get_ingresos_by_dni($data)
   {
      $builder = $this->db->table("adm_proceso_postulante a");
      $builder->select("p.descripcion");
      $builder->join('adm_proceso p',  "p.proceso = a.proceso", 'inner');
      $builder->where('a.numerodocumento',  $data->nro);
      $builder->where('a.ingresante',  "2");
      $builder->orderBy('a.proceso');
      $builder->having('COUNT(*) >', 0);
      $query = $builder->get();
      return  $query->getResultArray();;
   }

   public function lista_departamento()
   {
      $sql = "SELECT DISTINCT departamento, dep FROM ubigeo order by dep";
      $query = $this->db->query($sql);
      return  $query->getResultArray();;
   }
   public function lista_provincia($departamento)
   {
      $sql = "SELECT DISTINCT provincia, pro FROM ubigeo WHERE departamento='" . $departamento . "' order by pro";
      $query = $this->db->query($sql);
      return  $query->getResultArray();;
   }
   public function lista_distrito($departamento, $provincia)
   {
      $sql = "SELECT distrito, dis FROM ubigeo WHERE departamento='" . $departamento . "' AND provincia='" . $provincia . "' order by dis";
      $query = $this->db->query($sql);
      return  $query->getResultArray();;
   }

}
