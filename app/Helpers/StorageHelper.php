<?php

namespace App\Helpers;

class StorageHelper
{
    /**
     * Convert any stored file path or URL into a fully-qualified, accessible public asset URL.
     * Handles nulls, empty strings, absolute URLs, root-relative paths (/storage/...),
     * relative storage paths (storage/... or passports/...), and legacy paths.
     */
    public static function url(?string $path, ?string $fallback = null): ?string
    {
        if (empty($path)) {
            return $fallback;
        }

        $trimmed = trim($path);

        // Already an absolute URL or data URI
        if (preg_match('#^(https?://|data:)#i', $trimmed)) {
            return $trimmed;
        }

        // Clean leading slashes and redundant 'public/' prefixes
        $relativePath = ltrim($trimmed, '/');
        if (str_starts_with($relativePath, 'public/storage/')) {
            $relativePath = substr($relativePath, 7);
        } elseif (str_starts_with($relativePath, 'public/')) {
            $relativePath = substr($relativePath, 7);
        }

        // If path does not start with storage/, assets/, build/, js/, css/
        if (
            !str_starts_with($relativePath, 'storage/') &&
            !str_starts_with($relativePath, 'assets/') &&
            !str_starts_with($relativePath, 'build/') &&
            !str_starts_with($relativePath, 'js/') &&
            !str_starts_with($relativePath, 'css/')
        ) {
            // Check if adding storage/ points to an existing file in public or storage/app/public
            if (
                file_exists(public_path('storage/' . $relativePath)) ||
                file_exists(storage_path('app/public/' . $relativePath)) ||
                !file_exists(public_path($relativePath))
            ) {
                $relativePath = 'storage/' . $relativePath;
            }
        }

        // Resolve using Laravel's asset() helper to guarantee domain & subfolder compatibility
        return asset($relativePath);
    }

    /**
     * Check if a stored file physically exists on disk (in public or storage/app/public).
     */
    public static function exists(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        $trimmed = trim($path);

        if (preg_match('#^(https?://|data:)#i', $trimmed)) {
            return true;
        }

        $relativePath = ltrim($trimmed, '/');
        if (str_starts_with($relativePath, 'public/storage/')) {
            $relativePath = substr($relativePath, 7);
        } elseif (str_starts_with($relativePath, 'public/')) {
            $relativePath = substr($relativePath, 7);
        }

        if (str_starts_with($relativePath, 'storage/')) {
            $storageRelative = substr($relativePath, 8);
            if (file_exists(storage_path('app/public/' . $storageRelative)) || file_exists(public_path($relativePath))) {
                return true;
            }
        } else {
            if (
                file_exists(storage_path('app/public/' . $relativePath)) ||
                file_exists(public_path('storage/' . $relativePath)) ||
                file_exists(public_path($relativePath))
            ) {
                return true;
            }
        }

        return false;
    }
}

