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
    const ship_small_cargo_ship = 202;
    const ship_big_cargo_ship = 203;
    const ship_light_fighter = 204;
    const ship_heavy_fighter = 205;
    const ship_cruiser = 206;
    const ship_battleship = 207;
    const ship_colony_ship = 208;
    const ship_recycler = 209;
    const ship_espionage_probe = 210;
    const ship_bomber = 211;
    const ship_solar_satellite = 212;
    const ship_destroyer = 213;
    const ship_deathstar = 214;
    const ship_battlecruiser = 215;
}
