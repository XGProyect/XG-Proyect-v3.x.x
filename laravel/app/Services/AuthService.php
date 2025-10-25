<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private readonly PlanetService $planetService
    ) {}

    /**
     * Register a new user.
     */
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // Find free coordinates
            $galaxy = random_int(1, 9);
            $system = random_int(1, 499);
            $planetPosition = $this->planetService->findFreeCoordinates($galaxy, $system);

            if ($planetPosition === null) {
                // Try another system
                $system = random_int(1, 499);
                $planetPosition = $this->planetService->findFreeCoordinates($galaxy, $system);

                if ($planetPosition === null) {
                    throw new \Exception('No free coordinates available');
                }
            }

            // Create user
            $user = User::create([
                'user_name' => $data['user_name'],
                'user_email' => $data['user_email'],
                'user_password' => Hash::make($data['password']),
                'user_galaxy' => $galaxy,
                'user_system' => $system,
                'user_planet' => $planetPosition,
                'user_metal' => 500,
                'user_crystal' => 500,
                'user_deuterium' => 0,
                'user_dark_matter' => 0,
                'preference_lang' => $data['preference_lang'] ?? 'en',
                'user_registration' => now(),
            ]);

            // Create home planet
            $homePlanet = $this->planetService->createPlanet(
                $user,
                $galaxy,
                $system,
                $planetPosition,
                'Homeworld'
            );

            // Update user with home planet
            $user->update([
                'user_home_planet_id' => $homePlanet->planet_id,
                'user_current_planet_id' => $homePlanet->planet_id,
            ]);

            return $user->fresh();
        });
    }

    /**
     * Attempt to log in a user.
     */
    public function login(string $email, string $password): ?User
    {
        $user = User::where('user_email', $email)->first();

        if (!$user || !Hash::check($password, $user->user_password)) {
            return null;
        }

        if ($user->isBanned()) {
            throw new \Exception('Account is banned');
        }

        // Update last login
        $user->update(['user_lastlogin' => now()]);

        return $user;
    }

    /**
     * Create API token for user.
     */
    public function createToken(User $user, string $deviceName = 'api'): string
    {
        // Revoke old tokens
        $user->tokens()->delete();

        // Create new token
        return $user->createToken($deviceName)->plainTextToken;
    }

    /**
     * Logout user (revoke tokens).
     */
    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }
}
