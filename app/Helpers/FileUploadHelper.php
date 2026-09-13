<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;

class FileUploadHelper
{
    /**
     * Store an uploaded file safely without relying on PHP finfo extension.
     */
    public static function store(UploadedFile $file, string $folder = 'uploads', string $disk = 'public'): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
        if (empty($extension)) {
            $extension = 'bin';
        }

        $cleanFolder = trim(str_replace('\\', '/', $folder), '/');
        $filename = uniqid($cleanFolder . '_', true) . '_' . time() . '.' . $extension;

        $targetDir = storage_path('app/' . $disk . '/' . $cleanFolder);
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $file->move($targetDir, $filename);
        $savedPath = $targetDir . '/' . $filename;

        // Also copy directly to public/storage for cPanel web hosting without active symlink
        $publicDir = public_path('storage/' . $cleanFolder);
        if (!file_exists($publicDir)) {
            @mkdir($publicDir, 0777, true);
        }
        if (file_exists($savedPath) && file_exists($publicDir) && !file_exists($publicDir . '/' . $filename)) {
            @copy($savedPath, $publicDir . '/' . $filename);
        }

        return $cleanFolder . '/' . $filename;
    }
}
