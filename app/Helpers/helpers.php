<?php
// app/Helpers/helpers.php

use App\Services\SettingService;

if (!function_exists('setting')) {
    /**
     * Ambil nilai setting dari database
     */
    function setting(string $key, $default = null)
    {
        return app(SettingService::class)->get($key, $default);
    }
}

if (!function_exists('setting_typed')) {
    /**
     * Ambil nilai setting dengan konversi tipe
     */
    function setting_typed(string $key, $default = null)
    {
        return app(SettingService::class)->getTyped($key, $default);
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format angka menjadi mata uang
     */
    function format_currency($amount): string
    {
        $symbol = setting('currency_symbol', 'Rp');
        $position = setting('currency_position', 'before');
        $formatted = number_format((float) $amount, 0, ',', '.');
        
        return $position === 'before' 
            ? "{$symbol} {$formatted}" 
            : "{$formatted} {$symbol}";
    }
}