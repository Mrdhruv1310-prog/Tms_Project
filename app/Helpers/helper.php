<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

if (!function_exists('logException')) {

    function logException(\Throwable $e): void
    {
        $trace = collect($e->getTrace());

        $caller = $trace->first(function ($item) {
            return isset($item['file']) && str_contains($item['file'], base_path('app'));
        });

        Log::error('==================== EXCEPTION ====================');
        Log::error('Error    : ' . $e->getMessage());
        Log::error('File     : ' . basename($e->getFile()));
        Log::error('Line     : ' . $e->getLine());
        Log::error('Class    : ' . ($caller['class'] ?? 'N/A'));
        Log::error('Function : ' . ($caller['function'] ?? 'N/A'));
        Log::error('User ID  : ' . (Auth::id() ?? 'Guest'));
        Log::error('===================================================');
    }
}
