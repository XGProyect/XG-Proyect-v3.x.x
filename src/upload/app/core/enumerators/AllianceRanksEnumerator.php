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
    const DELETE = 1;
    const KICK = 2;
    const APPLICATIONS = 3;
    const VIEW_MEMBER_LIST = 4;
    const APPLICATION_MANAGEMENT = 5;
    const ADMINISTRATION = 6;
    const ONLINE_STATUS = 7;
    const SEND_CIRCULAR = 8;
    const RIGHT_HAND = 9;
}
