<?php

declare(strict_types=1);

namespace App\Core\Enumerators;

abstract class PreferencesEnumerator
{
    public const order = [
        'emergence' => 0,
        'coordinates' => 1,
        'alphabet' => 2,
        'size' => 3,
        'used_fields' => 4,
    ];

    public const sequence = [
        'up' => 0,
        'down' => 1,
    ];
}
