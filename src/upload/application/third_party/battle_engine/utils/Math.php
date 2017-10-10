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
abstract class Math
{
    public static function divide(Number $num, Number $denum, $real = false)
    {
        if ($real)
        {
            if ($denum->result == 0)
                throw new Exception('denum is zero');
            $shots = floor($num->result / $denum->result);
            $rest = Math::rest($num->result, $denum->result);
            return new Number($shots, $rest);
        }
        else
        {
            $shots = $num->result / $denum->result;
            return new Number($shots);
        }
    }
    public static function multiple(Number $first, Number $second, $real = false)
    {
        $result = $first->result * $second->result;
        if ($real)
        {            
            return new Number(floor($result),$result - floor($result));
        }
        return new Number($result);
    }
    public static function heaviside($x, $y)
    {
        if ($x >= $y)
        {
            return 1;
        }
        return 0;
    }
    public static function rest($dividendo, $divisore, $real = true)
    {
        while ($divisore < 1)
        {
            $divisore *= 10;
            $dividendo *= 10;
        }
        if (!$real)
        {
            $decimal = (int)$dividendo - $dividendo;
            return $divisore % $dividendo + $decimal;
        }
        return $dividendo % $divisore;
    }
    public static function tryEvent($probability, $callback, $callbackParam)
    {
        if(!is_callable($callback))
        {
            throw new Exception();
        }
        if (mt_rand(0, 99) < $probability)
            return call_user_func($callback, $callbackParam);
        return false;
    }
    public static function recursive_sum($array)
    {
        $sum = 0;
        $array_obj = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));
        foreach ($array_obj as $key => $value)
        {
            $sum += $value;
        }
        return $sum;
    }
    /*
    public static function matrix_scalar_moltiplication($matrix, $scalar)
    {
        $func = function ($value)
        {
            return $value * $scalar;
        }
        ;
        return array_map($func, $matrix);
    }  */
}

?>
