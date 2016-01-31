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
	 * __construct()
	 */
	public function __construct ()
	{
		parent::__construct();
	}

	/**
	 * method phalanx_range
	 * param $phalanx_level
	 * return the plalanx range
	 */
	public function phalanx_range ( $phalanx_level )
	{
		$range = 0;

		if ( $phalanx_level > 1 )
		{
			$range = pow ( $phalanx_level , 2 ) - 1;
		}
		elseif ( $phalanx_level == 1 )
		{
			$range = 1;
		}

		return $range;
	}

	/**
	 * method missile_range
	 * param $impulse_drive_level
	 * return the missile range
	 */
	public function missile_range ( $impulse_drive_level )
	{
		if ( $impulse_drive_level > 0 )
		{
			return ( $impulse_drive_level * 5 ) - 1;
		}

		return 0;
	}
}

/* end of FormulaLib.php */
