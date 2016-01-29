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

class Gauss
{
    /**
     * Random::getNext()
     * Return an random normal number
     * @return int 
     */
    public static function getNext()
    {
        $x = (float)mt_rand() / (float)mt_getrandmax();
        $y = (float)mt_rand() / (float)mt_getrandmax();
        $u = sqrt(-2 * log($x)) * cos(2 * pi() * $y);
        $v = sqrt(-2 * log($x)) * sin(2 * pi() * $y);
        return $u;
    }

    /**
     * Random::getNextMs()
     * Generates a random number from the normal distribution with specific mean and standard deviation
     * @param int $m: mean
     * @param int $s: standard deviation
     * @return int
     */
    public static function getNextMs($m, $s)
    {
        return self::getNext() * $s + $m;
    }

    /**
     * Random::getNextMsBetween()
     * Generates a random number from the normal distribution with specific mean and standard deviation.
     * The number must be between min and max.
     * @param int $m: mean
     * @param int $s: standard deviation
     * @param int $min: the minimum
     * @param int $max: the maximum
     * @return int
     */
    public static function getNextMsBetween($m, $s, $min, $max)
    {
        $i = 0;
        if ($min > $m || $max < $m)
        {
            throw new Exception("Mean is not bounded by min and max");
        }
        while (true)
        {
            $n = self::getNextMs($m, $s);
            if ($n >= $min && $n <= $max)
            {
                return $n;
            }
            $i++;
            if ($i > 10)
            {
                return mt_rand($min, $max);
            }
        }
    }
}

/**

 * //--------------------------- testing -----------------------

 * //--------edit only these!----------
 * define('MEAN', 55);
 * define('DEV', sqrt(7));
 * define('SIMULATIONS', 1000);
 * //----------------------------------


 * $a = array();
 * for ($i = 0; $i < SIMULATIONS; $i++)
 * {
 *     $a[] = Gauss::getNextMs(MEAN, DEV);
 * }

 * $l = array();
 * foreach ($a as $v)
 * {
 *     if (isset($l[$v]))
 *         $l[$v]++;
 *     else
 *         $l[$v] = 1;
 * }
 * ksort($l);
 * foreach ($l as $id => $v)
 * {
 *     $s = '';
 *     for ($i = 0; $i < $v; $i++)
 *     {
 *         $s .= '-';
 *     }
 *     echo $s . $id . '(' . $v . ')' . '<br>';
 * }
 */

?>