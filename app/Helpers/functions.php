<?php

if (!function_exists('app_file_url')) {
    /**
     * Helper function to get accessible public file URL.
     */
    function app_file_url(?string $path, ?string $fallback = null): ?string
    {
        return \App\Helpers\StorageHelper::url($path, $fallback);
    }
}

if (!function_exists('app_file_exists')) {
    /**
     * Helper function to check if file physically exists.
     */
    function app_file_exists(?string $path): bool
    {
        return \App\Helpers\StorageHelper::exists($path);
    }
}
