<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckMacAddress;
use App\Http\Middleware\TrackActivity;
use App\Console\Commands\UpdateClientsFromCsv;
use \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use \Illuminate\Contracts\Auth\Middleware\Authorize;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(['check.mac'=>CheckMacAddress::class]);
        $middleware->alias(['track.activity'=>TrackActivity::class]);
    })
    ->withCommands([
        UpdateClientsFromCsv::class,
        UpdateClientsBrandCsv::class,
        UpdateMarginFromCsv::class,
        
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
