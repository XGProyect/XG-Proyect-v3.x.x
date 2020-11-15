<?php
/**
 * Stay Library
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\libraries\missions;

use App\libraries\FleetsLib;
use App\libraries\Functions;

/**
 * Stay Class
 */
class Stay extends Missions
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // load Language
        parent::loadLang(['game/missions', 'game/stay']);
    }

    /**
     * stayMission
     *
     * @param array $fleet_row Fleet row
     *
     * @return void
     */
    public function stayMission($fleet_row)
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
            $this->langs->line('stay_mess_owner'),
            $target_name,
            FleetsLib::targetLink($fleet_row, ''),
            $fleet_row['fleet_resource_metal'],
            $fleet_row['fleet_resource_crystal'],
            $fleet_row['fleet_resource_deuterium']
        );

        $message[2] = sprintf(
            $this->langs->line('stay_mess_user'),
            $start_name,
            FleetsLib::startLink($fleet_row, ''),
            $target_name,
            FleetsLib::targetLink($fleet_row, ''),
            $fleet_row['fleet_resource_metal'],
            $fleet_row['fleet_resource_crystal'],
            $fleet_row['fleet_resource_deuterium']
        );

        $message[3] = sprintf(
            $this->langs->line('mi_fleet_back_without_resources'),
            $target_name,
            FleetsLib::targetLink($fleet_row, ''),
            $start_name,
            FleetsLib::startLink($fleet_row, '')
        );

        if ($fleet_row['fleet_mess'] == 0) {
            if ($fleet_row['fleet_start_time'] <= time()) {
                $this->stayMessage(
                    $start_owner_id, $message[1], $fleet_row['fleet_start_time'], $this->langs->line('stay_report_title')
                );

                $this->stayMessage(
                    $target_owner_id, $message[2], $fleet_row['fleet_start_time'], $this->langs->line('stay_report_title')
                );

                $this->startStay($fleet_row['fleet_id']);
            }

            if ($fleet_row['fleet_end_stay'] <= time()) {
                parent::returnFleet($fleet_row['fleet_id']);
            }
        } elseif ($fleet_row['fleet_end_time'] < time()) {
            $this->stayMessage(
                $start_owner_id, $message[3], $fleet_row['fleet_end_time'], $this->langs->line('mi_fleet_back_title')
            );

            parent::restoreFleet($fleet_row, true);
            parent::removeFleet($fleet_row['fleet_id']);
        }
    }

    /**
     * startStay
     *
     * @param int $fleet_id Fleed ID
     *
     * @return void
     */
    private function startStay($fleet_id)
    {
        $this->Missions_Model->updateFleetStatusToStayById($fleet_id);
    }

    /**
     * stayMessage
     *
     * @param int    $owner          Owner
     * @param string $message        Message
     * @param int    $time           Time
     * @param string $status_message Status message
     *
     * @return void
     */
    private function stayMessage($owner, $message, $time, $status_message)
    {
        Functions::sendMessage($owner, '', $time, 5, $this->langs->line('mi_fleet_command'), $status_message, $message);
    }
}
