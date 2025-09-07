<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Papeleta de salida</title>
  <style>
    /* @page {
      size: A4 landscape;
      margin: 10mm;
    }

    @media print {
      body {
        margin: 0;
        padding: 0;
      }

      .container {
        width: 100%;
        height: auto;
      }
    } */

    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      width: 100%;
    }

    .container {
  width: 100%;
  margin: 0 auto;
  border: 0px solid #000;
  box-sizing: border-box;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      border-bottom: 1px solid #1c55a1;
      padding-bottom: 5px;
    }

    .header img {
      height: 60px;
    }

    .header-text {
      text-align: center;
      flex-grow: 1;
    }

    .header-text h1 {
      font-size: 16px;
      margin: 0;
      color: #1c55a1;
    }

    .header-text small {
      display: block;
      font-size: 13px;
    }

    .cuadro-numero {
      display: flex;
      align-items: center;
      border: 1px solid gray;
      color: red;
      padding: 5px;
      font-size: 15px;
      border-radius: 8px;

    }

    .numero {
      margin-left: auto;
      width: 90px;
      font-weight: bold;
      font-size: 15px;
      padding: 4px 8px;
      color: red;
      text-align: center;
    }

    table {
      width: 100%;
      border-collapse: collapse;

    }

    td {
      padding: 5px;
      border: 1px solid #000;
    }

    .tabla-motivos th {

      border: 1px solid #000;
      font-size: 9px;

    }

    .tabla-motivos td {
      border: 1px solid #000;
      font-size: 9px;
      padding: 2px;
    }



    .firmas {
      display: flex;
      justify-content: space-between;
      text-align: center;
      margin-top: 60px;
    }

    .firma {
      width: 30%;
      border-top: 1px solid #000;
      font-size: 11px;
      padding-top: 4px;
    }

    .footer {
      margin-top: 20px;
      font-size: 10px;
      display: flex;
      justify-content: space-between;
      color: #1c55a1;
      background-color: #39547f;
      color: white;
      padding: 10px;
    }

    .contacto {
      display: flex;
      align-items: center;
      /* Alinea verticalmente */
      gap: 8px;
      /* Espacio entre imagen y texto */
      font-family: sans-serif;
    }

    .circulo {
      width: 20px;
      height: 20px;
      border-radius: 50%;
      overflow: hidden;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #ddd;
      /* opcional */
    }

    .circulo img {
      width: 15px;
      height: auto;
      filter: invert(32%) sepia(21%) saturate(1046%) hue-rotate(174deg) brightness(92%) contrast(91%);


    }

    .circulo {
      width: 25px;
      height: 25px;
      border-radius: 50%;
      background-color: white;
      /* puedes cambiar el color */
      display: flex;
      justify-content: center;
      text-align: center;
      align-items: center;
    }

    .texto-footer {
      line-height: 1.2;
      font-size: 8px;
    }
  </style>
</head>

<body>
  <?php

  ?>
  <div class="container">
    <!-- Encabezado -->
    <div class="header">
      <img src="<?= base_url('public/logo-empresa.png'); ?>" alt="Logo">
      <div class="header-text">

        <h1>REPORTE DE COMPRAS</h1>
        <h3 style="font-size:14px; text-align:center;">
          Desde <?= date("d/m/Y", strtotime($filtro["desde"])) ?> hasta <?= date("d/m/Y", strtotime($filtro["desde"])) ?>
        </h3>
      </div>
    </div>

    <!-- Datos principales -->

    <table class="tabla-motivos ">
      <thead>
        <tr>
          <th style="width: 60px;">Fecha</th>
          <th>Proveedor</th>
          <th>Producto</th>
          <th>MUE</th>
          <th>RTO</th>
          <th>SP</th>
          <th>BOL</th>
          <th>CAS</th>
          <th>HUM</th>
          <th>DST</th>
          <th>PLL</th>
          <th>N/M</th>
          <th>BM</th>
          <th>IMP</th>
          <th>DEF</th>
          <th>PT</th>
          <th>Cant.</th>
          <th>Prec.</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sumaCantidad = 0;
        $sumaTotal = 0;
        $sumaPrecio = 0;
        $contador = 0;

        // arrays de sumas para las columnas numéricas
        $sumas = [
          "muestra" => 0,
          "rendimiento" => 0,
          "segunda" => 0,
          "bola" => 0,
          "cascara" => 0,
          "humedad" => 0,
          "descarte" => 0,
          "pasilla" => 0,
          "negro" => 0,
          "ripio" => 0,
          "impureza" => 0,
          "defectos" => 0,
          "taza" => 0
        ];
        ?>

        <?php if (!empty($lista)): ?>
          <?php foreach ($lista as $row): ?>
            <?php
            $subtotal = $row['cantidad'] * $row['precio'];
            $sumaCantidad += $row['cantidad'];
            $sumaPrecio += $row['precio'];
            $sumaTotal += $subtotal;
            $contador++;

            foreach ($sumas as $campo => $valor) {
              $sumas[$campo] += isset($row[$campo]) ? $row[$campo] : 0;
            }
            ?>
            <tr>
              <td style="text-align: center"><?= htmlspecialchars($row['fecha']) ?></td>
              <td><?= htmlspecialchars($row['proveedor']) ?></td>
              <td><?= htmlspecialchars($row['producto']) ?></td>
              <td style="text-align:right;"><?= number_format($row['muestra'], 2) ?></td>
              <td style="text-align:right;"><?= number_format($row['rendimiento'], 2) ?></td>
              <td style="text-align:right;"><?= number_format($row['segunda'], 2) ?></td>
              <td style="text-align:right;"><?= number_format($row['bola'], 2) ?></td>
              <td style="text-align:right;"><?= number_format($row['cascara'], 2) ?></td>
              <td style="text-align:right;"><?= number_format($row['humedad'], 2) ?></td>
              <td style="text-align:right;"><?= number_format($row['descarte'], 2) ?></td>
              <td style="text-align:right;"><?= number_format($row['pasilla'], 2) ?></td>
              <td style="text-align:right;"><?= number_format($row['negro'], 2) ?></td>
              <td style="text-align:right;"><?= number_format($row['ripio'], 2) ?></td>
              <td style="text-align:right;"><?= number_format($row['impureza'], 2) ?></td>
              <td style="text-align:right;"><?= number_format($row['defectos'], 2) ?></td>
              <td style="text-align:right;"><?= number_format($row['taza'], 2) ?></td>
              <td style="text-align:right;"><?= number_format($row['cantidad'], 2) ?></td>
              <td style="text-align:right;"><?= number_format($row['precio'], 2) ?></td>
              <td style="text-align:right;"><?= number_format($subtotal, 2) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="19" style="text-align:center;">No hay compras registradas en este rango</td>
          </tr>
        <?php endif; ?>
      </tbody>

      <?php if (!empty($lista)): ?>
        <tfoot>
          <tr>
            <td colspan="3" style="text-align: center; font-weight:bold;">TOTAL</td>
            <td style="text-align:right; font-weight:bold;"></td>
            <td style="text-align:right; font-weight:bold;"></td>
            <td style="text-align:right; font-weight:bold;"></td>
            <td style="text-align:right; font-weight:bold;"></td>
            <td style="text-align:right; font-weight:bold;"></td>
            <td style="text-align:right; font-weight:bold;"></td>
            <td style="text-align:right; font-weight:bold;"></td>
            <td style="text-align:right; font-weight:bold;"></td>
            <td style="text-align:right; font-weight:bold;"></td>
            <td style="text-align:right; font-weight:bold;"></td>
            <td style="text-align:right; font-weight:bold;"></td>
            <td style="text-align:right; font-weight:bold;"></td>
            <td style="text-align:right; font-weight:bold;"></td>

            <td style="text-align:right; font-weight:bold;"><?= number_format($sumaCantidad, 2) ?></td>
            <td style="text-align:right; font-weight:bold;"><?= number_format($sumaPrecio / $contador, 2) ?></td>
            <td style="text-align:right; font-weight:bold;"><?= number_format($sumaTotal, 2) ?></td>
          </tr>
        </tfoot>
      <?php endif; ?>
    </table>



  </div>
</body>

</html>