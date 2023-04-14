<?php

namespace App\Libraries;

use App\Models\Libraries\MissionControlLib as MissionControlLibModel;

class MissionControlLib
{
    private ?MissionControlLibModel $missionControlLibModel = null;

    public function __construct()
    {
        $this->missionControlLibModel = new MissionControlLibModel();
    }

    /**
     * Get all the fleets that should be arriving by now
     *
     * @return void
     */
    public function arrivingFleets()
    {
        $this->processMissions(
            $this->missionControlLibModel->getArrivingFleets()
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
            $this->missionControlLibModel->getReturningFleets()
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
            $class_name = '\App\Libraries\Missions\\' . $name;

            $mission = new $class_name();
            $mission->$mission_name($fleet);
        }
    }
}
