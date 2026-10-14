<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/', function () {
    return view('welcome');
});

Route::get('/clear-O', function () {
    $exitCode = Artisan::call('optimize:clear');
    // return what you want
})->middleware(['JwtMiddleware', 'CheckUserRole:Admin']);
Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('cache:clear');

    return '<h1>Cache facade value cleared</h1>';
})->middleware(['JwtMiddleware', 'CheckUserRole:Admin']);

// Reoptimized class loader:
Route::get('/optimize', function () {
    $exitCode = Artisan::call('optimize');

    return '<h1>Reoptimized class loader</h1>';
})->middleware(['JwtMiddleware', 'CheckUserRole:Admin']);

// Route cache:
Route::get('/route-cache', function () {
    $exitCode = Artisan::call('route:cache');

    return '<h1>Routes cached</h1>';
})->middleware(['JwtMiddleware', 'CheckUserRole:Admin']);

// Clear Route cache:
Route::get('/route-clear', function () {
    $exitCode = Artisan::call('route:clear');

    return '<h1>Route cache cleared</h1>';
})->middleware(['JwtMiddleware', 'CheckUserRole:Admin']);

// Clear View cache:
Route::get('/view-clear', function () {
    $exitCode = Artisan::call('view:clear');

    return '<h1>View cache cleared</h1>';
})->middleware(['JwtMiddleware', 'CheckUserRole:Admin']);

// Clear Config cache:
Route::get('/config-cache', function () {
    $exitCode = Artisan::call('config:cache');

    return '<h1>Clear Config cleared</h1>';
})->middleware(['JwtMiddleware', 'CheckUserRole:Admin']);
