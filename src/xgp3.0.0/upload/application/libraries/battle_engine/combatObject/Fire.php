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
 * @version 21-03-2015
 * @link https://github.com/jstar88/opbe
 *
 *
 * Fire
 *  
 * This class rappresent the fire shotted by attackers to defenders or viceversa.
 * Using probabilistic theory, this class will help you in RF(Rapid Fire) calculation with O(1) time and memory functions.
 * Sometime i think that SpeedSim's RF calculation is bugged, so you can choose if return its result or not setting "SPEEDSIM" constant to true/false.
 */
class Fire
{
    private $attackerShipType;
    private $defenderFleet;

    /* old way
    const SPEEDSIM = true;
    const RAPIDFIRE = true;
    private $shots = null;
    private $power = null;
    */

    private $shots = 0;
    private $power = 0;

    /**
     * Fire::__construct()
     * 
     * @param ShipType $attackerShipType
     * @param Fleet $defenderFleet
     * @param bool $attacking
     * @return
     */
    public function __construct(ShipType $attackerShipType, Fleet $defenderFleet)
    {
        log_comment('calculating fire from attacker '.$attackerShipType->getId());
        $this->attackerShipType = $attackerShipType;
        $this->defenderFleet = $defenderFleet;
        $this->calculateTotal();
    }
    public function getPower()
    {
        return $this->attackerShipType->getPower();
    }
    public function getId()
    {
        return $this->attackerShipType->getId();
    }
    //----------- SENDED FIRE -------------

    /**
     * Fire::getAttackerTotalFire()
     * Return the total fire
     * @return int
     */
    public function getAttackerTotalFire()
    {
        return $this->power;
    }

    /**
     * Fire::getAttackerTotalShots()
     * Return the total shots
     * @return int
     */
    public function getAttackerTotalShots()
    {
        return $this->shots;
    }

    /**
     * Fire::calculateTotal()
     * Calculate the total power and shots amount of attacker, including RF and standart fire
     * @return void
     */
    private function calculateTotal()
    {
        $this->shots += $this->attackerShipType->getCount();
        $this->power += $this->getNormalPower();

        if (USE_RF)
        {
            $this->calculateRf();
        }
        log_var('$this->shots',$this->shots);
        /*  old way
        $this->shots = 0;
        $this->power = 0;          
        if (self::RAPIDFIRE)
        {
        $this->calculateRf();
        }
        if (!self::SPEEDSIM || !self::RAPIDFIRE)
        {
        $this->shots += $this->attackerShipType->getCount();
        $this->power += $this->getNormalPower();
        }
        */
    }

    /**
     * Fire::calculateRf()
     * This function implement the RF component of above function
     * @return void
     */
    private function calculateRf()
    {
        //rapid fire
        $tmpshots = round($this->getShotsFromOneAttackerShipOfType($this->attackerShipType) * $this->attackerShipType->getCount());
        log_var('$tmpshots',$tmpshots);
        $this->power += $tmpshots * $this->attackerShipType->getPower();
        $this->shots += $tmpshots;

        /* old way
        $tmpshots = round($this->getShotsFromOneAttackerShipOfType($this->attackerShipType) * $this->attackerShipType->getCount());
        if (self::SPEEDSIM && $tmpshots == 0)
        {
        $tmpshots = $this->attackerShipType->getCount();
        }
        $this->power += $tmpshots * $this->attackerShipType->getPower();
        $this->shots += $tmpshots;
        */
    }

    /**
     * Fire::getShotsFromOneAttackerShipOfType()
     * This function return the number of shots caused by RF from one ShipType to all defenders
     * @param ShipType $shipType_A
     * @return int
     */
    private function getShotsFromOneAttackerShipOfType(ShipType $shipType_A)
    {
        $p = $this->getProbabilityToShotAgainForAttackerShipOfType($shipType_A);
        $meanShots = GeometricDistribution::getMeanFromProbability(1 - $p) - 1;
        if (USE_RANDOMIC_RF)
        {
            $max = $meanShots * (1 + MAX_RF_BUFF);
            $min = $meanShots * (1 - MAX_RF_NERF);
            log_var('$max', $max);
            log_var('$min', $min);
            log_var('$mean', $meanShots);
            return Gauss::getNextMsBetween($meanShots, GeometricDistribution::getStandardDeviationFromProbability(1 - $p), $min, $max);
        }
        return $meanShots;
    }

    /**
     * Fire::getProbabilityToShotAgainForAttackerShipOfType()
     * This function return the probability of a ShipType to shot thanks RF
     * @param ShipType $shipType_A
     * @return int
     */
    private function getProbabilityToShotAgainForAttackerShipOfType(ShipType $shipType_A)
    {
        $p = 0;
        foreach ($this->defenderFleet->getIterator() as $idFleet => $shipType_D)
        {
            $RF = $shipType_A->getRfTo($shipType_D);
            $probabilityToShotAgain = 1 - GeometricDistribution::getProbabilityFromMean($RF);
            $probabilityToHitThisType = $shipType_D->getCount() / $this->defenderFleet->getTotalCount();
            $p += $probabilityToShotAgain * $probabilityToHitThisType;
        }
        return $p;

        /* old way
        $p = 0;
        foreach ($this->defenderFleet->getIterator() as $idFleet => $shipType_D)
        {
        $RF = $shipType_A->getRfTo($shipType_D);
        if (!self::SPEEDSIM)
        {
        $RF = max(0, $RF - 1);
        }
        $probabilityToShotAgain = ($RF != 0) ? 1 - GeometricDistribution::getProbabilityFromMean($RF) : 0;
        $probabilityToHitThisType = $shipType_D->getCount() / $this->defenderFleet->getTotalCount();
        $p += $probabilityToShotAgain * $probabilityToHitThisType;
        }
        return $p;
        */
    }

    /**
     * Fire::getNormalPower()
     * Return the total fire shotted from attacker ShipType to all defenders without RF
     * @return int
     */
    private function getNormalPower()
    {
        return $this->attackerShipType->getCount() * $this->attackerShipType->getPower();
    }
    //------- INCOMING FIRE------------

    public function getShotsFiredByAttackerTypeToDefenderType(ShipType $shipType_A, ShipType $shipType_D, $real = false)
    {
        $first = $this->getShotsFiredByAttackerToOne($shipType_A);
        $second = new Number($shipType_D->getCount());
        return Math::multiple($first, $second, $real);
    }
    public function getShotsFiredByAttackerToOne(ShipType $shipType_A, $real = false)
    {
        $num = $this->getShotsFiredByAttackerToAll($shipType_A);
        $denum = new Number($this->defenderFleet->getTotalCount());
        return Math::divide($num, $denum, $real);
    }
    public function getShotsFiredByAllToDefenderType(ShipType $shipType_D, $real = false)
    {
        $first = $this->getShotsFiredByAllToOne();
        $second = new Number($shipType_D->getCount());
        return Math::multiple($first, $second, $real);
    }
    public function getShotsFiredByAttackerToAll(ShipType $shipType_A, $real = false)
    {
        $num = new Number($this->getAttackerTotalShots() * $shipType_A->getCount());
        $denum = new Number($this->attackerShipType->getTotalCount());
        return Math::divide($num, $denum, $real);
    }
    public function getShotsFiredByAllToOne($real = false)
    {
        $num = new Number($this->getAttackerTotalShots());
        $denum = new Number($this->defenderFleet->getTotalCount());
        return Math::divide($num, $denum, $real);
    }
    /**
     * Fire::__toString()
     * Rappresentation of this object
     * @return
     */
    public function __toString()
    {
        #global $resource;
        #        $shots = $this->getAttackerTotalShots();
        #        $power = $this->getAttackerTotalFire();
        #        $iter = $this->attackerShipType->getIterator();
        #        $page = "<center><table bgcolor='#ADC9F4' border='1' ><body><tr><tr><td colspan='" . count($iter) . "'><center><font color='red'>Attackers</font></center></td></tr>";
        #        foreach ($iter as $attacher)
        #            $page .= "<td>" . $resource[$attacher->getId()] . "</td>";
        #        $page .= "</tr><tr>";
        #        foreach ($iter as $attacher)
        #            $page .= "<td><center>" . $attacher->getCount() . "</center></td>";
        #        $iter = $this->defenderFleet->getIterator();
        #        $page .= "</tr></body></table><br><table bgcolor='#ADC9F4' border='1'><body><tr><td colspan='" . count($iter) . "'><center><font color='red'>Defenders</font></center></td></tr></tr>";
        #        foreach ($iter as $defender)
        #            $page .= "<td>" . $resource[$defender->getId()] . "</td>";
        #        $page .= "<tr>";
        #        foreach ($iter as $defender)
        #            $page .= "<td><center>" . $defender->getCount() . "</center></td>";
        #        $page .= "</tr></body></table><br>";
        #        $page .= "The attacking fleet fires a total of $shots times with the power of $power upon the defenders.<br>";
        #        $page .= "</center>";
        #        return $page;
        return $this->getAttackerTotalFire() . '';
    }
    public function cloneMe()
    {
        return new Fire($this->attackerShipType, $this->defenderFleet);
    }

}
