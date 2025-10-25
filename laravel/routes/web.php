<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/

Route::get('/', function () {
    return response()->json([
        'app' => 'XG Proyect',
        'version' => '4.0.0',
        'framework' => 'Laravel 11',
        'database' => 'PostgreSQL 16',
        'status' => 'running',
    ]);
});

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});
