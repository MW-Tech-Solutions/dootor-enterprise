<?php

/**
 * Laravel Subfolder Entry Point
 * 
 * This file redirects the entry point to the public directory.
 * It helps run Laravel in subfolders on cPanel shared hosting without SSH/Terminal.
 */

if (!function_exists('mb_split')) {
    function mb_split($pattern, $string, $limit = -1) {
        return preg_split('/' . $pattern . '/u', $string, $limit);
    }
}

require __DIR__.'/public/index.php';
