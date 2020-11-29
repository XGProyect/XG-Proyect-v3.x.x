<?php
/**
 * Messages enumerator
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
 * MessagesEnumerator Class
 *
 * @category Enumerator
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
abstract class MessagesEnumerator
{
    const ESPIO = 0;
    const COMBAT = 1;
    const EXP = 2;
    const ALLY = 3;
    const USER = 4;
    const GENERAL = 5;
}
