<?php

if (!function_exists('rupiahJuta')) {
    function rupiahJuta($harga)
    {
        if (!is_numeric($harga)) return '-';

        $harga = (float) $harga;

        // Miliar
        if ($harga >= 1000000000) {
            $miliar = $harga / 1000000000;

            if (floor($miliar) == $miliar) {
                $formatted = number_format($miliar, 0, ',', '.');
            } else {
                $formatted = number_format($miliar, 1, ',', '.');
            }

            return 'Rp ' . $formatted . ' Miliar';
        }

        // Juta
        if ($harga >= 1000000) {
            $juta = $harga / 1000000;

            if (floor($juta) == $juta) {
                $formatted = number_format($juta, 0, ',', '.');
            } else {
                $formatted = number_format($juta, 1, ',', '.');
            }

            return 'Rp ' . $formatted . ' Juta';
        }

        // Di bawah 1 juta
        return 'Rp ' . number_format($harga, 0, ',', '.');
    }
}


