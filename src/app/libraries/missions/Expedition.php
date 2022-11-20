<?php
/**
 * Expedition Library
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
use App\libraries\FormatLib;
use App\libraries\Functions;

/**
 * Expedition Class
 */
class Expedition extends Missions
{
    /**
     * The amount of hazard for an expedition
     *
     * @var int
     */
    private $hazard;

    /**
     * A flag to indicate if a fleet was completly destroyed.
     *
     * @var int
     */
    private $all_destroyed = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // load Language
        parent::loadLang(['game/global', 'game/missions', 'game/expedition', 'game/ships']);
    }

    /**
     * expeditionMission
     *
     * @param array $fleet_row Fleet row
     *
     * @return void
     */
    public function expeditionMission($fleet_row)
    {
        // do mission
        if (parent::canStartMission($fleet_row)) {
            $ships_points = $this->setShipsPoints();
            $ships = FleetsLib::getFleetShipsArray($fleet_row['fleet_array']);
            $fleet_capacity = 0;
            $fleet_points = 0;
            $current_fleet = [];

            foreach ($ships as $id => $count) {
                $current_fleet[$id] = $count;
                $fleet_capacity += FleetsLib::getMaxStorage(
                    $this->pricelist[$id]['capacity'],
                    $fleet_row['research_hyperspace_technology']
                ) * $count;
                $fleet_points += ($count * $ships_points[$id]);
            }

            // GET A NUMBER BETWEEN 0 AND 10 RANDOMLY
            $this->hazard = mt_rand(0, 10);

            // EXPEDITION RESULT "HAZARD"
            switch ($this->hazard) {
                // BLACKHOLE
                case (($this->hazard < 3)):
                    $this->hazardBlackhole($fleet_row, $current_fleet);
                    break;
                // NOTHING
                case (($this->hazard == 3)):
                    $this->hazardNothing($fleet_row);
                    break;
                // RESOURCES
                case ((($this->hazard >= 4) && ($this->hazard < 7))):
                    $this->hazardResources($fleet_row, $fleet_capacity);
                    break;
                // NOTHING
                case (($this->hazard == 7)):
                    $this->hazardNothing($fleet_row);
                    break;
                // SHIPS
                case ((($this->hazard >= 8) && ($this->hazard < 11))):
                    $this->hazardShips($fleet_row, $fleet_points, $current_fleet);
                    break;
            }
        }

        // complete mission
        if (parent::canCompleteMission($fleet_row)) {
            if (!$this->all_destroyed) {
                $this->expeditionMessage(
                    $fleet_row['fleet_owner'],
                    $this->langs->line('exp_back_home'),
                    $fleet_row['fleet_end_time']
                );

                parent::restoreFleet($fleet_row, true);
                parent::removeFleet($fleet_row['fleet_id']);
            }
        }
    }

    /**
     * hazardBlackhole
     *
     * @param array $fleet_row     Fleet row
     * @param array $current_fleet Current fleet
     *
     * @return void
     */
    private function hazardBlackhole($fleet_row, $current_fleet)
    {
        $this->hazard += 1;
        $lost_amount = (($this->hazard * 33) + 1) / 100;

        if ($lost_amount == 1) {
            $this->all_destroyed = true;

            $this->expeditionMessage(
                $fleet_row['fleet_owner'],
                $this->langs->line('exp_blackholl_2'),
                $fleet_row['fleet_end_stay']
            );

            $this->missionsModel->updateLostShipsAndDefensePoints($fleet_row['fleet_owner'], $current_fleet);
            parent::removeFleet($fleet_row['fleet_id']);
        } else {
            $this->all_destroyed = true;
            $new_ships = [];
            $lost_ships = [];

            foreach ($current_fleet as $ship => $amount) {
                if (floor($amount * $lost_amount) != 0) {
                    $lost_ships[$ship] = floor($amount * $lost_amount);
                    $new_ships[$ship] = ($amount - $lost_ships[$ship]);
                    $this->all_destroyed = false;
                }
            }

            if (!$this->all_destroyed) {
                $this->expeditionMessage(
                    $fleet_row['fleet_owner'],
                    $this->langs->line('exp_blackholl_1'),
                    $fleet_row['fleet_end_stay']
                );

                $this->missionsModel->updateLostShipsAndDefensePoints($fleet_row['fleet_owner'], $lost_ships);
                $this->missionsModel->updateFleetArrayById([
                    'ships' => FleetsLib::setFleetShipsArray($new_ships),
                    'fleet_id' => $fleet_row['fleet_id'],
                ]);
            } else {
                $this->expeditionMessage(
                    $fleet_row['fleet_owner'],
                    $this->langs->line('exp_blackholl_2'),
                    $fleet_row['fleet_end_stay']
                );

                $this->missionsModel->updateLostShipsAndDefensePoints($fleet_row['fleet_owner'], $current_fleet);
                parent::removeFleet($fleet_row['fleet_id']);
            }
        }
    }

    /**
     * hazardNothing
     *
     * @param array $fleet_row Fleet row
     *
     * @return void
     */
    private function hazardNothing($fleet_row)
    {
        $this->expeditionMessage(
            $fleet_row['fleet_owner'],
            $this->langs->line('exp_nothing_' . mt_rand(1, 6)),
            $fleet_row['fleet_end_stay']
        );

        parent::returnFleet($fleet_row['fleet_id']);
    }

    /**
     * hazardResources
     *
     * @param array $fleet_row      Fleet row
     * @param int   $fleet_capacity Fleet capacity
     *
     * @return void
     */
    private function hazardResources($fleet_row, $fleet_capacity)
    {
        $fleet_current_capacity = $fleet_row['fleet_resource_metal'] + $fleet_row['fleet_resource_crystal'] + $fleet_row['fleet_resource_deuterium'];
        $fleet_capacity -= $fleet_current_capacity;

        if ($fleet_capacity > 5000) {
            $min_capacity = $fleet_capacity - 5000;
            $max_capacity = $fleet_capacity;
            $found_resources = mt_rand($min_capacity, $max_capacity);
            $found_metal = intval($found_resources / 2);
            $found_crystal = intval($found_resources / 4);
            $found_deuterium = intval($found_resources / 6);
            $found_darkmatter = ($fleet_capacity > 10000) ? intval(3 * log($fleet_capacity / 10000) * 100) : 0;
            $found_darkmatter = mt_rand(intval($found_darkmatter / 2), $found_darkmatter);

            $this->missionsModel->updateFleetResourcesById([
                'found' => [
                    'metal' => $found_metal,
                    'crystal' => $found_crystal,
                    'deuterium' => $found_deuterium,
                    'darkmatter' => $found_darkmatter,
                ],
                'fleet_id' => $fleet_row['fleet_id'],
            ]);

            $message = sprintf(
                $this->langs->line('exp_found_goods'),
                FormatLib::prettyNumber($found_metal),
                $this->langs->line('metal'),
                FormatLib::prettyNumber($found_crystal),
                $this->langs->line('crystal'),
                FormatLib::prettyNumber($found_deuterium),
                $this->langs->line('deuterium'),
                FormatLib::prettyNumber($found_darkmatter),
                $this->langs->line('dark_matter')
            );

            $this->expeditionMessage($fleet_row['fleet_owner'], $message, $fleet_row['fleet_end_stay']);
        }
    }

    /**
     * hazardShips
     *
     * @param array $fleet_row    Fleet row
     * @param int   $fleet_points Fleet points
     * @param array $current_fleet Current fleet
     *
     * @return void
     */
    private function hazardShips($fleet_row, $fleet_points, $current_fleet)
    {
        $ships_ratio = $this->setShipsRatios();
        $found_chance = $fleet_points / $fleet_row['fleet_amount'];

        for ($ship = 202; $ship <= 215; $ship++) {
            if (isset($current_fleet[$ship]) && $current_fleet[$ship] != 0) {
                $found_ship[$ship] = round($current_fleet[$ship] * $ships_ratio[$ship] * $found_chance) + 1;

                if ($found_ship[$ship] > 0) {
                    $current_fleet[$ship] += $found_ship[$ship];
                }
            }
        }

        $new_ships = [];
        $found_ship_message = '';

        foreach ($current_fleet as $ship => $count) {
            if ($count > 0) {
                $new_ships[$ship] = $count;
            }
        }

        if ($found_ship != null) {
            foreach ($found_ship as $ship => $count) {
                if ($count != 0) {
                    $found_ship_message .= $this->langs->line($this->resource[$ship]) . ": " . $count . "<br>";
                }
            }
        }

        $this->missionsModel->updateFleetArrayById([
            'ships' => FleetsLib::setFleetShipsArray($new_ships),
            'fleet_id' => $fleet_row['fleet_id'],
        ]);

        $message = sprintf($this->langs->line('exp_new_ships_' . mt_rand(1, 5)), $found_ship_message);

        $this->expeditionMessage($fleet_row['fleet_owner'], $message, $fleet_row['fleet_end_stay']);
    }

    /**
     * setShipsPoints
     *
     * @return array
     */
    private function setShipsPoints()
    {
        return [
            202 => 1.0, 203 => 1.5, 204 => 0.5, 205 => 1.5, 206 => 2.0,
            207 => 2.5, 208 => 0.5, 209 => 1.0, 210 => 0.01, 211 => 3.0,
            212 => 0.0, 213 => 3.5, 214 => 5.0, 215 => 3.2,
        ];
    }

    /**
     * setShipsRatios
     *
     * @return array
     */
    private function setShipsRatios()
    {
        return [
            202 => 0.1, 203 => 0.1, 204 => 0.1, 205 => 0.5, 206 => 0.25,
            207 => 0.125, 208 => 0.5, 209 => 0.1, 210 => 0.1, 211 => 0.0625,
            212 => 0.0, 213 => 0.0625, 214 => 0.03125, 215 => 0.0625,
        ];
    }

    /**
     * expeditionMessage
     *
     * @param int $owner      Owner
     * @param string $message Message
     * @param int $time       Time
     *
     * @return void
     */
    private function expeditionMessage($owner, $message, $time)
    {
        Functions::sendMessage(
            $owner,
            '',
            $time,
            5,
            $this->langs->line('mi_fleet_command'),
            $this->langs->line('exp_report_title'),
            $message
        );
    }
}
