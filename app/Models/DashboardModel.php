<?php

namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends Model
{

    public function getComprasMesActual()
    {
        $inicio_mes = date('Y-m-01 00:00:00');
        $fin_mes = date('Y-m-t 23:59:59');

        $builder = $this->db->table('compra');
        $builder->selectSum('total', 'compras_mes');
        $builder->where('fecha >=', $inicio_mes);
        $builder->where('fecha <=', $fin_mes);

        $query = $builder->get();
        return $query->getRow()->compras_mes ?? 0;
    }

    /**
     * Calcula la suma de las ventas (cantidad de producto de venta_detalle)
     * para el mes actual.
     */
    public function getVentasMesActual()
    {
        $inicio_mes = date('Y-m-01 00:00:00');
        $fin_mes = date('Y-m-t 23:59:59');

        $builder = $this->db->table('venta v');
        $builder->selectSum('vd.cantidad', 'ventas_mes');
        $builder->join('venta_detalle vd', 'v.id_venta = vd.id_venta');
        $builder->where('v.fecha >=', $inicio_mes);
        $builder->where('v.fecha <=', $fin_mes);

        $query = $builder->get();

        return $query->getRow()->ventas_mes ?? 0;
    }

    /**
     * Calcula el saldo total de la caja (Ingresos - Egresos).
     */
    public function getSaldoCaja()
    {
        $sql = "SELECT 
                    SUM(CASE WHEN movimiento = 'INGRESO' THEN monto ELSE 0 END) - 
                    SUM(CASE WHEN movimiento = 'EGRESO' THEN monto ELSE 0 END) as saldo_caja
                FROM caja";

        $query = $this->db->query($sql);
        return $query->getRow()->saldo_caja ?? 0;
    }
    
    // --- Consultas para Gráficos y Tablas ---

    /**
     * Obtiene los datos de Compras vs Ventas agrupados por mes para el gráfico.
     */
    public function getComprasVentasMensual()
    {
        $year = date('Y');

        // Compras por mes (usando un JOIN a compra_detalle sería más preciso si 'total' de compra es solo el monto)
        $sql_compras = "SELECT 
                            MONTH(fecha) as mes, 
                            SUM(total) as compras
                        FROM compra
                        WHERE YEAR(fecha) = {$year}
                        GROUP BY MONTH(fecha)";
        $compras = $this->db->query($sql_compras)->getResultArray();

        // Ventas por mes (cantidad de producto vendido)
        $sql_ventas = "SELECT 
                            MONTH(v.fecha) as mes, 
                            SUM(vd.cantidad) as ventas
                        FROM venta v
                        JOIN venta_detalle vd ON v.id_venta = vd.id_venta
                        WHERE YEAR(v.fecha) = {$year}
                        GROUP BY MONTH(v.fecha)";
        $ventas = $this->db->query($sql_ventas)->getResultArray();

        // Combina y formatea los datos para React
        $datos_combinados = [];
        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        for ($i = 1; $i <= 12; $i++) {
            $compra_mes = array_search($i, array_column($compras, 'mes'));
            $venta_mes = array_search($i, array_column($ventas, 'mes'));

            $datos_combinados[] = [
                'mes' => $meses[$i - 1],
                'compras' => $compra_mes !== false ? (float)$compras[$compra_mes]['compras'] : 0,
                'ventas' => $venta_mes !== false ? (float)$ventas[$venta_mes]['ventas'] : 0,
            ];
        }

        // Filtra para mostrar solo los meses que ya pasaron en el año actual
        return array_filter($datos_combinados, function ($item) use ($meses) {
            return array_search($item['mes'], $meses) <= date('n') - 1;
        });
    }

    /**
     * Obtiene el stock actual total por tipo de producto para el gráfico circular.
     */
    public function getStockPorTipo()
    {
        $sql = "SELECT 
                    p.producto AS name,
                    SUM(CASE WHEN k.operacion = 'I' THEN kd.cantidad ELSE 0 END) - 
                    SUM(CASE WHEN k.operacion = 'S' THEN kd.cantidad ELSE 0 END) AS value
                FROM kardex k
                JOIN kardex_detalle kd ON k.id_kardex = kd.id_kardex
                JOIN producto p ON kd.id_producto = p.id_producto 
                GROUP BY p.producto
                HAVING value > 0";

        $query = $this->db->query($sql);
        return $query->getResultArray(); // [{ name: "Pergamino", value: 250 }, ...]
    }

    /**
     * Obtiene los últimos movimientos de Kardex para la tabla.
     */
    public function getUltimosMovimientos(int $limit = 5)
    {
        $builder = $this->db->table('kardex k');
        $builder->select('k.fecha, k.operacion AS tipo, kd.cantidad, u.usuario, p.producto');
        $builder->join('kardex_detalle kd', 'k.id_kardex = kd.id_kardex');
        $builder->join('producto p', 'kd.id_producto = p.id_producto'); // Asume tabla 'producto'
        $builder->join('usuario u', 'k.id_usuario = u.id_usuario'); // Asume tabla 'usuario' con campo 'nombre'
        $builder->orderBy('k.fecha', 'DESC');
        $builder->limit($limit);

        return $builder->get()->getResultArray();
    }

    public function getStockTotal()
    {
        // Consulta para el stock total (usando solo Kardex)

        $sql = "SELECT 
                    SUM(
                        CASE 
                            WHEN k.operacion = 'I' THEN kd.cantidad  -- Suma si es Ingreso
                            WHEN k.operacion = 'S' THEN kd.cantidad * -1 -- Resta si es Salida
                            ELSE 0 
                        END
                    ) as stock_total
                FROM kardex k
                JOIN kardex_detalle kd ON k.id_kardex = kd.id_kardex";

        $query = $this->db->query($sql);
        return $query->getRow()->stock_total ?? 0;
    }
}
