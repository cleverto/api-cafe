<?php
function calcular_cese($fecha_ingreso, $fecha_fin = null)
{
  $ingreso = new DateTime($fecha_ingreso);

  if ($fecha_fin) {
    $fin = new DateTime($fecha_fin);
    $intervalo = $ingreso->diff($fin);

    $años = str_pad($intervalo->y, 2, "0", STR_PAD_LEFT);
    $meses = str_pad($intervalo->m, 2, "0", STR_PAD_LEFT);
    $dias = str_pad($intervalo->d, 2, "0", STR_PAD_LEFT);

    return "$años AÑOS $meses MESES $dias DÍAS";
  } else {
    return "LABORA A LA ACTUALIDAD";
  }
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Ficha de Datos - Docente</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 13px;
      margin: 0;
      padding: 0;
    }

    .container {
      width: 794px;
      max-width: 100%;
      box-sizing: border-box;
    }

    .header {
      text-align: center;
      margin-bottom: 15px;
    }

    .header small {
      display: block;
      font-size: 14px;
    }

    .title {
      font-size: 14px;
      font-weight: bold;
      color: #1c55a1;
    }

    .title2 {
      font-size: 16px;
      font-weight: bold;
      color: #1c55a1;
    }

    .subtitle {
      font-size: 12px;
      font-weight: bold;
      margin-top: 4px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
    }

    th {
      text-align: left;
      font-size: 12px;
      color: #1c55a1;
      padding: 6px 4px;
      border-bottom: 1px solid #000;
    }

    .borde-inferior {
      border-bottom: 1px solid #000;
    }

    td {
      padding: 2px;
      vertical-align: top;
    }

    .list-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .title-list-item {
      font-weight: bold;
    }

    .info-list-item {
      display: flex;
      flex-direction: column;

    }

    .container-row {
      display: flex;
      width: 100%;
      /* 100% del ancho de la pantalla */
    }

    .section-container {
      display: flex;
      flex-direction: column;
    }

    .box {
      width: 100%;
      padding-right: 15px;
    }

    .box-right {
      width: 40%;
      padding-right: 15px;
    }

    .header-right {
      display: flex;
      justify-content: flex-end;
    }

    .name-trabajador {
      font-size: 1.50rem;
      font-weight: 300;

      line-height: 1.2;
      font-family: 'Arial', sans-serif;
    }

    .estilo-unj {
      text-align: center;
      font-weight: bold;
      text-transform: uppercase;
      font-family: 'Arial', sans-serif;
      font-size: 16px;
      /* Puedes ajustarlo según el tamaño que desees */
      line-height: 1.5;
    }
  </style>
</head>

<body>
  <div class="container">

    <div class="container-row" style="margin-bottom:25px;">
      <div class="box" style="width: 190px;">
        <img src="<?= base_url('/escalafon/public/escudo.png'); ?>" alt="Cabecera" border="0" width="100px" />
      </div>
      <div class="box" style="text-align: center; margin-top:15px">
        <div class="estilo-unj">
          UNIVERSIDAD NACIONAL DE JAÉN<br>
          <span style="text-transform: none;">
            Resolución del Consejo Directivo N°002-2018-SUNEDU/CD
          </span><br>
          UNIDAD DE RECURSOS HUMANOS
        </div>
        ESCALAFON
        <br>

      </div>
      <div class="box " style="width: 190px;">
        <div class="header-right">
          <img src="<?= base_url('/escalafon/public/logo.png'); ?>" alt="Cabecera" border="0" width="140px" />
        </div>

      </div>
    </div>

    <div class="header">
      <small>REPORTE ESCALAFONARIO Nº10 <?= date("Y") ?></small>
    </div>

    <div class="container-row">
      <div class="box">
        <div class="section-container">
          <table>
            <tr>
              <th colspan="3"></th>
            </tr>
            <tr>
              <td style="width: 190px;"><strong>DNI</strong></td>
              <td style="width: 18px;">:</td>
              <td><?= $lista["dni"] ?></td>
            </tr>
            <tr>
              <td><strong>Apellidos y nombres</strong></td>
              <td>:</td>
              <td><?= $lista["trabajador"] ?></td>
            </tr>
            <tr>
              <td><strong>Sexo</strong></td>
              <td>:</td>
              <td><?= $lista["sexo"] == "F" ? "MASCULINO" : "FEMENINO" ?></td>
            </tr>
            <tr>
              <td><strong>Teléfono</strong></td>
              <td>:</td>
              <td><?= $lista["celular"] ?></td>
            </tr>
            <tr>
              <td><strong>Email</strong></td>
              <td>:</td>
              <td><?= $lista["correo"] ?></td>
            </tr>
            <tr>
              <td><strong>Domicilio</strong></td>
              <td>:</td>
              <td><?= $lista["domicilio"] ?></td>
            </tr>
          </table>
        </div>
        <div class="section-container">
          <table>
            <tr>
              <th colspan="3" style="text-align: center">FORMACIÓN PROFESIONAL</th>
            </tr>
          </table>

          <?php
          foreach ($lista_academica as $row) { ?>
            <table style="margin-bottom:0px">

              <tr>
                <td style="width: 190px;"><strong>Grado</strong></td>
                <td style="width: 18px;">:</td>
                <td><?= $row["grado"] ?></td>
              </tr>
              <tr>
                <td><strong>Título obtenido</strong></td>
                <td>:</td>
                <td><?= $row["carrera"] ?></td>
              </tr>
              <tr>
                <td><strong>Institución</strong></td>
                <td>:</td>
                <td><?= $row["institucion"] ?></td>
              </tr>
              <tr>
                <td><strong>Nro de colegiatura</strong></td>
                <td>:</td>
                <td><?= $row["colegiatura"] ?></td>
              </tr>
            </table>
          <?php } ?>
        </div>
        <div class="section-container">
          <table>
            <tr>
              <th colspan="3" style="text-align: center">CICLO LABORAL</th>
            </tr>
            <tr>
              <td style="width: 190px;"><strong>Ingreso</strong></td>
              <td style="width: 18px;">:</td>
              <td><?= isset($lista_vinculo) ?  $lista_vinculo["ingreso"] : "" ?></td>
            </tr>
            <tr>
              <td><strong>Remuneración</strong></td>
              <td>:</td>
              <td><?= isset($lista_vinculo) ? $lista_vinculo["remuneracion"] : "" ?></td>
            </tr>
            <tr>
              <td><strong>Contrato</strong></td>
              <td>:</td>
              <td><?= isset($lista_vinculo) ? $lista_vinculo["tipo_contrato"] : "" ?></td>
            </tr>
            <tr>
              <td><strong>Dependencia</strong></td>
              <td>:</td>
              <td><?= isset($lista_vinculo) ? $lista_vinculo["dependencia"] : "" ?></td>
            </tr>
            <tr>
              <td><strong>Régimen Laboal</strong></td>
              <td>:</td>
              <td><?= isset($lista_vinculo) ? $lista_vinculo["dependencia"] : "" ?></td>
            </tr>
            <tr>
              <td><strong>Tipo resolución</strong></td>
              <td>:</td>
              <td><?= isset($lista_vinculo) ? $lista_vinculo["tipo_resolucion"] : "" ?></td>
            </tr>
            <tr>
              <td><strong>Fecha resolución</strong></td>
              <td>:</td>
              <td><?= isset($lista_vinculo) ? $lista_vinculo["fecha_resolucion"] : "" ?></td>
            </tr>
            <tr>
              <td><strong>Fecha de cese</strong></td>
              <td>:</td>
              <td><?= isset($lista_vinculo) ? calcular_cese($lista_vinculo["ingreso"], $lista_vinculo["fin"]) : "" ?></td>
            </tr>
          </table>
        </div>
       
          <table>
            <tr>
              <th colspan="3" style="text-align: center">DESIGNACIONES Y/O ENCARGATURAS</th>
            </tr>
          </table>

          <?php
          foreach ($lista_designacion as $fila) {
          ?>
            <table>
              <tr>
                <td style="width: 190px;"><strong>Resolución y/o Documento</strong></td>
                <td style="width: 18px;">:</td>
                <td><?= isset($fila["resolucion"]) ?  $fila["resolucion"] : "" ?></td>
              </tr>
              <tr>
                <td><strong>Cargo</strong></td>
                <td>:</td>
                <td><?= isset($fila["cargo"]) ?  $fila["cargo"] : "" ?></td>
              </tr>
              <tr>
                <td><strong>Dependencia</strong></td>
                <td>:</td>
                <td><?= isset($fila["dependencia"]) ?  $fila["dependencia"] : "" ?></td>
              </tr>
              <tr>
                <td><strong>Fecha de Inicio</strong></td>
                <td>:</td>
                <td><?= isset($fila["inicio"]) ?  $fila["inicio"] : "" ?></td>
              </tr>
              <tr>
                <td><strong>Fecha de cese</strong></td>
                <td>:</td>
                <td><?= isset($fila["fin"]) ? $fila["fin"] : "NO ESPECIFICA" ?></td>
              </tr>

            </table>

          <?php

            if ($fila !== end($lista_designacion)) {
              echo '<hr style="margin: 0px; padding:0px; margin-top:5px;margin-bottom:5px;">';
            }
          }
          ?>
       
       
          <table>
            <tr>
              <th colspan="3" style="text-align: center">ROTACIONES TEMPORALES</th>
            </tr>
          </table>

          <?php
          foreach ($lista_rotacion as $fila) {
          ?>
            <table>
              <tr>
                <td style="width: 190px;"><strong>Resolución y/o Documento</strong></td>
                <td style="width: 18px;">:</td>
                <td><?= isset($fila["resolucion"]) ?  $fila["resolucion"] : "" ?></td>
              </tr>
              <tr>
                <td><strong>Dependencia</strong></td>
                <td>:</td>
                <td><?= isset($fila["dependencia"]) ?  $fila["dependencia"] : "" ?></td>
              </tr>
              <tr>
                <td><strong>Fecha de Inicio</strong></td>
                <td>:</td>
                <td><?= isset($fila["inicio"]) ?  $fila["inicio"] : "" ?></td>
              </tr>
              <tr>
                <td><strong>Fecha de cese</strong></td>
                <td>:</td>
                <td><?= isset($fila["fin"]) ? $fila["fin"] : "NO ESPECIFICA" ?></td>
              </tr>

            </table>

          <?php
            if ($fila !== end($lista_rotacion)) {
              echo '<hr style="margin: 0px; padding:0px; margin-top:5px;margin-bottom:5px;">';
            }
          }
          ?>
        
      </div>

    </div>


  </div>
</body>

</html>