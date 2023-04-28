<?php

namespace App\Libraries\Missions;

use App\Core\Language;
use App\Core\Objects;
use App\Libraries\FleetsLib;
use App\Libraries\UpdatesLibrary;
use App\Models\Libraries\Missions\Missions as MissionsModel;
use CiLang;

class Missions
{
    protected MissionsModel $missionsModel;
    protected $resource;
    protected $pricelist;
    protected $combat_caps;
    protected ?CiLang $langs = null;

    public function __construct()
    {
        $this->missionsModel = new MissionsModel();

        $this->resource = Objects::getInstance()->getObjects();
        $this->pricelist = Objects::getInstance()->getPrice();
        $this->combat_caps = Objects::getInstance()->getCombatSpecs();
    }

    protected function removeFleet(int $fleetId): void
    {
        $this->missionsModel->deleteFleetById($fleetId);
    }

    protected function returnFleet(int $fleetId): void
    {
        $this->missionsModel->updateFleetStatusToReturnById($fleetId);
    }

    protected function restoreFleet(array $fleetRow, bool $start = true): void
    {
        if ($start) {
            $galaxy = $fleetRow['fleet_start_galaxy'];
            $system = $fleetRow['fleet_start_system'];
            $planet = $fleetRow['fleet_start_planet'];
            $type = $fleetRow['fleet_start_type'];
        } else {
            $galaxy = $fleetRow['fleet_end_galaxy'];
            $system = $fleetRow['fleet_end_system'];
            $planet = $fleetRow['fleet_end_planet'];
            $type = $fleetRow['fleet_end_type'];
        }

        $this->makeUpdate($galaxy, $system, $planet, $type);

        $ships = FleetsLib::getFleetShipsArray($fleetRow['fleet_array']);
        $ships_fields = '';

        foreach ($ships as $id => $amount) {
            $ships_fields .= '`' . $this->resource[$id] . '` = `' .
            $this->resource[$id] . "` + '" . $amount . "', ";
        }

        $fuel_return = 0;

        if ($fleetRow['fleet_mission'] == 4 && !$start) {
            $fuel_return = $fleetRow['fleet_fuel'] / 2;
        }

        $updateArray = [
            'resources' => [
                'metal' => $fleetRow['fleet_resource_metal'],
                'crystal' => $fleetRow['fleet_resource_crystal'],
                'deuterium' => ($fleetRow['fleet_resource_deuterium'] + $fuel_return),
            ],
            'ships' => $ships_fields,
            'coords' => [
                'galaxy' => $galaxy,
                'system' => $system,
                'planet' => $planet,
                'type' => $type,
            ],
        ];

        $this->missionsModel->updatePlanetsShipsByCoords($updateArray);
    }

    protected function storeResources(array $fleetRow, $start = false): void
    {
        if ($start) {
            $galaxy = $fleetRow['fleet_start_galaxy'];
            $system = $fleetRow['fleet_start_system'];
            $planet = $fleetRow['fleet_start_planet'];
            $type = $fleetRow['fleet_start_type'];
        } else {
            $galaxy = $fleetRow['fleet_end_galaxy'];
            $system = $fleetRow['fleet_end_system'];
            $planet = $fleetRow['fleet_end_planet'];
            $type = $fleetRow['fleet_end_type'];
        }

        $this->makeUpdate($galaxy, $system, $planet, $type);

        $updateArray = [
            'resources' => [
                'metal' => $fleetRow['fleet_resource_metal'],
                'crystal' => $fleetRow['fleet_resource_crystal'],
                'deuterium' => $fleetRow['fleet_resource_deuterium'],
            ],
            'coords' => [
                'galaxy' => $galaxy,
                'system' => $system,
                'planet' => $planet,
                'type' => $type,
            ],
        ];

        $this->missionsModel->updatePlanetResourcesByCoords($updateArray);
    }

    protected function makeUpdate(int $galaxy, int $system, int $planet, int $type): void
    {
        $target_planet = $this->missionsModel->getAllPlanetDataByCoords([
            'coords' => [
                'galaxy' => $galaxy,
                'system' => $system,
                'planet' => $planet,
                'type' => $type,
            ],
        ]);

        $target_user = $this->missionsModel->getAllUserDataByUserId(
            $target_planet['planet_user_id']
        );

        // update planet resources and queues
        UpdatesLibrary::updatePlanetResources($target_user, $target_planet, time());
    }

    protected function canStartMission(array $fleet): bool
    {
        return ($fleet['fleet_mess'] == 0 && $fleet['fleet_start_time'] <= time() && $fleet['fleet_end_stay'] <= time());
    }

    protected function canCompleteMission(array $fleet): bool
    {
        return ($fleet['fleet_end_time'] <= time());
    }

    protected function loadLang(array $requiredLang): void
    {
        $this->langs = (new Language())->loadLang($requiredLang, true);
    }
}
