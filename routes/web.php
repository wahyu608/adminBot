<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/', function () {
    return redirect('admin/login');
});
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::get('/test-auth', function() {
    return auth()->user();
});

Route::middleware('auth')->group(function () {

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->name('verification.send');

});

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/admin/login?verified=1');
})->name('verification.verify');

Route::get('/debug-app', function () {
    return [
        'config_app_url' => config('app.url'),
        'env_app_url' => env('APP_URL'),
        'request_host' => request()->getHost(),
        'request_scheme' => request()->getScheme(),
    ];
});

