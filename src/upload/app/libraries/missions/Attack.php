<?php
/**
 * Attack Library
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\libraries\missions;

use App\helpers\UrlHelper;
use App\libraries\combatreport\Report;
use App\libraries\FleetsLib;
use App\libraries\FormatLib;
use App\libraries\Functions;
use App\libraries\missions\Attack_lang;
use App\libraries\missions\Missions;
use App\libraries\PlanetLib;
use App\libraries\UpdatesLibrary;
use Battle;
use DebugManager;
use Defense;
use Fleet;
use HomeFleet;
use LangManager;
use Player;
use PlayerGroup;
use Ship;

/**
 * Attack Class
 */
class Attack extends Missions
{
    const SHIP_MIN_ID = 202;
    const SHIP_MAX_ID = 215;
    const DEFENSE_MIN_ID = 401;
    const DEFENSE_MAX_ID = 408;

    /**
     * Contains each player hyperspace technology level
     *
     * @var array
     */
    private $hyperspace_technology = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // load Language
        parent::loadLang(['game/missions', 'game/attack', 'game/combatreport', 'game/defenses', 'game/ships']);
    }

    /**
     * attackMission
     *
     * @param array $fleet_row Fleet row
     *
     * @return void
     */
    public function attackMission($fleet_row)
    {
        // null == use default handlers
        $errorHandler = null;
        $exceptionHandler = null;

        $target_planet = $this->Missions_Model->getAllPlanetDataByCoords([
            'coords' => [
                'galaxy' => $fleet_row['fleet_end_galaxy'],
                'system' => $fleet_row['fleet_end_system'],
                'planet' => $fleet_row['fleet_end_planet'],
                'type' => $fleet_row['fleet_end_type'],
            ],
        ]);

        if ($fleet_row['fleet_mess'] == 0 && $fleet_row['fleet_start_time'] <= time()) {
            // require several stuff
            require XGP_ROOT . VENDOR_PATH .
                'battle_engine' . DIRECTORY_SEPARATOR .
                'utils' . DIRECTORY_SEPARATOR . 'includer.php';

            // require language implementation
            require XGP_ROOT . LIB_PATH .
                'missions' . DIRECTORY_SEPARATOR . 'Attack_lang.php';

            // set language for the reports
            LangManager::getInstance()->setImplementation(new Attack_lang($this->langs, $this->resource));

            if ($fleet_row['fleet_group'] > 0) {
                $this->Missions_Model->deleteAcsFleetById($fleet_row['fleet_group']);
                $this->Missions_Model->updateAcsFleetStatusByGroupId($fleet_row['fleet_group']);
            } else {
                parent::returnFleet($fleet_row['fleet_id']);
            }

            $targetUser = $this->Missions_Model->getAllUserDataByUserId($target_planet['planet_user_id']);
            $target_userID = $targetUser['user_id'];

            UpdatesLibrary::updatePlanetResources($targetUser, $target_planet, time());

            //----------------------- prepare players for battle ----------------------
            // attackers fleet sum
            $attackers = new PlayerGroup();

            // If we have a ACS attack
            if ($fleet_row['fleet_group'] != 0) {
                $fleets = $this->Missions_Model->getAllAcsFleetsByGroupId($fleet_row['fleet_group']);
                $attackers = $this->getPlayerGroupFromQuery($fleets);
            } else {
                $attackers = $this->getPlayerGroup($fleet_row);
            }

            // defenders fleet sum
            $def = $this->Missions_Model->getAllFleetsByEndCoordsAndTimes(
                [
                    'coords' => [
                        'galaxy' => $fleet_row['fleet_end_galaxy'],
                        'system' => $fleet_row['fleet_end_system'],
                        'planet' => $fleet_row['fleet_end_planet'],
                        'type' => $fleet_row['fleet_end_type'],
                    ],
                    'time' => time(),
                ]
            );
            $defenders = $this->getPlayerGroupFromQuery($def, $targetUser);

            //defenses sum
            $homeFleet = new HomeFleet(0);

            for ($i = self::DEFENSE_MIN_ID; $i <= self::DEFENSE_MAX_ID; $i++) {
                if (isset($this->resource[$i]) && isset($target_planet[$this->resource[$i]])) {
                    if ($target_planet[$this->resource[$i]] != 0) {
                        $homeFleet->addShipType($this->getShipType($i, $target_planet[$this->resource[$i]]));
                    }
                }
            }

            for ($i = self::SHIP_MIN_ID; $i <= self::SHIP_MAX_ID; $i++) {
                if (isset($this->resource[$i]) && isset($target_planet[$this->resource[$i]])) {
                    if ($target_planet[$this->resource[$i]] != 0) {
                        $homeFleet->addShipType($this->getShipType($i, $target_planet[$this->resource[$i]]));
                    }
                }
            }

            if (!$defenders->existPlayer($target_userID)) {
                $player = new Player($target_userID, [$homeFleet]);

                $player->setTech(
                    $targetUser['research_weapons_technology'],
                    $targetUser['research_shielding_technology'],
                    $targetUser['research_armour_technology']
                );

                $player->setCoords(
                    $fleet_row['fleet_end_galaxy'],
                    $fleet_row['fleet_end_system'],
                    $fleet_row['fleet_end_planet']
                );

                $player->setName($targetUser['user_name']);

                $defenders->addPlayer($player);
            } else {
                $defenders->getPlayer($target_userID)->addDefense($homeFleet);
            }
            //-------------------------------------------------------------------------
            //------------------------------ battle -----------------------------------
            $battle = new Battle($attackers, $defenders);
            $startBattle = DebugManager::runDebugged([$battle, 'startBattle'], $errorHandler, $exceptionHandler);

            $startBattle();
            //-------------------------------------------------------------------------
            //-------------------------- after battle stuff ---------------------------
            $report = $battle->getReport();
            $steal = $this->updateAttackers(
                $report->getPresentationAttackersFleetOnRound('START'),
                $report->getAfterBattleAttackers(),
                $target_planet
            );

            $report->setSteal($steal);

            $this->updateDefenders(
                $report->getPresentationDefendersFleetOnRound('START'),
                $report->getAfterBattleDefenders(),
                $target_planet,
                $steal
            );

            $this->updateDebris($fleet_row, $report);
            $this->updateMoon($fleet_row, $report, $target_userID);
            $this->createNewReportAndSendIt($fleet_row, $report, $target_planet['planet_name']);
        } elseif ($fleet_row['fleet_end_time'] <= time()) {
            $message = sprintf(
                $this->langs->line('mi_fleet_back_with_resources'),
                $fleet_row['planet_end_name'],
                FleetsLib::targetLink($fleet_row, ''),
                $fleet_row['planet_start_name'],
                FleetsLib::startLink($fleet_row, ''),
                FormatLib::prettyNumber($fleet_row['fleet_resource_metal']),
                FormatLib::prettyNumber($fleet_row['fleet_resource_crystal']),
                FormatLib::prettyNumber($fleet_row['fleet_resource_deuterium'])
            );

            Functions::sendMessage(
                $fleet_row['fleet_owner'],
                '',
                $fleet_row['fleet_end_time'],
                1,
                $this->langs->line('mi_fleet_command'),
                $this->langs->line('mi_fleet_back_title'),
                $message
            );

            parent::restoreFleet($fleet_row);
            parent::removeFleet($fleet_row['fleet_id']);
        }
    }

    /**
     * getShipType
     *
     * @param int $id    Ship ID
     * @param int $count Ship Count
     *
     * @return mixed(Ship|Defense)
     */
    private function getShipType($id, $count)
    {
        $rf = isset($this->combat_caps[$id]['sd']) ? $this->combat_caps[$id]['sd'] : 0;
        $shield = $this->combat_caps[$id]['shield'];
        $cost = [$this->pricelist[$id]['metal'], $this->pricelist[$id]['crystal']];
        $power = $this->combat_caps[$id]['attack'];

        if ($id >= self::SHIP_MIN_ID && $id <= self::SHIP_MAX_ID) {
            return new Ship($id, $count, $rf, $shield, $cost, $power);
        }

        return new Defense($id, $count, $rf, $shield, $cost, $power);
    }

    /**
     * updateDebris
     *
     * @param array  $fleet_row Fleet row
     * @param Report $report    Report
     *
     * @return void
     */
    private function updateDebris($fleet_row, $report)
    {
        list($metal, $crystal) = $report->getDebris();

        $this->Missions_Model->updatePlanetDebrisByCoords(
            [
                'time' => time(),
                'debris' => [
                    'metal' => $metal,
                    'crystal' => $crystal,
                ],
                'coords' => [
                    'galaxy' => $fleet_row['fleet_end_galaxy'],
                    'system' => $fleet_row['fleet_end_system'],
                    'planet' => $fleet_row['fleet_end_planet'],
                ],
            ]
        );
    }

    /**
     * getPlayerGroup
     *
     * @param array  $fleet_row Fleet row
     *
     * @return \PlayerGroup
     */
    private function getPlayerGroup($fleet_row)
    {
        $playerGroup = new PlayerGroup();
        $serializedTypes = FleetsLib::getFleetShipsArray($fleet_row['fleet_array']);
        $idPlayer = $fleet_row['fleet_owner'];
        $fleet = new Fleet($fleet_row['fleet_id']);

        $this->setHyperspaceTechLevel($idPlayer, $fleet_row['research_hyperspace_technology']);

        foreach ($serializedTypes as $id => $count) {
            if ($id != 0 && $count != 0) {
                $fleet->addShipType($this->getShipType($id, $count));
            }
        }

        $player_info = $this->Missions_Model->getTechnologiesByUserId($idPlayer);

        $player = new Player($idPlayer, [$fleet]);
        $player->setTech(
            $player_info['research_weapons_technology'],
            $player_info['research_shielding_technology'],
            $player_info['research_armour_technology']
        );

        $player->setName($player_info['user_name']);

        $player->setCoords(
            $fleet_row['fleet_start_galaxy'],
            $fleet_row['fleet_start_system'],
            $fleet_row['fleet_start_planet']
        );

        $playerGroup->addPlayer($player);

        return $playerGroup;
    }

    /**
     * Get player group from query
     *
     * @param array   $result      Result
     * @param boolean $target_user Target User
     *
     * @return \PlayerGroup
     */
    private function getPlayerGroupFromQuery($result, ?array $target_user = [])
    {
        $playerGroup = new PlayerGroup();

        if (!is_null($result)) {
            foreach ($result as $fleet_row) {
                //making the current fleet object
                $serializedTypes = FleetsLib::getFleetShipsArray($fleet_row['fleet_array']);
                $idPlayer = $fleet_row['fleet_owner'];
                $fleet = new Fleet($fleet_row['fleet_id']);

                foreach ($serializedTypes as $id => $count) {
                    if ($id != 0 && $count != 0) {
                        $fleet->addShipType($this->getShipType($id, $count));
                    }
                }

                //making the player object and add it to playerGroup object
                if (!$playerGroup->existPlayer($idPlayer)) {
                    if (!empty($target_user) && $target_user['user_id'] == $idPlayer) {
                        $player_info = $target_user;
                    } else {
                        $player_info = $this->Missions_Model->getTechnologiesByUserId($idPlayer);
                        $this->setHyperspaceTechLevel($idPlayer, $player_info['research_hyperspace_technology']);
                    }

                    if (isset($target_user['planet_id']) && $target_user['planet_id'] == $idPlayer) {
                        $fleetSouther = new Fleet();
                        $player = new Player($idPlayer, [$fleetSouther]);
                    } else {
                        $player = new Player($idPlayer, [$fleet]);
                    }

                    $player->setTech(
                        $player_info['research_weapons_technology'],
                        $player_info['research_shielding_technology'],
                        $player_info['research_armour_technology']
                    );

                    $player->setCoords(
                        $fleet_row['fleet_start_galaxy'],
                        $fleet_row['fleet_start_system'],
                        $fleet_row['fleet_start_planet']
                    );

                    $player->setName($player_info['user_name']);

                    $playerGroup->addPlayer($player);

                    if (isset($target_user['planet_id']) && $target_user['planet_id'] == $idPlayer) {
                        $playerGroup->getPlayer($idPlayer)->addFleet($fleet);
                    }
                } else {
                    $playerGroup->getPlayer($idPlayer)->addFleet($fleet);
                }
            }
        }

        return $playerGroup;
    }

    /**
     * updateMoon
     *
     * @param array  $fleet_row     Fleet Row
     * @param Report $report        Report
     * @param int    $target_userId Target User ID
     *
     * @return void
     */
    private function updateMoon($fleet_row, $report, $target_userId)
    {
        $moon = $report->tryMoon();

        if ($moon === false) {
            return;
        }

        $galaxy = $fleet_row['fleet_end_galaxy'];
        $system = $fleet_row['fleet_end_system'];
        $planet = $fleet_row['fleet_end_planet'];

        $moon_exists = $this->Missions_Model->getMoonIdByCoords([
            'coords' => [
                'galaxy' => $galaxy,
                'system' => $system,
                'planet' => $planet,
            ],
        ]);

        if ($moon_exists['planet_id'] != null) {
            return;
        }

        // $size and $fields
        extract($moon);

        // create the moon
        $_creator = new PlanetLib();
        $_creator->setNewMoon($galaxy, $system, $planet, $target_userId);
    }

    /**
     * Create a new report and attach it to a message
     *
     * @param array  $fleet_row Fleet Row
     * @param Report $report    Report
     *
     * @return void
     */
    private function createNewReportAndSendIt($fleet_row, $report, $target_planet_name)
    {
        $idAtts = $report->getAttackersId();
        $idDefs = $report->getDefendersId();
        $idAll = array_merge($idAtts, $idDefs);
        $owners = join(',', $idAll);
        $rid = md5($report) . time();
        $destroyed = ($report->getLastRoundNumber() == 1) ? 1 : 0;

        $this->Missions_Model->insertReport([
            'owners' => $owners,
            'rid' => $rid,
            'content' => addslashes($report),
            'time' => time(),
            'destroyed' => $destroyed,
        ]);

        foreach ($idAtts as $id) {
            if ($report->attackerHasWin()) {
                $style = 'green';
            } elseif ($report->isAdraw()) {
                $style = 'orange';
            } else {
                $style = 'red';
            }

            $raport = $this->buildReportLink(
                $style, $rid, $target_planet_name, $fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet']
            );

            Functions::sendMessage(
                $id, '', $fleet_row['fleet_start_time'], 1, $this->langs->line('mi_fleet_command'), $raport, ''
            );
        }

        foreach ($idDefs as $id) {
            if ($report->attackerHasWin()) {
                $style = 'red';
            } elseif ($report->isAdraw()) {
                $style = 'orange';
            } else {
                $style = 'green';
            }

            $raport = $this->buildReportLink(
                $style, $rid, $target_planet_name, $fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet']
            );

            Functions::sendMessage(
                $id, '', $fleet_row['fleet_start_time'], 1, $this->langs->line('mi_fleet_command'), $raport, ''
            );
        }
    }

    /**
     * Get cargo capacity for each ship
     *
     * @param PlayerGroup $players Players
     *
     * @return int
     */
    private function getCapacity(PlayerGroup $players)
    {
        $capacity = 0;

        foreach ($players->getIterator() as $idPlayer => $player) {
            foreach ($player->getIterator() as $idFleet => $fleet) {
                foreach ($fleet->getIterator() as $idShipType => $shipType) {
                    $capacity += $shipType->getCount() * FleetsLib::getMaxStorage(
                        $this->pricelist[$idShipType]['capacity'],
                        $this->hyperspace_technology[$idPlayer]
                    );
                }
            }
        }

        return $capacity;
    }

    /**
     * updateAttackers
     *
     * @param Battle $playerGroupBeforeBattle Player Group before battle
     * @param Battle $playerGroupAfterBattle  Player Group after battle
     * @param array  $target_planet           Target planet
     *
     * @return array
     */
    private function updateAttackers($playerGroupBeforeBattle, $playerGroupAfterBattle, $target_planet)
    {
        $fleetArray = '';
        $emptyFleets = [];
        $capacity = $this->getCapacity($playerGroupAfterBattle);
        $steal = [
            'metal' => 0,
            'crystal' => 0,
            'deuterium' => 0,
        ];

        foreach ($playerGroupBeforeBattle->getIterator() as $idPlayer => $player) {
            $existPlayer = $playerGroupAfterBattle->existPlayer($idPlayer);
            $Xplayer = null;

            if ($existPlayer) {
                $Xplayer = $playerGroupAfterBattle->getPlayer($idPlayer);
            }

            foreach ($player->getIterator() as $idFleet => $fleet) {
                $existFleet = $existPlayer && $Xplayer->existFleet($idFleet);
                $Xfleet = null;

                if ($existFleet) {
                    $Xfleet = $Xplayer->getFleet($idFleet);
                } else {
                    $emptyFleets[] = $idFleet;
                }

                $fleetCapacity = 0;
                $totalCount = 0;
                $fleetArray = [];

                foreach ($fleet as $idShipType => $fighters) {
                    $existShipType = $existFleet && $Xfleet->existShipType($idShipType);
                    $amount = 0;

                    if ($existShipType) {
                        $XshipType = $Xfleet->getShipType($idShipType);
                        $amount = $XshipType->getCount();
                        $fleetCapacity += $amount * $this->pricelist[$idShipType]['capacity'];
                        $totalCount += $amount;
                        $fleetArray[$idShipType] = $amount;
                    }
                }

                if ($existFleet) {
                    $fleetSteal = [
                        'metal' => 0,
                        'crystal' => 0,
                        'deuterium' => 0,
                    ];

                    if ($playerGroupAfterBattle->battleResult == BATTLE_WIN) {
                        $corrispectiveMetal = $target_planet['planet_metal'] * $fleetCapacity / $capacity;
                        $corrispectiveCrystal = $target_planet['planet_crystal'] * $fleetCapacity / $capacity;
                        $corrispectiveDeuterium = $target_planet['planet_deuterium'] * $fleetCapacity / $capacity;

                        $fleetSteal = $this->plunder(
                            $fleetCapacity,
                            $corrispectiveMetal,
                            $corrispectiveCrystal,
                            $corrispectiveDeuterium
                        );

                        $steal['metal'] += $fleetSteal['metal'];
                        $steal['crystal'] += $fleetSteal['crystal'];
                        $steal['deuterium'] += $fleetSteal['deuterium'];
                    }

                    $this->Missions_Model->updateReturningFleetData([
                        'ships' => FleetsLib::setFleetShipsArray($fleetArray),
                        'amount' => $totalCount,
                        'stolen' => [
                            'metal' => $fleetSteal['metal'],
                            'crystal' => $fleetSteal['crystal'],
                            'deuterium' => $fleetSteal['deuterium'],
                        ],
                        'fleet_id' => $idFleet,
                    ]);
                }
            }
        }

        // updating flying fleets
        $id_string = join(',', $emptyFleets);

        if (!empty($id_string)) {
            $this->Missions_Model->deleteMultipleFleetsByIds($id_string);
        }

        return $steal;
    }

    /**
     * updateDefenders
     *
     * @param Battle $playerGroupBeforeBattle Player Group before battle
     * @param Battle $playerGroupAfterBattle  Player Group after battle
     * @param array  $target_planet           Target planet
     * @param array  $steal                   Stealed resources
     *
     * @return void
     */
    private function updateDefenders($playerGroupBeforeBattle, $playerGroupAfterBattle, $target_planet, $steal)
    {
        $Xplayer = $Xfleet = $XshipType = null;
        $fleetArray = '';
        $emptyFleets = [];

        foreach ($playerGroupBeforeBattle->getIterator() as $idPlayer => $player) {
            $existPlayer = $playerGroupAfterBattle->existPlayer($idPlayer);

            if ($existPlayer) {
                $Xplayer = $playerGroupAfterBattle->getPlayer($idPlayer);
            }

            foreach ($player->getIterator() as $idFleet => $fleet) {
                $existFleet = $existPlayer && $Xplayer->existFleet($idFleet);

                if ($existFleet) {
                    $Xfleet = $Xplayer->getFleet($idFleet);
                } else {
                    $emptyFleets[] = $idFleet;
                }

                foreach ($fleet as $idShipType => $fighters) {
                    $existShipType = $existFleet && $Xfleet->existShipType($idShipType);
                    $amount = 0;

                    if ($existShipType) {
                        $XshipType = $Xfleet->getShipType($idShipType);
                        $amount = $XshipType->getCount();
                    }

                    $fleetArray .= '`' . $this->resource[$idShipType] . '` = ' . $amount . ', ';
                }
            }
        }

        // Updating defenses and ships on planet
        $this->Missions_Model->updatePlanetLossesById([
            'ships' => $fleetArray,
            'stolen' => [
                'metal' => $steal['metal'],
                'crystal' => $steal['crystal'],
                'deuterium' => $steal['deuterium'],
            ],
            'planet_id' => $target_planet['planet_id'],
        ]);

        // Updating flying fleets
        $id_string = join(",", $emptyFleets);

        if (!empty($id_string)) {
            $this->Missions_Model->deleteMultipleFleetsByIds($id_string);
        }
    }

    /**
     * plunder
     *
     * @param int $capacity  Capacity
     * @param int $metal     Metal
     * @param int $crystal   Crystal
     * @param int $deuterium Deuterium
     *
     * @return array
     */
    private function plunder($capacity, $metal, $crystal, $deuterium)
    {
        /**
         * 1. Fill up to 1/3 of cargo capacity with metal
         * 2. Fill up to half remaining capacity with crystal
         * 3. The rest will be filled with deuterium
         * 4. If there is still capacity available fill half of it with metal
         * 5. Now fill the rest with crystal
         */
        // Stolen resources
        $steal = [
            'metal' => 0,
            'crystal' => 0,
            'deuterium' => 0,
        ];

        // Max resources that can be take
        $metal /= 2;
        $crystal /= 2;
        $deuterium /= 2;

        // Fill up to 1/3 of cargo capacity with metal
        $stolen = min($capacity / 3, $metal);
        $steal['metal'] += $stolen;
        $metal -= $stolen;
        $capacity -= $stolen;

        // Fill up to half remaining capacity with crystal
        $stolen = min($capacity / 2, $crystal);
        $steal['crystal'] += $stolen;
        $crystal -= $stolen;
        $capacity -= $stolen;

        // The rest will be filled with deuterium
        $stolen = min($capacity, $deuterium);
        $steal['deuterium'] += $stolen;
        $deuterium -= $stolen;
        $capacity -= $stolen;

        // If there is still capacity available fill half of it with metal
        $stolen = min($capacity / 2, $metal);
        $steal['metal'] += $stolen;
        $metal -= $stolen;
        $capacity -= $stolen;

        // Now fill the rest with crystal
        $stolen = min($capacity, $crystal);
        $steal['crystal'] += $stolen;
        $crystal -= $stolen;
        $capacity -= $stolen;

        return $steal;
    }

    /**
     * buildReportLink
     *
     * @param string $color Color
     * @param string $rid   Report ID
     * @param int    $g     Galaxy
     * @param int    $s     System
     * @param int    $p     Planet
     *
     * @return string
     */
    private function buildReportLink($color, $rid, $target_planet_name, $g, $s, $p)
    {
        $style = 'style="color:' . $color . ';"';
        $js = "OnClick=\'f(\"game.php?page=combatreport&report=" . $rid . "\", \"\");\'";
        $content = sprintf($this->langs->line('at_report_title'), $target_planet_name, FormatLib::prettyCoords($g, $s, $p));

        return UrlHelper::setUrl(
            '',
            $content,
            '',
            $style . ' ' . $js
        );
    }

    /**
     * Set hyperspace technology level
     *
     * @param integer $user_id
     * @param integer $level
     * @return void
     */
    private function setHyperspaceTechLevel(int $user_id, int $level): void
    {
        $this->hyperspace_technology[$user_id] = $level;
    }
}
