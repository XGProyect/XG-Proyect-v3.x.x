<?php
/**
 * Formula Library
 *
 * PHP Version 5.5+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */

namespace application\libraries;

use application\core\XGPCore;

/**
 * FormulaLib Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class FormulaLib extends XGPCore
{
    /**
     * __construct
     *
     * return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * phalanxRange
     *
     * @param int $phalanx_level Phalanx level
     *
     * return int
     */
    public function phalanxRange($phalanx_level)
    {
        $range = 0;

        if ($phalanx_level > 1) {

            $range  = pow($phalanx_level, 2) - 1;
        } elseif ($phalanx_level == 1) {

            $range  = 1;
        }

        return $range;
    }

    /**
     * missileRange
     *
     * @param int $impulse_drive_level Impulse drive level
     *
     * return int
     */
    public function missileRange($impulse_drive_level)
    {
        if ($impulse_drive_level > 0) {

            return ($impulse_drive_level * 5) - 1;
        }

        return 0;
    }
}

/* end of FormulaLib.php */
