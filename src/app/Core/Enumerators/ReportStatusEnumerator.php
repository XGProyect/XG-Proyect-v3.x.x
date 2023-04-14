<?php

declare(strict_types=1);

namespace App\Core\Enumerators;

abstract class ReportStatusEnumerator
{
    public const fleetDestroyed = 1;
    public const fleetNotDestroyed = 0;
}
