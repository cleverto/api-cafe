<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de Trazabilidad por Compra</title>
  <style>
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6f9; color: #333; margin: 25px; }
    h4 { text-align: center; color: #004085; border-bottom: 3px solid #004085; padding-bottom: 10px; margin-bottom: 20px; }
    .grupo-compra { background: linear-gradient(90deg, #004085, #007bff); color: #fff; font-weight: bold; padding: 8px 12px; border-radius: 6px 6px 0 0; margin-top: 20px; }
    .tabla-detalle { width: 100%; border-collapse: collapse; margin-bottom: 5px; background: #fff; border-radius: 0 0 6px 6px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    .tabla-detalle th, .tabla-detalle td { border: 1px solid #ddd; padding: 6px 8px; font-size: 13px; }
    .tabla-detalle th { background: #e9ecef; text-align: center; color: #333; }
    .tabla-detalle tfoot td { font-weight: bold; background: #f1f1f1; text-align: right; }
    .salida { color: #d63333; font-weight: 600; }
    .ingreso { color: #1c7430; font-weight: 600; }
    .etapa { font-weight: 600; }
    .etapa-compra { color: #155724; }
    .etapa-secado { color: #856404; }
    .etapa-proceso { color: #004085; }
    .etapa-venta { color: #721c24; }
    .text-end { text-align: right; }
  </style>
</head>
<body>

<?php if (!empty($filtro["header"]) && $filtro["header"] == "1"): ?>
  <div class="header"><h4>Reporte de Trazabilidad por Compra</h4></div>
<?php endif; ?>

<?php
// print_r($lista);
// Función para mostrar un registro
function mostrarFila($row, $etapaColor) {
    $cantidad = $row['cantidad'] ?? 0;
    $total = $row['total'] ?? 0;
    $precio = $row['precio'] ?? 0;
    $esSalida = (strpos(strtolower($row['etapa'] ?? ''), 'salida') !== false || strtolower($row['etapa'] ?? '') === 'venta');
    if ($esSalida) { $cantidad = -abs($cantidad); $total = -abs($total); }

    echo "<tr>
        <td class='etapa $etapaColor'>".esc($row['etapa'] ?? '-')."</td>
        <td>".esc($row['comprobante'] ?? '-')."</td>
        <td>".esc($row['referencia'] ?? '-')."</td>
        <td class='text-end'>".esc($row['rendimiento'] ?? '-')."</td>
        <td class='text-end'>".esc($row['humedad'] ?? '-')."</td>
        <td class='text-end'>".esc($row['cascara'] ?? '-')."</td>
        <td class='text-end ".($cantidad<0?'salida':'ingreso')."'>".number_format($cantidad,2)."</td>
        <td class='text-end'>".number_format($precio,2)."</td>
        <td class='text-end'>".number_format($total,2)."</td>
    </tr>";
    return [$cantidad, $total];
}

// Recorremos cada compra
foreach($lista['compra'] ?? [] as $compra):

    $compraActual = $compra['nro_comprobante_compra'];
    $productoActual = $compra['producto'];

    $totalCantidad = 0;
    $totalValor = 0;

    // Encabezado del grupo
    echo "<div class='grupo-compra'>
        🧾 Compra: ".esc($compraActual)." &nbsp;|&nbsp; 📅 Fecha: ".esc($compra['fecha'] ?? '-')." &nbsp;|&nbsp; 🏷️ Producto: <strong>".esc($productoActual)."</strong>
    </div>";

    echo "<table class='tabla-detalle'>
        <thead>
        <tr>
            <th>Etapa</th><th>Comprobante</th><th>Referencia</th>
            <th>Rendimiento</th><th>Humedad</th><th>Cáscara</th>
            <th class='text-end'>Cantidad</th><th class='text-end'>Precio</th><th class='text-end'>Total</th>
        </tr>
        </thead>
        <tbody>";

    // 1️⃣ Mostramos la compra
    list($cant, $tot) = mostrarFila($compra, 'etapa-compra');
    $totalCantidad += $cant;
    $totalValor += $tot;

    // 2️⃣ Buscamos secado correspondiente
    foreach($lista['secado'] ?? [] as $secado):
        if($secado['nro_comprobante_compra']==$compraActual && $secado['producto']==$productoActual):
            list($cant, $tot) = mostrarFila($secado, 'etapa-secado');
            $totalCantidad += $cant;
            $totalValor += $tot;
        endif;
    endforeach;

    // 3️⃣ Buscamos proceso correspondiente
    foreach($lista['proceso'] ?? [] as $proceso):
        if($proceso['nro_comprobante_compra']==$compraActual && $proceso['producto']==$productoActual):
            list($cant, $tot) = mostrarFila($proceso, 'etapa-proceso');
            $totalCantidad += $cant;
            $totalValor += $tot;
        endif;
    endforeach;

    // 4️⃣ Buscamos venta correspondiente
    foreach($lista['venta'] ?? [] as $venta):
        if($venta['nro_comprobante_compra']==$compraActual && $venta['producto']==$productoActual):
            list($cant, $tot) = mostrarFila($venta, 'etapa-venta');
            $totalCantidad += $cant;
            $totalValor += $tot;
        endif;
    endforeach;

    // Total por grupo
    echo "</tbody>
        <tfoot>
        <tr>
            <td colspan='6' style='text-align:right;'><strong>Total grupo:</strong></td>
            <td class='".($totalCantidad<0?'salida':'ingreso')."'>".number_format($totalCantidad,2)."</td>
            <td class='text-end'>".number_format($totalCantidad !=0 ? $totalValor/abs($totalCantidad) : 0,2)."</td>
            <td class='text-end'>".number_format($totalValor,2)."</td>
        </tr>
        </tfoot>
    </table>";

endforeach;
?>
</body>
</html>
