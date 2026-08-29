<?php

/**
 * Laravel Subfolder Entry Point
 * 
 * This file redirects the entry point to the public directory.
 * It helps run Laravel in subfolders on cPanel shared hosting without SSH/Terminal.
 */

require __DIR__.'/public/index.php';
