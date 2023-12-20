<?php

namespace App\Libraries\Missions;

use App\Core\Objects;
use App\Libraries\FleetsLib;
use App\Libraries\FormatLib;
use App\Libraries\Functions;
use App\Services\Formulas\Expedition as FmlExpedition;

class Expedition extends Missions
{
    private FmlExpedition $fmlExpedition;
    private int $resourceExpeditionPoints = 0;
    private int $shipExpeditionPoints = 0;
    private int $fleetCapacity = 0;

    public function __construct()
    {
        parent::__construct();

        // load Language
        parent::loadLang(['game/global', 'game/missions', 'game/expedition', 'game/ships']);

        $this->fmlExpedition = new FmlExpedition();
    }

    public function expeditionMission(array $fleet): void
    {
        // do mission
        if (parent::canStartMission($fleet)) {
            $this->setExpeditionPoints($fleet);

            switch ($this->fmlExpedition->getExpeditionResult()) {
                case 'darkMatter':
                    $this->resultDarkMatter($fleet);
                    break;
                case 'ships':
                    $this->resultShips($fleet);
                    break;
                case 'resources':
                    $this->resultResources($fleet);
                    break;
                case 'pirates':
                    //$this->resultPirates($fleet);
                    $this->resultNothing($fleet);
                    break;
                case 'aliens':
                    //$this->resultAliens($fleet);
                    $this->resultNothing($fleet);
                    break;
                case 'delay':
                    $this->resultDelay($fleet);
                    break;
                case 'early':
                    $this->resultEarly($fleet);
                    break;
                case 'merchant':
                    //$this->resultMerchant($fleet);
                    $this->resultNothing($fleet);
                    break;
                case 'blackHole':
                    $this->resultBlackHole($fleet);
                    break;
                case 'nothing':
                default:
                    $this->resultNothing($fleet);
                    break;
            }
        } elseif (parent::canCompleteMission($fleet)) {
            $fleetUsedStorage = $fleet['fleet_resource_metal'] + $fleet['fleet_resource_crystal'] + $fleet['fleet_resource_deuterium'];

            if ($fleetUsedStorage === 0) {
                $message = sprintf(
                    $this->langs->line('mi_fleet_back_without_resources'),
                    $fleet['planet_end_name'],
                    FormatLib::prettyCoords($fleet['fleet_end_galaxy'], $fleet['fleet_end_system'], $fleet['fleet_end_planet']),
                    $fleet['planet_start_name'],
                    FormatLib::prettyCoords($fleet['fleet_start_galaxy'], $fleet['fleet_start_system'], $fleet['fleet_start_planet']),
                );

                $this->expeditionMessage(
                    (int) $fleet['fleet_owner'],
                    $message,
                    (int) $fleet['fleet_end_stay'],
                    [
                        'galaxy' => $fleet['fleet_end_galaxy'],
                        'system' => $fleet['fleet_end_system'],
                        'planet' => $fleet['fleet_end_planet'],
                    ]
                );
            } else {
                $message = sprintf(
                    $this->langs->line('mi_fleet_back_with_resources'),
                    $fleet['planet_end_name'],
                    FormatLib::prettyCoords($fleet['fleet_end_galaxy'], $fleet['fleet_end_system'], $fleet['fleet_end_planet']),
                    $fleet['planet_start_name'],
                    FormatLib::prettyCoords($fleet['fleet_start_galaxy'], $fleet['fleet_start_system'], $fleet['fleet_start_planet']),
                    FormatLib::prettyNumber($fleet['fleet_resource_metal']),
                    FormatLib::prettyNumber($fleet['fleet_resource_crystal']),
                    FormatLib::prettyNumber($fleet['fleet_resource_deuterium'])
                );

                $this->expeditionMessage(
                    (int) $fleet['fleet_owner'],
                    $message,
                    (int) $fleet['fleet_end_stay'],
                    [
                        'galaxy' => $fleet['fleet_end_galaxy'],
                        'system' => $fleet['fleet_end_system'],
                        'planet' => $fleet['fleet_end_planet'],
                    ]
                );
            }

            parent::restoreFleet($fleet, true);
            parent::removeFleet($fleet['fleet_id']);
        }
    }

    private function setExpeditionPoints(array $fleet): void
    {
        $priceList = Objects::getInstance()->getPrice();
        $expeditionPoints = 0;

        foreach (FleetsLib::getFleetShipsArray($fleet['fleet_array']) as $id => $count) {
            if (in_array($id, $this->fmlExpedition->getPossibleShips())) {
                $expeditionPoints += $this->fmlExpedition->calculateExpeditionPoints(
                    ($priceList[$id]['metal'] + $priceList[$id]['crystal'])
                ) * $count;
            }

            $this->fleetCapacity += FleetsLib::getMaxStorage(
                $priceList[$id]['capacity'],
                $fleet['research_hyperspace_technology']
            ) * $count;
        }

        $topPlayerPoints = $this->missionsModel->getTopPlayerPoints();

        $maxResourceFindExpeditionPoints = $this->fmlExpedition->getMaxExpeditionPoints(
            $topPlayerPoints
        );
        $maxShipsFindExpeditionPoints = $this->fmlExpedition->getMaxShipsExpeditionPoints(
            $topPlayerPoints
        );

        $this->resourceExpeditionPoints = $expeditionPoints;
        $this->shipExpeditionPoints = $expeditionPoints;

        // limit the amount of resources that can be found
        if ($expeditionPoints > $maxResourceFindExpeditionPoints) {
            $this->resourceExpeditionPoints = $maxResourceFindExpeditionPoints;
        }

        // limit the amount of ships that can be found
        if ($expeditionPoints > $maxShipsFindExpeditionPoints) {
            $this->shipExpeditionPoints = $maxShipsFindExpeditionPoints;
        }
    }

    /**
     * @todo needs polishing, there are 3 types of packages
     * small package: 300-400 DM
     * medium package: 500-700 DM
     * large package: 1.000-1.800 DM
     *
     * needs review because I replicated previous used logic for resources
     * I couldn't find any rule behind this...
     */
    private function resultDarkMatter(array $fleet): void
    {
        $darkMatterFound = $this->fmlExpedition->getDarkMatterSourceSize(
            $this->fmlExpedition->calculateDarkMatterSourceSize()
        );

        $this->expeditionMessage(
            (int) $fleet['fleet_owner'],
            $this->langs->line('exp_dm_' . mt_rand(1, 5)),
            (int) $fleet['fleet_end_stay'],
            [
                'galaxy' => $fleet['fleet_end_galaxy'],
                'system' => $fleet['fleet_end_system'],
                'planet' => $fleet['fleet_end_planet'],
            ]
        );

        $this->missionsModel->updateDarkMatter((int) $fleet['fleet_owner'], $darkMatterFound);

        parent::returnFleet($fleet['fleet_id']);
    }

    /**
     * @todo probably not 100% like the original game
     */
    private function resultShips(array $fleet): void
    {
        $shipsRatio = $this->fmlExpedition->getShipsObtainableChances();
        $foundChance = $this->shipExpeditionPoints / $fleet['fleet_amount'];
        $currentFleet = FleetsLib::getFleetShipsArray($fleet['fleet_array']);
        $foundShip = [];

        for ($ship = 202; $ship <= 215; $ship++) {
            if (isset($currentFleet[$ship]) && $currentFleet[$ship] != 0) {
                $foundShip[$ship] = round($currentFleet[$ship] * $shipsRatio[$ship] * $foundChance) + 1;

                if ($foundShip[$ship] > 0) {
                    $currentFleet[$ship] += $foundShip[$ship];
                }
            }
        }

        $newShips = [];
        $found_ship_message = '';

        foreach ($currentFleet as $ship => $count) {
            if ($count > 0) {
                $newShips[$ship] = $count;
            }
        }

        if ($foundShip != null) {
            foreach ($foundShip as $ship => $count) {
                if ($count != 0) {
                    $found_ship_message .= $this->langs->line($this->resource[$ship]) . ': ' . $count . '<br>';
                }
            }
        }

        $this->missionsModel->updateFleetArrayById([
            'ships' => FleetsLib::setFleetShipsArray($newShips),
            'fleet_id' => $fleet['fleet_id'],
        ]);

        $message = sprintf(
            $this->langs->line('exp_new_ships_' . mt_rand(1, 5)),
            $found_ship_message
        );

        $this->expeditionMessage(
            $fleet['fleet_owner'],
            $message,
            (int) $fleet['fleet_end_stay'],
            [
                'galaxy' => $fleet['fleet_end_galaxy'],
                'system' => $fleet['fleet_end_system'],
                'planet' => $fleet['fleet_end_planet'],
            ]
        );
    }

    private function resultResources(array $fleet): void
    {
        // fleet capacity
        $fleetUsedStorage = $fleet['fleet_resource_metal'] + $fleet['fleet_resource_crystal'] + $fleet['fleet_resource_deuterium'];
        $fleetMaxCapacity = $this->fleetCapacity - $fleetUsedStorage;

        // expedition resources obtained calculations
        $typeObtained = $this->fmlExpedition->calculateResourceTypeObtained();
        $foundAmount = $this->fmlExpedition->getResourceFoundAmount(
            $this->fmlExpedition->getResourceSourceSizeMultChances(
                $typeObtained
            ),
            $this->resourceExpeditionPoints,
            $typeObtained
        );

        if ($foundAmount > $fleetMaxCapacity) {
            $fillFleetStorage = $fleetMaxCapacity;
        } else {
            $fillFleetStorage = $foundAmount;
        }

        $this->missionsModel->updateFleetResourcesById(
            (int) $fleet['fleet_id'],
            $typeObtained,
            $fillFleetStorage
        );

        $this->expeditionMessage(
            (int) $fleet['fleet_owner'],
            $this->langs->line('exp_new_resources_' . mt_rand(1, 4)),
            (int) $fleet['fleet_end_stay'],
            [
                'galaxy' => $fleet['fleet_end_galaxy'],
                'system' => $fleet['fleet_end_system'],
                'planet' => $fleet['fleet_end_planet'],
            ]
        );

        parent::returnFleet($fleet['fleet_id']);
    }

    /**
     * @todo implement
     */
    private function resultPirates(array $fleet): void
    {
    }

    /**
     * @todo implement
     */
    private function resultAliens(array $fleet): void
    {
    }

    /**
     * @todo probably not 100% like the original game
     */
    private function resultDelay(array $fleet): void
    {
        $fleetDelayMultiplier = $this->fmlExpedition->getFleetDeplay();
        $returnTime = (int) $fleet['fleet_end_time'] - (int) $fleet['fleet_end_stay'];

        $this->missionsModel->updateFleetEndTime(
            (int) $fleet['fleet_id'],
            ($fleet['fleet_end_time'] + ($returnTime * $fleetDelayMultiplier))
        );

        $this->expeditionMessage(
            (int) $fleet['fleet_owner'],
            $this->langs->line('exp_delay_' . mt_rand(1, 5)),
            (int) $fleet['fleet_end_stay'],
            [
                'galaxy' => $fleet['fleet_end_galaxy'],
                'system' => $fleet['fleet_end_system'],
                'planet' => $fleet['fleet_end_planet'],
            ]
        );

        parent::returnFleet($fleet['fleet_id']);
    }

    /**
     * @todo probably not 100% like the original game
     */
    private function resultEarly(array $fleet): void
    {
        $returnTime = (int) $fleet['fleet_end_time'] - (int) $fleet['fleet_end_stay'];

        $this->missionsModel->updateFleetEndTime(
            (int) $fleet['fleet_id'],
            ($fleet['fleet_end_time'] - ($returnTime / 2))
        );

        $this->expeditionMessage(
            (int) $fleet['fleet_owner'],
            $this->langs->line('exp_delay_' . mt_rand(1, 5)),
            (int) $fleet['fleet_end_stay'],
            [
                'galaxy' => $fleet['fleet_end_galaxy'],
                'system' => $fleet['fleet_end_system'],
                'planet' => $fleet['fleet_end_planet'],
            ]
        );

        parent::returnFleet($fleet['fleet_id']);
    }

    /**
     * @todo implement
     */
    private function resultMerchant(array $fleet): void
    {
    }

    /**
     * @todo probably not 100% like the original game
     */
    private function resultBlackHole(array $fleet): void
    {
        $lostChances = (mt_rand(0, 3) * 33 + 1) / 100;

        if ($lostChances == 1) {
            $this->expeditionMessage(
                $fleet['fleet_owner'],
                $this->langs->line('exp_lost_1'),
                (int) $fleet['fleet_end_stay'],
                [
                    'galaxy' => $fleet['fleet_end_galaxy'],
                    'system' => $fleet['fleet_end_system'],
                    'planet' => $fleet['fleet_end_planet'],
                ]
            );

            $this->missionsModel->updateLostShipsAndDefensePoints(
                $fleet['fleet_owner'],
                FleetsLib::getFleetShipsArray($fleet['fleet_array'])
            );
            parent::removeFleet($fleet['fleet_id']);
        } else {
            $newShips = [];
            $lostShips = [];
            $lostAll = true;

            foreach (FleetsLib::getFleetShipsArray($fleet['fleet_array']) as $ship => $amount) {
                if (floor($amount * $lostChances) != 0) {
                    $lostShips[$ship] = floor($amount * $lostChances);
                    $newShips[$ship] = ($amount - $lostShips[$ship]);
                    $lostAll = false;
                }
            }

            if (!$lostAll) {
                $this->expeditionMessage(
                    $fleet['fleet_owner'],
                    $this->langs->line('exp_lost_1'),
                    (int) $fleet['fleet_end_stay'],
                    [
                        'galaxy' => $fleet['fleet_end_galaxy'],
                        'system' => $fleet['fleet_end_system'],
                        'planet' => $fleet['fleet_end_planet'],
                    ]
                );

                $this->missionsModel->updateLostShipsAndDefensePoints($fleet['fleet_owner'], $lostShips);
                $this->missionsModel->updateFleetArrayById([
                    'ships' => FleetsLib::setFleetShipsArray($newShips),
                    'fleet_id' => $fleet['fleet_id'],
                ]);
            } else {
                $this->expeditionMessage(
                    $fleet['fleet_owner'],
                    $this->langs->line('exp_lost_1'),
                    (int) $fleet['fleet_end_stay'],
                    [
                        'galaxy' => $fleet['fleet_end_galaxy'],
                        'system' => $fleet['fleet_end_system'],
                        'planet' => $fleet['fleet_end_planet'],
                    ]
                );

                $this->missionsModel->updateLostShipsAndDefensePoints(
                    $fleet['fleet_owner'],
                    FleetsLib::getFleetShipsArray($fleet['fleet_array'])
                );
                parent::removeFleet($fleet['fleet_id']);
            }
        }
    }

    private function resultNothing(array $fleet): void
    {
        $this->expeditionMessage(
            $fleet['fleet_owner'],
            $this->langs->line('exp_nothing_' . mt_rand(1, 6)),
            (int) $fleet['fleet_end_stay'],
            [
                'galaxy' => $fleet['fleet_end_galaxy'],
                'system' => $fleet['fleet_end_system'],
                'planet' => $fleet['fleet_end_planet'],
            ]
        );

        parent::returnFleet($fleet['fleet_id']);
    }

    private function expeditionMessage(int $owner, string $message, int $time, array $coords): void
    {
        $subject = sprintf(
            $this->langs->line('exp_report_title'),
            FormatLib::prettyCoords($coords['galaxy'], $coords['system'], $coords['planet'])
        );

        Functions::sendMessage(
            $owner,
            '',
            $time,
            5,
            $this->langs->line('mi_fleet_command'),
            $subject,
            $message
        );
    }
}
