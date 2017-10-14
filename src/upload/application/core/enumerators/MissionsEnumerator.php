<?php
/**
 * Missions enumerator
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

namespace application\core\enumerators;

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
    const attack        = 1;
    const acs           = 2;
    const transport     = 3;
    const deploy        = 4;
    const stay          = 5;
    const spy           = 6;
    const colonize      = 7;
    const recycle       = 8;
    const destroy       = 9;
    const missile       = 10;
    const expedition    = 15;
}

/* end of MissionsEnumerator.php */
