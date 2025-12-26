<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return redirect('admin/login');
});
Route::get('/test-auth', function() {
    return auth()->user();
});


// Route::get('/login', function () {
//     return redirect('admin/login');
// })->name('login');
