<?php
/**
 * Alliance ranks Enumerator
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
 * AllianceRanksEnumerator Class
 *
 * @category Enumerator
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
abstract class AllianceRanksEnumerator
{
    public const DELETE = 1;
    public const KICK = 2;
    public const APPLICATIONS = 3;
    public const VIEW_MEMBER_LIST = 4;
    public const APPLICATION_MANAGEMENT = 5;
    public const ADMINISTRATION = 6;
    public const ONLINE_STATUS = 7;
    public const SEND_CIRCULAR = 8;
    public const RIGHT_HAND = 9;
}
