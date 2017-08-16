<?php

/**
 *  OPBE
 *  Copyright (C) 2013  Jstar
 *
 * This file is part of OPBE.
 * 
 * OPBE is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPBE is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with OPBE.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OPBE
 * @author Jstar <frascafresca@gmail.com>
 * @copyright 2015 Jstar <frascafresca@gmail.com>
 * @license http://www.gnu.org/licenses/ GNU AGPLv3 License
 * @version 17-03-2015
 * @link https://github.com/jstar88/opbe
 */
$path = dirname(dirname(dirname(__dir__ ))) . DIRECTORY_SEPARATOR;
require ($path . 'utils' . DIRECTORY_SEPARATOR . 'includer.php');
require ($path . 'implementations' . DIRECTORY_SEPARATOR . '2Moons' . DIRECTORY_SEPARATOR . '1_3_injectionMode' . DIRECTORY_SEPARATOR . 'LangImplementation.php');

define('ID_MIN_SHIPS', 100);
define('ID_MAX_SHIPS', 300);
define('HOME_FLEET', 0);
define('DEFENDERS_WON', 'r');
define('ATTACKERS_WON', 'a');
define('DRAW', 'w');
define('METAL_ID', 901);
define('CRYSTAL_ID', 902);


/**
 * calculateAttack()
 * Calculate the battle using OPBE.
 * 
 * OPBE ,to decrease memory usage, don't save both the initial and end state of fleets in a single round: only the end state is saved.
 * Then OPBE store the first round in BattleReport and don't start it, just to show the fleets before the battle.
 * Also,cause OPBE start the rounds without saving the initial state, the informations about how many shots were fired etc must be asked to the next round.
 * Logically, the last round can't ask the next round because there is not.
 * 
 * @param array &$attackers
 * @param array &$defenders
 * @param mixed $FleetTF
 * @param mixed $DefTF
 * @return array
 */
function calculateAttack(&$attackers, &$defenders, $FleetTF, $DefTF)
{
    //null == use default handlers
    $errorHandler = null;
    $exceptionHandler = null;

    $CombatCaps = $GLOBALS['CombatCaps'];
    $pricelist = $GLOBALS['pricelist'];
    $resource = $GLOBALS['resource'];
    $ExtraDM = $GLOBALS['ExtraDM'];
    $OfficerInfo = $GLOBALS['OfficerInfo'];

    /********** BUILDINGS MODELS **********/
    /** Note: we are transform array of data like
     *  fleetID => infos
     *  into object tree structure like
     *  playerGroup -> player -> fleet -> shipType
     */

    //attackers
    $attackerGroupObj = new PlayerGroup();
    foreach ($attackers as $fleetID => $attacker)
    {
        $player = $attacker['user'];
        $player['factor'] = getFactors($player, $resource, $ExtraDM, $OfficerInfo);
        //techs + bonus. Note that the bonus is divided by the factor because the result sum will be multiplied by the same inside OPBE
        list($attTech, $defenceTech, $shieldTech) = getTechsFromArray($player);
        //--
        $attackerPlayerObj = $attackerGroupObj->createPlayerIfNotExist($player['id'], array(), $attTech, $shieldTech, $defenceTech);
        $attackerFleetObj = new Fleet($fleetID);
        foreach ($attacker['detail'] as $element => $amount)
        {
            if (empty($amount))
                continue;
            $shipType = getShipType($element, $amount);
            $attackerFleetObj->addShipType($shipType);
        }
        $attackerPlayerObj->addFleet($attackerFleetObj);
    }
    //defenders
    $defenderGroupObj = new PlayerGroup();
    foreach ($defenders as $fleetID => $defender)
    {
        $player = $defender['user'];
        $player['factor'] = getFactors($player, $resource, $ExtraDM, $OfficerInfo);
        //techs + bonus. Note that the bonus is divided by the factor because the result sum will be multiplied by the same inside OPBE
        list($attTech, $defenceTech, $shieldTech) = getTechsFromArray($player);
        //--
        $defenderPlayerObj = $defenderGroupObj->createPlayerIfNotExist($player['id'], array(), $attTech, $shieldTech, $defenceTech);
        $defenderFleetObj = getFleet($fleetID);
        foreach ($defender['def'] as $element => $amount)
        {
            if (empty($amount))
                continue;
            $shipType = getShipType($element, $amount);
            $defenderFleetObj->addShipType($shipType);
        }
        $defenderPlayerObj->addFleet($defenderFleetObj);
    }

    /********** BATTLE ELABORATION **********/
    $opbe = new Battle($attackerGroupObj, $defenderGroupObj);
    $startBattle = DebugManager::runDebugged(array($opbe, 'startBattle'), $errorHandler, $exceptionHandler);
    $startBattle();
    $report = $opbe->getReport();

    /********** WHO WON **********/
    if ($report->defenderHasWin())
    {
        $won = DEFENDERS_WON;
    }
    elseif ($report->attackerHasWin())
    {
        $won = ATTACKERS_WON;
    }
    elseif ($report->isAdraw())
    {
        $won = DRAW;
    }
    else
    {
        throw new Exception('problem');
    }

    /********** ROUNDS INFOS **********/

    $ROUND = array();
    $lastRound = $report->getLastRoundNumber();
    for ($i = 0; $i <= $lastRound; $i++)
    {
        // in case of last round, ask for rebuilt defenses. to change rebuils prob see constants/battle_constants.php
        $attackerGroupObj = ($lastRound == $i) ? $report->getAfterBattleAttackers() : $report->getResultAttackersFleetOnRound($i);
        $defenderGroupObj = ($lastRound == $i) ? $report->getAfterBattleDefenders() : $report->getResultDefendersFleetOnRound($i);
        $attInfo = updatePlayers($attackerGroupObj, $attackers, "detail");
        $defInfo = updatePlayers($defenderGroupObj, $defenders, "def");
        $ROUND[$i] = roundInfo($report, $attackers, $defenders, $attackerGroupObj, $defenderGroupObj, $i + 1, $attInfo, $defInfo);
    }

    /********** DEBRIS **********/
    //attackers
    $debAtt = $report->getAttackerDebris();
    $debAttMet = $debAtt[0];
    $debAttCry = $debAtt[1];
    //defenders
    $debDef = $report->getDefenderDebris();
    $debDefMet = $debDef[0];
    $debDefCry = $debDef[1];
    //total
    $debris = array('att' => array($debAttMet, $debAttCry), 'def' => array($debDefMet, $debDefCry));

    /********** LOST UNITS **********/
    $totalLost = array('att' => $report->getTotalAttackersLostUnits(), 'def' => $report->getTotalDefendersLostUnits());

    /********** RETURNS **********/
    return array(
        'won' => $won,
        'debree' => $debris,
        'rw' => $ROUND,
        'lost' => $totalLost);
}


/**
 * roundInfo()
 * Return the info required to fill $ROUND
 * @param BattleReport $report
 * @param array $attackers
 * @param array $defenders
 * @param PlayerGroup $attackerGroupObj
 * @param PlayerGroup $defenderGroupObj
 * @param int $i
 * @return array
 */
function roundInfo(BattleReport $report, $attackers, $defenders, PlayerGroup $attackerGroupObj, PlayerGroup $defenderGroupObj, $i, $attInfo, $defInfo)
{
    $round = null;
    // the last round doesn't has next round, so we not ask for fire etc
    if ($i <= $report->getLastRoundNumber())
    {
        $round = $report->getRound($i);
    }
    return array(
        'attack' => ($i > $report->getLastRoundNumber()) ? 0 : $round->getAttackersFirePower(),
        'defense' => ($i > $report->getLastRoundNumber()) ? 0 : $round->getDefendersFirePower(),
        'defShield' => ($i > $report->getLastRoundNumber()) ? 0 : $round->getDefendersAssorbedDamage(),
        'attackShield' => ($i > $report->getLastRoundNumber()) ? 0 : $round->getAttachersAssorbedDamage(),
        'attackAmount' => ($i > $report->getLastRoundNumber()) ? 0 : $round->getAttackersFireCount(),
        'defendAmount' => ($i > $report->getLastRoundNumber()) ? 0 : $round->getDefendersFireCount(),
        'attackers' => $attackers,
        'defenders' => $defenders,
        'attackA' => $attInfo[1],
        'defenseA' => $defInfo[1],
        'infoA' => $attInfo[0],
        'infoD' => $defInfo[0]);
}


/**
 * updatePlayers()
 * Update players array as default 2moons require.
 * OPBE keep the internal array data full to decrease memory size, so a PlayerGroup object don't have data about 
 * empty users(an user is empty when fleets are empty and fleet is empty when the ships count is zero)
 * Instead, the old system require to have also array of zero: to update the array of users, after a round, we must iterate them
 * and check the corrispective OPBE value if empty.  
 * 
 * @param PlayerGroup $playerGroup
 * @param array &$players
 * @return null
 */
function updatePlayers(PlayerGroup $playerGroup, &$players, $index)
{
    $plyArray = array();
    $amountArray = array();
    foreach ($players as $idFleet => $info)
    {
        $players[$idFleet]['techs'] = getTechsFromArrayForReport($info['user']);
        foreach ($info[$index] as $idShipType => $amount)
        {
            if ($playerGroup->existPlayer($info['user']['id']))
            {
                $player = $playerGroup->getPlayer($info['user']['id']);
                if ($player->existFleet($idFleet)) //if after battle still there are some ship types in this fleet
                {
                    $fleet = $player->getFleet($idFleet);
                    if ($fleet->existShipType($idShipType)) //if there are some ships of this type
                    {
                        $shipType = $fleet->getShipType($idShipType);
                        //used to show life,power and shield of each ships in the report
                        $plyArray[$idFleet][$idShipType] = array(
                            'def' => $shipType->getCurrentLife(),
                            'shield' => $shipType->getCurrentShield(),
                            'att' => $shipType->getCurrentPower());
                        $players[$idFleet][$index][$idShipType] = $shipType->getCount();
                    }
                    else //all ships of this type were destroyed
                    {
                        $players[$idFleet][$index][$idShipType] = 0;
                    }
                }
                else //the fleet is empty, so all ships of this type were destroyed
                {
                    $players[$idFleet][$index][$idShipType] = 0;
                }
            }
            else // is empty
            {
                $players[$idFleet][$index][$idShipType] = 0;
            }

            //initialization
            if (!isset($amountArray[$idFleet]))
            {
                $amountArray[$idFleet] = 0;
            }
            if (!isset($amountArray['total']))
            {
                $amountArray['total'] = 0;
            }
            //increment
            $currentAmount = $players[$idFleet][$index][$idShipType];
            $amountArray[$idFleet] = $amountArray[$idFleet] + $currentAmount;
            $amountArray['total'] = $amountArray['total'] + $currentAmount;
        }
    }
    return array($plyArray, $amountArray);
}


/**
 * getShipType()
 * Choose the correct class type by ID
 * 
 * @param int $id
 * @param int $count
 * @return a Ship or Defense instance
 */
function getShipType($id, $count)
{
    $CombatCaps = $GLOBALS['CombatCaps'];
    $pricelist = $GLOBALS['pricelist'];
    $rf = isset($CombatCaps[$id]['sd']) ? $CombatCaps[$id]['sd'] : 0;
    $shield = $CombatCaps[$id]['shield'];
    $cost = array($pricelist[$id]['metal'], $pricelist[$id]['crystal']);
    $power = $CombatCaps[$id]['attack'];
    if ($id > ID_MIN_SHIPS && $id < ID_MAX_SHIPS)
    {
        return new Ship($id, $count, $rf, $shield, $cost, $power);
    }
    return new Defense($id, $count, $rf, $shield, $cost, $power);
}


/**
 * getFleet()
 * Choose the correct class type by ID
 * 
 * @param int $id
 * @return a Fleet or HomeFleet instance
 */
function getFleet($id)
{
    if ($id == HOME_FLEET)
    {
        return new HomeFleet(HOME_FLEET);
    }
    return new Fleet($id);
}

function getFactors($attacker, $resource, $ExtraDM, $OfficerInfo)
{
    $attTech =    $OfficerInfo[602]['info'] * $attacker['rpg_amiral'] + (TIMESTAMP - $attacker[$resource[700]] <= 0) ? $ExtraDM[700]['add'] : 0; //attaque
    $defTech =    $OfficerInfo[602]['info'] * $attacker['rpg_amiral'] + (TIMESTAMP - $attacker[$resource[701]] <= 0) ? $ExtraDM[701]['add'] : 0; //bouclier
    $shieldTech = $OfficerInfo[602]['info'] * $attacker['rpg_amiral'] + (TIMESTAMP - $attacker[$resource[707]] <= 0) ? $ExtraDM[707]['add'] : 0;
    return array(
        "attack" => $attTech,
        "shield" => $shieldTech,
        "defensive" => $defTech);
}

function getTechsFromArray($player)
{
    $attTech = $player['military_tech'] + $player['factor']['attack'] / WEAPONS_TECH_INCREMENT_FACTOR;
    $shieldTech = $player['shield_tech'] + $player['factor']['shield'] / SHIELDS_TECH_INCREMENT_FACTOR;
    $defenceTech = $player['defence_tech'] + $player['factor']['defensive'] / ARMOUR_TECH_INCREMENT_FACTOR;
    return array(
        $attTech,
        $defenceTech,
        $shieldTech);
}

function getTechsFromArrayForReport($player)
{
    list($attTech, $defenceTech, $shieldTech) = getTechsFromArray($player);
    $attTech = 1 + $attTech * WEAPONS_TECH_INCREMENT_FACTOR;
    $defenceTech = 1 + $defenceTech * ARMOUR_TECH_INCREMENT_FACTOR;
    $shieldTech = 1 + $shieldTech * SHIELDS_TECH_INCREMENT_FACTOR;

    return array(
        $attTech,
        $defenceTech,
        $shieldTech);
}

?>
