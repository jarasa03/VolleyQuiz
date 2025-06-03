<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// MODO MANTENIMIENTO
if (file_exists(__DIR__ . '/../storage/framework/maintenance.php')) {
    require __DIR__ . '/../storage/framework/maintenance.php';
}

// AUTOLOADER DE COMPOSER
require __DIR__ . '/../vendor/autoload.php';

// BOOTSTRAP LARAVEL
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->handleRequest(
    Request::capture()
);
