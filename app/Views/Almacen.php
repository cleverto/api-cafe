<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Reporte de Almacén</title>

  <style>
    .cont {
      font-family: 'Segoe UI', Arial, sans-serif;
      color: #333;
    }

    h4 {
      text-align: center;
      color: #004085;
      border-bottom: 3px solid #004085;
    }

    h5,
    h3 {
      color: #004085;
      margin-bottom: 10px;
    }

    .tabla-detalle {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
      background: #fff;
      border-radius: 6px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .tabla-detalle th,
    .tabla-detalle td {
      border: 1px solid #ddd;
      padding: 6px 8px;
      font-size: 13px;
    }

    .tabla-detalle th {
      background: #e9ecef;
      text-align: center;
      color: #333;
    }

    .tabla-detalle tfoot td {
      font-weight: bold;
      background: #f1f1f1;
      text-align: right;
    }

    .salida {
      color: #d63333;
      font-weight: 600;
    }

    .ingreso {
      color: #1c7430;
      font-weight: 600;
    }

    .etapa {
      font-weight: 600;
    }

    .etapa-compra {
      color: #155724;
    }

    .etapa-secado {
      color: #856404;
    }

    .etapa-proceso {
      color: #004085;
    }

    .etapa-venta {
      color: #721c24;
    }

    .text-end {
      text-align: right;
    }

    /* Estilos adicionales mínimos para el header */
    .header {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
    }

    .header img {
      width: 80px;
      margin-right: 15px;
    }

    .header-text h1 {
      margin: 0;
      color: #004085;
    }
  </style>
</head>

<body class="cont">
  <?php if (!empty($filtro["header"]) && $filtro["header"] == "1"): ?>
    <div class="header">

      <div class="header-text">
        <h1>REPORTE DE ALMACÉN</h1>
        <h3 style="font-size:14px; text-align:center;">
          Desde <?= date("d/m/Y", strtotime($filtro["desde"])) ?> hasta <?= date("d/m/Y", strtotime($filtro["hasta"])) ?>
        </h3>
      </div>
    </div>
  <?php endif; ?>
  <table class="tabla-detalle">
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

        $saldo = $saldo + $ingreso - $salida;

        $esIngreso = $ingreso > 0 ? "ingreso" : "";
        $esSalida = $salida > 0 ? "salida" : "";
      ?>
        <tr>
          <td width="90px"><?= date("d-m-Y", strtotime($row["fecha"])) ?></td>
          <td width="auto"><?= $row["producto"] ?></td>
          <td width="80px"><?= ucfirst($row["motivo"]) ?></td>

          <td class="text-end <?= $esIngreso ?>"><?= $ingreso ?></td>
          <td class="text-end <?= $esSalida ?>"><?= $salida ?></td>
         <td class="text-end"><?= number_format($saldo, 2) ?></td>

        </tr>

      <?php endforeach; ?>
    </tbody>
  </table>

</body>

</html>