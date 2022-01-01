<?php
/**
 * Defenses enumerator
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */

namespace App\core\enumerators;

/**
 * DefensesEnumerator Class
 *
 * @category Enumerator
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
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
