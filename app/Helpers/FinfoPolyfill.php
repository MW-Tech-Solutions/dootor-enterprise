<?php

if (!class_exists('finfo')) {
    if (!defined('FILEINFO_NONE')) define('FILEINFO_NONE', 0);
    if (!defined('FILEINFO_SYMLINK')) define('FILEINFO_SYMLINK', 2);
    if (!defined('FILEINFO_MIME_TYPE')) define('FILEINFO_MIME_TYPE', 16);
    if (!defined('FILEINFO_MIME_ENCODING')) define('FILEINFO_MIME_ENCODING', 1024);
    if (!defined('FILEINFO_MIME')) define('FILEINFO_MIME', 1040);
    if (!defined('FILEINFO_PRESERVE_ATIME')) define('FILEINFO_PRESERVE_ATIME', 128);
    if (!defined('FILEINFO_RAW')) define('FILEINFO_RAW', 256);

    class finfo
    {
        private int $flags;

        public function __construct(int $flags = FILEINFO_NONE, ?string $magic_file = null)
        {
            $this->flags = $flags;
        }

        public function file(string $filename, int $flags = FILEINFO_NONE, $context = null): string|false
        {
            if (!file_exists($filename)) {
                return false;
            }

            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                'webp' => 'image/webp',
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xls' => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'txt' => 'text/plain',
                'csv' => 'text/csv',
                'zip' => 'application/zip',
                'rar' => 'application/vnd.rar',
                '7z' => 'application/x-7z-compressed',
                'mp3' => 'audio/mpeg',
                'mp4' => 'video/mp4',
            ];

            return $mimeTypes[$ext] ?? 'application/octet-stream';
        }

        public function buffer(string $string, int $flags = FILEINFO_NONE, $context = null): string|false
        {
            return 'application/octet-stream';
        }

        public function set_flags(int $flags): bool
        {
            $this->flags = $flags;
            return true;
        }
    }

    if (!function_exists('finfo_open')) {
        function finfo_open(int $flags = FILEINFO_NONE, ?string $magic_file = null)
        {
            return new finfo($flags, $magic_file);
        }
    }

    if (!function_exists('finfo_file')) {
        function finfo_file($finfo, string $filename, int $flags = FILEINFO_NONE, $context = null)
        {
            if ($finfo instanceof finfo) {
                return $finfo->file($filename, $flags, $context);
            }
            return false;
        }
    }

    if (!function_exists('finfo_buffer')) {
        function finfo_buffer($finfo, string $string, int $flags = FILEINFO_NONE, $context = null)
        {
            if ($finfo instanceof finfo) {
                return $finfo->buffer($string, $flags, $context);
            }
            return false;
        }
    }

    if (!function_exists('finfo_close')) {
        function finfo_close($finfo): bool
        {
            return true;
        }
    }
}
