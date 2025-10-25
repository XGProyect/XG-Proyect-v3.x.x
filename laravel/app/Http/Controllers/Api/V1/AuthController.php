<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->register($request->validated());
            $token = $this->authService->createToken($user, 'web');

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => new UserResource($user->load(['homePlanet', 'planets'])),
                    'token' => $token,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Login user.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->login(
                $request->validated('user_email'),
                $request->validated('password')
            );

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            $token = $this->authService->createToken(
                $user,
                $request->validated('remember') ? 'web-remember' : 'web'
            );

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => new UserResource($user->load(['homePlanet', 'currentPlanet', 'alliance'])),
                    'token' => $token,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get current authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user()->load(['homePlanet', 'currentPlanet', 'planets', 'alliance'])),
        ]);
    }
}
