<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'verify.bot' => \App\Http\Middleware\VerifyBotToken::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'broadcasting/auth',
            'livewire/upload-file'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();

// Override storage path ke /tmp (writable di Vercel)
$app->useStoragePath('/tmp');

// Pastikan folder yang dibutuhkan Laravel ada
$paths = [
    '/tmp/framework',
    '/tmp/framework/cache',
    '/tmp/framework/views',
    '/tmp/framework/sessions',
    '/tmp/logs',
];

foreach ($paths as $path) {
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

return $app;