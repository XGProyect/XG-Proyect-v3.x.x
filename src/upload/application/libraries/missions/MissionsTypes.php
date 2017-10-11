<?php
/**
 * Missions Library
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

namespace application\libraries\missions;

/**
 * MissionsTypes Class
 *
 * @category Enumerator
 * @package  Libraries
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
abstract class MissionsTypes
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

/* end of MissionsTypes.php */
