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
