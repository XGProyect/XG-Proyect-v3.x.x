<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Planet extends BaseModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'planets';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'planet_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'planet_name',
        'planet_galaxy',
        'planet_system',
        'planet_planet',
        'planet_type',
        'planet_fields',
        'planet_debris',
        'planet_production',
        'planet_metal',
        'planet_crystal',
        'planet_deuterium',
        'planet_image',
        'planet_diameter',
        'planet_field_current',
        'planet_field_max',
        'planet_temp_min',
        'planet_temp_max',
        'planet_last_update',
        'planet_last_jump_time',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'planet_fields' => 'array',      // PostgreSQL JSONB
        'planet_debris' => 'array',      // PostgreSQL JSONB
        'planet_production' => 'array',  // PostgreSQL JSONB
        'planet_metal' => 'decimal:2',
        'planet_crystal' => 'decimal:2',
        'planet_deuterium' => 'decimal:2',
        'planet_last_update' => 'datetime',
        'planet_last_jump_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the planet owner.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get galaxy coordinates as string.
     */
    public function getCoordinatesAttribute(): string
    {
        return "{$this->planet_galaxy}:{$this->planet_system}:{$this->planet_planet}";
    }

    /**
     * Update resources based on production.
     */
    public function updateResources(): void
    {
        $timeDiff = now()->diffInSeconds($this->planet_last_update);

        $production = $this->planet_production ?? [
            'metal' => 0,
            'crystal' => 0,
            'deuterium' => 0,
        ];

        // Calculate production per second
        $metalProduced = ($production['metal'] ?? 0) * $timeDiff / 3600;
        $crystalProduced = ($production['crystal'] ?? 0) * $timeDiff / 3600;
        $deuteriumProduced = ($production['deuterium'] ?? 0) * $timeDiff / 3600;

        $this->update([
            'planet_metal' => $this->planet_metal + $metalProduced,
            'planet_crystal' => $this->planet_crystal + $crystalProduced,
            'planet_deuterium' => $this->planet_deuterium + $deuteriumProduced,
            'planet_last_update' => now(),
        ]);
    }

    /**
     * Check if planet has enough resources.
     */
    public function hasResources(float $metal, float $crystal, float $deuterium): bool
    {
        return $this->planet_metal >= $metal &&
               $this->planet_crystal >= $crystal &&
               $this->planet_deuterium >= $deuterium;
    }

    /**
     * Deduct resources from planet.
     */
    public function deductResources(float $metal, float $crystal, float $deuterium): bool
    {
        if (!$this->hasResources($metal, $crystal, $deuterium)) {
            return false;
        }

        $this->update([
            'planet_metal' => $this->planet_metal - $metal,
            'planet_crystal' => $this->planet_crystal - $crystal,
            'planet_deuterium' => $this->planet_deuterium - $deuterium,
        ]);

        return true;
    }

    /**
     * Scope: Search planets by name (Full-Text Search).
     */
    public function scopeSearch($query, string $searchTerm)
    {
        return $query->whereRaw(
            "search_vector @@ plainto_tsquery('english', ?)",
            [$searchTerm]
        );
    }

    /**
     * Scope: Get planets in galaxy.
     */
    public function scopeInGalaxy($query, int $galaxy, ?int $system = null)
    {
        $query->where('planet_galaxy', $galaxy);

        if ($system !== null) {
            $query->where('planet_system', $system);
        }

        return $query;
    }

    /**
     * Scope: Get only planets (exclude moons/debris).
     */
    public function scopeOnlyPlanets($query)
    {
        return $query->where('planet_type', 'planet');
    }

    /**
     * Scope: Get only moons.
     */
    public function scopeOnlyMoons($query)
    {
        return $query->where('planet_type', 'moon');
    }
}
