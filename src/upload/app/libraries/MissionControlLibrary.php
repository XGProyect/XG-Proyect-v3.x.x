<?php
/**
 * Mission Control Library
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\libraries;

use App\libraries\Functions;

/**
 * MissionControlLibrary Class
 */
class MissionControlLibrary
{
    /**
     * Contains the model MissionControlLibrary
     *
     * @var \MissionControlLibrary
     */
    private $Mission_control_library_Model = null;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        // load the required model
        $this->Mission_control_library_Model = Functions::modelLoader('libraries/MissionControlLibrary');
    }

    /**
     * Get all the fleets that should be arriving by now
     *
     * @return void
     */
    public function arrivingFleets()
    {
        $this->processMissions(
            $this->Mission_control_library_Model->getArrivingFleets()
        );
    }

    /**
     * Get all the fleets that should be returning by now
     *
     * @return void
     */
    public function returningFleets()
    {
        $this->processMissions(
            $this->Mission_control_library_Model->getReturningFleets()
        );
    }

    /**
     * Process the mission based on the provided fleets
     *
     * @param array $all_fleets A list of fleets
     *
     * @return void
     */
    private function processMissions($all_fleets = [])
    {
        // validate
        if (!is_array($all_fleets) or empty($all_fleets)) {
            return;
        }

        // missions list
        $missions = [
            1 => 'Attack',
            2 => 'Acs',
            3 => 'Transport',
            4 => 'Deploy',
            5 => 'Stay',
            6 => 'Spy',
            7 => 'Colonize',
            8 => 'Recycle',
            9 => 'Destroy',
            10 => 'Missile',
            15 => 'Expedition',
        ];

        // Process missions
        foreach ($all_fleets as $fleet) {
            $name = $missions[$fleet['fleet_mission']];
            $mission_name = $name . 'Mission';
            $class_name = '\App\libraries\missions\\' . $name;

            $mission = new $class_name();
            $mission->$mission_name($fleet);
        }
    }
}
