<?php
/**
 * Missions Library
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
use application\libraries\UpdatesLibrary;

/**
 * Missions Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Missions extends XGPCore
{
    protected $resource;
    protected $pricelist;
    protected $combat_caps;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // load model
        parent::loadModel('libraries/missions/missions');

        $this->resource = parent::$objects->getObjects();
        $this->pricelist = parent::$objects->getPrice();
        $this->combat_caps = parent::$objects->getCombatSpecs();
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
        $this->Missions_Model->updateFleetStatusToReturnById($fleet_id);
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
            $type = $fleet_row['fleet_start_type'];
        } else {
            $galaxy = $fleet_row['fleet_end_galaxy'];
            $system = $fleet_row['fleet_end_system'];
            $planet = $fleet_row['fleet_end_planet'];
            $type = $fleet_row['fleet_end_type'];
        }

        self::makeUpdate($galaxy, $system, $planet, $type);

        $ships = FleetsLib::getFleetShipsArray($fleet_row['fleet_array']);
        $ships_fields = '';

        foreach ($ships as $id => $amount) {
            $ships_fields .= "`" . $this->resource[$id] . "` = `" .
            $this->resource[$id] . "` + '" . $amount . "', ";
        }

        $fuel_return = 0;

        if ($fleet_row['fleet_mission'] == 4 && !$start) {
            $fuel_return = $fleet_row['fleet_fuel'] / 2;
        }

        $update_array = [
            'resources' => [
                'metal' => $fleet_row['fleet_resource_metal'],
                'crystal' => $fleet_row['fleet_resource_crystal'],
                'deuterium' => ($fleet_row['fleet_resource_deuterium'] + $fuel_return),
            ],
            'ships' => $ships_fields,
            'coords' => [
                'galaxy' => $galaxy,
                'system' => $system,
                'planet' => $planet,
                'type' => $type,
            ],
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
            $type = $fleet_row['fleet_start_type'];
        } else {
            $galaxy = $fleet_row['fleet_end_galaxy'];
            $system = $fleet_row['fleet_end_system'];
            $planet = $fleet_row['fleet_end_planet'];
            $type = $fleet_row['fleet_end_type'];
        }

        self::makeUpdate($galaxy, $system, $planet, $type);

        $update_array = [
            'resources' => [
                'metal' => $fleet_row['fleet_resource_metal'],
                'crystal' => $fleet_row['fleet_resource_crystal'],
                'deuterium' => $fleet_row['fleet_resource_deuterium'],
            ],
            'coords' => [
                'galaxy' => $galaxy,
                'system' => $system,
                'planet' => $planet,
                'type' => $type,
            ],
        ];

        $this->Missions_Model->updatePlanetResourcesByCoords($update_array);
    }

    /**
     * Update planet resources, ships, and queues
     *
     * @param int   $galaxy    Galaxy
     * @param int   $system    System
     * @param int   $planet    Planet
     * @param int   $type      Planet Type
     *
     * @return void
     */
    protected function makeUpdate($galaxy, $system, $planet, $type)
    {
        $target_planet = $this->Missions_Model->getAllPlanetDataByCoords([
            'coords' => [
                'galaxy' => $galaxy,
                'system' => $system,
                'planet' => $planet,
                'type' => $type,
            ],
        ]);

        $target_user = $this->Missions_Model->getAllUserDataByUserId(
            $target_planet['planet_user_id']
        );

        // update planet resources and queues
        UpdatesLibrary::updatePlanetResources($target_user, $target_planet, time());
    }

    /**
     * Check if the mission can be started
     *
     * @param array $fleet
     * @return boolean
     */
    protected function canStartMission(array $fleet): bool
    {
        return ($fleet['fleet_mess'] == 0 && $fleet['fleet_start_time'] <= time() && $fleet['fleet_end_stay'] <= time());
    }

    /**
     * Check if the mission can be completed
     *
     * @param array $fleet
     * @return boolean
     */
    protected function canCompleteMission(array $fleet): bool
    {
        return ($fleet['fleet_end_time'] <= time());
    }
}

/* end of missions.php */
