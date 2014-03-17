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
class Type
{
    private $id;
    private $count;

    public function __construct($id, $count)
    {
        $this->id = $id;
        $this->count = $count;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getCount()
    {
        return $this->count;
    }
    public function increment($number)
    {
        $this->count += $number;
    }
    public function decrement($number)
    {
        $this->count -= $number;
    }
    public function setCount($number)
    {
        $this->count = $number;
    }
    public function __toString()
    {
        ob_start();
        $_type = $this;
        require(OPBEPATH."views/type.html");
        return ob_get_clean();
    }
    public function isEmpty()
    {
        return $this->count == 0;
    }
    
    public function cloneMe()
    {
        return new Type($this->id,$this->count);
    }
}
