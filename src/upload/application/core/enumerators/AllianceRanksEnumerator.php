<?php
/**
 * Alliance ranks Enumerator
 *
 * PHP Version 5.5+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\libraries\enumerators;

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

    const name = 1;
    const send_circular = 2;
    const delete = 3;
    const kick = 4;
    const applications = 5;
    const administration = 6;
    const application_management = 7;
    const view_member_list = 8;
    const online_status = 9;
    const right_hand = 10;

}

/* end of AllianceRanksEnumerator.php */
