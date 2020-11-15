<?php
/**
 * Colonize Library
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
use App\libraries\PlanetLib;
use App\libraries\Statistics_library;

/**
 * Colonize Class
 */
class Colonize extends Missions
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // load Language
        parent::loadLang(['game/missions', 'game/colonize']);
    }

    /**
     * Colonize mission
     *
     * @param array $fleet_row
     * @return void
     */
    public function colonizeMission($fleet_row)
    {
        if ($fleet_row['fleet_mess'] == 0) {
            $colonization_check = $this->Missions_Model->getPlanetAndUserCountsCounts([
                'user_id' => $fleet_row['fleet_owner'],
                'coords' => [
                    'galaxy' => $fleet_row['fleet_end_galaxy'],
                    'system' => $fleet_row['fleet_end_system'],
                    'planet' => $fleet_row['fleet_end_planet'],
                ],
            ]);

            // some required values
            $target_coords = sprintf($this->langs->line('mi_planet_coordinates'), $fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet']);
            $max_colonies = FleetsLib::getMaxColonies($colonization_check['astro_level']);
            $planet_count = $colonization_check['planet_count'] - 1; // the total amount of planets minus 1 (because the main planet is not considered)

            // different types of messages
            $message[1] = sprintf($this->langs->line('col_max_colonies'), $target_coords, ($max_colonies + 1));
            $message[2] = sprintf($this->langs->line('col_successful'), $target_coords);
            $message[3] = sprintf($this->langs->line('col_occupied'), $target_coords);
            $message[4] = sprintf($this->langs->line('col_astro_level'), $target_coords);

            if ($colonization_check['galaxy_count'] == 0) {
                if ($planet_count >= $max_colonies) {
                    $this->colonize_message($fleet_row['fleet_owner'], $message[1], $fleet_row['fleet_start_time']);

                    parent::returnFleet($fleet_row['fleet_id']);
                } elseif (!$this->positionAllowed($fleet_row['fleet_end_planet'], $colonization_check['astro_level'])) {
                    $this->colonize_message($fleet_row['fleet_owner'], $message[4], $fleet_row['fleet_start_time']);

                    parent::returnFleet($fleet_row['fleet_id']);
                } else {
                    if ($this->start_creation($fleet_row)) {
                        $this->colonize_message($fleet_row['fleet_owner'], $message[2], $fleet_row['fleet_start_time']);

                        if ($fleet_row['fleet_amount'] == 1) {
                            $this->Missions_Model->updateColonizationStatistics([
                                'points' => Statistics_library::calculatePoints(208, 1),
                                'coords' => [
                                    'galaxy' => $fleet_row['fleet_start_galaxy'],
                                    'system' => $fleet_row['fleet_start_system'],
                                    'planet' => $fleet_row['fleet_start_planet'],
                                    'type' => $fleet_row['fleet_start_type'],
                                ],
                            ]);
                            parent::storeResources($fleet_row);
                            parent::removeFleet($fleet_row['fleet_id']);
                        } else {
                            parent::storeResources($fleet_row);

                            $this->Missions_Model->updateColonizatonReturningFleet([
                                'ships' => $this->build_new_fleet($fleet_row['fleet_array']),
                                'points' => Statistics_library::calculatePoints(208, 1),
                                'fleet_id' => $fleet_row['fleet_id'],
                                'coords' => [
                                    'galaxy' => $fleet_row['fleet_start_galaxy'],
                                    'system' => $fleet_row['fleet_start_system'],
                                    'planet' => $fleet_row['fleet_start_planet'],
                                    'type' => $fleet_row['fleet_start_type'],
                                ],
                            ]);
                        }
                    } else {
                        $this->colonize_message($fleet_row['fleet_owner'], $message[3], $fleet_row['fleet_end_time']);

                        parent::returnFleet($fleet_row['fleet_id']);
                    }
                }
            } else {
                $this->colonize_message($fleet_row['fleet_owner'], $message[3], $fleet_row['fleet_end_time']);

                parent::returnFleet($fleet_row['fleet_id']);
            }
        }

        if ($fleet_row['fleet_end_time'] < time()) {
            parent::restoreFleet($fleet_row, true);
            parent::removeFleet($fleet_row['fleet_id']);
        }
    }

    /**
     * Start planet creation
     *
     * @param array $fleet_row
     * @return void
     */
    private function start_creation($fleet_row)
    {
        $creator = new PlanetLib();

        return $creator->setNewPlanet($fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet'], $fleet_row['fleet_owner']);
    }

    /**
     * Build new fleet
     *
     * @param array $fleet_array
     * @return void
     */
    private function build_new_fleet($fleet_array)
    {
        $current_fleet = FleetsLib::getFleetShipsArray($fleet_array);
        $new_fleet = [];

        foreach ($current_fleet as $ship => $count) {
            if ($ship == 208) {
                if ($count > 1) {
                    $new_fleet[$ship] = ($count - 1);
                }
            } else {
                if ($count != 0) {
                    $new_fleet[$ship] = $count;
                }
            }
        }

        return FleetsLib::setFleetShipsArray($new_fleet);
    }

    /**
     * Send colonization message
     *
     * @param int $owner
     * @param string $message
     * @param int $time
     * @return void
     */
    private function colonize_message($owner, $message, $time)
    {
        Functions::sendMessage($owner, '', $time, 5, $this->langs->line('col_report_from'), $this->langs->line('col_report_title'), $message);
    }

    /**
     * Check if position is allowed
     *
     * @param int $position
     * @param int $level
     * @return bool
     */
    private function positionAllowed(int $position, int $level): bool
    {
        // CHECK IF THE POSITION IS NEAR THE SPACE LIMITS
        if ($position <= 3 or $position >= 13) {
            // POSITIONS 3 AND 13 CAN BE POPULATED FROM LEVEL 4 ONWARDS.
            if ($level >= 4 && ($position == 3 or $position == 13)) {
                return true;
            }

            // POSITIONS 2 AND 14 CAN BE POPULATED FROM LEVEL 6 ONWARDS.
            if ($level >= 6 && ($position == 2 or $position == 14)) {
                return true;
            }

            // POSITIONS 1 AND 15 CAN BE POPULATED FROM LEVEL 8 ONWARDS.
            if ($level >= 8) {
                return true;
            }

            return false;
        } else {
            return true;
        }
    }
}
