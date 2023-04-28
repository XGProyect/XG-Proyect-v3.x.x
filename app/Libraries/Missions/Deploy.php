<?php

declare(strict_types=1);

namespace App\Libraries\Missions;

use App\Helpers\StringsHelper;
use App\Libraries\FleetsLib as Fleets;
use App\Libraries\FormatLib as Format;
use App\Libraries\Functions;

class Deploy extends Missions
{
    public function __construct()
    {
        parent::__construct();

        // load Language
        parent::loadLang(['game/missions', 'game/deploy']);
    }

    /**
     * Deploy mission - move fleets from one planet to another
     *
     * @param array $fleet
     * @return void
     */
    public function deployMission(array $fleet): void
    {
        // by default we send ships to the target
        $start_planet = false;

        // do mission
        if (parent::canStartMission($fleet)) {
            // message
            $this->sendDeploymentMessage($fleet);
        } elseif (parent::canCompleteMission($fleet)) {
            // in this case, complete mission = cancel mission,
            // since deployment can only go one way, except if the fleet it's returned
            $start_planet = true;

            // message
            $this->sendReturnMessage($fleet);
        }

        // transfer the ships to the planet
        parent::restoreFleet($fleet, $start_planet);
        parent::removeFleet((int) $fleet['fleet_id']);
    }

    /**
     * Send a deploymeny message to the fleet owner
     *
     * @param array $fleet
     * @return void
     */
    private function sendDeploymentMessage(array $fleet): void
    {
        // send message
        Functions::sendMessage(
            $fleet['fleet_owner'],
            '',
            $fleet['fleet_start_time'],
            5,
            $this->langs->line('mi_fleet_command'),
            $this->langs->line('dep_report_title'),
            StringsHelper::parseReplacements($this->langs->line('dep_report_deployed'), [
                $fleet['planet_start_name'],
                Fleets::startLink($fleet, ''),
                $fleet['planet_end_name'],
                Fleets::targetLink($fleet, ''),
                Format::prettyNumber($fleet['fleet_resource_metal']),
                Format::prettyNumber($fleet['fleet_resource_crystal']),
                Format::prettyNumber($fleet['fleet_resource_deuterium']),
            ])
        );
    }

    /**
     * Send a message informing that the fleet is back
     *
     * @param array $fleet
     * @return void
     */
    private function sendReturnMessage(array $fleet): void
    {
        $text = $this->langs->line('dep_report_back');
        $replacements = [
            $fleet['planet_end_name'],
            Fleets::targetLink($fleet, ''),
            $fleet['planet_start_name'],
            Fleets::startLink($fleet, ''),
        ];

        if (Fleets::hasResources($fleet)) {
            $text = $this->langs->line('dep_report_deployed');
            $replacements = [
                $fleet['planet_end_name'],
                Fleets::targetLink($fleet, ''),
                $fleet['planet_start_name'],
                Fleets::startLink($fleet, ''),
                Format::prettyNumber($fleet['fleet_resource_metal']),
                Format::prettyNumber($fleet['fleet_resource_crystal']),
                Format::prettyNumber($fleet['fleet_resource_deuterium']),
            ];
        }

        // send message
        Functions::sendMessage(
            $fleet['fleet_owner'],
            '',
            $fleet['fleet_end_time'],
            5,
            $this->langs->line('mi_fleet_command'),
            $this->langs->line('dep_report_title'),
            StringsHelper::parseReplacements($text, $replacements)
        );
    }
}
