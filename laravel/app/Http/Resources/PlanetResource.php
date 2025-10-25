<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->planet_id,
            'name' => $this->planet_name,
            'type' => $this->planet_type,
            'coordinates' => [
                'galaxy' => $this->planet_galaxy,
                'system' => $this->planet_system,
                'planet' => $this->planet_planet,
                'formatted' => $this->coordinates,
            ],
            'resources' => [
                'metal' => (float) $this->planet_metal,
                'crystal' => (float) $this->planet_crystal,
                'deuterium' => (float) $this->planet_deuterium,
            ],
            'production' => $this->planet_production ?? [
                'metal' => 0,
                'crystal' => 0,
                'deuterium' => 0,
            ],
            'fields' => [
                'used' => $this->planet_field_current,
                'max' => $this->planet_field_max,
                'percentage' => $this->planet_field_max > 0
                    ? round(($this->planet_field_current / $this->planet_field_max) * 100, 2)
                    : 0,
                'available' => $this->planet_field_max - $this->planet_field_current,
            ],
            'info' => [
                'image' => $this->planet_image,
                'diameter' => $this->planet_diameter,
                'temperature' => [
                    'min' => $this->planet_temp_min,
                    'max' => $this->planet_temp_max,
                    'average' => round(($this->planet_temp_min + $this->planet_temp_max) / 2),
                ],
            ],
            'debris' => $this->planet_debris,
            'owner' => new UserResource($this->whenLoaded('user')),
            'last_update' => $this->planet_last_update->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
