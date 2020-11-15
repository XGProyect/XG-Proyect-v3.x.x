<?php declare (strict_types = 1);

/**
 * Transport Library
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\libraries\missions;

use App\helpers\StringsHelper;
use App\libraries\FleetsLib as Fleets;
use App\libraries\FormatLib as Format;
use App\libraries\Functions;
use App\libraries\missions\Missions;

/**
 * Transport Class
 */
class Transport extends Missions
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // load Language
        parent::loadLang(['game/missions', 'game/transport']);
    }

    /**
     * Transport mission - deliver resources between planets
     *
     * @param array $fleet
     * @return void
     */
    public function transportMission(array $fleet): void
    {
        // get required data
        $trading_planets = $this->getTradingPlanetsData($fleet);

        // do mission
        if (parent::canStartMission($fleet)) {
            // messages
            $this->sendDeliveryMessageToOwner($fleet, $trading_planets);
            $this->sendDeliveryMessageToReceiver($fleet, $trading_planets);

            // transfer the fleet resources to the planet
            parent::storeResources($fleet, false);
            $this->Missions_Model->updateReturningFleetResources($fleet['fleet_id']);
        }

        // complete mission
        if (parent::canCompleteMission($fleet)) {
            // message
            $this->sendReturnMessage($fleet, $trading_planets);

            // transfer the ships to the planet
            parent::restoreFleet($fleet);
            parent::removeFleet($fleet['fleet_id']);
        }
    }

    /**
     * Get data for the planets that are trading resources
     *
     * @param array $fleet
     * @return array
     */
    private function getTradingPlanetsData(array $fleet): array
    {
        return $this->Missions_Model->getFriendlyPlanetData([
            'coords' => [
                'start' => [
                    'galaxy' => $fleet['fleet_start_galaxy'],
                    'system' => $fleet['fleet_start_system'],
                    'planet' => $fleet['fleet_start_planet'],
                    'type' => $fleet['fleet_start_type'],
                ],
                'end' => [
                    'galaxy' => $fleet['fleet_end_galaxy'],
                    'system' => $fleet['fleet_end_system'],
                    'planet' => $fleet['fleet_end_planet'],
                    'type' => $fleet['fleet_end_type'],
                ],
            ],
        ]);
    }

    /**
     * Send a delivery message to the fleet owner
     *
     * @param array $fleet
     * @param array $trading_planets
     * @return void
     */
    private function sendDeliveryMessageToOwner(array $fleet, array $trading_planets): void
    {
        // send message
        Functions::sendMessage(
            $trading_planets['start_id'],
            '',
            $fleet['fleet_start_time'],
            5,
            $this->langs->line('mi_fleet_command'),
            $this->langs->line('tra_reaching'),
            StringsHelper::parseReplacements($this->langs->line('tra_delivered_resources'), [
                $trading_planets['start_name'],
                Fleets::startLink($fleet, ''),
                $trading_planets['target_name'],
                Fleets::targetLink($fleet, ''),
                Format::prettyNumber($fleet['fleet_resource_metal']),
                Format::prettyNumber($fleet['fleet_resource_crystal']),
                Format::prettyNumber($fleet['fleet_resource_deuterium']),
            ])
        );
    }

    /**
     * Send a delivery message to the receiver, only if the target planet is not a planet from the same user
     *
     * @param array $fleet
     * @param array $trading_planets
     * @return void
     */
    private function sendDeliveryMessageToReceiver(array $fleet, array $trading_planets): void
    {
        if ($trading_planets['start_id'] != $trading_planets['target_id']) {
            // send message
            Functions::sendMessage(
                $trading_planets['target_id'],
                '',
                $fleet['fleet_start_time'],
                5,
                $this->langs->line('tra_incoming_from'),
                $this->langs->line('tra_incoming_title'),
                StringsHelper::parseReplacements($this->langs->line('tra_incoming_delivery'), [
                    $trading_planets['start_user_name'],
                    $trading_planets['start_name'],
                    Fleets::startLink($fleet, ''),
                    $trading_planets['target_name'],
                    Fleets::targetLink($fleet, ''),
                    Format::prettyNumber($fleet['fleet_resource_metal']),
                    Format::prettyNumber($fleet['fleet_resource_crystal']),
                    Format::prettyNumber($fleet['fleet_resource_deuterium']),
                    Format::prettyNumber($trading_planets['target_metal']),
                    Format::prettyNumber($trading_planets['target_crystal']),
                    Format::prettyNumber($trading_planets['target_deuterium']),
                    Format::prettyNumber($trading_planets['target_metal'] + $fleet['fleet_resource_metal']),
                    Format::prettyNumber($trading_planets['target_crystal'] + $fleet['fleet_resource_crystal']),
                    Format::prettyNumber($trading_planets['target_deuterium'] + $fleet['fleet_resource_deuterium']),
                ])
            );
        }
    }

    /**
     * Send a message informing that the fleet is back
     *
     * @param array $fleet
     * @param array $trading_planets
     * @return void
     */
    private function sendReturnMessage(array $fleet, array $trading_planets): void
    {
        $text = $this->langs->line('mi_fleet_back_without_resources');
        $replacements = [
            $trading_planets['target_name'],
            Fleets::targetLink($fleet, ''),
            $trading_planets['start_name'],
            Fleets::startLink($fleet, ''),
        ];

        if (Fleets::hasResources($fleet)) {
            $text = $this->langs->line('mi_fleet_back_with_resources');
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
            $trading_planets['start_id'],
            '',
            $fleet['fleet_end_time'],
            5,
            $this->langs->line('mi_fleet_command'),
            $this->langs->line('mi_fleet_back_title'),
            StringsHelper::parseReplacements($text, $replacements)
        );
    }
}
