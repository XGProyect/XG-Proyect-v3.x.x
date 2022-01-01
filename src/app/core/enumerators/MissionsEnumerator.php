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
