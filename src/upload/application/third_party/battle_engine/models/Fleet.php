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
 * @version 23-3-2015)
 * @link https://github.com/jstar88/opbe
 */
class Fleet extends IterableUtil
{
    protected $array = array();
    private $count;
    private $id;
    // added but only used in report templates
    private $weapons_tech = 0;
    private $shields_tech = 0;
    private $armour_tech = 0;
    private $name;
    public function __construct($id, $shipTypes = array(),$weapons_tech = null, $shields_tech = null, $armour_tech = null, $name = "")
    {
        $this->id = $id;
        $this->count = 0;
        $this->name = $name;
        if($this->id != -1)
        {
            $this->setTech($weapons_tech, $shields_tech, $armour_tech);
        }
        foreach ($shipTypes as $shipType)
        {
            $this->addShipType($shipType);
        }
    }
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;    
    }
    public function getId()
    {
        return $this->id;
    }
    public function setTech($weapons = null, $shields = null, $armour = null)
    {
        foreach ($this->array as $id => $shipType)
        {
            $shipType->setWeaponsTech($weapons);
            $shipType->setShieldsTech($shields);
            $shipType->setArmourTech($armour);
        }
        if(is_numeric($weapons)) $this->weapons_tech = intval($weapons);
        if(is_numeric($shields)) $this->shields_tech = intval($shields);
        if(is_numeric($armour)) $this->armour_tech = intval($armour);
    }
    public function addShipType(ShipType $shipType)
    {
        if (isset($this->array[$shipType->getId()]))
        {
            $this->array[$shipType->getId()]->increment($shipType->getCount());
        }
        else
        {
            $shipType = $shipType->cloneMe();//avoid collateral effects
            if($this->id != -1)
            {
                $shipType->setWeaponsTech($this->weapons_tech);
                $shipType->setShieldsTech($this->shields_tech);
                $shipType->setArmourTech($this->armour_tech);
            }
            $this->array[$shipType->getId()] = $shipType; 
        }
        $this->count += $shipType->getCount();
    }
    public function decrement($id, $count)
    {
        $this->array[$id]->decrement($count);
        $this->count -= $count;
        if ($this->array[$id]->getCount() <= 0)
        {
            unset($this->array[$id]);
        }
    }
    public function mergeFleet(Fleet $other)
    {
        foreach ($other->getIterator() as $idShipType => $shipType)
        {
            $this->addShipType($shipType);
        }
    }
    public function getShipType($id)
    {
        return $this->array[$id];
    }
    public function existShipType($id)
    {
        return isset($this->array[$id]);   
    }
    public function getTypeCount($type)
    {
        return $this->array[$type]->getCount();
    }
    public function getTotalCount()
    {
        return $this->count;
    }
    public function __toString()
    {
        ob_start();
        $_fleet = $this;
        $_st = "";
        require(OPBEPATH."views/fleet.html");
        return ob_get_clean();
    }
    public function inflictDamage(FireManager $fires)
    {
        $physicShots = array();
        //doesn't matter who shot first, but who receive first the damage
        foreach ($fires->getIterator() as $fire)
        {
            $tmp = array();
            foreach ($this->getOrderedIterator() as $idShipTypeDefender => $shipTypeDefender)
            {
                $idShipTypeAttacker = $fire->getId();
                log_comment( "---- firing from $idShipTypeAttacker to $idShipTypeDefender ----");
                $xs = $fire->getShotsFiredByAllToDefenderType($shipTypeDefender, true);
                $ps = $shipTypeDefender->inflictDamage($fire->getPower(), $xs->result);
                log_var('$xs',$xs);
                $tmp[$idShipTypeDefender] = $xs->rest;
                if ($ps != null)
                    $physicShots[$idShipTypeDefender][] = $ps;
            }
            log_var('$tmp',$tmp);
            // assign the last shot to the more likely shitType
            $m = 0;
            $f = 0;
            foreach ($tmp as $k => $v)
            {
                if ($v > $m)
                {
                    $m = $v;
                    $f = $k;
                }    
            }
            if ($f != 0)
            {
                log_comment('adding 1 shot');
                $ps = $this->getShipType($f)->inflictDamage($fire->getPower(), 1);
                $physicShots[$f][] = $ps;
            }

        }
        return $physicShots;
    }
    public function getOrderedIterator()
    {
        if (!ksort($this->array))
        {
            throw new Exception('Unable to order types');
        }
        return $this->array;
    }


    public function cleanShips()
    {
        $shipsCleaners = array();
        foreach ($this->array as $id => $shipType)
        {
            log_comment("---- exploding $id ----");
            $sc = $shipType->cleanShips();
            $this->count -= $sc->getExplodedShips();
            if ($shipType->isEmpty())
            {
                unset($this->array[$id]);
            }
            $shipsCleaners[$shipType->getId()] = $sc;
        }
        return $shipsCleaners;
    }
    public function repairShields()
    {
        foreach ($this->array as $id => $shipTypeDefender)
        {
            $shipTypeDefender->repairShields();
        }
    }
    public function isEmpty()
    {
        foreach ($this->array as $id => $shipType)
        {
            if (!$shipType->isEmpty())
            {
                return false;
            }
        }
        return true;
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
    public function cloneMe()
    {
        $types = array_values($this->array);
        $class = get_class($this);
        return new $class($this->id, $types ,$this->weapons_tech, $this->shields_tech, $this->armour_tech);
    }
}
