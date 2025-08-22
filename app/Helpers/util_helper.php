<?php

if (!function_exists('letras')) {
    function letras($numero)
    {
        $unidades = ["", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
        $especiales = ["diez", "once", "doce", "trece", "catorce", "quince", "dieciséis", "diecisiete", "dieciocho", "diecinueve"];
        $decenas = ["", "diez", "veinte", "treinta", "cuarenta", "cincuenta", "sesenta", "setenta", "ochenta", "noventa"];
        $centenas = ["", "ciento", "doscientos", "trescientos", "cuatrocientos", "quinientos", "seiscientos", "setecientos", "ochocientos", "novecientos"];

        if ($numero == 0) {
            return "cero";
        }

        $parteEntera = floor($numero);
        $parteDecimal = round(($numero - $parteEntera) * 100);

        function convertirCentenas($num, $unidades, $especiales, $decenas, $centenas)
        {
            if ($num == 100) return "cien";
            elseif ($num < 10) return $unidades[$num];
            elseif ($num < 20) return $especiales[$num - 10];
            elseif ($num < 100) return $decenas[floor($num / 10)] . ($num % 10 ? " y " . $unidades[$num % 10] : "");
            else return $centenas[floor($num / 100)] . ($num % 100 ? " " . convertirCentenas($num % 100, $unidades, $especiales, $decenas, $centenas) : "");
        }

        function convertirMiles($num, $unidades, $especiales, $decenas, $centenas)
        {
            if ($num < 1000) return convertirCentenas($num, $unidades, $especiales, $decenas, $centenas);
            elseif ($num < 2000) return "mil " . convertirCentenas($num % 1000, $unidades, $especiales, $decenas, $centenas);
            else return convertirCentenas(floor($num / 1000), $unidades, $especiales, $decenas, $centenas) . " mil" . 
                ($num % 1000 ? " " . convertirCentenas($num % 1000, $unidades, $especiales, $decenas, $centenas) : "");
        }

        function convertirMillones($num, $unidades, $especiales, $decenas, $centenas)
        {
            if ($num < 1000000) return convertirMiles($num, $unidades, $especiales, $decenas, $centenas);
            elseif ($num < 2000000) return "un millón " . convertirMiles($num % 1000000, $unidades, $especiales, $decenas, $centenas);
            else return convertirCentenas(floor($num / 1000000), $unidades, $especiales, $decenas, $centenas) . " millones" . 
                ($num % 1000000 ? " " . convertirMiles($num % 1000000, $unidades, $especiales, $decenas, $centenas) : "");
        }

        $montoEnLetras = ucfirst(convertirMillones($parteEntera, $unidades, $especiales, $decenas, $centenas)) . 
                         " con " . str_pad($parteDecimal, 2, "0", STR_PAD_LEFT) . "/100";

        return $montoEnLetras;
    }
    function sumar($data){
        $total = 0;
        foreach ($data as $fila) {
            $total += $fila['total'];
        }
        return $total;
    }
}
