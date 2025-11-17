<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Reporte de Compras</title>

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
      font-size: 11px;
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

    .ingreso {
      color: #1c7430;
      font-weight: 600;
    }

    .salida {
      color: #d63333;
      font-weight: 600;
    }

    .text-end {
      text-align: right;
    }

    /* Header */
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

  <div class="container">
    <?php if (!empty($filtro["header"]) && $filtro["header"] == "1"): ?>
      <div class="header">
        <div class="header-text">
          <h1>REPORTE DE COMPRAS</h1>
          <h3 style="font-size:14px; text-align:center;">
            Desde <?= date("d/m/Y", strtotime($filtro["desde"])) ?>
            hasta <?= date("d/m/Y", strtotime($filtro["hasta"])) ?>
          </h3>
        </div>
      </div>
    <?php endif; ?>
    <table class="tabla-detalle">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Cliente</th>
          <th>Producto</th>
          <th>Cant.</th>
          <th>Prec.</th>
          <th>Total</th>
        </tr>
      </thead>

      <tbody>
        <?php
        $sumaCantidad = 0;
        $sumaPrecio = 0;
        $sumaTotal = 0;
        $contador = 0;
        ?>

        <?php if (!empty($lista)): ?>
          <?php foreach ($lista as $row): ?>
            <?php
            $subtotal = $row['cantidad'] * $row['precio'];
            $sumaCantidad += $row['cantidad'];
            $sumaPrecio += $row['precio'];
            $sumaTotal += $subtotal;
            $contador++;
            ?>

            <tr>
              <td class="" width="80px"><?= date("d-m-Y", strtotime($row['fecha'])) ?></td>
              <td><?= htmlspecialchars($row['proveedor']) ?></td>
              <td><?= htmlspecialchars($row['producto']) ?></td>


              <td class="text-end"><?= number_format($row['cantidad'], 2) ?></td>
              <td class="text-end"><?= number_format($row['precio'], 2) ?></td>
              <td class="text-end"><?= number_format($subtotal, 2) ?></td>
            </tr>

          <?php endforeach; ?>

        <?php else: ?>
          <tr>
            <td colspan="19" style="text-align:center;">No hay compras registradas</td>
          </tr>
        <?php endif; ?>

      </tbody>

      <?php if (!empty($lista)): ?>
        <tfoot>
          <tr>
            <td colspan="3" style="text-align:center; font-weight:bold;">TOTAL</td>

            <td class="text-end" style="font-weight:bold;"><?= number_format($sumaCantidad, 2) ?></td>
            <td class="text-end" style="font-weight:bold;"><?= number_format($sumaPrecio / $contador, 2) ?></td>
            <td class="text-end" style="font-weight:bold;"><?= number_format($sumaTotal, 2) ?></td>
          </tr>
        </tfoot>
      <?php endif; ?>

    </table>

  </div>

</body>

</html>