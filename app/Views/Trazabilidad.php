<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de Trazabilidad por Producto</title>
  <style>
    .cont { font-family: 'Segoe UI', Arial, sans-serif;  color: #333; }
    h4 { text-align: center; color: #004085; border-bottom: 3px solid #004085;  }
    h5, h3 { color: #004085; margin-bottom: 10px; }
    .tabla-detalle { width: 100%; border-collapse: collapse; margin-bottom: 20px; background: #fff; border-radius: 6px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
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
  <div class="header"><h4>Reporte de Trazabilidad por Producto</h4></div>
<?php endif; ?>

<?php
// Combinar todas las operaciones en un solo array
$all = [];
foreach($lista['compra'] ?? [] as $c) { $c['etapa_color'] = 'etapa-compra'; $all[] = $c; }
foreach($lista['secado'] ?? [] as $s) { $s['etapa_color'] = 'etapa-secado'; $all[] = $s; }
foreach($lista['proceso'] ?? [] as $p) { $p['etapa_color'] = 'etapa-proceso'; $all[] = $p; }
foreach($lista['venta'] ?? [] as $v) { $v['etapa_color'] = 'etapa-venta'; $all[] = $v; }

// Agrupar por producto
$productos = [];
foreach($all as $row) {
    $prod = $row['producto'] ?? 'Sin producto';
    $productos[$prod][] = $row;
}

// Función para mostrar una fila
function mostrarFila($row) {
    $cantidad = $row['cantidad'] ?? 0;
    $total = $row['total'] ?? 0;
    $precio = $row['precio'] ?? 0;
    $esSalida = (strpos(strtolower($row['etapa'] ?? ''),'salida') !== false || strtolower($row['etapa'] ?? '') === 'venta');
    if ($esSalida) { $cantidad = -abs($cantidad); $total = -abs($total); }

    echo "<tr>
        <td class='etapa {$row['etapa_color']}'>".htmlspecialchars($row['etapa'] ?? '-')."</td>
        <td>".htmlspecialchars($row['comprobante'] ?? '-')."</td>
        <td>".htmlspecialchars($row['referencia'] ?? '-')."</td>
        <td class='text-end'>".htmlspecialchars($row['rendimiento'] ?? '-')."</td>
        <td class='text-end'>".htmlspecialchars($row['humedad'] ?? '-')."</td>
        <td class='text-end'>".htmlspecialchars($row['cascara'] ?? '-')."</td>
        <td class='text-end ".($cantidad<0?'salida':'ingreso')."'>".number_format($cantidad,2)."</td>
        <td class='text-end'>".number_format($precio,2)."</td>
        <td class='text-end'>".number_format($total,2)."</td>
    </tr>";

    return [$cantidad, $total];
}

// Recorrer cada producto y mostrar su tabla
foreach($productos as $prodNombre => $datosProducto):

    // Ordenar por módulo y fecha
    $moduloPeso = [
        'etapa-compra' => 1,
        'etapa-secado' => 2,
        'etapa-proceso'=> 3,
        'etapa-venta'  => 4
    ];
    usort($datosProducto, function($a, $b) use ($moduloPeso){
        $modA = $moduloPeso[$a['etapa_color']] ?? 99;
        $modB = $moduloPeso[$b['etapa_color']] ?? 99;

        if ($modA === $modB) {
            return strcmp($a['fecha'] ?? '1900-01-01', $b['fecha'] ?? '1900-01-01');
        }
        return $modA - $modB;
    });

    echo "<h5>Producto: <strong>".htmlspecialchars($prodNombre)."</strong></h5>";

    echo "<table class='tabla-detalle'>
        <thead>
        <tr>
            <th>Etapa</th><th>Comprobante</th><th>Referencia</th>
            <th>Rendimiento</th><th>Humedad</th><th>Cáscara</th>
            <th class='text-end'>Cantidad</th><th class='text-end'>Precio</th><th class='text-end'>Total</th>
        </tr>
        </thead>
        <tbody>";

    $totalCantidad = 0;
    $totalValor = 0;

    foreach($datosProducto as $row) {
        list($cant,$tot) = mostrarFila($row);
        $totalCantidad += $cant;
        $totalValor += $tot;
    }

    echo "</tbody>
        <tfoot>
        <tr>
            <td colspan='6' style='text-align:right;'><strong>Total producto:</strong></td>
            <td class='".($totalCantidad<0?'salida':'ingreso')."'>".number_format($totalCantidad,2)."</td>
            <td class='text-end'>".number_format($totalCantidad!=0?$totalValor/abs($totalCantidad):0,2)."</td>
            <td class='text-end'>".number_format($totalValor,2)."</td>
        </tr>
        </tfoot>
    </table>";
endforeach;
?>
</body>
</html>
