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
 */
class FireManager extends IterableUtil
{
    protected $array = array();
    public function add(Fire $fire)
    {
        $this->array[] = $fire;
    }
    public function getAttackerTotalShots()
    {
        $tmp = 0;
        foreach ($this->array as $id => $fire)
        {
            $tmp += $fire->getAttackerTotalShots();
        }
        return $tmp;
    }
    public function getAttackerTotalFire()
    {
        $tmp = 0;
        foreach ($this->array as $id => $fire)
        {
            $tmp += $fire->getAttackerTotalFire();
        }
        return $tmp;
    }

}
