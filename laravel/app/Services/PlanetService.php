<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Planet;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PlanetService
{
    /**
     * Create a new planet for user.
     */
    public function createPlanet(
        User $user,
        int $galaxy,
        int $system,
        int $planet,
        string $name = 'Homeworld',
        string $type = 'planet'
    ): Planet {
        return DB::transaction(function () use ($user, $galaxy, $system, $planet, $name, $type) {
            $newPlanet = Planet::create([
                'user_id' => $user->user_id,
                'planet_name' => $name,
                'planet_galaxy' => $galaxy,
                'planet_system' => $system,
                'planet_planet' => $planet,
                'planet_type' => $type,
                'planet_metal' => 500,
                'planet_crystal' => 500,
                'planet_deuterium' => 0,
                'planet_image' => $this->getRandomPlanetImage(),
                'planet_diameter' => random_int(9500, 15000),
                'planet_field_current' => 0,
                'planet_field_max' => 163,
                'planet_temp_min' => random_int(-40, 0),
                'planet_temp_max' => random_int(10, 40),
                'planet_fields' => [
                    'used' => 0,
                    'max' => 163,
                ],
                'planet_production' => [
                    'metal' => 30,
                    'crystal' => 15,
                    'deuterium' => 0,
                    'energy' => 0,
                ],
                'planet_last_update' => now(),
            ]);

            return $newPlanet;
        });
    }

    /**
     * Abandon a planet.
     */
    public function abandonPlanet(Planet $planet): void
    {
        if ($planet->user->user_home_planet_id === $planet->planet_id) {
            throw new \Exception('Cannot abandon home planet');
        }

        DB::transaction(function () use ($planet) {
            $planet->delete();
        });
    }

    /**
     * Rename a planet.
     */
    public function renamePlanet(Planet $planet, string $name): Planet
    {
        $planet->update(['planet_name' => $name]);

        return $planet->fresh();
    }

    /**
     * Calculate storage capacity based on building levels.
     */
    public function getStorageCapacity(Planet $planet): array
    {
        // This will be enhanced when buildings are implemented
        $baseStorage = 10000;

        return [
            'metal' => $baseStorage,
            'crystal' => $baseStorage,
            'deuterium' => $baseStorage,
        ];
    }

    /**
     * Update planet resources based on production rates.
     */
    public function updatePlanetResources(Planet $planet): Planet
    {
        $planet->updateResources();

        return $planet->fresh();
    }

    /**
     * Get random planet image.
     */
    private function getRandomPlanetImage(): string
    {
        $images = [
            'normaltempplanet01',
            'normaltempplanet02',
            'normaltempplanet03',
            'normaltempplanet04',
            'normaltempplanet05',
            'normaltempplanet06',
            'normaltempplanet07',
        ];

        return $images[array_rand($images)];
    }

    /**
     * Check if coordinates are available.
     */
    public function areCoordinatesAvailable(int $galaxy, int $system, int $planet): bool
    {
        return !Planet::where('planet_galaxy', $galaxy)
            ->where('planet_system', $system)
            ->where('planet_planet', $planet)
            ->exists();
    }

    /**
     * Find free coordinates in galaxy/system.
     */
    public function findFreeCoordinates(int $galaxy, int $system): ?int
    {
        for ($position = 1; $position <= 15; $position++) {
            if ($this->areCoordinatesAvailable($galaxy, $system, $position)) {
                return $position;
            }
        }

        return null;
    }
}
