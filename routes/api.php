<?php

use App\Http\Controllers\Api\CommandController;
use Illuminate\Support\Facades\Route;

Route::middleware(['verify.bot'])
    ->prefix('/')
    ->group(function () {
        Route::get('/command', [CommandController::class, 'index']);
        Route::get('/{command}', [CommandController::class, 'execute'])
            ->where('command', '.*');
    });
