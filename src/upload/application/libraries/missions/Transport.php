<?php
/**
 * Transport Library
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

use application\libraries\FleetsLib;
use application\libraries\FunctionsLib;

/**
 * Transport Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Transport extends Missions
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
     * transportMission
     *
     * @param array $fleet_row Fleet row
     *
     * @return void
     */
    public function transportMission($fleet_row)
    {
        $friendly_planet = $this->Missions_Model->getFriendlyPlanetData([
            'coords' => [
                'start' => [
                    'galaxy' => $fleet_row['fleet_start_galaxy'],
                    'system' => $fleet_row['fleet_start_system'],
                    'planet' => $fleet_row['fleet_start_planet'],
                    'type' => $fleet_row['fleet_start_type']
                ],
                'end' => [
                    'galaxy' => $fleet_row['fleet_end_galaxy'],
                    'system' => $fleet_row['fleet_end_system'],
                    'planet' => $fleet_row['fleet_end_planet'],
                    'type' => $fleet_row['fleet_end_type']
                ]
            ]
        ]);

        // SOME REQUIRED VALUES
        $start_name         = $friendly_planet['start_name'];
        $start_owner_id     = $friendly_planet['start_id'];
        $target_name        = $friendly_planet['target_name'];
        $target_owner_id    = $friendly_planet['target_id'];

        // DIFFERENT TYPES OF MESSAGES
        $message[1] = sprintf(
            $this->langs['sys_tran_mess_owner'],
            $target_name,
            FleetsLib::targetLink($fleet_row, ''),
            $fleet_row['fleet_resource_metal'],
            $this->langs['Metal'],
            $fleet_row['fleet_resource_crystal'],
            $this->langs['Crystal'],
            $fleet_row['fleet_resource_deuterium'],
            $this->langs['Deuterium']
        );

        $message[2] = sprintf(
            $this->langs['sys_tran_mess_user'],
            $start_name,
            FleetsLib::startLink($fleet_row, ''),
            $target_name,
            FleetsLib::targetLink($fleet_row, ''),
            $fleet_row['fleet_resource_metal'],
            $this->langs['Metal'],
            $fleet_row['fleet_resource_crystal'],
            $this->langs['Crystal'],
            $fleet_row['fleet_resource_deuterium'],
            $this->langs['Deuterium']
        );

        $message[3] = sprintf(
            $this->langs['sys_tran_mess_back'],
            $start_name,
            FleetsLib::startLink($fleet_row, '')
        );

        if ($fleet_row['fleet_mess'] == 0) {

            if ($fleet_row['fleet_start_time'] < time()) {

                parent::storeResources($fleet_row, false);

                $this->transportMessage(
                    $start_owner_id,
                    $message[1],
                    $fleet_row['fleet_start_time'],
                    $this->langs['sys_mess_transport']
                );

                // MESSAGE FOR THE OTHER USER, IN CASE WE ARE TRANSPORTING TO ANOTHER USER
                if ($target_owner_id <> $start_owner_id) {

                    $this->transportMessage(
                        $target_owner_id,
                        $message[2],
                        $fleet_row['fleet_start_time'],
                        $this->langs['sys_mess_transport']
                    );
                }

                $this->Missions_Model->updateReturningFleetResources($fleet_row['fleet_id']);
            }
        } elseif ($fleet_row['fleet_end_time'] < time()) {

            $this->transportMessage(
                $start_owner_id,
                $message[3],
                $fleet_row['fleet_end_time'],
                $this->langs['sys_mess_fleetback']
            );

            parent::restoreFleet($fleet_row, true);
            parent::removeFleet($fleet_row['fleet_id']);
        }
    }

    /**
     * transportMessage
     *
     * @param int    $owner          Owner
     * @param string $message        Message
     * @param int    $time           Time
     * @param string $status_message Status message
     *
     * @return void
     */
    private function transportMessage($owner, $message, $time, $status_message)
    {
        FunctionsLib::sendMessage($owner, '', $time, 5, $this->langs['sys_mess_tower'], $status_message, $message);
    }
}

/* end of transport.php */
