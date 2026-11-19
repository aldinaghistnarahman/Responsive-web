<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Mengalihkan tamu (belum login) ke halaman login
        $middleware->redirectTo('/login');
        
        // Mengalihkan user yang sudah login jika mereka mencoba mengakses halaman login/register lagi
        $middleware->redirectUsersTo('/mahasiswa');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
