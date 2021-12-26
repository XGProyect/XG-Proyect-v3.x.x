<?php
/**
 * Ships enumerator
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
 * ShipsEnumerator Class
 *
 * @category Enumerator
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
abstract class ShipsEnumerator
{
    public const ship_small_cargo_ship = 202;
    public const ship_big_cargo_ship = 203;
    public const ship_light_fighter = 204;
    public const ship_heavy_fighter = 205;
    public const ship_cruiser = 206;
    public const ship_battleship = 207;
    public const ship_colony_ship = 208;
    public const ship_recycler = 209;
    public const ship_espionage_probe = 210;
    public const ship_bomber = 211;
    public const ship_solar_satellite = 212;
    public const ship_destroyer = 213;
    public const ship_deathstar = 214;
    public const ship_battlecruiser = 215;
}
