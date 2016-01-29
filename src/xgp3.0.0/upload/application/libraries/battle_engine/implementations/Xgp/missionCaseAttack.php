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
 * @copyright 2013 Jstar <frascafresca@gmail.com>
 * @license http://www.gnu.org/licenses/ GNU AGPLv3 License
 * @version beta(26-10-2013)
 * @link https://github.com/jstar88/opbe
 */
global $pricelist, $lang, $resource, $CombatCaps, $user;

define('SHIP_MIN_ID', 202);
define('SHIP_MAX_ID', 217);
define('DEFENSE_MIN_ID', 401);
define('DEFENSE_MAX_ID', 503);

if ($FleetRow['fleet_mess'] == 0 && $FleetRow['fleet_start_time'] <= time())
{
    //-------------------------- include OPBE ------------------------------- 
    $base = dirname(dirname(__dir__ )) . DIRECTORY_SEPARATOR;
    require ($base . 'utils' . DIRECTORY_SEPARATOR . 'includer.php');
    require ($base . 'implementations' . DIRECTORY_SEPARATOR . 'Xgp' . DIRECTORY_SEPARATOR . 'LangImplementation.php');
    //-----------------------------------------------------------------------

    //---------------------------- errors -----------------------------------
    //default handlers
    $errorHandler = array('DebugManager', 'myErrorHandler');
    $exceptionHandler = array('DebugManager', 'save');
    //then,hack them merging a new function to move back fleets.
    $myFunc = function ()
    {
        global $debug;
        $debug->error('Check "opbe/errors/" folder for more informations. '.date('d-m-y__H-i-s'), 'Battle error');
    }
    ;
    $errorHandler = DebugManager::intercept($errorHandler, $myFunc);
    $exceptionHandler = DebugManager::intercept($exceptionHandler, $myFunc);
    //-------------------------------------------------------------------------
    
    //----------------- turn back fleets also if errors happens ---------------
    if ($FleetRow['fleet_group'] > 0)
    {
        doquery("DELETE FROM {{table}} WHERE id =" . $FleetRow['fleet_group'], 'aks');
        doquery("UPDATE {{table}} SET fleet_mess=1 WHERE fleet_group=" . $FleetRow['fleet_group'], 'fleets');
    }
    else
    {
        doquery("UPDATE {{table}} SET fleet_mess=1 WHERE fleet_id=" . $FleetRow['fleet_id'], 'fleets');
    }
    //-------------------------------------------------------------------------
    
    //-------------------- retriving informations from DB ---------------------
    $targetPlanet = doquery("SELECT * FROM {{table}} WHERE `galaxy` = " . $FleetRow['fleet_end_galaxy'] . " AND `system` = " . $FleetRow['fleet_end_system'] . " AND `planet_type` = " . $FleetRow['fleet_end_type'] . " AND `planet` = " . $FleetRow['fleet_end_planet'] . ";", 'planets', true);
    $targetUser = doquery('SELECT * FROM {{table}} WHERE id=' . $targetPlanet['id_owner'], 'users', true);
    $TargetUserID = $targetUser['id'];
    //-------------------------------------------------------------------------

    PlanetResourceUpdate($targetUser, $targetPlanet, time());

    //----------------------- prepare players for battle ----------------------
    // attackers fleet sum
    $attackers = new PlayerGroup();
    if ($FleetRow['fleet_group'] != 0)
    {
        $fleets = doquery('SELECT * FROM {{table}} WHERE fleet_group=' . $FleetRow['fleet_group'], 'fleets');
        $attackers = getPlayerGroupFromQuery($fleets);
    }
    else
    {
        $attackers = getPlayerGroup($FleetRow);
    }
    //defenders fleet sum
    $def = doquery('SELECT * FROM {{table}} WHERE `fleet_end_galaxy` = ' . $FleetRow['fleet_end_galaxy'] . ' AND `fleet_end_system` = ' . $FleetRow['fleet_end_system'] . ' AND `fleet_end_type` = ' . $FleetRow['fleet_end_type'] . ' AND `fleet_end_planet` = ' . $FleetRow['fleet_end_planet'] . ' AND fleet_start_time<' . time() . ' AND fleet_end_stay>=' . time(), 'fleets');
    $defenders = getPlayerGroupFromQuery($def, true, $targetUser);
    //defenses sum
    $homeFleet = new HomeFleet(0);
    for ($i = DEFENSE_MIN_ID; $i < DEFENSE_MAX_ID; $i++)
    {
        if (isset($resource[$i]) && isset($targetPlanet[$resource[$i]]))
        {
            if ($targetPlanet[$resource[$i]] != 0)
            {
                $homeFleet->addShipType(getShipType($i, $targetPlanet[$resource[$i]]));
            }
        }
    }
    for ($i = SHIP_MIN_ID; $i < SHIP_MAX_ID; $i++)
    {
        if (isset($resource[$i]) && isset($targetPlanet[$resource[$i]]))
        {
            if ($targetPlanet[$resource[$i]] != 0)
            {
                $homeFleet->addShipType(getShipType($i, $targetPlanet[$resource[$i]]));
            }
        }
    }
    if (!$defenders->existPlayer($TargetUserID))
    {
        $player = new Player($TargetUserID, array($homeFleet));
        $player->setTech($targetUser['military_tech'], $targetUser['shield_tech'], $targetUser['defence_tech']);
        $player->setName($targetUser['name']);
        $defenders->addPlayer($player);
    }
    else
    {
        $defenders->getPlayer($TargetUserID)->addDefense($homeFleet);
    }
    //-------------------------------------------------------------------------
    
    //------------------------------ battle -----------------------------------
    $battle = new Battle($attackers, $defenders);
    $startBattle = DebugManager::runDebugged(array($battle, 'startBattle'), $errorHandler, $exceptionHandler);
    $startBattle();
    //-------------------------------------------------------------------------
    
    //-------------------------- after battle stuff ---------------------------
    $report = $battle->getReport();
    $steal = updateAttackers($report->getPresentationAttackersFleetOnRound('START'), $report->getAfterBattleAttackers(), $targetPlanet, $resource, $pricelist);
    $report->setSteal($steal);
    updateDefenders($report->getPresentationDefendersFleetOnRound('START'), $report->getAfterBattleDefenders(), $targetPlanet, $resource, $steal);
    updateDebris($FleetRow, $report);
    updateMoon($FleetRow, $report, '', $TargetUserID, $targetPlanet);
    sendMessage($FleetRow, $report, $lang, $resource);
    //-------------------------------------------------------------------------

}
elseif ($FleetRow['fleet_end_time'] <= time())
{
    $Message = sprintf($lang['sys_fleet_won'], $TargetName, GetTargetAdressLink($FleetRow, ''), Format::pretty_number($FleetRow['fleet_resource_metal']), $lang['Metal'], Format::pretty_number($FleetRow['fleet_resource_crystal']), $lang['Crystal'], Format::pretty_number($FleetRow['fleet_resource_deuterium']), $lang['Deuterium']);
    SendSimpleMessage($FleetRow['fleet_owner'], '', $FleetRow['fleet_end_time'], 3, $lang['sys_mess_tower'], $lang['sys_mess_fleetback'], $Message);
    $this->RestoreFleetToPlanet($FleetRow);
    doquery('DELETE FROM {{table}} WHERE `fleet_id`=' . intval($FleetRow['fleet_id']), 'fleets');
}

function getShipType($id, $count)
{
    global $CombatCaps, $pricelist;
    $rf = $CombatCaps[$id]['sd'];
    $shield = $CombatCaps[$id]['shield'];
    $cost = array($pricelist[$id]['metal'], $pricelist[$id]['crystal']);
    $power = $CombatCaps[$id]['attack'];
    if ($id >= SHIP_MIN_ID && $id <= SHIP_MAX_ID)
    {
        return new Ship($id, $count, $rf, $shield, $cost, $power);
    }
    return new Defense($id, $count, $rf, $shield, $cost, $power);
}
function updateDebris($FleetRow, $report)
{
    list($metal, $crystal) = $report->getDebris();
    $QryUpdateGalaxy = "UPDATE {{table}} SET ";
    $QryUpdateGalaxy .= "`invisible_start_time` = '" . time() . "', ";
    $QryUpdateGalaxy .= "`metal` = `metal` +'$metal', ";
    $QryUpdateGalaxy .= "`crystal` = `crystal` + '$crystal' ";
    $QryUpdateGalaxy .= "WHERE ";
    $QryUpdateGalaxy .= "`galaxy` = '" . $FleetRow['fleet_end_galaxy'] . "' AND ";
    $QryUpdateGalaxy .= "`system` = '" . $FleetRow['fleet_end_system'] . "' AND ";
    $QryUpdateGalaxy .= "`planet` = '" . $FleetRow['fleet_end_planet'] . "' ";
    $QryUpdateGalaxy .= "LIMIT 1;";
    doquery($QryUpdateGalaxy, 'galaxy');
}
function getPlayerGroup($fleetRow)
{
    $playerGroup = new PlayerGroup();
    $serializedTypes = explode(';', $fleetRow['fleet_array']);
    $idPlayer = $fleetRow['fleet_owner'];
    $fleet = new Fleet($fleetRow['fleet_id']);
    foreach ($serializedTypes as $serializedType)
    {
        list($id, $count) = explode(',', $serializedType);
        if ($id != 0 && $count != 0)
        {
            $fleet->addShipType(getShipType($id, $count));
        }
    }
    $player_info = doquery("SELECT * FROM {{table}} WHERE id =$idPlayer", 'users', true);
    $player = new Player($idPlayer, array($fleet));
    $player->setTech($player_info['military_tech'], $player_info['shield_tech'], $player_info['defence_tech']);
    $player->setName($player_info['name']);
    $playerGroup->addPlayer($player);
    return $playerGroup;
}
function getPlayerGroupFromQuery($result, $targetUser = false)
{
    $playerGroup = new PlayerGroup();
    while ($fleetRow = mysql_fetch_assoc($result))
    {
        //making the current fleet object
        $serializedTypes = explode(';', $fleetRow['fleet_array']);
        $idPlayer = $fleetRow['fleet_owner'];
        $fleet = new Fleet($fleetRow['fleet_id']);
        foreach ($serializedTypes as $serializedType)
        {
            list($id, $count) = explode(',', $serializedType);
            if ($id != 0 && $count != 0)
            {
                $fleet->addShipType(getShipType($id, $count));
            }
        }
        //making the player object and add it to playerGroup object
        if (!$playerGroup->existPlayer($idPlayer))
        {
            $player_info = ($targetUser !== false && $targetUser['id'] == $idPlayer) ? $targetUser : doquery("SELECT * FROM {{table}} WHERE id =$idPlayer", 'users', true);
            $player = new Player($idPlayer, array($fleet));
            $player->setTech($player_info['military_tech'], $player_info['shield_tech'], $player_info['defence_tech']);
            $player->setName($player_info['name']);
            $playerGroup->addPlayer($player);
        }
        else
        {
            $playerGroup->getPlayer($idPlayer)->addFleet($fleet);
        }
    }
    return $playerGroup;
}
function updateMoon($FleetRow, $report, $moonName, $targetUserId, $targetPlanet)
{
    $moon = $report->tryMoon();
    if ($moon === false)
        return;
    $galaxy = $FleetRow['fleet_end_galaxy'];
    $system = $FleetRow['fleet_end_system'];
    $planet = $FleetRow['fleet_end_planet'];

    $QryGetMoonGalaxyData = "SELECT id_luna FROM {{table}} ";
    $QryGetMoonGalaxyData .= "WHERE ";
    $QryGetMoonGalaxyData .= "`galaxy` = '$galaxy' AND ";
    $QryGetMoonGalaxyData .= "`system` = '$system' AND ";
    $QryGetMoonGalaxyData .= "`planet` = '$planet';";
    $MoonGalaxy = doquery($QryGetMoonGalaxyData, 'galaxy', true);
    if ($MoonGalaxy['id_luna'] != 0)
        return;
    extract($moon); //$size and $fields
    $maxtemp = $targetPlanet['temp_max'] - rand(0, MOON_MAX_HIGHT_TEMP_DIFFERENCE_FROM_PLANET);
    $mintemp = $targetPlanet['temp_min'] - rand(0, MOON_MAX_LOW_TEMP_DIFFERENCE_FROM_PLANET);
    $QryInsertMoonInPlanet = "INSERT INTO {{table}} SET ";
    $QryInsertMoonInPlanet .= "`name` = '" . (($MoonName == '') ? DEFAULT_MOON_NAME : $MoonName) . "', ";
    $QryInsertMoonInPlanet .= "`id_owner` = '$targetUserId', ";
    $QryInsertMoonInPlanet .= "`galaxy` = '$galaxy', ";
    $QryInsertMoonInPlanet .= "`system` = '$system', ";
    $QryInsertMoonInPlanet .= "`planet` = '$planet', ";
    $QryInsertMoonInPlanet .= "`last_update` = '" . time() . "', ";
    $QryInsertMoonInPlanet .= "`planet_type` = '3', ";
    $QryInsertMoonInPlanet .= "`image` = 'mond', ";
    $QryInsertMoonInPlanet .= "`diameter` = '$size', ";
    $QryInsertMoonInPlanet .= "`field_max` = '1', "; // should be $fields and current_field= 1,
    $QryInsertMoonInPlanet .= "`temp_min` = '$mintemp', ";
    $QryInsertMoonInPlanet .= "`temp_max` = '$maxtemp', ";
    $QryInsertMoonInPlanet .= "`metal` = '0', ";
    $QryInsertMoonInPlanet .= "`metal_perhour` = '0', ";
    $QryInsertMoonInPlanet .= "`metal_max` = '" . BASE_STORAGE_SIZE . "', ";
    $QryInsertMoonInPlanet .= "`crystal` = '0', ";
    $QryInsertMoonInPlanet .= "`crystal_perhour` = '0', ";
    $QryInsertMoonInPlanet .= "`crystal_max` = '" . BASE_STORAGE_SIZE . "', ";
    $QryInsertMoonInPlanet .= "`deuterium` = '0', ";
    $QryInsertMoonInPlanet .= "`deuterium_perhour` = '0', ";
    $QryInsertMoonInPlanet .= "`deuterium_max` = '" . BASE_STORAGE_SIZE . "';";
    doquery($QryInsertMoonInPlanet, 'planets');

    $QryGetMoonIdFromPlanet = "SELECT id FROM {{table}} ";
    $QryGetMoonIdFromPlanet .= "WHERE ";
    $QryGetMoonIdFromPlanet .= "`galaxy` = '$galaxy' AND ";
    $QryGetMoonIdFromPlanet .= "`system` = '$system' AND ";
    $QryGetMoonIdFromPlanet .= "`planet` = '$planet' AND ";
    $QryGetMoonIdFromPlanet .= "`planet_type` = '3';";
    $lunarow = doquery($QryGetMoonIdFromPlanet, 'planets', true);


    $QryUpdateMoonInGalaxy = "UPDATE {{table}} SET ";
    $QryUpdateMoonInGalaxy .= "`id_luna` = '" . $lunarow['id'] . "', ";
    $QryUpdateMoonInGalaxy .= "`luna` = '0' ";
    $QryUpdateMoonInGalaxy .= "WHERE ";
    $QryUpdateMoonInGalaxy .= "`galaxy` = '$galaxy' AND ";
    $QryUpdateMoonInGalaxy .= "`system` = '$system' AND ";
    $QryUpdateMoonInGalaxy .= "`planet` = '$planet';";
    doquery($QryUpdateMoonInGalaxy, 'galaxy');


}
function sendMessage($FleetRow, $report, $lang, $resource)
{

    $idAtts = $report->getAttackersId();
    $idDefs = $report->getDefendersId();
    $idAll = array_merge($idAtts, $idDefs);
    $owners = implode(',', $idAll);

    $rid = md5($report) . time();

    doquery('INSERT INTO {{table}} SET
				owners = \'' . ($FleetRow['fleet_owner'] . ',' . $FleetRow['fleet_target_owner']) . '\',
				rid = \'' . $rid . '\',
				raport = \'' . addslashes($report) . '\',
				a_zestrzelona = 0,
				time = \'' . time() . '\'', 'rw');

    foreach ($idAtts as $id)
    {
        if ($report->attackerHasWin())
        {
            $style = "green";
        }
        elseif ($report->isAdraw())
        {
            $style = "orange";
        }
        else
        {
            $style = "red";
        }
        $raport = "<a href=\"#\" style=\"color:" . $style . ";\" OnClick=\'f(\"CombatReport.php?raport=" . $rid . "\", \"\");\' >" . $lang['sys_mess_attack_report'] . " [" . $FleetRow['fleet_end_galaxy'] . ":" . $FleetRow['fleet_end_system'] . ":" . $FleetRow['fleet_end_planet'] . "]</a>";
        SendSimpleMessage($id, '', $FleetRow['fleet_start_time'], 3, $lang['sys_mess_tower'], $raport, '');
    }
    foreach ($idDefs as $id)
    {
        if ($report->attackerHasWin())
        {
            $style = "red";
        }
        elseif ($report->isAdraw())
        {
            $style = "orange";
        }
        else
        {
            $style = "green";
        }
        $raport = "<a href=\"#\" style=\"color:" . $style . ";\" OnClick=\'f(\"CombatReport.php?raport=" . $rid . "\", \"\");\' >" . $lang['sys_mess_attack_report'] . " [" . $FleetRow['fleet_end_galaxy'] . ":" . $FleetRow['fleet_end_system'] . ":" . $FleetRow['fleet_end_planet'] . "]</a>";
        SendSimpleMessage($id, '', $FleetRow['fleet_start_time'], 3, $lang['sys_mess_tower'], $raport, '');
    }

}
function getCapacity(PlayerGroup $players, $pricelist)
{
    $capacity = 0;
    foreach ($players->getIterator() as $idPlayer => $player)
    {
        foreach ($player->getIterator() as $idFleet => $fleet)
        {
            foreach ($fleet->getIterator() as $idShipType => $shipType)
            {
                $capacity += $shipType->getCount() * $pricelist[$idShipType]['capacity'];
            }
        }
    }
    return $capacity;
}
function updateAttackers($playerGroupBeforeBattle, $playerGroupAfterBattle, $targetPlanet, $resource, $pricelist)
{
    $fleetArray = '';
    $emptyFleets = array();
    $capacity = getCapacity($playerGroupAfterBattle, $pricelist);
    $steal = array(
        'metal' => 0,
        'crystal' => 0,
        'deuterium' => 0);
    foreach ($playerGroupBeforeBattle->getIterator() as $idPlayer => $player)
    {
        $existPlayer = $playerGroupAfterBattle->existPlayer($idPlayer);
        $Xplayer = null;
        if ($existPlayer)
        {
            $Xplayer = $playerGroupAfterBattle->getPlayer($idPlayer);
        }
        foreach ($player->getIterator() as $idFleet => $fleet)
        {
            $existFleet = $existPlayer && $Xplayer->existFleet($idFleet);
            $Xfleet = null;
            if ($existFleet)
            {
                $Xfleet = $Xplayer->getFleet($idFleet);
            }
            else
            {
                $emptyFleets[] = $idFleet;
            }
            $fleetCapacity = 0;
            $totalCount = 0;
            $fleetArray = '';
            foreach ($fleet as $idShipType => $fighters)
            {
                $existShipType = $existFleet && $Xfleet->existShipType($idShipType);
                $amount = 0;
                if ($existShipType)
                {
                    $XshipType = $Xfleet->getShipType($idShipType);
                    $amount = $XshipType->getCount();
                    $fleetCapacity += $amount * $pricelist[$idShipType]['capacity'];
                    $totalCount += $amount;
                    $fleetArray .= "$idShipType,$amount;";
                }
            }
            if ($existFleet)
            {
                $fleetSteal = array(
                    'metal' => 0,
                    'crystal' => 0,
                    'deuterium' => 0);
                if ($playerGroupAfterBattle->battleResult == BATTLE_WIN)
                {
                    $corrispectiveMetal = $targetPlanet['metal'] * $fleetCapacity / $capacity;
                    $corrispectiveCrystal = $targetPlanet['crystal'] * $fleetCapacity / $capacity;
                    $corrispectiveDeuterium = $targetPlanet['deuterium'] * $fleetCapacity / $capacity;
                    $fleetSteal = plunder($fleetCapacity, $corrispectiveMetal, $corrispectiveCrystal, $corrispectiveDeuterium);
                    $steal['metal'] += $fleetSteal['metal'];
                    $steal['crystal'] += $fleetSteal['crystal'];
                    $steal['deuterium'] += $fleetSteal['deuterium'];
                }
                $QryUpdateFleet = "UPDATE {{table}} SET ";
                $QryUpdateFleet .= "`fleet_array` = '" . substr($fleetArray, 0, -1) . "', ";
                $QryUpdateFleet .= "`fleet_amount` = $totalCount, ";
                $QryUpdateFleet .= "`fleet_mess` = 1, ";
                $QryUpdateFleet .= "`fleet_resource_metal` = `fleet_resource_metal` + '" . $fleetSteal['metal'] . "' , ";
                $QryUpdateFleet .= "`fleet_resource_crystal` = `fleet_resource_crystal` + '" . $fleetSteal['crystal'] . "' , ";
                $QryUpdateFleet .= "`fleet_resource_deuterium` = `fleet_resource_deuterium` + '" . $fleetSteal['deuterium'] . "' ";
                $QryUpdateFleet .= "WHERE ";
                $QryUpdateFleet .= "`fleet_id`= $idFleet ;";
                doquery($QryUpdateFleet, 'fleets');
            }
        }
    }
    //updating flying fleets
    $id_string = implode(",", $emptyFleets);
    if (!empty($id_string))
    {
        doquery("DELETE FROM {{table}} WHERE `fleet_id` in ($id_string)", 'fleets');
    }
    return $steal;
}
function updateDefenders($playerGroupBeforeBattle, $playerGroupAfterBattle, $targetPlanet, $resource, $steal)
{
    $Xplayer = $Xfleet = $XshipType = null;
    $fleetArray = '';
    $emptyFleets = array();
    foreach ($playerGroupBeforeBattle->getIterator() as $idPlayer => $player)
    {
        $existPlayer = $playerGroupAfterBattle->existPlayer($idPlayer);
        if ($existPlayer)
        {
            $Xplayer = $playerGroupAfterBattle->getPlayer($idPlayer);
        }
        foreach ($player->getIterator() as $idFleet => $fleet)
        {
            $existFleet = $existPlayer && $Xplayer->existFleet($idFleet);
            if ($existFleet)
            {
                $Xfleet = $Xplayer->getFleet($idFleet);
            }
            else
            {
                $emptyFleets[] = $idFleet;
            }
            foreach ($fleet as $idShipType => $fighters)
            {
                $existShipType = $existFleet && $Xfleet->existShipType($idShipType);
                $amount = 0;
                if ($existShipType)
                {
                    $XshipType = $Xfleet->getShipType($idShipType);
                    $amount = $XshipType->getCount();
                }
                $fleetArray .= '`' . $resource[$idShipType] . '`=' . $amount . ', ';
            }
        }
    }
    //updating defenses and ships on planet
    $QryUpdateTarget = "UPDATE {{table}} SET ";
    $QryUpdateTarget .= $fleetArray;
    $QryUpdateTarget .= "`metal` = `metal` - '" . $steal['metal'] . "', ";
    $QryUpdateTarget .= "`crystal` = `crystal` - '" . $steal['crystal'] . "', ";
    $QryUpdateTarget .= "`deuterium` = `deuterium` - '" . $steal['deuterium'] . "' ";
    $QryUpdateTarget .= "WHERE ";
    $QryUpdateTarget .= "`id` = '{$targetPlanet['id']}' ;";
    doquery($QryUpdateTarget, 'planets');
    //updating flying fleets
    $id_string = implode(",", $emptyFleets);
    if (!empty($id_string))
    {
        doquery("DELETE FROM {{table}} WHERE `fleet_id` in ($id_string)", 'fleets');
    }
}

/**
 * 1. Fill up to 1/3 of cargo capacity with metal
 * 2. Fill up to half remaining capacity with crystal
 * 3. The rest will be filled with deuterium
 * 4. If there is still capacity available fill half of it with metal
 * 5. Now fill the rest with crystal
 */
function plunder($capacity, $metal, $crystal, $deuterium)
{
    //stolen resources
    $steal = array(
        'metal' => 0,
        'crystal' => 0,
        'deuterium' => 0);
    //max resources that can be take
    $metal /= 2;
    $crystal /= 2;
    $deuterium /= 2;

    //Fill up to 1/3 of cargo capacity with metal
    $stolen = min($capacity / 3, $metal);
    $steal['metal'] += $stolen;
    $metal -= $stolen;
    $capacity -= $stolen;

    //Fill up to half remaining capacity with crystal
    $stolen = min($capacity / 2, $crystal);
    $steal['crystal'] += $stolen;
    $crystal -= $stolen;
    $capacity -= $stolen;

    //The rest will be filled with deuterium
    $stolen = min($capacity, $deuterium);
    $steal['deuterium'] += $stolen;
    $deuterium -= $stolen;
    $capacity -= $stolen;

    //If there is still capacity available fill half of it with metal
    $stolen = min($capacity / 2, $metal);
    $steal['metal'] += $stolen;
    $metal -= $stolen;
    $capacity -= $stolen;

    //Now fill the rest with crystal
    $stolen = min($capacity, $crystal);
    $steal['crystal'] += $stolen;
    $crystal -= $stolen;
    $capacity -= $stolen;

    return $steal;
}

?>
