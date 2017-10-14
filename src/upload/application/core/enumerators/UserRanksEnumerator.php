<?php
/**
 * User ranks enumerator
 *
 * PHP Version 5.5+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */

namespace application\core\enumerators;

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
    const player    = 0;
    const go        = 1;
    const sgo       = 2;
    const admin     = 3;
}

/* end of UserRanksEnumerator.php */
