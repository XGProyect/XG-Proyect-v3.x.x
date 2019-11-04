<?php
/**
 * Deploy Library
 *
 * PHP Version 7.1+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\libraries\missions;

use application\libraries\FleetsLib;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;

/**
 * Deploy Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Deploy extends Missions
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
     * deployMission
     *
     * @param array $fleet_row Fleet row
     *
     * @return void
     */
    public function deployMission($fleet_row)
    {
        if ($fleet_row['fleet_mess'] == 0) {
            if ($fleet_row['fleet_start_time'] <= time()) {
                $target_coords = sprintf(
                    $this->langs['sys_adress_planet'], $fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet']
                );

                $target_resources = sprintf(
                    $this->langs['sys_stay_mess_goods'], $this->langs['Metal'], FormatLib::prettyNumber($fleet_row['fleet_resource_metal']), $this->langs['Crystal'], FormatLib::prettyNumber($fleet_row['fleet_resource_crystal']), $this->langs['Deuterium'], FormatLib::prettyNumber($fleet_row['fleet_resource_deuterium'])
                );

                $target_message = $this->langs['sys_stay_mess_start'] . "<a href=\"game.php?page=galaxy&mode=3&galaxy=" .
                    $fleet_row['fleet_end_galaxy'] . "&system=" . $fleet_row['fleet_end_system'] . "\">";
                $target_message .= $target_coords . "</a>" . $this->langs['sys_stay_mess_end'] .
                    "<br />" . $target_resources;

                FunctionsLib::sendMessage(
                    $fleet_row['fleet_target_owner'], '', $fleet_row['fleet_start_time'], 5, $this->langs['sys_mess_qg'], $this->langs['sys_stay_mess_stay'], $target_message
                );

                parent::restoreFleet($fleet_row, false);
                parent::removeFleet($fleet_row['fleet_id']);
            }
        } else {

            if ($fleet_row['fleet_end_time'] <= time()) {

                $message = sprintf(
                    $this->langs['sys_tran_mess_user'], '', FleetsLib::targetLink($fleet_row, ''), FormatLib::prettyNumber($fleet_row['fleet_resource_metal']), $this->langs['Metal'], FormatLib::prettyNumber($fleet_row['fleet_resource_crystal']), $this->langs['Crystal'], FormatLib::prettyNumber($fleet_row['fleet_resource_deuterium']), $this->langs['Deuterium']
                );

                FunctionsLib::sendMessage(
                    $fleet_row['fleet_owner'], '', $fleet_row['fleet_end_time'], 5, $this->langs['sys_mess_qg'], $this->langs['sys_mess_fleetback'], $message
                );

                parent::restoreFleet($fleet_row, true);
                parent::removeFleet($fleet_row['fleet_id']);
            }
        }
    }
}

/* end of deploy.php */
