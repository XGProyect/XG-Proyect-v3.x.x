<?php

/**
 * Deploy Library.
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

use application\libraries\FormatLib;
use application\libraries\FunctionsLib;

/**
 * Deploy Class.
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
class Deploy extends Missions
{
    /**
     * __construct.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * deployMission.
     *
     * @param array $fleet_row Fleet row
     */
    public function deployMission($fleet_row)
    {
        if ($fleet_row['fleet_mess'] == 0) {
            if ($fleet_row['fleet_start_time'] <= time()) {
                $target_coords = sprintf(
                    $this->_lang['sys_adress_planet'],
                    $fleet_row['fleet_end_galaxy'],
                    $fleet_row['fleet_end_system'],
                    $fleet_row['fleet_end_planet']
                );

                $target_resources = sprintf(
                    $this->_lang['sys_stay_mess_goods'],
                    $this->_lang['Metal'],
                    FormatLib::pretty_number($fleet_row['fleet_resource_metal']),
                    $this->_lang['Crystal'],
                    FormatLib::pretty_number($fleet_row['fleet_resource_crystal']),
                    $this->_lang['Deuterium'],
                    FormatLib::pretty_number($fleet_row['fleet_resource_deuterium'])
                );

                $target_message = $this->_lang['sys_stay_mess_start'] . '<a href="game.php?page=galaxy&mode=3&galaxy=' .
                    $fleet_row['fleet_end_galaxy'] . '&system=' . $fleet_row['fleet_end_system'] . '">';
                $target_message .= $target_coords . '</a>' . $this->_lang['sys_stay_mess_end'] .
                    '<br />' . $target_resources;

                FunctionsLib::send_message(
                    $fleet_row['fleet_target_owner'],
                    '',
                    $fleet_row['fleet_start_time'],
                    5,
                    $this->_lang['sys_mess_qg'],
                    $this->_lang['sys_stay_mess_stay'],
                    $target_message
                );

                parent::restore_fleet($fleet_row, false);
                parent::remove_fleet($fleet_row['fleet_id']);
            }
        } else {
            if ($fleet_row['fleet_end_time'] <= time()) {
                $target_coords = sprintf(
                        $this->_lang['sys_adress_planet'],
                        $fleet_row['fleet_start_galaxy'],
                        $fleet_row['fleet_start_system'],
                        $fleet_row['fleet_start_planet']
                    );

                $target_resources = sprintf(
                        $this->_lang['sys_stay_mess_goods'],
                        $this->_lang['Metal'],
                        FormatLib::pretty_number($fleet_row['fleet_resource_metal']),
                        $this->_lang['Crystal'],
                        FormatLib::pretty_number($fleet_row['fleet_resource_crystal']),
                        $this->_lang['Deuterium'],
                        FormatLib::pretty_number($fleet_row['fleet_resource_deuterium'])
                    );

                $target_message = $this->_lang['sys_stay_mess_back'] .
                        '<a href="game.php?page=galaxy&mode=3&galaxy=' .
                        $fleet_row['fleet_start_galaxy'] . '&system=' . $fleet_row['fleet_start_system'] . '">';
                $target_message .= $target_coords . '</a>' . $this->_lang['sys_stay_mess_bend'] .
                        '<br />' . $target_resources;

                FunctionsLib::send_message(
                        $fleet_row['fleet_owner'],
                        '',
                        $fleet_row['fleet_end_time'],
                        5,
                        $this->_lang['sys_mess_qg'],
                        $this->_lang['sys_mess_fleetback'],
                        $target_message
                    );

                parent::restore_fleet($fleet_row, true);
                parent::remove_fleet($fleet_row['fleet_id']);
            }
        }
    }
}

/* end of deploy.php */
