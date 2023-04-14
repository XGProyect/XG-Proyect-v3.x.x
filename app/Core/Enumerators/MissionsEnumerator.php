<?php

declare(strict_types=1);

namespace App\Core\Enumerators;

abstract class MissionsEnumerator
{
    public const ATTACK = 1;
    public const ACS = 2;
    public const TRANSPORT = 3;
    public const DEPLOY = 4;
    public const STAY = 5;
    public const SPY = 6;
    public const COLONIZE = 7;
    public const RECYCLE = 8;
    public const DESTROY = 9;
    public const MISSILE = 10;
    public const EXPEDITION = 15;
}
