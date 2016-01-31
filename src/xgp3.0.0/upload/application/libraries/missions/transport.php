<?php

/**
 * Transport Library.
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

use application\libraries\FleetsLib;
use application\libraries\FunctionsLib;

/**
 * Transport Class.
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
class Transport extends Missions
{
    /**
     * __construct().
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * method transport_mission
     * param $fleet_row
     * return the transport result.
     */
    public function transportMission($fleet_row)
    {
        $transport_check = parent::$db->queryFetch(
                'SELECT pc1.`planet_user_id` AS start_id,
                pc1.`planet_name` AS start_name,
                pc2.`planet_user_id` AS target_id,
                pc2.`planet_name` AS target_name
                FROM ' . PLANETS . ' AS pc1, ' . PLANETS . " AS pc2
                WHERE pc1.planet_galaxy = '" . $fleet_row['fleet_start_galaxy'] . "' AND
                pc1.`planet_system` = '" . $fleet_row['fleet_start_system'] . "' AND
                pc1.`planet_planet` = '" . $fleet_row['fleet_start_planet'] . "' AND
                pc1.`planet_type` = '" . $fleet_row['fleet_start_type'] . "' AND
                pc2.`planet_galaxy` = '" . $fleet_row['fleet_end_galaxy'] . "' AND
                pc2.`planet_system` = '" . $fleet_row['fleet_end_system'] . "' AND
                pc2.`planet_planet` = '" . $fleet_row['fleet_end_planet'] . "' AND
                pc2.`planet_type` = '" . $fleet_row['fleet_end_type'] . "'"
            );

        // SOME REQUIRED VALUES
        $start_name      = $transport_check['start_name'];
        $start_owner_id  = $transport_check['start_id'];
        $target_name     = $transport_check['target_name'];
        $target_owner_id = $transport_check['target_id'];

        // DIFFERENT TYPES OF MESSAGES
        $message[1] = sprintf($this->_lang['sys_tran_mess_owner'], $target_name, FleetsLib::target_link($fleet_row, ''), $fleet_row['fleet_resource_metal'], $this->_lang['Metal'], $fleet_row['fleet_resource_crystal'], $this->_lang['Crystal'], $fleet_row['fleet_resource_deuterium'], $this->_lang['Deuterium']);
        $message[2] = sprintf($this->_lang['sys_tran_mess_user'], $start_name, FleetsLib::start_link($fleet_row, ''), $target_name, FleetsLib::target_link($fleet_row, ''), $fleet_row['fleet_resource_metal'], $this->_lang['Metal'], $fleet_row['fleet_resource_crystal'], $this->_lang['Crystal'], $fleet_row['fleet_resource_deuterium'], $this->_lang['Deuterium']);
        $message[3] = sprintf($this->_lang['sys_tran_mess_back'], $start_name, FleetsLib::start_link($fleet_row, ''));

        if ($fleet_row['fleet_mess'] == 0) {
            if ($fleet_row['fleet_start_time'] < time()) {
                parent::store_resources($fleet_row, false);

                $this->transport_message($start_owner_id, $message[1], $fleet_row['fleet_start_time'], $this->_lang['sys_mess_transport']);

                if ($target_owner_id != $start_owner_id) {
                    // MESSAGE FOR THE OTHER USER, IN CASE WE ARE TRANSPORTING TO ANOTHER USER

                    $this->transport_message($target_owner_id, $message[2], $fleet_row['fleet_start_time'], $this->_lang['sys_mess_transport']);
                }

                parent::$db->query('UPDATE ' . FLEETS . " SET
										`fleet_resource_metal` = '0' ,
										`fleet_resource_crystal` = '0' ,
										`fleet_resource_deuterium` = '0' ,
										`fleet_mess` = '1'
										WHERE `fleet_id` = '" . (int) $fleet_row['fleet_id'] . "'
										LIMIT 1 ;");
            }
        } elseif ($fleet_row['fleet_end_time'] < time()) {
            $this->transport_message($start_owner_id, $message[3], $fleet_row['fleet_end_time'], $this->_lang['sys_mess_fleetback']);

            parent::restore_fleet($fleet_row, true);
            parent::remove_fleet($fleet_row['fleet_id']);
        }
    }

    /**
     * method transport_message
     * param $owner
     * param $message
     * param $time
     * param $status_message
     * return send a message with the transport details.
     */
    private function transport_message($owner, $message, $time, $status_message)
    {
        FunctionsLib::send_message($owner, '', $time, 5, $this->_lang['sys_mess_tower'], $status_message, $message);
    }
}

/* end of transport.php */
