<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Ensure PHP temp directories are available to Laravel and Symfony request handling.
$tempDir = __DIR__ . '/../storage/framework/temp';
if (!is_dir($tempDir)) {
    @mkdir($tempDir, 0777, true);
}
$tempDir = realpath($tempDir) ?: $tempDir;
if (is_dir($tempDir) && is_writable($tempDir)) {
    @ini_set('sys_temp_dir', $tempDir);
    @ini_set('upload_tmp_dir', $tempDir);
    @putenv('TMP=' . $tempDir);
    @putenv('TEMP=' . $tempDir);
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
