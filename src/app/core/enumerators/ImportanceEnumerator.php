<?php

declare(strict_types=1);

namespace App\Core\Enumerators;

abstract class ImportanceEnumerator
{
    public const unimportant = 0;
    public const normal = 1;
    public const important = 2;
}
