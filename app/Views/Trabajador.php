<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Ficha de Datos - Docente</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 11px;
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
      width: 60%;
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
  </style>
</head>

<body>
  <div class="container">

    <div class="container-row" style="margin-bottom:25px;">
      <div class="box">
        <img src="<?= base_url('/escalafon/public/escudo-cabecera.png'); ?>" alt="Cabecera" border="0" width="350px" />
      </div>
      <div class="box">
        <div class="header-right">
          <span> UNIDAD DE RECURSOS HUMANOS<br>
        </div>
        <div class="header-right">
          <span> ESCALAFON</span>
        </div>
      </div>
    </div>

    <div class="header">
      <small>“Decenio de la Igualdad de Oportunidad para Mujeres y Hombres Año del Bicentenario [...] Junín y Ayacucho”</small>

      <div class="name-trabajador"><?= $lista["trabajador"] ?></div>
      <div class="title2">FICHA DE DATOS - <?= $lista["tipo_trabajador"] ?></div>
      <br>
    </div>

    <div class="container-row">
      <div class="box">
        <div class="section-container">
          <table>
            <tr>
              <th colspan="3">DATOS DEL TRABAJADOR</th>
            </tr>
            <tr>
              <td style="width: 110px;"><strong>DNI</strong></td>
              <td style="width: 14px;">:</td>
              <td><?= $lista["dni"] ?></td>
            </tr>
            <tr>
              <td><strong>Apellido Paterno</strong></td>
              <td>:</td>
              <td><?= $lista["paterno"] ?></td>
            </tr>
            <tr>
              <td><strong>Apellido Materno</strong></td>
              <td>:</td>
              <td><?= $lista["materno"] ?></td>
            </tr>
            <tr>
              <td><strong>Nombre</strong></td>
              <td>:</td>
              <td><?= $lista["nombres"] ?></td>
            </tr>
            <tr>
              <td><strong>Sexo</strong></td>
              <td>:</td>
              <td><?= $lista["sexo"] == "F" ? "MASCULINO" : "FEMENINO" ?></td>
            </tr>
            <tr>
              <td><strong>Fecha Nacimiento</strong></td>
              <td>:</td>
              <td><?= $lista["nacimiento"] != "0000-00-00" ? $lista["nacimiento"] : "" ?></td>
            </tr>
            <tr>
              <td><strong>Estado Civil</strong></td>
              <td>:</td>
              <td><?= $lista["id_estado_civil"] == 1 ? "SOLTERO" : "CASADO" ?></td> <!-- Ejemplo -->
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
              <td><strong>Dirección</strong></td>
              <td>:</td>
              <td><?= $lista["domicilio"] ?></td>
            </tr>
          </table>
        </div>
        <div class="section-container">
          <table>
            <tr>
              <th colspan="3">FORMACIÓN ACADÉMICA</th>
            </tr>
          </table>

          <?php
          foreach ($lista_academica as $row) { ?>
            <table style="margin-bottom:0px">

              <tr>
                <td style="width: 110px;"><strong>Grado</strong></td>
                <td style="width: 14px;">:</td>
                <td><?= $row["grado"] ?></td>
              </tr>
              <tr>
                <td><strong>Título obtenido</strong></td>
                <td style="width: 14px;">:</td>
                <td><?= $row["carrera"] ?></td>
              </tr>
              <tr>
                <td><strong>Institución</strong></td>
                <td>:</td>
                <td><?= $row["institucion"] ?></td>
              </tr>
              <tr>
                <td><strong>Año de egresado</strong></td>
                <td>:</td>
                <td><?= $row["egresado"] ?></td>
              </tr>
              <tr>
                <td><strong>Nro de colegiatura</strong></td>
                <td>:</td>
                <td><?= $row["colegiatura"] ?></td>
              </tr>
            </table>
            <hr style="margin: 0px; padding:0px; margin-top:5px;margin-bottom:5px;">
          <?php } ?>
        </div>
      </div>
      <div class="box-right">
        <div class="section-container">
          <table>
            <tr>
              <th colspan="3">Registro</th>
            </tr>
            <tr>
              <td style="width: 110px;"><strong>Regis ARIRHSP</strong></td>
              <td style="width: 14px;">:</td>
              <td><?= $lista["arirhsp"] ?></td>
            </tr>
            <tr>
              <td><strong>SIAF</strong></td>
              <td>:</td>
              <td colspan="2"><?= $lista["rdp"] ?></td>
            </tr>
            <tr>
              <td><strong>SISPER</strong></td>
              <td>:</td>
              <td colspan="2"><?= $lista["sisper"] ?></td>
            </tr>
            <tr>
              <td><strong>T-REGISTRO</strong></td>
              <td>:</td>
              <td colspan="2"><?= $lista["tregistro"] ?></td>
            </tr>
            <tr>
              <td><strong>SIGA</strong></td>
              <td>:</td>
              <td colspan="2"><?= $lista["siga"] ?></td>
            </tr>
          </table>
        </div>
        <?php
        if ($lista["id_tipo_trabajador"] == "1") {
        ?>
          <div class="section-container">
            <table>
              <tr>
                <th colspan="3">Vínculo Laboral</th>
              </tr>
              <tr>
                <td style="width: 110px;"><strong>Ingreso</strong></td>
                <td style="width: 14px;">:</td>
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
                <td><strong>Horas</strong></td>
                <td>:</td>
                <td><?= isset($lista_vinculo) ? $lista_vinculo["horas"] : "" ?></td>
              </tr>
              <tr>
                <td><strong>Departamento</strong></td>
                <td>:</td>
                <td><?= isset($lista_vinculo) ? $lista_vinculo["departamento_academico"] : "" ?></td>
              </tr>
              <tr>
                <td><strong>Nro de plaza</strong></td>
                <td>:</td>
                <td><?= isset($lista_vinculo) ? $lista_vinculo["nro_plaza"] : "" ?></td>
              </tr>
              <tr>
                <td><strong>Tipo resolución</strong></td>
                <td>:</td>
                <td><?= isset($lista_vinculo) ? $lista_vinculo["tipo_resolucion"] : "" ?></td>
              </tr>
              <tr>s
                <td><strong>Fecha resolución</strong></td>
                <td>:</td>
                <td><?= isset($lista_vinculo) ? $lista_vinculo["fecha_resolucion"] : "" ?></td>
              </tr>
            </table>
          </div>
        <?php
        }
        ?>
        <?php
       
          if ($lista["id_tipo_trabajador"] == "2" || $lista["id_tipo_trabajador"] == "3") {

        ?>
            <div class="section-container">
              <table>
                <tr>
                  <th colspan="3">Vínculo Laboral</th>
                </tr>
                <tr>
                  <td style="width: 110px;"><strong>Ingreso</strong></td>
                  <td style="width: 14px;">:</td>
                  <td><?= isset($lista_vinculo) ? $lista_vinculo["ingreso"] : ""?></td>
                </tr>
                <tr>
                  <td><strong>Remuneración</strong></td>
                  <td>:</td>
                  <td><?=isset($lista_vinculo) ?  $lista_vinculo["tipo_remuneracion"] . " - " . $lista_vinculo["remuneracion"]  : "" ?></td>
                </tr>
                <tr>
                  <td><strong>Resolución</strong></td>
                  <td>:</td>
                  <td><?=isset($lista_vinculo) ?  $lista_vinculo["tipo_resolucion"] . " - " . $lista_vinculo["tipo_resolucion"]  : "" ?> </td>
                </tr>
                <tr>
                  <td><strong>Fecha resolución</strong></td>
                  <td>:</td>
                  <td><?=isset($lista_vinculo) ?  $lista_vinculo["fecha_resolucion"]  : "" ?></td>
                </tr>
              </table>
            </div>
        <?php
          }
        ?>
        <div class="section-container">
          <table>
            <tr>
              <th colspan="3">Cuenta de Ahorrros</th>
            </tr>
            <tr>
              <td style="width: 110px;"><strong>Nro cuenta</strong></td>
              <td style="width: 14px;">:</td>
              <td><?= isset($lista_cuenta) ? $lista_cuenta["cuenta"] : "" ?></td>
            </tr>
            <tr>
              <td><strong>CCI</strong></td>
              <td>:</td>
              <td><?= isset($lista_cuenta) ? $lista_cuenta["cci"] : "" ?></td>
            </tr>
          </table>
        </div>
        <div class="section-container">

          <table>
            <tr>
              <th colspan="3">Familiar</th>
            </tr>
            <tr>
              <td style="width: 110px;"><strong>Parentezco</strong></td>
              <td style="width: 14px;">:</td>
              <td><?= isset($lista_familiar) ? $lista_familiar["parentezco"] : "" ?></td>
            </tr>
            <tr>
              <td><strong>Nombres y apellidos</strong></td>
              <td>:</td>
              <td><?= isset($lista_familiar) ? $lista_familiar["familiar"] : "" ?></td>
            </tr>
            <tr>
              <td><strong>Dirección</strong></td>
              <td>:</td>
              <td><?= isset($lista_familiar) ? $lista_familiar["direccion"] : "" ?></td>
            </tr>
            <tr>
              <td><strong>Celular</strong></td>
              <td>:</td>
              <td><?= isset($lista_familiar) ? $lista_familiar["celular"] : "" ?></td>
            </tr>
          </table>
        </div>
        <div class="section-container">

          <table>
            <tr>
              <th colspan="3">Hijos menores</th>
            </tr>
            <tr>
              <td style="width: 110px;"><strong>Menores de edad</strong></td>
              <td style="width: 14px;">:</td>
              <td><?= isset($lista_hijos) ? $lista_hijos["cantidad"] : "" ?></td>
            </tr>
            <tr>
              <td><strong>Con discapacidad</strong></td>
              <td>:</td>
              <td><?= isset($lista_hijos) ? $lista_hijos["discapacitado"] : "" ?></td>
            </tr>
          </table>
        </div>
        <div class="section-container">

          <table>
            <tr>
              <th colspan="3">Sistema Pensionario</th>
            </tr>
            <tr>
              <td style="width: 110px;"><strong>Nombre</strong></td>
              <td>:</td>
              <td><?= isset($lista_pension) ? $lista_pension["pension"] : "" ?></td>
            </tr>
            <tr>
              <td><strong>Afiliado</strong></td>
              <td>:</td>
              <td><?= isset($lista_pension) ? $lista_pension["afiliado"] : "" ?></td>
            </tr>
            <tr>
              <td><strong>CUSPP</strong></td>
              <td>:</td>
              <td><?= isset($lista_pension) ? $lista_pension["cuspp"] : "" ?></td>
            </tr>

          </table>
        </div>
      </div>
    </div>


  </div>
</body>

</html>