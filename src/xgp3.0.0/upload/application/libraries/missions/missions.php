<?php
/**
 * Missions Library
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

use application\core\XGPCore;
use application\libraries\FleetsLib;
use application\libraries\UpdateResourcesLib;

/**
 * Missions Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Missions extends XGPCore
{
    protected $langs;
    protected $resource;
    protected $pricelist;
    protected $combat_caps;

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

        $this->langs        = parent::$lang;
        $this->resource     = parent::$objects->getObjects();
        $this->pricelist    = parent::$objects->getPrice();
        $this->combat_caps  = parent::$objects->getCombatSpecs();
    }

    /**
     * removeFleet
     *
     * @param int $fleet_id Fleed ID
     *
     * @return void
     */
    protected function removeFleet($fleet_id)
    {
        parent::$db->query("DELETE FROM " . FLEETS . " WHERE `fleet_id` = " . (int) $fleet_id);
    }

    /**
     * returnFleet
     *
     * @param int $fleet_id Fleed ID
     *
     * @return void
     */
    protected function returnFleet($fleet_id)
    {
        parent::$db->query(
            "UPDATE " . FLEETS . " SET
            `fleet_mess` = '1'
            WHERE `fleet_id` = " . (int)$fleet_id
        );
    }

    /**
     * restoreFleet
     *
     * @param array   $fleet_row Fleet row
     * @param boolean $start     Start
     *
     * @return void
     */
    protected function restoreFleet($fleet_row, $start = true)
    {
        if ($start) {

            $galaxy = $fleet_row['fleet_start_galaxy'];
            $system = $fleet_row['fleet_start_system'];
            $planet = $fleet_row['fleet_start_planet'];
            $type   = $fleet_row['fleet_start_type'];
        } else {

            $galaxy = $fleet_row['fleet_end_galaxy'];
            $system = $fleet_row['fleet_end_system'];
            $planet = $fleet_row['fleet_end_planet'];
            $type   = $fleet_row['fleet_end_type'];
        }

        self::makeUpdate($fleet_row, $galaxy, $system, $planet, $type);

        $ships          = explode(';', $fleet_row['fleet_array']);
        $ships_fields   = '';

        foreach ($ships as $item => $group) {

            if ($group != '') {

                $ship           = explode(",", $group);
                $ships_fields   .= "`" . $this->resource[$ship[0]] . "` = `" .
                    $this->resource[$ship[0]] . "` + '" . $ship[1] . "', ";
            }
        }

        $fuel_return = 0;
        
        if ($fleet_row['fleet_mission'] == 4 && !$start) {

            $fuel_return = $fleet_row['fleet_fuel'] / 2;
        }
        
        parent::$db->query(
            "UPDATE " . PLANETS . " AS p
            INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id` SET
            {$ships_fields}
            `planet_metal` = `planet_metal` + '" . $fleet_row['fleet_resource_metal'] . "',
            `planet_crystal` = `planet_crystal` + '" . $fleet_row['fleet_resource_crystal'] . "',
            `planet_deuterium` = `planet_deuterium` + '" . ($fleet_row['fleet_resource_deuterium'] + $fuel_return) . "'
            WHERE `planet_galaxy` = '" . $galaxy . "' AND
                `planet_system` = '" . $system . "' AND
                `planet_planet` = '" . $planet . "' AND
                `planet_type` = '" . $type . "'"
        );
    }

    /**
     * storeResources
     *
     * @param array   $fleet_row Fleet row
     * @param boolean $start     Start
     *
     * @return void
     */
    protected function storeResources($fleet_row, $start = false)
    {
        if ($start) {

            $galaxy = $fleet_row['fleet_start_galaxy'];
            $system = $fleet_row['fleet_start_system'];
            $planet = $fleet_row['fleet_start_planet'];
            $type   = $fleet_row['fleet_start_type'];
        } else {

            $galaxy = $fleet_row['fleet_end_galaxy'];
            $system = $fleet_row['fleet_end_system'];
            $planet = $fleet_row['fleet_end_planet'];
            $type   = $fleet_row['fleet_end_type'];
        }

        self::makeUpdate($fleet_row, $galaxy, $system, $planet, $type);

        parent::$db->query(
            "UPDATE " . PLANETS . " SET
            `planet_metal` = `planet_metal` + '" . $fleet_row['fleet_resource_metal'] . "',
            `planet_crystal` = `planet_crystal` + '" . $fleet_row['fleet_resource_crystal'] . "',
            `planet_deuterium` = `planet_deuterium` + '" . $fleet_row['fleet_resource_deuterium'] . "'
            WHERE `planet_galaxy` = '" . $galaxy . "' AND
                `planet_system` = '" . $system . "' AND
                `planet_planet` = '" . $planet . "' AND
                `planet_type` = '" . $type . "'
                LIMIT 1;"
        );
    }

    /**
     * makeUpdate
     *
     * @param array $fleet_row Fleet row
     * @param int   $galaxy    Galaxy
     * @param int   $system    System
     * @param int   $planet    Planet
     * @param int   $type      Type
     *
     * @return void
     */
    protected function makeUpdate($fleet_row, $galaxy, $system, $planet, $type)
    {
        $target_planet = parent::$db->queryFetch(
            "SELECT *
            FROM `" . PLANETS . "` AS p
            LEFT JOIN `" . BUILDINGS . "` AS b ON b.building_planet_id = p.`planet_id`
            LEFT JOIN `" . DEFENSES . "` AS d ON d.defense_planet_id = p.`planet_id`
            LEFT JOIN `" . SHIPS . "` AS s ON s.ship_planet_id = p.`planet_id`
            WHERE `planet_galaxy` = " . $galaxy . " AND
                `planet_system` = " . $system . " AND
                `planet_planet` = " . $planet . " AND
                `planet_type` = " . $type . ";"
        );

        $target_user = parent::$db->queryFetch(
            "SELECT *
            FROM `" . USERS . "` AS u
            INNER JOIN " . RESEARCH . " AS r ON r.research_user_id = u.user_id
            INNER JOIN " . PREMIUM . " AS pr ON pr.premium_user_id = u.user_id
            WHERE u.`user_id` = " . $target_planet['planet_user_id']
        );

        UpdateResourcesLib::updateResources($target_user, $target_planet, time());
    }
}

/* end of missions.php */
