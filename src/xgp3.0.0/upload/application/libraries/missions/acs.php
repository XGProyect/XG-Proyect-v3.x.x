<?php

/**
 * Acs Library.
 *
 * PHP Version 5.5+
 *
 * @category Library
 *
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 *
 * @link     http://www.xgproyect.org
 *
 * @version  3.0.0
 */

namespace application\libraries\missions;

/**
 * Acs Class.
 *
 * @category Classes
 *
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 *
 * @link     http://www.xgproyect.org
 *
 * @version  3.0.0
 */
class Acs extends Missions
{
    /**
     * __construct.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * acsMission.
     *
     * @param array $fleet_row Fleet row
     */
    public function acsMission($fleet_row)
    {
        if ($fleet_row['fleet_mess'] == 0 && $fleet_row['fleet_start_time'] > time()) {
            parent::return_fleet($fleet_row['fleet_id']);
        }

        if ($fleet_row['fleet_end_time'] <= time()) {
            parent::restore_fleet($fleet_row);
            parent::remove_fleet($fleet_row['fleet_id']);
        }
    }
}

/* end of acs.php */
