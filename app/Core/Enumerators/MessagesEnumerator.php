<?php

declare(strict_types=1);

namespace App\Core\Enumerators;

abstract class MessagesEnumerator
{
    public const ESPIO = 0;
    public const COMBAT = 1;
    public const EXP = 2;
    public const ALLY = 3;
    public const USER = 4;
    public const GENERAL = 5;
}
