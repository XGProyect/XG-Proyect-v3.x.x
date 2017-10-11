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

use application\core\Database;
use application\core\XGPCore;
use application\libraries\Updates_library;

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

        // load model
        parent::loadModel('libraries/missions/missions');
        
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
        $this->Missions_Model->deleteFleetById($fleet_id);
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
        $this->Missions_Model->updateFleetStatusById($fleet_id);
        
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

        foreach ($ships as $group) {

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
        
        $update_array = [
            'resources' => [
                'metal' => $fleet_row['fleet_resource_metal'],
                'crystal' => $fleet_row['fleet_resource_crystal'],
                'deuterium' => ($fleet_row['fleet_resource_deuterium'] + $fuel_return) 
            ],
            'ships' => $ships_fields,
            'coords' => [
                'galaxy' => $galaxy,
                'system' => $system,
                'planet' => $planet,
                'type' => $type
            ]
        ];
        
        $this->Missions_Model->updatePlanetsShipsByCoords($update_array);
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

        $update_array = [
            'resources' => [
                'metal' => $fleet_row['fleet_resource_metal'],
                'crystal' => $fleet_row['fleet_resource_crystal'],
                'deuterium' => $fleet_row['fleet_resource_deuterium'] 
            ],
            'coords' => [
                'galaxy' => $galaxy,
                'system' => $system,
                'planet' => $planet,
                'type' => $type
            ]
        ];
        
        $this->Missions_Model->updatePlanetResourcesByCoords($update_array);
    }

    /**
     * makeUpdate
     *
     * @param int   $galaxy    Galaxy
     * @param int   $system    System
     * @param int   $planet    Planet
     * @param int   $type      Type
     *
     * @return void
     */
    protected function makeUpdate($galaxy, $system, $planet, $type)
    {
        $target_planet = $this->_db->queryFetch(
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

        $target_user = $this->_db->queryFetch(
            "SELECT *
            FROM `" . USERS . "` AS u
            INNER JOIN " . RESEARCH . " AS r ON r.research_user_id = u.user_id
            INNER JOIN " . PREMIUM . " AS pr ON pr.premium_user_id = u.user_id
            WHERE u.`user_id` = " . $target_planet['planet_user_id']
        );

        Updates_library::updatePlanetResources($target_user, $target_planet, time());
    }
}

/* end of missions.php */
