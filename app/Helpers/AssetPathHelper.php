<?php

if (!function_exists('asset_path')) {
    function asset_path(string $path = ''): string
    {
        $prefix = trim((string) env('ASSET_PREFIX', ''), '/');
        $path = ltrim($path, '/');
        $resolvedPath = $prefix !== '' ? $prefix . '/' . $path : $path;

        return asset($resolvedPath);
    }
}
