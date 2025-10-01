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
    th, td {
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
        <th>Fecha</th>
        <th>Proveedor</th>
        <th>Nro Documento</th>
        <th>Nro Referencia</th>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Rendimiento</th>
        <th>Cáscara</th>
        <th>Humedad</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($lista as $row): ?>
        <tr class="secado">
          <td><?= date("Y-m-d", strtotime($row["fecha"])) ?></td>
          <td><?= $row["proveedor"] ?></td>
          <td><?= $row["nro_documento"] ?></td>
          <td><?= $row["nro_referencia"] ?></td>
          <td><?= $row["producto"] ?></td>
          <td><?= $row["cantidad"] ?></td>
          <td><?= $row["rendimiento"] ?></td>
          <td><?= $row["cascara"] ?></td>
          <td><?= $row["humedad"] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</body>

</html>
