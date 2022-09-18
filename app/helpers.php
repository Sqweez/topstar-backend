<?php

use Carbon\Carbon;

if (!function_exists('__hardcoded')) {
    function __hardcoded($value) {
        return $value;
    }
}

if (!function_exists('format_datetime')) {
    function format_datetime ($value): ?string {
        return $value ? Carbon::parse($value)->format('d.m.Y H:i:s') : null;
    }
}

if (!function_exists('format_date')) {
    function format_date ($value): ?string {
        return $value ? Carbon::parse($value)->format('d.m.Y') : null;
    }
}

if (!function_exists('mask_phone_old')) {
    function mask_phone_old ($phone) : string {
        $splitPhone = str_split($phone);
        $output = "+7 ";
        foreach ($splitPhone as $key => $item) {
            if ($key === 8 || $key == 5) {
                $output .= " ";
            }
            if ($key === 10) {
                $output .= "-";
            }
            $output .= $item;
        }
        return $output;
    }
}
