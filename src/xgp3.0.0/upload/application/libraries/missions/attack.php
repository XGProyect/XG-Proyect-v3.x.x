<?php
/**
 * Attack Library
 *
 * PHP Version 5.5+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */

namespace application\libraries\missions;

use application\libraries\FleetsLib;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use application\libraries\UpdateResourcesLib;
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
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Attack extends Missions
{
    const SHIP_MIN_ID       = 202;
    const SHIP_MAX_ID       = 215;
    const DEFENSE_MIN_ID    = 401;
    const DEFENSE_MAX_ID    = 503;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
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
        $errorHandler       = null;
        $exceptionHandler   = null;

        $target_planet = parent::$db->queryFetch(
            "SELECT *
            FROM " . PLANETS . " AS p
            INNER JOIN " . BUILDINGS . " AS b ON b.building_planet_id = p.`planet_id`
            INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id`
            INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id`
            WHERE `planet_galaxy` = ". (int)$fleet_row['fleet_end_galaxy'] ." AND
                `planet_system` = ". (int)$fleet_row['fleet_end_system'] ." AND
                `planet_type` = ". (int)$fleet_row['fleet_end_type'] ." AND
                `planet_planet` = ". (int)$fleet_row['fleet_end_planet'] .";"
        );
        
        if ($fleet_row['fleet_mess'] == 0 && $fleet_row['fleet_start_time'] <= time()) {

            // require several stuff
            require XGP_ROOT . VENDOR_PATH .
                    'battle_engine' . DIRECTORY_SEPARATOR .
                    'utils' . DIRECTORY_SEPARATOR . 'includer.php';

            // require language implementation
            require XGP_ROOT . LIB_PATH .
                    'missions' . DIRECTORY_SEPARATOR . 'attackLang.php';
            
            // set language for the reports
            LangManager::getInstance()->setImplementation(new AttackLang($this->langs));
            
            if ($fleet_row['fleet_group'] > 0) {

                parent::$db->query(
                    "DELETE FROM `" . ACS_FLEETS . "`
                    WHERE `acs_fleet_id` = '" . (int)$fleet_row['fleet_group'] . "'"
                );

                parent::$db->query(
                    "UPDATE `" . FLEETS . "` SET
                    `fleet_mess` = '1'
                    WHERE `fleet_group` = '" . $fleet_row['fleet_group'] . "'"
                );
            } else {

                parent::returnFleet($fleet_row['fleet_id']);
            }

            $targetUser = parent::$db->queryFetch(
                "SELECT u.*,
                    r.*,
                    pr.*
                FROM " . USERS . " AS u
                    INNER JOIN " . RESEARCH . " AS r ON r.research_user_id = u.user_id
                    INNER JOIN " . PREMIUM . " AS pr ON pr.premium_user_id = u.user_id
                WHERE u.user_id = '" . intval($target_planet['planet_user_id']) . "';"
            );

            $target_userID  = $targetUser['user_id'];

            UpdateResourcesLib::updateResources($targetUser, $target_planet, time());

            //----------------------- prepare players for battle ----------------------
            // attackers fleet sum
            $attackers  = new PlayerGroup();

            // If we have a ACS attack
            if ($fleet_row['fleet_group'] != 0) {
                    
                $fleets     = parent::$db->queryFetch(
                    "SELECT * FROM `" . FLEETS . "` WHERE `fleet_group` = '" . $fleet_row['fleet_group'] . "';"
                );

                $attackers  = $this->getPlayerGroupFromQuery($fleets);
            } else {

                $attackers  = $this->getPlayerGroup($fleet_row);
            }
            
            // defenders fleet sum
            $def = parent::$db->query(
                "SELECT * FROM `" . FLEETS . "` 
                WHERE `fleet_end_galaxy` = '" . $fleet_row['fleet_end_galaxy'] . "' AND 
                    `fleet_end_system` = '" . $fleet_row['fleet_end_system'] . "' AND 
                    `fleet_end_type` = '" . $fleet_row['fleet_end_type'] . "' AND 
                    `fleet_end_planet` = '" . $fleet_row['fleet_end_planet'] . "' AND
                    `fleet_start_time` < '" . time() . "' AND 
                    `fleet_end_stay` >= '" . time() . "';"
            );

            $defenders  = $this->getPlayerGroupFromQuery($def, $targetUser);

            //defenses sum
            $homeFleet  = new HomeFleet(0);

            for ($i = self::DEFENSE_MIN_ID; $i < self::DEFENSE_MAX_ID; $i++) {

                if (isset($this->resource[$i]) && isset($target_planet[$this->resource[$i]])) {

                    if ($target_planet[$this->resource[$i]] != 0) {

                        $homeFleet->addShipType($this->getShipType($i, $target_planet[$this->resource[$i]]));
                    }
                }
            }

            for ($i = self::SHIP_MIN_ID; $i < self::SHIP_MAX_ID; $i++) {

                if (isset($this->resource[$i]) && isset($target_planet[$this->resource[$i]])) {

                    if ($target_planet[$this->resource[$i]] != 0) {

                        $homeFleet->addShipType($this->getShipType($i, $target_planet[$this->resource[$i]]));
                    }
                }
            }

            if (!$defenders->existPlayer($target_userID)) {

                $player = new Player($target_userID, array($homeFleet));

                $player->setTech(
                    $targetUser['research_weapons_technology'],
                    $targetUser['research_shielding_technology'],
                    $targetUser['research_armour_technology']
                );

                $player->setName($targetUser['user_name']);
                
                $defenders->addPlayer($player);
            } else {

                $defenders->getPlayer($target_userID)->addDefense($homeFleet);
            }
            //-------------------------------------------------------------------------

            //------------------------------ battle -----------------------------------
            $battle         = new Battle($attackers, $defenders);
            $startBattle    = DebugManager::runDebugged(
                array($battle, 'startBattle'),
                $errorHandler,
                $exceptionHandler
            );
            
            $startBattle();
            //-------------------------------------------------------------------------

            //-------------------------- after battle stuff ---------------------------
            $report = $battle->getReport();
            $steal  = $this->updateAttackers(
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
            $this->updateMoon($fleet_row, $report, '', $target_userID, $target_planet);
            $this->sendMessage($fleet_row, $report);

        } elseif ($fleet_row['fleet_end_time'] <= time()) {
            
            $message    = sprintf(
                $this->langs['sys_fleet_won'],
                $target_planet['planet_name'],
                FleetsLib::targetLink($fleet_row, ''),
                FormatLib::prettyNumber($fleet_row['fleet_resource_metal']),
                $this->langs['Metal'],
                FormatLib::prettyNumber($fleet_row['fleet_resource_crystal']),
                $this->langs['Crystal'],
                FormatLib::prettyNumber($fleet_row['fleet_resource_deuterium']),
                $this->langs['Deuterium']
            );

            FunctionsLib::sendMessage(
                $fleet_row['fleet_owner'],
                '',
                $fleet_row['fleet_end_time'],
                1,
                $this->langs['sys_mess_tower'],
                $this->langs['sys_mess_fleetback'],
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
        $rf     = isset($this->combat_caps[$id]['sd']) ? $this->combat_caps[$id]['sd'] : 0;
        $shield = $this->combat_caps[$id]['shield'];
        $cost   = array($this->pricelist[$id]['metal'], $this->pricelist[$id]['crystal']);
        $power  = $this->combat_caps[$id]['attack'];

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
        list($metal, $crystal)  = $report->getDebris();

        parent::$db->query(
            "UPDATE " . PLANETS . " SET
                `planet_invisible_start_time` = '".time()."',
                `planet_debris_metal` = `planet_debris_metal` + '" . $metal . "',
                `planet_debris_crystal` = `planet_debris_crystal` + '" . $crystal . "'
            WHERE `planet_galaxy` = '" . $fleet_row['fleet_end_galaxy'] . "' AND
                `planet_system` = '" . $fleet_row['fleet_end_system'] . "' AND
                `planet_planet` = '" . $fleet_row['fleet_end_planet'] . "' AND
                `planet_type` = 1
            LIMIT 1;"
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
        $playerGroup        = new PlayerGroup();
        $serializedTypes    = explode(';', $fleet_row['fleet_array']);
        $idPlayer           = $fleet_row['fleet_owner'];
        $fleet              = new Fleet($fleet_row['fleet_id']);

        foreach ($serializedTypes as $serializedType) {

            if (!empty($serializedType)) {
                
                list($id, $count)   = explode(',', $serializedType);

                if ($id != 0 && $count != 0) {
                    $fleet->addShipType($this->getShipType($id, $count));
                }
            }
        }

        $player_info    = parent::$db->queryFetch(
            "SELECT u.user_name,
                r.research_weapons_technology,
                r.research_shielding_technology,
                r.research_armour_technology
            FROM " . USERS . " AS u
                INNER JOIN " . RESEARCH . " AS r ON r.research_user_id = u.user_id
            WHERE u.user_id = '" . $idPlayer . "';"
        );

        $player = new Player($idPlayer, array($fleet));
        $player->setTech(
            $player_info['research_weapons_technology'],
            $player_info['research_shielding_technology'],
            $player_info['research_armour_technology']
        );

        $player->setName($player_info['user_name']);
        
        $playerGroup->addPlayer($player);

        return $playerGroup;
    }

    /**
     * getPlayerGroupFromQuery
     *
     * @param array   $result      Result
     * @param boolean $target_user Target User
     *
     * @return \PlayerGroup
     */
    private function getPlayerGroupFromQuery($result, $target_user = false)
    {
        if ($result == null) {
            return;
        }
        
        $playerGroup    = new PlayerGroup();
        
        while ($fleet_row = parent::$db->fetchAssoc($result)) {

            //making the current fleet object
            $serializedTypes    = explode(';', $fleet_row['fleet_array']);
            $idPlayer           = $fleet_row['fleet_owner'];
            $fleet              = new Fleet($fleet_row['fleet_id']);

            foreach ($serializedTypes as $serializedType) {

                if (!empty($serializedType)) {
                    
                    list ( $id , $count ) = explode(',', $serializedType);

                    if ($id != 0 && $count != 0) {
                        $fleet->addShipType($this->getShipType($id, $count));
                    }
                }
            }

            //making the player object and add it to playerGroup object
            if (!$playerGroup->existPlayer($idPlayer)) {

                if ($target_user !== false && $target_user['user_id'] == $idPlayer) {

                    $player_info    = $target_user;
                } else {

                    $player_info    = parent::$db->queryFetch(
                        "SELECT u.user_name,
                            r.research_weapons_technology,
                            r.research_shielding_technology,
                            r.research_armour_technology
                        FROM " . USERS . " AS u
                            INNER JOIN " . RESEARCH . " AS r ON r.research_user_id = u.user_id
                        WHERE u.user_id = '" . $idPlayer . "';"
                    );
                }

                $player = new Player($idPlayer, array($fleet));

                $player->setTech(
                    $player_info['research_weapons_technology'],
                    $player_info['research_shielding_technology'],
                    $player_info['research_armour_technology']
                );

                $player->setName($player_info['user_name']);
                
                $playerGroup->addPlayer($player);
            } else {

                $playerGroup->getPlayer($idPlayer)->addFleet($fleet);
            }
        }

        return $playerGroup;
    }

    /**
     * updateMoon
     *
     * @param array  $fleet_row     Fleet Row
     * @param Report $report        Report
     * @param string $moonName      Moon Name
     * @param int    $target_userId Target User ID
     * @param array  $target_planet Target planet
     *
     * @return void
     */
    private function updateMoon($fleet_row, $report, $moonName, $target_userId, $target_planet)
    {
        $moon   = $report->tryMoon();

        if ($moon === false) {
            return;
        }

        $galaxy = $fleet_row['fleet_end_galaxy'];
        $system = $fleet_row['fleet_end_system'];
        $planet = $fleet_row['fleet_end_planet'];

        $moon_exists    = parent::$db->queryFetch(
            "SELECT `planet_id`
            FROM `" . PLANETS . "`
            WHERE `planet_galaxy` = '" . $galaxy . "'
                AND `planet_system` = '" . $system . "'
                AND `planet_planet` = '" . $planet . "'
                AND `planet_type` = '3';"
        );

        if ($moon_exists['planet_id'] != 0) {
            return;
        }

        // $size and $fields
        extract($moon);

        $_creator   = FunctionsLib::loadLibrary('PlanetLib');
        $_creator->setNewMoon($galaxy, $system, $planet, $target_userId, $moonName, '', $size);
    }

    /**
     * sendMessage
     *
     * @param array  $fleet_row Fleet Row
     * @param Report $report    Report
     *
     * @return void
     */
    private function sendMessage($fleet_row, $report)
    {
        $idAtts = $report->getAttackersId();
        $idDefs = $report->getDefendersId();
        $idAll  = array_merge($idAtts, $idDefs);
        $owners = implode(',', $idAll);
        $rid    = md5($report) . time();

        parent::$db->query(
            "INSERT INTO `" . REPORTS . "` SET
            `report_owners` = '" . $owners . "',
            `report_rid` = '" . $rid . "',
            `report_content` = '" . addslashes($report) . "',
            `report_time` = '" . time() . "'"
        );

        foreach ($idAtts as $id) {

            if ($report->attackerHasWin()) {

                $style  = 'green';
            } elseif ($report->isAdraw()) {

                $style  = 'orange';
            } else {

                $style  = 'red';
            }

            $raport = $this->buildReportLink(
                $style,
                $rid,
                $fleet_row['fleet_end_galaxy'],
                $fleet_row['fleet_end_system'],
                $fleet_row['fleet_end_planet']
            );

            FunctionsLib::sendMessage(
                $id,
                '',
                $fleet_row['fleet_start_time'],
                1,
                $this->langs['sys_mess_tower'],
                $raport,
                ''
            );
        }

        foreach ($idDefs as $id) {

            if ($report->attackerHasWin()) {

                $style  = 'red';
            } elseif ($report->isAdraw()) {

                $style  = 'orange';
            } else {

                $style  = 'green';
            }

            $raport = $this->buildReportLink(
                $style,
                $rid,
                $fleet_row['fleet_end_galaxy'],
                $fleet_row['fleet_end_system'],
                $fleet_row['fleet_end_planet']
            );
            
            FunctionsLib::sendMessage(
                $id,
                '',
                $fleet_row['fleet_start_time'],
                1,
                $this->langs['sys_mess_tower'],
                $raport,
                ''
            );
        }
    }

    /**
     * getCapacity
     *
     * @param PlayerGroup $players Players
     *
     * @return int
     */
    private function getCapacity(PlayerGroup $players)
    {
        $capacity   = 0;

        foreach ($players->getIterator() as $idPlayer => $player) {

            foreach ($player->getIterator() as $idFleet => $fleet) {

                foreach ($fleet->getIterator() as $idShipType => $shipType) {

                    $capacity   += $shipType->getCount() * $this->pricelist[$idShipType]['capacity'];
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
        $fleetArray     = '';
        $emptyFleets    = array();
        $capacity       = $this->getCapacity($playerGroupAfterBattle);
        $steal          = array(
            'metal'     => 0,
            'crystal'   => 0,
            'deuterium' => 0
        );

        foreach ($playerGroupBeforeBattle->getIterator() as $idPlayer => $player) {

            $existPlayer    = $playerGroupAfterBattle->existPlayer($idPlayer);
            $Xplayer        = null;

            if ($existPlayer) {

                $Xplayer = $playerGroupAfterBattle->getPlayer($idPlayer);
            }

            foreach ($player->getIterator() as $idFleet => $fleet) {

                $existFleet = $existPlayer && $Xplayer->existFleet($idFleet);
                $Xfleet     = null;

                if ($existFleet) {

                    $Xfleet         = $Xplayer->getFleet($idFleet);
                } else {

                    $emptyFleets[]  = $idFleet;
                }

                $fleetCapacity  = 0;
                $totalCount     = 0;
                $fleetArray     = '';

                foreach ($fleet as $idShipType => $fighters) {
                    
                    $existShipType  = $existFleet && $Xfleet->existShipType($idShipType);
                    $amount         = 0;

                    if ($existShipType) {

                        $XshipType      = $Xfleet->getShipType($idShipType);
                        $amount         = $XshipType->getCount();
                        $fleetCapacity  += $amount * $this->pricelist[$idShipType]['capacity'];
                        $totalCount     += $amount;
                        $fleetArray     .= "$idShipType,$amount;";
                    }
                }

                if ($existFleet) {

                    $fleetSteal = array(
                        'metal'     => 0,
                        'crystal'   => 0,
                        'deuterium' => 0
                    );

                    if ($playerGroupAfterBattle->battleResult == BATTLE_WIN) {

                        $corrispectiveMetal     = $target_planet['planet_metal'] * $fleetCapacity / $capacity;
                        $corrispectiveCrystal   = $target_planet['planet_crystal'] * $fleetCapacity / $capacity;
                        $corrispectiveDeuterium = $target_planet['planet_deuterium'] * $fleetCapacity / $capacity;

                        $fleetSteal = $this->plunder(
                            $fleetCapacity,
                            $corrispectiveMetal,
                            $corrispectiveCrystal,
                            $corrispectiveDeuterium
                        );

                        $steal['metal']         += $fleetSteal['metal'];
                        $steal['crystal']       += $fleetSteal['crystal'];
                        $steal['deuterium']     += $fleetSteal['deuterium'];
                    }

                    parent::$db->query(
                        "UPDATE `" . FLEETS . "` SET
                        `fleet_array` = '" . substr($fleetArray, 0, -1) . "',
                        `fleet_amount` = '" . $totalCount . "',
                        `fleet_mess` = '1',
                        `fleet_resource_metal` = `fleet_resource_metal` + '" . $fleetSteal['metal'] . "' ,
                        `fleet_resource_crystal` = `fleet_resource_crystal` + '" . $fleetSteal['crystal'] . "' ,
                        `fleet_resource_deuterium` = `fleet_resource_deuterium` + '" . $fleetSteal['deuterium'] . "'
                        WHERE `fleet_id` = '" . $idFleet . "';"
                    );
                }
            }
        }

        // updating flying fleets
        $id_string  = implode(',', $emptyFleets);

        if (!empty($id_string)) {

            parent::$db->query(
                "DELETE FROM `" . FLEETS . "`
                WHERE `fleet_id` IN (" . $id_string . ")"
            );
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
        $Xplayer        = $Xfleet = $XshipType = null;
        $fleetArray     = '';
        $emptyFleets    = array();

        foreach ($playerGroupBeforeBattle->getIterator() as $idPlayer => $player) {

            $existPlayer = $playerGroupAfterBattle->existPlayer($idPlayer);

            if ($existPlayer) {

                $Xplayer    = $playerGroupAfterBattle->getPlayer($idPlayer);
            }

            foreach ($player->getIterator() as $idFleet => $fleet) {

                $existFleet = $existPlayer && $Xplayer->existFleet($idFleet);

                if ($existFleet) {

                    $Xfleet         = $Xplayer->getFleet($idFleet);
                } else {

                    $emptyFleets[]  = $idFleet;
                }

                foreach ($fleet as $idShipType => $fighters) {
                    
                    $existShipType  = $existFleet && $Xfleet->existShipType($idShipType);
                    $amount         = 0;

                    if ($existShipType) {
                        
                        $XshipType  = $Xfleet->getShipType($idShipType);
                        $amount     = $XshipType->getCount();
                    }

                    $fleetArray .= '`' . $this->resource[$idShipType] . '` = ' . $amount . ', ';
                }
            }
        }

        // Updating defenses and ships on planet
        parent::$db->query(
            "UPDATE `" . PLANETS . "`, `" . SHIPS . "`, `" . DEFENSES . "`  SET
            " . $fleetArray. "
            `planet_metal` = `planet_metal` -  " . $steal['metal'] . ",
            `planet_crystal` = `planet_crystal` -  " . $steal['crystal'] . ",
            `planet_deuterium` = `planet_deuterium` -  " . $steal['deuterium'] . "
            WHERE `planet_id` = '" . $target_planet['planet_id'] . "' AND
                `ship_planet_id` = '" . $target_planet['planet_id'] . "'"
        );

        // Updating flying fleets
        $id_string  = implode(",", $emptyFleets);

        if (!empty($id_string)) {

            parent::$db->query(
                "DELETE FROM `" . FLEETS . "`
                WHERE `fleed_id` IN (" . $id_string . ")"
            );
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
        $steal  = array(
            'metal'     => 0,
            'crystal'   => 0,
            'deuterium' => 0
        );

        // Max resources that can be take
        $metal      /= 2;
        $crystal    /= 2;
        $deuterium  /= 2;

        // Fill up to 1/3 of cargo capacity with metal
        $stolen             = min($capacity / 3, $metal);
        $steal['metal']     += $stolen;
        $metal              -= $stolen;
        $capacity           -= $stolen;

        // Fill up to half remaining capacity with crystal
        $stolen             = min($capacity / 2, $crystal);
        $steal['crystal']   += $stolen;
        $crystal            -= $stolen;
        $capacity           -= $stolen;

        // The rest will be filled with deuterium
        $stolen             = min($capacity, $deuterium);
        $steal['deuterium'] += $stolen;
        $deuterium          -= $stolen;
        $capacity           -= $stolen;

        // If there is still capacity available fill half of it with metal
        $stolen             = min($capacity / 2, $metal);
        $steal['metal']     += $stolen;
        $metal              -= $stolen;
        $capacity           -= $stolen;

        // Now fill the rest with crystal
        $stolen             = min($capacity, $crystal);
        $steal['crystal']   += $stolen;
        $crystal            -= $stolen;
        $capacity           -= $stolen;

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
    private function buildReportLink($color, $rid, $g, $s, $p)
    {
        $style      = 'style="color:' . $color . ';"';
        $js         = "OnClick=\'f(\"game.php?page=combatreport&report=" . $rid . "\", \"\");\'";
        $content    = $this->langs['sys_mess_attack_report'] . ' ' . FormatLib::prettyCoords($g, $s, $p);
        
        return FunctionsLib::setUrl(
            '',
            '',
            $content,
            $style . ' ' . $js
        );
    }
}

/* end of attack.php */
