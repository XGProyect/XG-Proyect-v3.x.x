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
 * @version 21-03-2015)
 * @link https://github.com/jstar88/opbe
 */

abstract class GeometricDistribution
{
    /**
     * GeometricDistribution::getProbabilityFromMean()
     * 
     * @param int $m: the mean
     * @return int
     */
    public static function getProbabilityFromMean($m)
    {
        if ($m <= 1 )
        {
            return 1;
        }
        return 1 / $m;
    }

    /**
     * GeometricDistribution::getMeanFromProbability()
     * 
     * @param int $p: the probability
     * @return int
     */
    public static function getMeanFromProbability($p)
    {
        if ($p == 0)
        {
            return INF;
        }
        return 1 / $p;
    }

    /**
     * GeometricDistribution::getVarianceFromProbability()
     * 
     * @param int $p: the probability
     * @return int
     */
    public static function getVarianceFromProbability($p)
    {
        if ($p == 0)
        {
            return INF;
        }
        return (1 - $p) / ($p * $p);
    }
    
    /**
     * GeometricDistribution::getStandardDeviationFromProbability()
     * 
     * @param int $p: the probability
     * @return int
     */
    public static function getStandardDeviationFromProbability($p)
    {
        return sqrt(self::getVarianceFromProbability($p));        
    }

}

?>