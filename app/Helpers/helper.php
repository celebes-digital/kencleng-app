<?php

if (!function_exists('h_format_date')) {
    function h_format_date($date)
    {
        return date('d F Y', strtotime($date));
    }
}

if (!function_exists('h_format_datetime')) {
    function h_format_datetime($date)
    {
        return date('d F Y, H:i', strtotime($date));
    }
}

if (!function_exists('h_format_currency')) {
    function h_format_currency($value, $currency = 'IDR', $short = false)
    {
        if ($short) {
            if ($value >= 1000000000000) {
                $value = number_format($value / 1000000000000, 1) . ' T';
            } elseif ($value >= 1000000000) {
                $value = number_format($value / 1000000000, 1) . ' M';
            } elseif ($value >= 1000000) {
                $value = number_format($value / 1000000, 1) . ' Jt';
            } else {
                $value = number_format($value, 0, ',', '.');
            }
        } else {
            $value = number_format($value, 0, ',', '.');
        }
        return $currency . ' ' . $value;
    }
}
