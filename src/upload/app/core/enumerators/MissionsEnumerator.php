<?php
/**
 * Missions enumerator
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
 * MissionsEnumerator Class
 *
 * @category Enumerator
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
abstract class MissionsEnumerator
{
    const ATTACK = 1;
    const ACS = 2;
    const TRANSPORT = 3;
    const DEPLOY = 4;
    const STAY = 5;
    const SPY = 6;
    const COLONIZE = 7;
    const RECYCLE = 8;
    const DESTROY = 9;
    const MISSILE = 10;
    const EXPEDITION = 15;
}
