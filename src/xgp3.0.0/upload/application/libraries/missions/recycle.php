<?php
/**
 * Recycle Library
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
 * @version  3.0.0
 */
class Recycle extends Missions
{
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

        // SOME REQUIRED VALUES
        $target_name        = $recycled_resources['target_name'];
        
        if ($fleet_row['fleet_mess'] == '0') {

            if ($fleet_row['fleet_start_time'] <= time()) {
                
                parent::$db->query(
                    "UPDATE " . PLANETS . ", " . FLEETS . " SET
                    `planet_debris_metal` = `planet_debris_metal` - '" . $recycled_resources['metal'] . "',
                    `planet_debris_crystal` = `planet_debris_crystal` - '" . $recycled_resources['crystal'] . "',
                    `fleet_resource_metal` = `fleet_resource_metal` + '" . $recycled_resources['metal'] . "',
                    `fleet_resource_crystal` = `fleet_resource_crystal` + '" . $recycled_resources['crystal'] . "',
                    `fleet_mess` = '1'
                    WHERE `planet_galaxy` = '" . $fleet_row['fleet_end_galaxy'] . "' AND
                                    `planet_system` = '" . $fleet_row['fleet_end_system'] . "' AND
                                    `planet_planet` = '" . $fleet_row['fleet_end_planet'] . "' AND
                                    `planet_type` = 1 AND
                                    `fleet_id` = '" . (int) $fleet_row['fleet_id'] . "'"
                );

                $message    = sprintf(
                    $this->langs['sys_recy_gotten'],
                    FormatLib::prettyNumber($recycled_resources['metal']),
                    $this->langs['Metal'],
                    FormatLib::prettyNumber($recycled_resources['crystal']),
                    $this->langs['Crystal']
                );
                $this->recycle_message(
                    $fleet_row['fleet_owner'],
                    $message,
                    $fleet_row['fleet_start_time'],
                    $this->langs['sys_recy_report']
                );
            }
        } elseif ($fleet_row['fleet_end_time'] <= time()) {

            $message    = sprintf(
                $this->langs['sys_tran_mess_owner'],
                $target_name,
                FleetsLib::targetLink($fleet_row, ''),
                FormatLib::prettyNumber($fleet_row['fleet_resource_metal']),
                $this->langs['Metal'],
                FormatLib::prettyNumber($fleet_row['fleet_resource_crystal']),
                $this->langs['Crystal'],
                FormatLib::prettyNumber($fleet_row['fleet_resource_deuterium']),
                $this->langs['Deuterium']
            );

            $this->recycleMessage(
                $fleet_row['fleet_owner'],
                $message,
                $fleet_row['fleet_end_time'],
                $this->langs['sys_mess_fleetback']
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
        $target_planet = parent::$db->queryFetch(
            "SELECT 
                `planet_name` AS target_name, 
                `planet_debris_metal`, 
                `planet_debris_crystal`
            FROM " . PLANETS . "
            WHERE `planet_galaxy` = '" . $fleet_row['fleet_end_galaxy'] . "' AND
                `planet_system` = '" . $fleet_row['fleet_end_system'] . "' AND
                `planet_planet` = '" . $fleet_row['fleet_end_planet'] . "' AND
                `planet_type` = 1
            LIMIT 1;"
        );

        // SOME REQUIRED VALUES
        $ships              = explode(';', $fleet_row['fleet_array']);
        $recycle_capacity   = 0;
        $other_capacity     = 0;
        $current_resources  = $fleet_row['fleet_resource_metal'] +
            $fleet_row['fleet_resource_crystal'] + $fleet_row['fleet_resource_deuterium'];

        // CALCULATE STORAGE FOR EACH KIND OF SHIP
        foreach ($ships as $item => $group) {

            if ($group != '') {

                $ship   = explode(",", $group);

                if ($ship[0] == 209) {

                    $recycle_capacity   += $this->pricelist[$ship[0]]['capacity'] * $ship[1];
                } else {

                    $other_capacity     += $this->pricelist[$ship[0]]['capacity'] * $ship[1];
                }
            }
        }

        if ($current_resources > $other_capacity) {

            $recycle_capacity   -= ($current_resources - $other_capacity);
        }

        if (( $target_planet['planet_debris_metal'] + $target_planet['planet_debris_crystal'] ) <= $recycle_capacity) {

            $recycled_resources['metal']    = $target_planet['planet_debris_metal'];
            $recycled_resources['crystal']  = $target_planet['planet_debris_crystal'];
        } else {

            if (($target_planet['planet_debris_metal'] > $recycle_capacity / 2)
                && ($target_planet['planet_debris_crystal'] > $recycle_capacity / 2)) {

                $recycled_resources['metal']    = $recycle_capacity / 2;
                $recycled_resources['crystal']  = $recycle_capacity / 2;
            } else {

                if ($target_planet['planet_debris_metal'] > $target_planet['planet_debris_crystal']) {

                    $recycled_resources['crystal']  = $target_planet['planet_debris_crystal'];

                    if ($target_planet['planet_debris_metal'] >
                        ( $recycle_capacity - $recycled_resources['crystal'])) {

                        $recycled_resources['metal']    = $recycle_capacity - $recycled_resources['crystal'];
                    } else {

                        $recycled_resources['metal']    = $target_planet['planet_debris_metal'];
                    }
                } else {

                    $recycled_resources['metal']    = $target_planet['planet_debris_metal'];

                    if ($target_planet['planet_debris_crystal'] >
                        ($recycle_capacity - $recycled_resources['metal'])) {

                        $recycled_resources['crystal']  = $recycle_capacity - $recycled_resources['metal'];
                    } else {

                        $recycled_resources['crystal']  = $target_planet['planet_debris_crystal'];
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
            $owner,
            '',
            $time,
            5,
            $this->langs['sys_mess_spy_control'],
            $status_message,
            $message
        );
    }
}

/* end of recycle.php */
