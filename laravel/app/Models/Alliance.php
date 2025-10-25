<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alliance extends BaseModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'alliances';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'alliance_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'alliance_name',
        'alliance_tag',
        'alliance_owner',
        'alliance_description',
        'alliance_web',
        'alliance_image',
        'alliance_request_notallow',
        'alliance_request',
        'alliance_request_waiting',
        'alliance_text_intern',
        'alliance_text_extern',
        'alliance_text_apply',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'alliance_request_notallow' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the alliance owner.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alliance_owner', 'user_id');
    }

    /**
     * Get all alliance members.
     */
    public function members(): HasMany
    {
        return $this->hasMany(User::class, 'alliance_id', 'alliance_id');
    }

    /**
     * Get member count.
     */
    public function getMemberCountAttribute(): int
    {
        return $this->members()->count();
    }

    /**
     * Check if alliance accepts applications.
     */
    public function acceptsApplications(): bool
    {
        return !$this->alliance_request_notallow;
    }

    /**
     * Scope: Search alliances (Full-Text Search).
     */
    public function scopeSearch($query, string $searchTerm)
    {
        return $query->whereRaw(
            "search_vector @@ plainto_tsquery('english', ?)",
            [$searchTerm]
        );
    }

    /**
     * Scope: Alliances accepting applications.
     */
    public function scopeAcceptingApplications($query)
    {
        return $query->where('alliance_request_notallow', false);
    }
}
