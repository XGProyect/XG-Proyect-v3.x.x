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
class Player extends IterableUtil
{
    private $id;
    protected $array = array();

    private $weapons_tech = 0;
    private $shields_tech = 0;
    private $armour_tech = 0;
    private $name;

    public function __construct($id, $fleets = array(), $weapons_tech = null, $shields_tech = null, $armour_tech = null, $name = "")
    {
        $this->id = $id;
        $this->name = $name;
        $this->setTech($weapons_tech, $shields_tech, $armour_tech);
        foreach ($fleets as $fleet)
        {
            $this->addFleet($fleet);
        }
    }
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
        foreach ($this->array as $id => $fleet)
        {
            $fleet->setName($name);
        }
    }
    public function addFleet(Fleet $fleet)
    {
        $fleet = $fleet->cloneMe();
        $fleet->setTech($this->weapons_tech, $this->shields_tech, $this->armour_tech);
        $fleet->setName($this->name);
        $this->array[$fleet->getId()] = $fleet; //avoid collateral effects: when the object or array is an argument && it's saved in a structure
    }
    public function setTech($weapons = null, $shields = null, $armour = null)
    {
        foreach ($this->array as $id => $fleet)
        {
            $fleet->setTech($weapons, $shields, $armour);
        }
        if(is_numeric($weapons)) $this->weapons_tech = intval($weapons);
        if(is_numeric($shields)) $this->shields_tech = intval($shields);
        if(is_numeric($armour)) $this->armour_tech = intval($armour);
    }
    public function getId()
    {
        return $this->id;
    }
    public function decrement($idFleet, $idShipType, $count)
    {
        $this->array[$idFleet]->decrement($idShipType, $count);
        if ($this->array[$idFleet]->isEmpty())
        {
            unset($this->array[$idFleet]);
        }
    }
    public function getWeaponsTech()
    {
        return $this->weapons_tech;
    }
    public function getShieldsTech()
    {
        return $this->shields_tech;
    }
    public function getArmourTech()
    {
        return $this->armour_tech;
    }
    public function getOrderedItereator()
    {
        $this->order();
        return $this->array;
    }
    private function order()
    {
        if (!ksort($this->array))
        {
            throw new Exception('Unable to order fleets');
        }
    }
    public function getFleet($id)
    {
        return $this->array[$id];
    }
    public function existFleet($idFleet)
    {
        return isset($this->array[$idFleet]);       
    }
    public function isEmpty()
    {
        foreach ($this->array as $id => $fleet)
        {
            if (!$fleet->isEmpty())
            {
                return false;
            }
        }
        return true;
    }
    public function __toString()
    {
        ob_start();
        $_player = $this;
        $_st = "";
        require(OPBEPATH."views/player2.html");
        return ob_get_clean();
    }
    public function inflictDamage(FireManager $fire)
    {
        $physicShots = array();
        foreach ($this->array as $idFleet => $fleet)
        {
            echo "------- firing to fleet with ID = $idFleet -------- <br>";
            $ps = $fleet->inflictDamage($fire);
            $physicShots[$idFleet] = $ps;
        }
        return $physicShots;
    }
    public function cleanShips()
    {
        $shipsCleaners = array();
        foreach ($this->array as $idFleet => $fleet)
        {
            echo "------- cleanShips to fleet with ID = $idFleet -------- <br>";
            $sc = $fleet->cleanShips();
            $shipsCleaners[$this->getId()] = $sc;
            if ($fleet->isEmpty())
            {
                unset($this->array[$idFleet]);
            }
        }
        return $shipsCleaners;
    }
    public function repairShields()
    {
        foreach ($this->array as $idFleet => $fleet)
        {
            $fleet->repairShields();
        }
    }
    public function getEquivalentFleetContent()
    {
        $merged = new Fleet(-1);
        foreach ($this->array as $idFleet => $fleet)
        {
            $merged->mergeFleet($fleet);
        }
        return $merged;
    }
    public function addDefense(Fleet $fleetDefender) // da fare: controllare ordine 
    {
        $fleetDefender = $fleetDefender->cloneMe();
        $fleetDefender->setTech($this->weapons_tech, $this->shields_tech, $this->armour_tech);
        $this->order();
        $fl = current($this->array);
        if ($fl === false)
        {
            $this->array[$fleetDefender->getId()] = $fleetDefender;//avoid collateral effects: when the object or array is an argument && it's saved in a structure
        }
        else
        {
            $fl->mergeFleet($fleetDefender);
        }
    }
    public function mergePlayerFleets(Player $player)
    {
        foreach ($player->getIterator() as $idFleet => $fleet)
        {
            $this->array[$id] = $fleet->cloneMe();//avoid collateral effects: when the object or array is an argument && it's saved in a structure
        }
    }
    public function getTotalCount()
    {
        $amount = 0;
        foreach ($this->array as $idFleet => $fleet)
        {
            $amount += $fleet->getTotalCount();
        }
        return $amount;

    }
    public function cloneMe()
    {
        $fleets = array_values($this->array);
        return new Player($this->id, $fleets ,$this->weapons_tech, $this->shields_tech, $this->armour_tech, $this->name);
    }
}
