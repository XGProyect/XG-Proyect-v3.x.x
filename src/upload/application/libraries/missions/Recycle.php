<?php
/**
 * Recycle Library
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
 * Recycle Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Recycle extends Missions
{

    /**
     *
     * @var string
     */
    private $planet_name;

    /**
     * bbCode function.
     *
     * @param string $string String
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
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
                $this->langs['sys_recy_gotten'], FormatLib::prettyNumber($recycled_resources['metal']), $this->langs['Metal'], FormatLib::prettyNumber($recycled_resources['crystal']), $this->langs['Crystal']
            );

            $this->recycleMessage(
                $fleet_row['fleet_owner'], $message, $fleet_row['fleet_start_time'], $this->langs['sys_recy_report']
            );
        } elseif ($fleet_row['fleet_end_time'] <= time()) {

            $message = sprintf(
                $this->langs['sys_tran_mess_user'], $this->planet_name, FleetsLib::targetLink($fleet_row, ''), FormatLib::prettyNumber($fleet_row['fleet_resource_metal']), $this->langs['Metal'], FormatLib::prettyNumber($fleet_row['fleet_resource_crystal']), $this->langs['Crystal'], FormatLib::prettyNumber($fleet_row['fleet_resource_deuterium']), $this->langs['Deuterium']
            );

            $this->recycleMessage(
                $fleet_row['fleet_owner'], $message, $fleet_row['fleet_end_time'], $this->langs['sys_mess_fleetback']
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

        $this->planet_name = $target_planet['target_name'];

        // SOME REQUIRED VALUES
        $ships = FleetsLib::getFleetShipsArray($fleet_row['fleet_array']);
        $recycle_capacity = 0;
        $other_capacity = 0;
        $current_resources = $fleet_row['fleet_resource_metal'] +
            $fleet_row['fleet_resource_crystal'] + $fleet_row['fleet_resource_deuterium'];

        // CALCULATE STORAGE FOR EACH KIND OF SHIP
        foreach ($ships as $id => $amount) {

            if ($id == 209) {

                $recycle_capacity += $this->pricelist[$id]['capacity'] * $amount;
            } else {

                $other_capacity += $this->pricelist[$id]['capacity'] * $amount;
            }
        }

        if ($current_resources > $other_capacity) {

            $recycle_capacity -= ($current_resources - $other_capacity);
        }

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
        FunctionsLib::sendMessage(
            $owner, '', $time, 5, $this->langs['sys_mess_spy_control'], $status_message, $message
        );
    }
}

/* end of recycle.php */
