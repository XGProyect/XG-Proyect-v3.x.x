<?php

declare(strict_types=1);

namespace App\Core\Enumerators;

abstract class DefensesEnumerator
{
    public const defense_rocket_launcher = 401;
    public const defense_light_laser = 402;
    public const defense_heavy_laser = 403;
    public const defense_gauss_cannon = 404;
    public const defense_ion_cannon = 405;
    public const defense_plasma_turret = 406;
    public const defense_small_shield_dome = 407;
    public const defense_large_shield_dome = 408;
    public const defense_anti_ballistic_missile = 502;
    public const defense_interplanetary_missile = 503;
}
