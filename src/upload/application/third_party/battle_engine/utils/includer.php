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

define('OPBEPATH', dirname(__DIR__).DIRECTORY_SEPARATOR);

require (OPBEPATH.'utils'.DIRECTORY_SEPARATOR.'GeometricDistribution.php');
require (OPBEPATH.'utils'.DIRECTORY_SEPARATOR.'Gauss.php');
require (OPBEPATH.'utils'.DIRECTORY_SEPARATOR.'DebugManager.php'); 
require (OPBEPATH.'utils'.DIRECTORY_SEPARATOR.'IterableUtil.php');
require (OPBEPATH.'utils'.DIRECTORY_SEPARATOR.'Math.php');
require (OPBEPATH.'utils'.DIRECTORY_SEPARATOR.'Number.php');
require (OPBEPATH.'utils'.DIRECTORY_SEPARATOR.'Events.php');
require (OPBEPATH.'utils'.DIRECTORY_SEPARATOR.'Lang.php');
require (OPBEPATH.'utils'.DIRECTORY_SEPARATOR.'LangManager.php');
require (OPBEPATH.'models'.DIRECTORY_SEPARATOR.'Type.php');
require (OPBEPATH.'models'.DIRECTORY_SEPARATOR.'ShipType.php');
require (OPBEPATH.'models'.DIRECTORY_SEPARATOR.'Fleet.php');
require (OPBEPATH.'models'.DIRECTORY_SEPARATOR.'HomeFleet.php');
require (OPBEPATH.'models'.DIRECTORY_SEPARATOR.'Defense.php');
require (OPBEPATH.'models'.DIRECTORY_SEPARATOR.'Ship.php');
require (OPBEPATH.'models'.DIRECTORY_SEPARATOR.'Player.php');
require (OPBEPATH.'models'.DIRECTORY_SEPARATOR.'PlayerGroup.php');
require (OPBEPATH.'combatObject'.DIRECTORY_SEPARATOR.'Fire.php');
require (OPBEPATH.'combatObject'.DIRECTORY_SEPARATOR.'PhysicShot.php');
require (OPBEPATH.'combatObject'.DIRECTORY_SEPARATOR.'ShipsCleaner.php');
require (OPBEPATH.'combatObject'.DIRECTORY_SEPARATOR.'FireManager.php');
require (OPBEPATH.'core'.DIRECTORY_SEPARATOR.'Battle.php');
require (OPBEPATH.'core'.DIRECTORY_SEPARATOR.'BattleReport.php');
require (OPBEPATH.'core'.DIRECTORY_SEPARATOR.'Round.php');
require (OPBEPATH.'constants'.DIRECTORY_SEPARATOR.'battle_constants.php');
?>
