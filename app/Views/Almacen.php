<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Reporte de Almacén</title>
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
    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    th {
      border: 1px solid #ddd;
      padding: 1px;
      text-align: center;
    }

    th {
      background: #007bff;
      color: #fff;
    }

    tr:nth-child(even) {
      background: #f9f9f9;
    }

    .compra {}

    .secado {
      background: #fff3cd !important;
      color: #856404;
      font-weight: bold;
    }
  </style>
</head>

<body>

  <div class="header">
    <img src="<?= base_url('public/logo-empresa.png'); ?>" alt="Logo">
    <div class="header-text">

      <h1>REPORTE DE ALMACEN</h1>
      <h3 style="font-size:14px; text-align:center;">
        Desde <?= date("d/m/Y", strtotime($filtro["desde"])) ?> hasta <?= date("d/m/Y", strtotime($filtro["desde"])) ?>
      </h3>
    </div>
  </div>


  <table>
    <thead>
      <tr>
        <th>Fecha</th>
        <th>Producto</th>
        <th>Motivo</th>
        <th>Ingreso</th>
        <th>Salida</th>
        <th>Saldo</th>

      </tr>
    </thead>
    <tbody>
      <?php
      $saldo = 0;
      foreach ($lista as $row):
        $ingreso = $row["operacion"] === "I" ? $row["cantidad"] : 0;
        $salida  = $row["operacion"] === "S" ? $row["cantidad"] : 0;
        $saldo   = $saldo + $ingreso - $salida;

        // Clases CSS para resaltar Compra o Secado
        $claseMotivo = strtolower($row["motivo"]) === "compra" ? "compra" : "secado";
      ?>
        <tr class="<?= $claseMotivo ?>">
          <td width="80px"><?= date("Y-m-d", strtotime($row["fecha"])) ?></td>
          <td width="120px"><?= $row["producto"] ?></td>
          <td width="80px"><?= ucfirst($row["motivo"]) ?></td>
          <td class="text-end"><?= $ingreso ?></td>
          <td class="text-end"><?= $salida ?></td>
          <td class="text-end"><?= $saldo ?></td>


        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</body>

</html>