<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Reporte de Compras y Secados</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 11px;
      background: #f9f9f9;
      color: #333;
      margin: 20px;
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

    .header-text h1 {
      font-size: 16px;
      margin: 0;
      color: #1c55a1;
    }

    .header-text {
      text-align: center;
      flex-grow: 1;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    th {
      border: 1px solid #ddd;
      padding: 3px 5px;
      text-align: center;
    }

    th {
      background: #007bff;
      color: #fff;
    }

    tr:nth-child(even) {
      background: #f9f9f9;
    }

    .secado {
      background: #fff3cd !important;
      color: #856404;
      font-weight: bold;
    }

    .center-row {
      width: 50px;
      text-align: center;
      vertical-align: middle;
    }

    /* .compra {
      background: #d4edda !important;
      color: #155724;
      font-weight: bold;
    } */
  </style>
</head>

<body>

  <div class="header">
    <img src="<?= base_url('public/logo-empresa.png'); ?>" alt="Logo">
    <div class="header-text">
      <h1>REPORTE DE COMPRAS Y SECADOS</h1>
      <h3 style="font-size:14px; text-align:center;">
        Desde <?= date("d/m/Y", strtotime($filtro["desde"])) ?> hasta <?= date("d/m/Y", strtotime($filtro["hasta"])) ?>
      </h3>
    </div>
  </div>

<table>
  <thead>
    <tr>
      <th width="100px">Fecha</th>
      <th>Producto</th>
      <th>Operación</th>
      <th width="80px">Cantidad</th>
      <th width="80px">Rendimiento</th>
      <th width="80px">Cáscara</th>
      <th width="80px">Humedad</th>
    </tr>
  </thead>
  <tbody>
    <?php 
      $totalCantidad = 0;
      $totalRendimiento = 0;
      $totalCascara = 0;
      $totalHumedad = 0;
      $count = 0;

      foreach ($lista as $row): 
        $totalCantidad += $row["cantidad"];
        $totalRendimiento += $row["rendimiento"];
        $totalCascara += $row["cascara"];
        $totalHumedad += $row["humedad"];
        $count++;
    ?>
      <tr class="<?= strtolower($row["operacion"]) ?>">
        <td><?= date("Y-m-d", strtotime($row["fecha"])) ?></td>
        <td><?= $row["producto"] ?></td>
        <td class="center-row"><?= $row["operacion"] ?></td>
        <td class="center-row"><?= $row["cantidad"] ?></td>
        <td class="center-row"><?= $row["rendimiento"] ?></td>
        <td class="center-row"><?= $row["cascara"] ?></td>
        <td class="center-row"><?= $row["humedad"] ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
  <tfoot>
    <tr style="font-weight:bold; background:#f0f0f0;">
      <td colspan="3" style="text-align:right;">Totales / Promedios:</td>
      <td class="center-row"><?= $totalCantidad ?></td>
      <td class="center-row"><?= $count > 0 ? round($totalRendimiento / $count, 2) : 0 ?></td>
      <td class="center-row"><?= $count > 0 ? round($totalCascara / $count, 2) : 0 ?></td>
      <td class="center-row"><?= $count > 0 ? round($totalHumedad / $count, 2) : 0 ?></td>
    </tr>
  </tfoot>
</table>

</body>

</html>