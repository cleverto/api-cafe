<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\DashboardModel;

class Dashboard extends BaseController
{
    public function main()
    {
        $model = new DashboardModel();

  
        // 1. Obtener KPIs
        $kpi_data = [
            'compras' =>(float) $model->getComprasMesActual(),
            'ventas' => (float)$model->getVentasMesActual(),
            'stock' => (float)$model->getStockPorTipo(), // El stock total se calcula dentro de getStockPorTipo
            'caja' =>(float) $model->getSaldoCaja(),
        ];

        // 2. Obtener datos para gráficos y tabla
        $compras_ventas_data = $model->getComprasVentasMensual();
        $stock_data = $model->getStockPorTipo();
        $ultimos_movimientos = $model->getUltimosMovimientos(5);

        // Actualizar el KPI de stock con el cálculo de stock por tipo (la suma de los valores)
        $kpi_data['stock'] = array_sum(array_column($stock_data, 'value'));


        $respuesta = [
            'kpi' => $kpi_data,
            'comprasVentasData' => $compras_ventas_data,
            'stockData' => $stock_data,
            'ultimosMovimientos' => $ultimos_movimientos,
        ];

        // Retorna la respuesta como JSON
        return $this->response->setJSON ($respuesta);
    }

}
