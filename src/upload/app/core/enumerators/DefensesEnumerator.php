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
    const defense_rocket_launcher = 401;
    const defense_light_laser = 402;
    const defense_heavy_laser = 403;
    const defense_gauss_cannon = 404;
    const defense_ion_cannon = 405;
    const defense_plasma_turret = 406;
    const defense_small_shield_dome = 407;
    const defense_large_shield_dome = 408;
    const defense_anti_ballistic_missile = 502;
    const defense_interplanetary_missile = 503;
}
