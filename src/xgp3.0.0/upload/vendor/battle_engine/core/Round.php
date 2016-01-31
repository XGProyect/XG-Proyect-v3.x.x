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
/**
 * Round
 * 
 * This class rappresent the round in a battle.
 * When it is started, the PlayerGroup objects inside it will be updated.
 * Then, this class offers some methods to retrive informations about the round and the updated players.
 */
class Round
{
    private $attackers; // PlayerGroup attackers , will be updated when round start
    private $defenders; // PlayerGroup defenders, will be updated when round start

    private $fire_a; // a fire manager that rappresent all fires from attackers to defenders
    private $fire_d; // a fire manager that rappresent all fires from defenders to attackers

    private $physicShotsToDefenders;
    private $physicShotsToAttachers;

    private $attacherShipsCleaner;
    private $defenderShipsCleaner;

    private $number; // this round number

    /**
     * Round::__construct()
     * Construct a new Round object. No side effects.
     * @param PlayerGroup: the attackers
     * @param PlayerGroup: the defenders
     * @param int: the round number 
     * @return void
     */
    public function __construct(PlayerGroup $attackers, PlayerGroup $defenders, $number)
    {
        $this->number = $number;
        $this->fire_a = new FireManager();
        $this->fire_d = new FireManager();

        $this->attackers = $attackers->cloneMe();
        $this->defenders = $defenders->cloneMe();
    }
    
    /**
     * Round::startRound()
     * Start the current round and update the players instance inside this object.
     * @return
     */
    public function startRound()
    {
        echo '--- Round ' . $this->number . ' ---<br><br>';
        //---------------------- Generating the fire -------------------------------//
        //note that we don't need to check the order of fire, because we will order when splitting the fire later
        
        // here we add to fire manager each fire shotted from an attacker's ShipType to all defenders 
        $defendersMerged = $this->defenders->getEquivalentFleetContent();
        foreach ($this->attackers->getIterator() as $idPlayer => $player)
        {
            foreach ($player->getIterator() as $idFleet => $fleet)
            {
                foreach ($fleet->getIterator() as $idShipType => $shipType)
                {
                    $this->fire_a->add(new Fire($shipType, $defendersMerged));
                }
            }
        }       
        // here we add to fire manager each fire shotted from an defender's ShipType to all attackers
        $attackersMerged = $this->attackers->getEquivalentFleetContent();
        foreach ($this->defenders->getIterator() as $idPlayer => $player)
        {
            foreach ($player->getIterator() as $idFleet => $fleet)
            {
                foreach ($fleet->getIterator() as $idShipType => $shipType)
                {
                    $this->fire_d->add(new Fire($shipType, $attackersMerged));
                }
            }
        }        
        //--------------------------------------------------------------------------//

        //------------------------- Sending the fire -------------------------------//
        echo "***** firing to defenders *****<br>";
        $this->physicShotsToDefenders = $this->defenders->inflictDamage($this->fire_a);
        echo "***** firing to attackers *****<br>";
        $this->physicShotsToAttachers = $this->attackers->inflictDamage($this->fire_d);
        //--------------------------------------------------------------------------//

        //------------------------- Cleaning ships ---------------------------------//
        $this->defenderShipsCleaner = $this->defenders->cleanShips();
        $this->attacherShipsCleaner = $this->attackers->cleanShips();
        //--------------------------------------------------------------------------//
        
        //------------------------- Repairing shields ------------------------------//
        $this->defenders->repairShields();
        $this->attackers->repairShields();
        //--------------------------------------------------------------------------//
    }
    
    /**
     * Round::getAttackersFire()
     * Return the FireManager of the attacker
     * @return FireManager: attacker
     */
    public function getAttackersFire()
    {
        return $this->fire_a;
    }
    
    /**
     * Round::getDefendersFire()
     * Return the FireManager of the defender
     * @return FireManager: defender
     */
    public function getDefendersFire()
    {
        return $this->fire_d;
    }
    
    /**
     * Round::getAttachersPhysicShots()
     * Return an array of attacker PhysicShots (multidimensional)
     * @return array
     */
    public function getAttachersPhysicShots()
    {
        return $this->physicShotsToDefenders;
    }
    
    /**
     * Round::getDefendersPhysicShots()
     * Return an array of defender PhysicShots (multidimensional)
     * @return array
     */
    public function getDefendersPhysicShots()
    {
        return $this->physicShotsToAttachers;
    }
    
    /**
     * Round::getAttachersShipsCleaner()
     * Return an array of attacker ShipsCleaner (multidimensional)
     * @return array
     */
    public function getAttachersShipsCleaner()
    {
        return $this->attacherShipsCleaner;
    }
    
    /**
     * Round::getDefendersShipsCleaner()
     * Return an array of defender ShipsCleaner (multidimensional)
     * @return array
     */
    public function getDefendersShipsCleaner()
    {
        return $this->defenderShipsCleaner;
    }
    
    /**
     * Round::getAfterBattleAttackers()
     * Return the attackers after the round.
     * @return PlayerGroup: attackers
     */
    public function getAfterBattleAttackers()
    {
        return $this->attackers;
    }
    
    /**
     * Round::getAfterBattleDefenders()
     * Return the defenders after the round.
     * @return PlayerGroup: defenders
     */
    public function getAfterBattleDefenders()
    {
        return $this->defenders;
    }
    
    /**
     * Round::__toString()
     * An html rappresentation of this object
     * @return string
     */
    public function __toString()
    {
        ob_start();
        $_round = $this;
        $_i = $this->number;
        require(OPBEPATH."views/round.html");
        return ob_get_clean();
    }
    
    /**
     * Round::getNumber()
     * Return this round number
     * @return int: number
     */
    public function getNumber()
    {
        return $this->number;
    }
    
    public function getAttackersFirePower()
    {
        return $this->getAttackersFire()->getAttackerTotalFire();
    }
    public function getAttackersFireCount()
    {
        return $this->getAttackersFire()->getAttackerTotalShots();
    }
    public function getDefendersFirePower()
    {
        return $this->getDefendersFire()->getAttackerTotalFire();
    }
    public function getDefendersFireCount()
    {
        return $this->getDefendersFire()->getAttackerTotalShots();
    }
    public function getAttachersAssorbedDamage()
    {
        $playerGroupPS = $this->getDefendersPhysicShots();
        return $this->getPlayersAssorbedDamage($playerGroupPS);
    }
    public function getDefendersAssorbedDamage()
    {
        $playerGroupPS = $this->getAttachersPhysicShots();
        return $this->getPlayersAssorbedDamage($playerGroupPS);
    }
    private function getPlayersAssorbedDamage($playerGroupPS)
    {
        $ass = 0;
        foreach ($playerGroupPS as $idPlayer => $playerPs)
        {
            foreach ($playerPs as $idFleet => $fleetPS)
            {
                foreach ($fleetPS as $idTypeD => $typeDPS)
                {
                    foreach ($typeDPS as $typeAPS)
                    {
                        $ass += $typeAPS->getAssorbedDamage();
                    }
                }
            }
        }
        return $ass;
    }
}
