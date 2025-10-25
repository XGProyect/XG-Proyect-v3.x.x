<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseModel extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Indicates if all mass assignment is enabled.
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        return $this->table ?? str_replace(
            '\\',
            '',
            \Illuminate\Support\Str::snake(\Illuminate\Support\Str::pluralStudly(class_basename($this)))
        );
    }
}
