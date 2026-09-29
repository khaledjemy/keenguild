<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo('/admin/login');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

$app->afterBootstrapping(LoadConfiguration::class, function (Application $app): void {
    $publicPath = $app['config']->get('keenguild.public_path');
    if (is_string($publicPath) && $publicPath !== '') {
        $app->usePublicPath($publicPath);
    }
});

return $app;
