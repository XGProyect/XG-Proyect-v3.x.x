<?php
/**
 * Expedition Library
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

use application\libraries\FormatLib;
use application\libraries\FunctionsLib;

/**
 * Expedition Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Expedition extends Missions
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
     * bbCode function.
     *
     * @param string $string String
     *
     * @return void
     */
    public function expeditionMission($fleet_row)
    {
        if ($fleet_row['fleet_mess'] == 0) {

            if ($fleet_row['fleet_end_stay'] < time()) {

                $ships_points   = $this->set_ships_points();
                $ships          = explode(";", $fleet_row['fleet_array']);
                $fleet_capacity = 0;
                $fleet_points   = 0;
                
                foreach ($ships as $item => $group) {

                    if ($group != '') {

                        $ship                           = explode(",", $group);
                        $ship_number                    = $ship[0];
                        $ship_amount                    = $ship[1];
                        $current_fleet[$ship_number]    = $ship_amount;
                        $fleet_capacity                 += $this->pricelist[$ship_number]['capacity']
                            * $ship_amount;
                        $fleet_points                   += ( $ship_amount * $ships_points[$ship_number] );
                    }
                }

                // GET A NUMBER BETWEEN 0 AND 10 RANDOMLY
                $hazard = mt_rand(0, 10);

                // EXPEDITION RESULT "HAZARD"
                switch ($hazard) {
                    // BLACKHOLE
                    case ( ( $hazard < 3 ) ):

                        $this->hazard_blackhole($fleet_row, $current_fleet);

                        break;

                    // NOTHING
                    case ( ( $hazard == 3 ) ):

                        $this->hazard_nothing($fleet_row);

                        break;

                    // RESOURCES
                    case ( ( ( $hazard >= 4 ) && ( $hazard < 7 ) ) ):

                        $this->hazard_resources($fleet_row, $fleet_capacity);

                        break;

                    // NOTHING
                    case ( ( $hazard == 7 ) ):

                        $this->hazard_nothing($fleet_row);

                        break;


                    // SHIPS
                    case ( ( ( $hazard >= 8 ) && ( $hazard < 11 ) ) ):

                        $this->hazard_ships($fleet_row, $fleet_points);

                        break;
                }
            }
        }

        if ($fleet_row['fleet_end_time'] < time()) {
            $this->expedition_message(
                $fleet_row['fleet_owner'],
                $this->langs['sys_expe_back_home'],
                $fleet_row['fleet_end_time']
            );

            parent::restoreFleet($fleet_row, true);
            parent::removeFleet($fleet_row['fleet_id']);
        }
    }

    /**
     * bbCode function.
     *
     * @param string $string String
     *
     * @return void
     */
    private function hazard_blackhole($fleet_row, $current_fleet)
    {
        $hazard += 1;
        $lost_amount = ( ( $hazard * 33 ) + 1 ) / 100;

        if ($lost_amount == 1) {
            $this->expedition_message($fleet_row['fleet_owner'], $this->langs['sys_expe_blackholl_2'], $fleet_row['fleet_end_stay']);

            parent::removeFleet($fleet_row['fleet_id']);
        } else {
            $all_destroyed = true;

            foreach ($current_fleet as $ship => $amount) {
                if (floor($amount * $lost_amount) != 0) {
                    $lost_ships[$ship] = floor($amount * $lost_amount);
                    $new_ships .= $ship . "," . ( $amount - $lost_ships[$ship] ) . ";";
                    $all_destroyed = false;
                }
            }

            if (!$all_destroyed) {
                $this->expedition_message($fleet_row['fleet_owner'], $this->langs['sys_expe_blackholl_1'], $fleet_row['fleet_end_stay']);

                parent::$db->query("UPDATE " . FLEETS . " SET
										`fleet_array` = '" . $new_ships . "',
										`fleet_mess` = '1'
										WHERE `fleet_id` = '" . $fleet_row['fleet_id'] . "';");
            } else {
                $this->expedition_message($fleet_row['fleet_owner'], $this->langs['sys_expe_blackholl_2'], $fleet_row['fleet_end_stay']);

                parent::removeFleet($fleet_row['fleet_id']);
            }
        }
    }

    /**
     * bbCode function.
     *
     * @param string $string String
     *
     * @return void
     */
    private function hazard_nothing($fleet_row)
    {
        $this->expedition_message(
            $fleet_row['fleet_owner'],
            $this->langs['sys_expe_nothing_' . mt_rand(1, 2)],
            $fleet_row['fleet_end_stay']
        );

        parent::returnFleet($fleet_row['fleet_id']);
    }

    /**
     * bbCode function.
     *
     * @param string $string String
     *
     * @return void
     */
    private function hazard_resources($fleet_row, $fleet_capacity)
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
            $found_darkmatter = ( $fleet_capacity > 10000 ) ? intval(3 * log($fleet_capacity / 10000) * 100) : 0;
            $found_darkmatter = mt_rand($found_darkmatter / 2, $found_darkmatter);

            parent::$db->query("UPDATE " . FLEETS . " AS f
									INNER JOIN " . PREMIUM . " AS pr ON pr.premium_user_id = f.fleet_owner SET
									`fleet_resource_metal` = `fleet_resource_metal` + '" . $found_metal . "',
									`fleet_resource_crystal` = `fleet_resource_crystal` + '" . $found_crystal . "',
									`fleet_resource_deuterium` = `fleet_resource_deuterium` + '" . $found_deuterium . "',
									`premium_dark_matter` = `premium_dark_matter` + '" . $found_darkmatter . "',
									`fleet_mess` = '1'
									WHERE `fleet_id` = '" . $fleet_row['fleet_id'] . "';");

            $message = sprintf($this->langs['sys_expe_found_goods'], FormatLib::prettyNumber($found_metal), $this->langs['Metal'], FormatLib::prettyNumber($found_crystal), $this->langs['Crystal'], FormatLib::prettyNumber($found_deuterium), $this->langs['Deuterium'], FormatLib::prettyNumber($found_darkmatter), $this->langs['Darkmatter']);
            $this->expedition_message($fleet_row['fleet_owner'], $message, $fleet_row['fleet_end_stay']);
        }
    }

    /**
     * bbCode function.
     *
     * @param string $string String
     *
     * @return void
     */
    private function hazard_ships($fleet_row, $fleet_points)
    {
        $ships_ratio = $this->set_ships_ratios();
        $found_chance = $fleet_points / $fleet_row['fleet_amount'];

        for ($ship = 202; $ship <= 215; $ship++) {
            if ($current_fleet[$ship] != 0) {
                $found_ship[$ship] = round($current_fleet[$ship] * $ships_ratio[$ship]) + 1;

                if ($found_ship[$ship] > 0) {
                    $current_fleet[$ship] += $found_ship[$ship];
                }
            }
        }

        $new_ships = "";
        $found_ship_message = "";

        foreach ($current_fleet as $ship => $count) {
            if ($count > 0) {
                $new_ships .= $ship . "," . $count . ";";
            }
        }

        if ($found_ship != NULL) {
            foreach ($found_ship as $ship => $count) {
                if ($count != 0) {
                    $found_ship_message .= $count . " " . $this->langs['tech'][$ship] . ",";
                }
            }
        }

        parent::$db->query("UPDATE " . FLEETS . " SET
								`fleet_array` = '" . $new_ships . "',
								`fleet_mess` = '1'
								WHERE `fleet_id` = '" . $fleet_row['fleet_id'] . "';");

        $message = $this->langs['sys_expe_found_ships'] . $found_ship_message;

        $this->expedition_message($fleet_row['fleet_owner'], $message, $fleet_row['fleet_end_stay']);
    }

    /**
     * bbCode function.
     *
     * @param string $string String
     *
     * @return void
     */
    private function set_ships_points()
    {
        return array(202 => 1.0, 203 => 1.5, 204 => 0.5, 205 => 1.5, 206 => 2.0, 207 => 2.5, 208 => 0.5, 209 => 1.0, 210 => 0.01, 211 => 3.0, 212 => 0.0, 213 => 3.5, 214 => 5.0, 215 => 3.2);
    }

    /**
     * bbCode function.
     *
     * @param string $string String
     *
     * @return void
     */
    private function set_ships_ratio()
    {
        return array(202 => 0.1, 203 => 0.1, 204 => 0.1, 205 => 0.5, 206 => 0.25, 207 => 0.125, 208 => 0.5, 209 => 0.1, 210 => 0.1, 211 => 0.0625, 212 => 0.0, 213 => 0.0625, 214 => 0.03125, 215 => 0.0625);
    }

    /**
     * bbCode function.
     *
     * @param string $string String
     *
     * @return void
     */
    private function expedition_message($owner, $message, $time)
    {
        FunctionsLib::sendMessage(
            $owner,
            '',
            $time,
            5,
            $this->langs['sys_mess_qg'],
            $this->langs['sys_expe_report'],
            $message
        );
    }
}

/* end of expedition.php */
