<?php declare (strict_types = 1);

/**
 * Acs Library
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\libraries\missions;

/**
 * Acs Class
 */
class Acs extends Missions
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ACS Attack - attack between several players
     *
     * @param array $fleet
     * @return void
     */
    public function acsMission(array $fleet): void
    {
        // do mission
        if (parent::canStartMission($fleet)) {
            parent::returnFleet($fleet['fleet_id']);
        }

        // complete mission
        if (parent::canCompleteMission($fleet)) {
            // transfer the ships to the planet
            parent::restoreFleet($fleet);
            parent::removeFleet($fleet['fleet_id']);
        }
    }
}
