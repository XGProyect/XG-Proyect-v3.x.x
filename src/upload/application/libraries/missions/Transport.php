<?php
/**
 * Transport Library
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
use application\libraries\FunctionsLib;

/**
 * Transport Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Transport extends Missions
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // load Language
        parent::loadLang(['missions', 'game/transport']);
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
                    'type' => $fleet_row['fleet_start_type'],
                ],
                'end' => [
                    'galaxy' => $fleet_row['fleet_end_galaxy'],
                    'system' => $fleet_row['fleet_end_system'],
                    'planet' => $fleet_row['fleet_end_planet'],
                    'type' => $fleet_row['fleet_end_type'],
                ],
            ],
        ]);

        // SOME REQUIRED VALUES
        $start_name = $friendly_planet['start_name'];
        $start_owner_id = $friendly_planet['start_id'];
        $target_name = $friendly_planet['target_name'];
        $target_owner_id = $friendly_planet['target_id'];

        // DIFFERENT TYPES OF MESSAGES
        $message[1] = sprintf(
            $this->langs->line('tra_delivered_resources'),
            $start_name,
            FleetsLib::startLink($fleet_row, ''),
            $target_name,
            FleetsLib::targetLink($fleet_row, ''),
            $fleet_row['fleet_resource_metal'],
            $fleet_row['fleet_resource_crystal'],
            $fleet_row['fleet_resource_deuterium']
        );

        $message[2] = sprintf(
            $this->langs->line('tra_delivered_resources'),
            $start_name,
            FleetsLib::startLink($fleet_row, ''),
            $target_name,
            FleetsLib::targetLink($fleet_row, ''),
            $fleet_row['fleet_resource_metal'],
            $fleet_row['fleet_resource_crystal'],
            $fleet_row['fleet_resource_deuterium']
        );

        $message[3] = sprintf(
            $this->langs->line('mi_fleet_back'),
            $target_name,
            FleetsLib::targetLink($fleet_row, ''),
            $start_name,
            FleetsLib::startLink($fleet_row, '')
        );

        if ($fleet_row['fleet_mess'] == 0 && $fleet_row['fleet_start_time'] <= time()) {
            parent::storeResources($fleet_row, false);

            $this->transportMessage(
                $start_owner_id, $message[1], $fleet_row['fleet_start_time'], $this->langs->line('tra_reaching')
            );

            // MESSAGE FOR THE OTHER USER, IN CASE WE ARE TRANSPORTING TO ANOTHER USER
            if ($target_owner_id != $start_owner_id) {
                $this->transportMessage(
                    $target_owner_id, $message[2], $fleet_row['fleet_start_time'], $this->langs->line('tra_reaching')
                );
            }

            $this->Missions_Model->updateReturningFleetResources($fleet_row['fleet_id']);
        } elseif ($fleet_row['fleet_end_time'] < time()) {
            $this->transportMessage(
                $start_owner_id, $message[3], $fleet_row['fleet_end_time'], $this->langs->line('mi_fleet_back')
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
        FunctionsLib::sendMessage($owner, '', $time, 5, $this->langs->line('mi_fleet_command'), $status_message, $message);
    }
}

/* end of transport.php */
