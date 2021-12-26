<?php
/**
 * User ranks enumerator
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */

namespace App\core\enumerators;

/**
 * UserRanksEnumerator Class
 *
 * @category Enumerator
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
abstract class UserRanksEnumerator
{
    public const PLAYER = 0;
    public const GO = 1;
    public const SGO = 2;
    public const ADMIN = 3;
}
