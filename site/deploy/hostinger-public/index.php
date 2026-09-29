<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Place this file in public_html; keep the Laravel application in ../site.
$site = dirname(__DIR__).'/site';

if (file_exists($maintenance = $site.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $site.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once $site.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
