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
class BattleReport
{
    private $rounds;
    private $roundsCount;
    private $steal;
    private $attackersLostUnits;
    private $defendersLostUnits;

    public $css = '../../';

    public function __construct()
    {
        $this->rounds = array();
        $this->roundsCount = 0;
        $this->steal = 0;
    }

    /**
     * BattleReport::addRound()
     * Store a round
     * @param Round $round
     * @return void
     */
    public function addRound(Round $round)
    {
        if (ONLY_FIRST_AND_LAST_ROUND && $this->roundsCount == 2)
        {
            $this->rounds[1] = $round;
            return;
        }
        $this->rounds[$this->roundsCount++] = $round;
    }

    /**
     * BattleReport::getRound()
     * Retrive a round.
     * @param mixed $number: "START" to get the first round, "END" to get the last one, an integer(from zero) to get the corrispective round
     * @return Round
     */
    public function getRound($number)
    {
        if ($number === 'END')
        {
            return $this->rounds[$this->roundsCount - 1];
        }
        elseif ($number === 'START')
        {
            return $this->rounds[0];
        }
        elseif (intval($number) < 0 || intval($number) > $this->getLastRoundNumber())
        {
            throw new Exception('Invalid round number');
        }
        else
        {
            return $this->rounds[intval($number)];
        }
    }

    /**
     * BattleReport::getResultRound()
     * Alias of getRound(). Get the round after it was processed
     * @param int $number: the corrispective round number(from 0)
     * @return
     */
    private function getResultRound($number)
    {
        return $this->getRound($number);
    }

    /**
     * BattleReport::getPresentationRound()
     * Get the round before it was processed.
     * @param int $number: the corrispective round (from 1)
     * @return
     */
    private function getPresentationRound($number)
    {
        if ($number !== 'START' && $number !== 'END')
        {
            $number -= 1;
        }
        return $this->getRound($number);
    }

    /**
     * BattleReport::setBattleResult()
     * Set the result of a battle
     * @param int $att (BATTLE_WIN ,BATTLE_LOSE, BATTLE_DRAW)
     * @param int $def (BATTLE_WIN ,BATTLE_LOSE, BATTLE_DRAW)
     * @return void
     */
    public function setBattleResult($att, $def)
    {
        $this->getRound('END')->getAfterBattleAttackers()->battleResult = $att;
        $this->getRound('END')->getAfterBattleDefenders()->battleResult = $def;
    }


    /**
     * BattleReport::attackerHasWin()
     * Check if attackers won the battle
     * @return boolean
     */
    public function attackerHasWin()
    {
        return $this->getRound('END')->getAfterBattleAttackers()->battleResult === BATTLE_WIN;
    }


    /**
     * BattleReport::defenderHasWin()
     * Check if defenders won the battle
     * @return boolean
     */
    public function defenderHasWin()
    {
        return $this->getRound('END')->getAfterBattleDefenders()->battleResult === BATTLE_WIN;
    }


    /**
     * BattleReport::isAdraw()
     * Check if the battle ended with a draw
     * @return boolean
     */
    public function isAdraw()
    {
        return $this->getRound('END')->getAfterBattleAttackers()->battleResult === BATTLE_DRAW;
    }


    public function getPresentationAttackersFleetOnRound($number)
    {
        return $this->getPresentationRound($number)->getAfterBattleAttackers();
    }
    public function getPresentationDefendersFleetOnRound($number)
    {
        return $this->getPresentationRound($number)->getAfterBattleDefenders();
    }
    public function getResultAttackersFleetOnRound($number)
    {
        return $this->getResultRound($number)->getAfterBattleAttackers();
    }
    public function getResultDefendersFleetOnRound($number)
    {
        return $this->getResultRound($number)->getAfterBattleDefenders();
    }

    //-------------------  Lost units functions -------------------
    public function getTotalAttackersLostUnits()
    {
        return Math::recursive_sum($this->getAttackersLostUnits());
    }
    public function getTotalDefendersLostUnits()
    {
        return Math::recursive_sum($this->getDefendersLostUnits());
    }
    public function getAttackersLostUnits($repair = True)
    {
        $attackersBefore = $this->getRound('START')->getAfterBattleAttackers();
        $attackersAfter = $this->getRound('END')->getAfterBattleAttackers();
        return $this->getPlayersLostUnits($attackersBefore, $attackersAfter, $repair);
    }
    public function getDefendersLostUnits($repair = True)
    {
        $defendersBefore = $this->getRound('START')->getAfterBattleDefenders();
        $defendersAfter = $this->getRound('END')->getAfterBattleDefenders();
        return $this->getPlayersLostUnits($defendersBefore, $defendersAfter, $repair);
    }
    private function getPlayersLostUnits(PlayerGroup $playersBefore, PlayerGroup $playersAfter, $repair = True)
    {
        $lostShips = $this->getPlayersLostShips($playersBefore, $playersAfter);
        $defRepaired = $this->getPlayerRepaired($playersBefore, $playersAfter);
        $return = array();
        foreach ($lostShips->getIterator() as $idPlayer => $player)
        {
            foreach ($player->getIterator() as $idFleet => $fleet)
            {
                foreach ($fleet->getIterator() as $idShipType => $shipType)
                {
                    $cost = $shipType->getCost();
                    $repairedAmount = 0;
                    if ($repair && $defRepaired->existPlayer($idPlayer) && $defRepaired->getPlayer($idPlayer)->existFleet($idFleet) && $defRepaired->getPlayer($idPlayer)->getFleet($idFleet)->existShipType($idShipType))
                    {
                        $repairedAmount = $defRepaired->getPlayer($idPlayer)->getFleet($idFleet)->getShipType($idShipType)->getCount();
                    }
                    $count = $shipType->getCount() - $repairedAmount;
                    if ($count > 0)
                    {
                        $return[$idPlayer][$idFleet][get_class($shipType)][$idShipType] = array($cost[0] * $count, $cost[1] * $count);
                    }
                    elseif ($count < 0)
                    {
                        throw new Exception('Count negative');
                    }
                }
            }
        }
        return $return;
    }
    //--------------------------------------------------------------
    public function tryMoon()
    {
        $prob = $this->getMoonProb();
        return Math::tryEvent($prob, 'Events::event_moon', $prob);
    }
    public function getMoonProb()
    {
        return min(floor(array_sum($this->getDebris()) / MOON_UNIT_PROB), MAX_MOON_PROB);
    }
    public function getAttackerDebris()
    {
        $metal = 0;
        $crystal = 0;
        foreach ($this->getAttackersLostUnits(!REPAIRED_DO_DEBRIS) as $idPlayer => $player)
        {
            foreach ($player as $idFleet => $fleet)
            {
                foreach($fleet as $role => $values)
                {
                    foreach ($values as $idShipType => $lost)
                    {
                        $metal += $lost[0];
                        $crystal += $lost[1];      
                    }
                    $factor = constant(strtoupper($role).'_DEBRIS_FACTOR');
                    $metal *= $factor;
                    $crystal *= $factor;
                }
            }
        }
        return array($metal, $crystal);
    }
    public function getDefenderDebris()
    {
        $metal = 0;
        $crystal = 0;
        foreach ($this->getDefendersLostUnits(!REPAIRED_DO_DEBRIS) as $idPlayer => $player)
        {
            foreach ($player as $idFleet => $fleet)
            {
                foreach($fleet as $role => $values)
                {
                    foreach ($values as $idShipType => $lost)
                    {
                        $metal += $lost[0];
                        $crystal += $lost[1];      
                    }
                    $factor = constant(strtoupper($role).'_DEBRIS_FACTOR');
                    $metal *= $factor;
                    $crystal *= $factor;
                }
            }
        }
        return array($metal, $crystal);
    }
    public function getDebris()
    {
        $aDebris = $this->getAttackerDebris();
        $dDebris = $this->getDefenderDebris();
        return array($aDebris[0] + $dDebris[0], $aDebris[1] + $dDebris[1]);
    }
    
    public function getAttackersTech()
    {
        $techs = array();
        $players = $this->getRound('START')->getAfterBattleAttackers()->getIterator();
        foreach ($players->getIterator() as $id => $player)
        {
            $techs[$player->getId()] = array(
                $player->getWeaponsTech(),
                $player->getShieldsTech(),
                $player->getArmourTech());
        }
        return $techs;
    }
    public function getDefendersTech()
    {
        $techs = array();
        $players = $this->getRound('START')->getAfterBattleDefenders()->getIterator();
        foreach ($players->getIterator() as $id => $player)
        {
            $techs[$player->getId()] = array(
                $player->getWeaponsTech(),
                $player->getShieldsTech(),
                $player->getArmourTech());
        }
        return $techs;
    }
    public function getLastRoundNumber()
    {
        return $this->roundsCount - 1;
    }
    public function __toString()
    {
        ob_start();
        $css = $this->css;
        require (OPBEPATH . "views/report.html");
        return ob_get_clean();
    }
    public function getDefendersRepaired()
    {
        $defendersBefore = $this->getRound('START')->getAfterBattleDefenders();
        $defendersAfter = $this->getRound('END')->getAfterBattleDefenders();
        return $this->getPlayerRepaired($defendersBefore, $defendersAfter);
    }
    public function getAttackersRepaired()
    {
        $attackersBefore = $this->getRound('START')->getAfterBattleAttackers();
        $attackersAfter = $this->getRound('END')->getAfterBattleAttackers();
        return $this->getPlayerRepaired($attackersBefore, $attackersAfter);
    }
    public function getAfterBattleAttackers()
    {
        $players = $this->getResultAttackersFleetOnRound('END')->cloneMe();
        $playersRepaired = $this->getAttackersRepaired();
        return $this->getAfterBattlePlayerGroup($players, $playersRepaired);
    }
    public function getAfterBattleDefenders()
    {
        $players = $this->getResultDefendersFleetOnRound('END')->cloneMe();
        $playersRepaired = $this->getDefendersRepaired();
        return $this->getAfterBattlePlayerGroup($players, $playersRepaired);
    }
    private function getAfterBattlePlayerGroup($players, $playersRepaired)
    {
        foreach ($playersRepaired->getIterator() as $idPlayer => $playerRepaired)
        {
            if (!$players->existPlayer($idPlayer)) // player is completely destroyed
            {
                $players->addPlayer($playerRepaired);
                continue;
            }
            $endPlayer = $players->getPlayer($idPlayer);
            foreach ($playerRepaired->getIterator() as $idFleet => $fleetRepaired)
            {
                if (!$endPlayer->existFleet($idFleet))
                {
                    $endPlayer->addFleet($fleetRepaired);
                    continue;
                }
                $endFleet = $endPlayer->getFleet($idFleet);
                foreach ($fleetRepaired->getIterator() as $idShipType => $shipTypeRepaired)
                {
                    $endFleet->addShipType($shipTypeRepaired);
                }
            }
        }
        return $players;
    }
    private function getPlayerRepaired($playersBefore, $playersAfter)
    {
        $lostShips = $this->getPlayersLostShips($playersBefore, $playersAfter);
        foreach ($lostShips->getIterator() as $idPlayer => $player)
        {
            foreach ($player->getIterator() as $idFleet => $fleet)
            {
                foreach ($fleet->getIterator() as $idShipType => $shipType)
                {
                    $lostShips->decrement($idPlayer, $idFleet, $idShipType, round($shipType->getCount() * (1 - $shipType->getRepairProb())));
                }
            }
        }
        return $lostShips;
    }
    private function getPlayersLostShips(PlayerGroup $playersBefore, PlayerGroup $playersAfter)
    {
        $playersBefore_clone = $playersBefore->cloneMe();

        foreach ($playersAfter->getIterator() as $idPlayer => $playerAfter)
        {
            foreach ($playerAfter->getIterator() as $idFleet => $fleet)
            {
                foreach ($fleet->getIterator() as $idShipType => $shipType)
                {
                    $playersBefore_clone->decrement($idPlayer, $idFleet, $idShipType, $shipType->getCount());
                }
            }
        }
        return $playersBefore_clone;
    }
    public function getAttackersId()
    {
        $array=array();
        foreach($this->getPresentationAttackersFleetOnRound('START') as $id => $group)
        {
            $array[] = $id;
        }       
        return $array;
    }
    public function getDefendersId()
    {
        $array=array();
        foreach($this->getPresentationDefendersFleetOnRound('START') as $id => $group)
        {
            $array[] = $id;
        }
        return $array;
    }
    public function setSteal($array)
    {
        $this->steal = $array;
    }
    public function getSteal()
    {
        return $this->steal;
    }  
}