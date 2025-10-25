<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PlanetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
|
*/

Route::prefix('v1')->group(function () {

    // Health check
    Route::get('/health', function () {
        try {
            $dbStatus = DB::connection()->getPdo() ? 'connected' : 'disconnected';
        } catch (\Exception $e) {
            $dbStatus = 'disconnected';
        }

        try {
            Cache::store('redis')->put('health_check', true, 1);
            $redisStatus = Cache::store('redis')->get('health_check') ? 'connected' : 'disconnected';
        } catch (\Exception $e) {
            $redisStatus = 'disconnected';
        }

        return response()->json([
            'status' => 'ok',
            'database' => $dbStatus,
            'redis' => $redisStatus,
            'timestamp' => now()->toIso8601String(),
        ]);
    });

    // Public routes
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {

        // Auth routes
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });

        // User routes
        Route::get('/user', function (Request $request) {
            return response()->json([
                'success' => true,
                'data' => new \App\Http\Resources\UserResource($request->user()->load(['planets', 'alliance'])),
            ]);
        });

        // Planets API
        Route::apiResource('planets', PlanetController::class)->only(['index', 'show', 'update']);

        // Fleets API (placeholder)
        Route::prefix('fleets')->group(function () {
            Route::get('/', function () {
                return response()->json([
                    'success' => true,
                    'message' => 'Fleets list - implementation coming soon',
                    'data' => [],
                ]);
            });
        });

        // Research API (placeholder)
        Route::prefix('research')->group(function () {
            Route::get('/', function () {
                return response()->json([
                    'success' => true,
                    'message' => 'Research list - implementation coming soon',
                    'data' => [],
                ]);
            });
        });

        // Alliances API (placeholder)
        Route::prefix('alliances')->group(function () {
            Route::get('/', function () {
                return response()->json([
                    'success' => true,
                    'message' => 'Alliances list - implementation coming soon',
                    'data' => [],
                ]);
            });
        });

    });

});
