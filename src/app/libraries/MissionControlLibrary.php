<?php

namespace App\libraries;

use App\libraries\Functions;

class MissionControlLibrary
{
    private ?MissionControlLibrary $missionControlLibraryModel = null;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        // load the required model
        $this->missionControlLibraryModel = Functions::model('libraries/MissionControlLibrary');
    }

    /**
     * Get all the fleets that should be arriving by now
     *
     * @return void
     */
    public function arrivingFleets()
    {
        $this->processMissions(
            $this->missionControlLibraryModel->getArrivingFleets()
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
            $this->missionControlLibraryModel->getReturningFleets()
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
