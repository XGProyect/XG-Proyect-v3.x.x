<?php
/**
 * Acs Library
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
namespace application\libraries\missions;

/**
 * Acs Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Acs extends Missions
{

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * acsMission
     *
     * @param array $fleet_row Fleet row
     *
     * @return void
     */
    public function acsMission($fleet_row)
    {
        if ($fleet_row['fleet_mess'] == 0 && $fleet_row['fleet_start_time'] > time()) {

            parent::returnFleet($fleet_row['fleet_id']);
        }

        if ($fleet_row['fleet_end_time'] <= time()) {

            parent::restoreFleet($fleet_row);
            parent::removeFleet($fleet_row['fleet_id']);
        }
    }
}

/* end of acs.php */
