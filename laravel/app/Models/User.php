<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'users';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_name',
        'user_email',
        'user_password',
        'user_ip',
        'user_galaxy',
        'user_system',
        'user_planet',
        'alliance_id',
        'preference_lang',
        'preference_planet_sort',
        'preference_planet_order',
        'preference_spy_probes',
        'preference_vacation_mode',
        'preference_vacation_mode_until',
        'user_metal',
        'user_crystal',
        'user_deuterium',
        'user_dark_matter',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'user_password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_registration' => 'datetime',
        'user_lastlogin' => 'datetime',
        'user_banned' => 'boolean',
        'user_banned_until' => 'datetime',
        'premium_dark_matter_expire_time' => 'datetime',
        'premium_officer_commander_until' => 'datetime',
        'premium_officer_admiral_until' => 'datetime',
        'premium_officer_engineer_until' => 'datetime',
        'premium_officer_geologist_until' => 'datetime',
        'premium_officer_technocrat_until' => 'datetime',
        'preference_spy_probes' => 'boolean',
        'preference_vacation_mode' => 'boolean',
        'preference_vacation_mode_until' => 'datetime',
        'user_metal' => 'decimal:2',
        'user_crystal' => 'decimal:2',
        'user_deuterium' => 'decimal:2',
        'user_dark_matter' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the password for authentication.
     */
    public function getAuthPassword(): string
    {
        return $this->user_password;
    }

    /**
     * Get all planets owned by the user.
     */
    public function planets(): HasMany
    {
        return $this->hasMany(Planet::class, 'user_id', 'user_id')
            ->orderBy('planet_galaxy')
            ->orderBy('planet_system')
            ->orderBy('planet_planet');
    }

    /**
     * Get the home planet.
     */
    public function homePlanet(): BelongsTo
    {
        return $this->belongsTo(Planet::class, 'user_home_planet_id', 'planet_id');
    }

    /**
     * Get the current planet.
     */
    public function currentPlanet(): BelongsTo
    {
        return $this->belongsTo(Planet::class, 'user_current_planet_id', 'planet_id');
    }

    /**
     * Get user's alliance.
     */
    public function alliance(): BelongsTo
    {
        return $this->belongsTo(Alliance::class, 'alliance_id', 'alliance_id');
    }

    /**
     * Check if user is banned.
     */
    public function isBanned(): bool
    {
        if (!$this->user_banned) {
            return false;
        }

        if ($this->user_banned_until && $this->user_banned_until->isPast()) {
            $this->update([
                'user_banned' => false,
                'user_banned_until' => null,
                'user_ban_reason' => null,
            ]);

            return false;
        }

        return true;
    }

    /**
     * Check if user has active premium.
     */
    public function hasPremium(): bool
    {
        return $this->premium_dark_matter_expire_time &&
               $this->premium_dark_matter_expire_time->isFuture();
    }

    /**
     * Check if user has specific officer.
     */
    public function hasOfficer(string $officer): bool
    {
        $field = "premium_officer_{$officer}_until";

        return $this->$field && $this->$field->isFuture();
    }

    /**
     * Check if user is in vacation mode.
     */
    public function isOnVacation(): bool
    {
        if (!$this->preference_vacation_mode) {
            return false;
        }

        if ($this->preference_vacation_mode_until &&
            $this->preference_vacation_mode_until->isPast()) {
            $this->update([
                'preference_vacation_mode' => false,
                'preference_vacation_mode_until' => null,
            ]);

            return false;
        }

        return true;
    }

    /**
     * Scope: Search users by name or email (Full-Text Search).
     */
    public function scopeSearch($query, string $searchTerm)
    {
        return $query->whereRaw(
            "search_vector @@ plainto_tsquery('english', ?)",
            [$searchTerm]
        );
    }

    /**
     * Scope: Active users (not banned, not on vacation).
     */
    public function scopeActive($query)
    {
        return $query->where('user_banned', false)
            ->where('preference_vacation_mode', false);
    }

    /**
     * Scope: Users in specific galaxy.
     */
    public function scopeInGalaxy($query, int $galaxy)
    {
        return $query->where('user_galaxy', $galaxy);
    }
}
