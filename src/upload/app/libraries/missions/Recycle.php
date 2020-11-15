<?php
/**
 * Recycle Library
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
 * Recycle Class
 */
class Recycle extends Missions
{
    /**
     * Contains the target planet debris amount
     *
     * @var array
     */
    private $planet_debris = [
        'metal' => 0,
        'crystal' => 0,
    ];

    /**
     * Contains the maximum capacity of the recyclers
     *
     * @var integer
     */
    private $recyclers_capacity = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // load Language
        parent::loadLang(['game/missions', 'game/recycle']);
    }

    /**
     * recycleMission
     *
     * @param array $fleet_row Fleet row
     *
     * @return void
     */
    public function recycleMission($fleet_row)
    {
        $recycled_resources = $this->calculateCapacity($fleet_row);

        if ($fleet_row['fleet_mess'] == 0 && $fleet_row['fleet_start_time'] <= time()) {
            $this->Missions_Model->updatePlanetDebrisFieldAndFleet([
                'recycled' => [
                    'metal' => $recycled_resources['metal'],
                    'crystal' => $recycled_resources['crystal'],
                ],
                'coords' => [
                    'galaxy' => $fleet_row['fleet_end_galaxy'],
                    'system' => $fleet_row['fleet_end_system'],
                    'planet' => $fleet_row['fleet_end_planet'],
                ],
                'fleet_id' => $fleet_row['fleet_id'],
            ]);

            $message = sprintf(
                $this->langs->line('rec_result'),
                FormatLib::prettyNumber($fleet_row['fleet_amount']),
                FormatLib::prettyNumber($this->recyclers_capacity),
                FleetsLib::targetLink($fleet_row, ''),
                FormatLib::prettyNumber($this->planet_debris['metal']),
                FormatLib::prettyNumber($this->planet_debris['crystal']),
                FormatLib::prettyNumber($recycled_resources['metal']),
                FormatLib::prettyNumber($recycled_resources['crystal'])
            );

            $this->recycleMessage(
                $fleet_row['fleet_owner'],
                $message,
                $fleet_row['fleet_start_time'],
                sprintf($this->langs->line('rec_report_title'), FleetsLib::targetLink($fleet_row, ''))
            );
        } elseif ($fleet_row['fleet_end_time'] <= time()) {
            $message = sprintf(
                $this->langs->line('mi_fleet_back_with_resources'),
                $fleet_row['planet_end_name'],
                FleetsLib::targetLink($fleet_row, ''),
                $fleet_row['planet_start_name'],
                FleetsLib::startLink($fleet_row, ''),
                FormatLib::prettyNumber($fleet_row['fleet_resource_metal']),
                FormatLib::prettyNumber($fleet_row['fleet_resource_crystal']),
                FormatLib::prettyNumber($fleet_row['fleet_resource_deuterium']),
            );

            $this->recycleMessage(
                $fleet_row['fleet_owner'], $message, $fleet_row['fleet_end_time'], $this->langs->line('mi_fleet_back_title')
            );

            parent::restoreFleet($fleet_row, true);
            parent::removeFleet($fleet_row['fleet_id']);
        }
    }

    /**
     * calculateCapacity
     *
     * @param array $fleet_row Fleet row
     *
     * @return void
     */
    private function calculateCapacity($fleet_row)
    {
        $target_planet = $this->Missions_Model->getPlanetDebris([
            'coords' => [
                'galaxy' => $fleet_row['fleet_end_galaxy'],
                'system' => $fleet_row['fleet_end_system'],
                'planet' => $fleet_row['fleet_end_planet'],
            ],
        ]);

        $this->planet_debris = [
            'metal' => $target_planet['planet_debris_metal'],
            'crystal' => $target_planet['planet_debris_crystal'],
        ];

        // SOME REQUIRED VALUES
        $ships = FleetsLib::getFleetShipsArray($fleet_row['fleet_array']);
        $recycle_capacity = 0;
        $other_capacity = 0;
        $current_resources = $fleet_row['fleet_resource_metal'] +
            $fleet_row['fleet_resource_crystal'] + $fleet_row['fleet_resource_deuterium'];

        // CALCULATE STORAGE FOR EACH KIND OF SHIP
        foreach ($ships as $id => $amount) {
            $ship_storage = FleetsLib::getMaxStorage(
                $this->pricelist[$id]['capacity'],
                $fleet_row['research_hyperspace_technology']
            );

            if ($id == 209) {
                $recycle_capacity += $ship_storage * $amount;
            } else {
                $other_capacity += $ship_storage * $amount;
            }
        }

        if ($current_resources > $other_capacity) {
            $recycle_capacity -= ($current_resources - $other_capacity);
        }

        $this->recyclers_capacity = $recycle_capacity;

        if (($target_planet['planet_debris_metal'] + $target_planet['planet_debris_crystal']) <= $recycle_capacity) {
            $recycled_resources['metal'] = $target_planet['planet_debris_metal'];
            $recycled_resources['crystal'] = $target_planet['planet_debris_crystal'];
        } else {
            if (($target_planet['planet_debris_metal'] > $recycle_capacity / 2) && ($target_planet['planet_debris_crystal'] > $recycle_capacity / 2)) {
                $recycled_resources['metal'] = $recycle_capacity / 2;
                $recycled_resources['crystal'] = $recycle_capacity / 2;
            } else {
                if ($target_planet['planet_debris_metal'] > $target_planet['planet_debris_crystal']) {
                    $recycled_resources['crystal'] = $target_planet['planet_debris_crystal'];

                    if ($target_planet['planet_debris_metal'] >
                        ($recycle_capacity - $recycled_resources['crystal'])) {
                        $recycled_resources['metal'] = $recycle_capacity - $recycled_resources['crystal'];
                    } else {
                        $recycled_resources['metal'] = $target_planet['planet_debris_metal'];
                    }
                } else {
                    $recycled_resources['metal'] = $target_planet['planet_debris_metal'];

                    if ($target_planet['planet_debris_crystal'] >
                        ($recycle_capacity - $recycled_resources['metal'])) {
                        $recycled_resources['crystal'] = $recycle_capacity - $recycled_resources['metal'];
                    } else {
                        $recycled_resources['crystal'] = $target_planet['planet_debris_crystal'];
                    }
                }
            }
        }

        return $recycled_resources;
    }

    /**
     * recycleMessage
     *
     * @param int    $owner          Owner
     * @param string $message        Message
     * @param int    $time           Time
     * @param string $status_message Status message
     *
     * @return void
     */
    private function recycleMessage($owner, $message, $time, $status_message)
    {
        Functions::sendMessage(
            $owner, '', $time, 5, $this->langs->line('rec_report_from'), $status_message, $message
        );
    }
}
