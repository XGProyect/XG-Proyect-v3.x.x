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
    public const ESPIO = 0;
    public const COMBAT = 1;
    public const EXP = 2;
    public const ALLY = 3;
    public const USER = 4;
    public const GENERAL = 5;
}
