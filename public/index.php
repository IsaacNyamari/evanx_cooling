<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Locate the application root. Normally this file lives in <project>/public, so the project is one
// level up. On shared hosting where the document root can't be changed, the contents of public/ are
// copied into public_html and the project sits beside it (e.g. ~/evanx_cooling).
$basePath = is_file(__DIR__.'/../vendor/autoload.php')
    ? __DIR__.'/..'
    : dirname(__DIR__).'/evanx_cooling';

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $basePath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $basePath.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $basePath.'/bootstrap/app.php';

// Serve uploads, build assets etc. from the folder this file is in (public_html when copied).
$app->usePublicPath(__DIR__);

$app->handleRequest(Request::capture());
