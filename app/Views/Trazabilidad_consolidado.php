<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte Consolidado</title>

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
    </style>
</head>

<body>
    <div class="cont">
        <?php if (!empty($filtro["header"]) && $filtro["header"] == "1"): ?>
            <h4>REPORTE CONSOLIDADO POR PRODUCTO</h4>
        <?php endif; ?>
        <table class="tabla-detalle">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($lista['kardex'])): ?>
                    <?php foreach ($lista['kardex'] as $item): ?>
                        <tr>
                            <td><?= $item['producto'] ?></td>

                            <td class="text-end ingreso">
                                <?= number_format($item['cantidad'], 2) ?>
                            </td>
                            <td class="text-end etapa-proceso">
                                <?php
                                $cantidad = $item['cantidad'] ?? 0;
                                $total = $item['total'] ?? 0;

                                echo ($cantidad != 0)
                                    ? number_format($total / $cantidad, 2)
                                    : "0.00";
                                ?>
                            </td>
                            <td class="text-end etapa-proceso">
                                <?= number_format($item['total'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align:center; font-weight:bold; color:#999;">
                            No se encontraron datos en el rango seleccionado
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <!-- 
            <tfoot>
                <tr>
                    <td colspan="3">Reporte generado automáticamente</td>
                </tr>
            </tfoot> -->
        </table>

    </div>
</body>

</html>