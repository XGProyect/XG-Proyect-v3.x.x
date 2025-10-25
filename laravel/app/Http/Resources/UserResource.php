<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->user_id,
            'name' => $this->user_name,
            'email' => $this->user_email,
            'coordinates' => [
                'galaxy' => $this->user_galaxy,
                'system' => $this->user_system,
                'planet' => $this->user_planet,
                'formatted' => "{$this->user_galaxy}:{$this->user_system}:{$this->user_planet}",
            ],
            'resources' => [
                'metal' => (float) $this->user_metal,
                'crystal' => (float) $this->user_crystal,
                'deuterium' => (float) $this->user_deuterium,
                'dark_matter' => (float) $this->user_dark_matter,
            ],
            'premium' => [
                'active' => $this->hasPremium(),
                'expires_at' => $this->premium_dark_matter_expire_time?->toIso8601String(),
                'officers' => [
                    'commander' => $this->hasOfficer('commander'),
                    'admiral' => $this->hasOfficer('admiral'),
                    'engineer' => $this->hasOfficer('engineer'),
                    'geologist' => $this->hasOfficer('geologist'),
                    'technocrat' => $this->hasOfficer('technocrat'),
                ],
            ],
            'status' => [
                'banned' => $this->isBanned(),
                'vacation' => $this->isOnVacation(),
                'vacation_until' => $this->preference_vacation_mode_until?->toIso8601String(),
            ],
            'preferences' => [
                'language' => $this->preference_lang,
                'planet_sort' => $this->preference_planet_sort,
                'planet_order' => $this->preference_planet_order,
                'spy_probes' => $this->preference_spy_probes,
            ],
            'alliance' => new AllianceResource($this->whenLoaded('alliance')),
            'planets' => PlanetResource::collection($this->whenLoaded('planets')),
            'registered_at' => $this->user_registration->toIso8601String(),
            'last_login' => $this->user_lastlogin?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
