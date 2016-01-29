<?php

/**
 *  OPBE
 *  Copyright (C) 2015  Jstar
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
 * @version 6-3-2015
 * @link https://github.com/jstar88/opbe
 */
class ShipsCleaner
{
    private $shipType;
    private $lastShipHit;
    private $lastShots;

    private $exploded;
    private $remainLife;
    /**
     * ShipsCleaner::__construct()
     * 
     * @param mixed $shipType
     * @param int $lastShipHit
     * @param int $lastShot
     * @return ShipsCleaner
     */
    public function __construct(ShipType $shipType, $lastShipHit, $lastShots)
    {
        if ($lastShipHit < 0)
            throw new Exception('Negative $lastShipHit');
        if ($lastShots < 0)
            throw new Exception('Negative $lastShots');
        $this->fighters = $shipType->cloneMe();
        $this->lastShipHit = $lastShipHit;
        $this->lastShots = $lastShots;
    }
    /**
     * ShipsCleaner::start()
     * Start the system
     * @return null
     */
    public function start()
    {
        /*** calculating probability to explode ***/

        //the mean probably to explode based on damage
        $prob = 1 - $this->fighters->getCurrentLife() / ($this->fighters->getHull() * $this->fighters->getCount());
        if ($prob < 0 && $prob > -EPSILON)
        {
            $prob = 0;
        }
        if ($prob < 0)
        {
            throw new Exception("Negative prob");
        }
        //if most of ships are hitten,then we can apply the more realistic way
        if (USE_BIEXPLOSION_SYSTEM && $this->lastShipHit >= $this->fighters->getCount() / PROB_TO_REAL_MAGIC)
        {
            log_comment('lastShipHit bigger than getCount()/magic');
            if ($prob < MIN_PROB_TO_EXPLODE)
            {
                $probToExplode = 0;
            }
            else
            {
                $probToExplode = $prob;
            }
        }
        //otherwise  statistically:
        else
        {
            log_comment('lastShipHit smaller than getCount()/magic');
            $probToExplode = $prob * (1 - MIN_PROB_TO_EXPLODE);
        }


        /*** calculating the amount of exploded ships ***/

        $teoricExploded = round($this->fighters->getCount() * $probToExplode);
        if (USE_EXPLODED_LIMITATION)
        {
            $teoricExploded = min($teoricExploded, $this->lastShots);
        }
        $this->exploded = $teoricExploded; //bounded by the total shots fired to simulate a real combat :)


        /*** calculating the life of destroyed ships ***/

        //$this->remainLife = $this->exploded * (1 - $prob) * ($this->fighters->getCurrentLife() / $this->fighters->getCount());
        $this->remainLife = $this->fighters->getCurrentLife() / $this->fighters->getCount();
        log_var('prob',$prob);
        log_var('probToExplode',$probToExplode);
        log_var('teoricExploded',$teoricExploded);
        log_var('exploded',$this->exploded);
        log_var('remainLife',$this->remainLife);
    }
    /**
     * ShipsCleaner::getExplodeShips()
     * Return the number of exploded ships
     * @return int
     */
    public function getExplodedShips()
    {
        return $this->exploded;
    }
    /**
     * ShipsCleaner::getRemainLife()
     * Return the life of exploded ships
     * @return float
     */
    public function getRemainLife()
    {
        return $this->remainLife;
    }

}
