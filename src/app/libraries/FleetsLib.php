<?php
/**
 * Fleets Library
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */

namespace App\libraries;

use App\core\enumerators\DefensesEnumerator as Defenses;
use App\core\enumerators\MissionsEnumerator as Missions;
use App\core\Language;
use App\core\Template;
use App\core\XGPCore;
use App\helpers\UrlHelper;
use App\libraries\FleetsLib;
use App\libraries\FormatLib;
use App\libraries\Functions;
use App\libraries\OfficiersLib;
use App\libraries\Page;
use App\libraries\TimingLibrary as Timing;
use App\libraries\Users;
use CI_Lang;

/**
 * FleetsLib Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 *
 */
class FleetsLib extends XGPCore
{
    /**
     * Determine ship consumption
     *
     * @param  int   $ship
     * @param  array $user
     * @return void
     */
    public static function shipConsumption($ship, $user)
    {
        if ($user['research_impulse_drive'] >= 5) {
            return parent::$objects->getPrice($ship, 'consumption2');
        } else {
            return parent::$objects->getPrice($ship, 'consumption');
        }
    }

    /**
     * targetDistance
     *
     * @param int $orig_galaxy Origin Galaxy
     * @param int $dest_galaxy Destiny Ga
     * @param int $orig_system Origin System
     * @param int $dest_system Destiny System
     * @param int $orig_planet Origin Planet
     * @param int $dest_planet Destiny Planet
     *
     * @return int
     */
    public static function targetDistance($orig_galaxy, $dest_galaxy, $orig_system, $dest_system, $orig_planet, $dest_planet)
    {
        $distance = 5;

        if (($orig_galaxy - $dest_galaxy) != 0) {
            $distance = abs($orig_galaxy - $dest_galaxy) * 20000;
        } elseif (($orig_system - $dest_system) != 0) {
            $distance = abs($orig_system - $dest_system) * 5 * 19 + 2700;
        } elseif (($orig_planet - $dest_planet) != 0) {
            $distance = abs($orig_planet - $dest_planet) * 5 + 1000;
        }

        return $distance;
    }

    /**
     * The formula calculates the mission duration
     *
     * @param int $percentage      The speed percentage set by the user
     * @param int $max_fleet_speed Max fleet speed
     * @param int $distance        The distance
     * @param int $speed_factor    The game speed factor
     *
     * @return int
     */
    public static function missionDuration($percentage, $max_fleet_speed, $distance, $speed_factor)
    {
        // original formula: 3500 / Factor(percentage) * sqrt($distance * 10 / $max_fleet_speed) + 10)
        return (35000 / $percentage * sqrt($distance * 10 / $max_fleet_speed) + 10) / $speed_factor;
    }

    /**
     * fleetMaxSpeed
     *
     * @param array  $fleet_array Fleet
     * @param int    $fleet       Fleed id
     * @param string $user        User
     *
     * @return int
     */
    public static function fleetMaxSpeed($fleet_array, $fleet, $user)
    {
        $pricelist = parent::$objects->getPrice();
        $speed_all = [];

        if ($fleet != 0) {
            $fleet_array = [];
            $fleet_array[$fleet] = 1;
        }

        if (!empty($fleet_array) && !is_null($fleet_array)) {
            foreach ($fleet_array as $ship => $count) {
                /**
                 * Special condition for Small cargo
                 */
                if ($ship == 202) {
                    if ($user['research_impulse_drive'] >= 5) {
                        $speed_all[$ship] = $pricelist[$ship]['speed2'] + ($pricelist[$ship]['speed2'] * $user['research_impulse_drive'] * 0.2);
                    } else {
                        $speed_all[$ship] = $pricelist[$ship]['speed'] + (($pricelist[$ship]['speed'] * $user['research_combustion_drive']) * 0.1);
                    }
                }

                /**
                 * Special condition for Recycler
                 */
                if ($ship == 209) {
                    $speed_all[$ship] = $pricelist[$ship]['speed'] + (($pricelist[$ship]['speed'] * $user['research_combustion_drive']) * 0.1);

                    if ($user['research_impulse_drive'] >= 17) {
                        $speed_all[$ship] = $pricelist[$ship]['speed2'] + (($pricelist[$ship]['speed2'] * $user['research_impulse_drive']) * 0.2);
                    }

                    if ($user['research_hyperspace_drive'] >= 15) {
                        $speed_all[$ship] = $pricelist[$ship]['speed2'] + (($pricelist[$ship]['speed2'] * $user['research_hyperspace_drive']) * 0.3);
                    }
                }

                if ($ship == 203 or $ship == 204 or $ship == 210) {
                    $speed_all[$ship] = $pricelist[$ship]['speed'] + (($pricelist[$ship]['speed'] * $user['research_combustion_drive']) * 0.1);
                }

                if ($ship == 205 or $ship == 206 or $ship == 208) {
                    $speed_all[$ship] = $pricelist[$ship]['speed'] + (($pricelist[$ship]['speed'] * $user['research_impulse_drive']) * 0.2);
                }

                if ($ship == 211) {
                    if ($user['research_hyperspace_drive'] >= 8) {
                        $speed_all[$ship] = $pricelist[$ship]['speed2'] + (($pricelist[$ship]['speed2'] * $user['research_hyperspace_drive']) * 0.3);
                    } else {
                        $speed_all[$ship] = $pricelist[$ship]['speed'] + (($pricelist[$ship]['speed'] * $user['research_hyperspace_drive']) * 0.2);
                    }
                }

                if ($ship == 207 or $ship == 213 or $ship == 214 or $ship == 215) {
                    $speed_all[$ship] = $pricelist[$ship]['speed'] + (($pricelist[$ship]['speed'] * $user['research_hyperspace_drive']) * 0.3);
                }
            }
        }

        if ($fleet != 0) {
            $ship_speed = isset($speed_all[$ship]) ? $speed_all[$ship] : 0;
            $speed_all = $ship_speed;
        }

        return $speed_all;
    }

    /**
     * fleetConsumption
     *
     * @param array $fleet_array      Fleet
     * @param int   $speed_factor     Speed factor
     * @param int   $mission_duration Mission duration
     * @param int   $mission_distance Mission distance
     * @param array $user             User
     *
     * @return int
     */
    public static function fleetConsumption($fleet_array, $speed_factor, $mission_duration, $mission_distance, $user)
    {
        $consumption = 0;
        $basic_consumption = 0;

        foreach ($fleet_array as $ship => $count) {
            if ($ship > 0) {
                $ship_speed = self::fleetMaxSpeed("", $ship, $user);
                $ship_consumption = self::shipConsumption($ship, $user);
                $spd = 35000 / ($mission_duration * $speed_factor - 10) * sqrt($mission_distance * 10 / $ship_speed);

                $basic_consumption = $spd + $count * $ship_consumption * pow((($spd / 10) + 1), 2);
                $consumption += $basic_consumption * $mission_distance / 35000 + 1;
            }
        }

        return round($consumption);
    }

    /**
     * getMaxFleets
     *
     * @param int $computer_tech Computer tech level
     * @param int $amiral_level  Amiral available
     *
     * @return int
     */
    public static function getMaxFleets($computer_tech, $amiral_level)
    {
        return OfficiersLib::getMaxComputer($computer_tech, $amiral_level);
    }

    /**
     * getMaxExpeditions
     *
     * @param int $astrophysics_tech Astrophysics Tech level
     *
     * @return int
     */
    public static function getMaxExpeditions($astrophysics_tech)
    {
        return floor(sqrt($astrophysics_tech));
    }

    /**
     * getMaxColonies
     *
     * @param int $astrophysics_tech Astrophysics Tech level
     *
     * @return int
     */
    public static function getMaxColonies($astrophysics_tech)
    {
        return ceil($astrophysics_tech / 2);
    }

    /**
     * startLink
     *
     * @param array  $fleet_row
     * @param string $fleet_type
     *
     * @return string
     */
    public static function startLink($fleet_row, $fleet_type)
    {
        $coords = FormatLib::prettyCoords(
            $fleet_row['fleet_start_galaxy'],
            $fleet_row['fleet_start_system'],
            $fleet_row['fleet_start_planet']
        );

        $link = "game.php?page=galaxy&mode=3&galaxy=" .
            $fleet_row['fleet_start_galaxy'] . "&system=" . $fleet_row['fleet_start_system'];

        return UrlHelper::setUrl($link, $coords, '', $fleet_type);
    }

    /**
     * targetLink
     *
     * @param array  $fleet_row
     * @param string $fleet_type
     *
     * @return string
     */
    public static function targetLink($fleet_row, $fleet_type)
    {
        $coords = FormatLib::prettyCoords(
            $fleet_row['fleet_end_galaxy'],
            $fleet_row['fleet_end_system'],
            $fleet_row['fleet_end_planet']
        );

        $link = "game.php?page=galaxy&mode=3&galaxy=" .
            $fleet_row['fleet_end_galaxy'] . "&system=" . $fleet_row['fleet_end_system'];

        return UrlHelper::setUrl($link, $coords, '', $fleet_type);
    }

    /**
     * fleetResourcesPopup
     *
     * @param array  $fleet_row  Fleet row
     * @param string $text       Text
     * @param string $fleet_type Fleet type
     *
     * @return void
     */
    public static function fleetResourcesPopup($fleet_row, $text, $fleet_type)
    {
        $total_resources = $fleet_row['fleet_resource_metal'] + $fleet_row['fleet_resource_crystal'] + $fleet_row['fleet_resource_deuterium'];

        if ($total_resources != 0) {
            $popup['fleet_resource_metal'] = FormatLib::prettyNumber($fleet_row['fleet_resource_metal']);
            $popup['fleet_resource_crystal'] = FormatLib::prettyNumber($fleet_row['fleet_resource_crystal']);
            $popup['fleet_resource_deuterium'] = FormatLib::prettyNumber($fleet_row['fleet_resource_deuterium']);

            $resources_popup = (new Page(new Users()))->jsReady(
                self::getTemplate()->set(
                    'general/fleet_resources_popup_view',
                    array_merge($popup, self::loadLanguage(['game/global'])->language)
                )
            );
        } else {
            $resources_popup = '';
        }

        if ($resources_popup != '') {
            $pop_up = "<a href='#' onmouseover=\"return overlib('" . $resources_popup . "');";
            $pop_up .= "\" onmouseout=\"return nd();\" class=\"" . $fleet_type . "\">" . $text . "</a>";
        } else {
            $pop_up = $text . "";
        }

        return $pop_up;
    }

    /**
     * fleetShipsPopup
     *
     * @param array  $fleet_row    Fleet row
     * @param string $text         Text
     * @param string $fleet_type   Fleet type
     * @param array  $current_user Current user
     *
     * @return void
     */
    public static function fleetShipsPopup($fleet_row, $text, $fleet_type, $current_user = '')
    {
        $lang = static::loadLanguage(['game/events', 'game/ships']);
        $objects = parent::$objects->getObjects();

        $ships = self::getFleetShipsArray($fleet_row['fleet_array']);
        $pop_up = "<a href='#' onmouseover=\"return overlib('";
        $pop_up .= "<table width=200>";

        $espionage_tech = OfficiersLib::getMaxEspionage(
            $current_user['research_espionage_technology'],
            $current_user['premium_officier_technocrat']
        );

        if ($espionage_tech < 2 && $fleet_row['fleet_owner'] != $current_user['user_id']) {
            $pop_up .= "<tr><td width=50% align=left><font color=white>" .
            $lang->line('ev_no_fleet_data') . "</font></td></tr>";
        } elseif ($espionage_tech >= 2 && $espionage_tech < 4 && $fleet_row['fleet_owner'] != $current_user['user_id']) {
            $pop_up .= "<tr><td width=50% align=left><font color=white>" .
            $lang->line('ev_aproaching') . $fleet_row['fleet_amount'] .
            $lang->line('ev_ships') . "</font></td></tr>";
        } else {
            if ($fleet_row['fleet_owner'] != $current_user['user_id']) {
                $pop_up .= "<tr><td width=100% align=left><font color=white>" .
                $lang->line('ev_aproaching') . $fleet_row['fleet_amount'] . $lang->line('ev_ships') .
                    ":</font></td></tr>";
            }

            foreach ($ships as $ship => $amount) {
                if ($fleet_row['fleet_owner'] == $current_user['user_id']) {
                    $pop_up .= "<tr><td width=50% align=left><font color=white>" .
                    $lang->language[$objects[$ship]] .
                    ":</font></td><td width=50% align=right><font color=white>" .
                    FormatLib::prettyNumber($amount) . "</font></td></tr>";
                } elseif ($fleet_row['fleet_owner'] != $current_user['user_id']) {
                    if ($espionage_tech >= 4 && $espionage_tech < 8) {
                        $pop_up .= "<tr><td width=50% align=left><font color=white>" .
                        $lang->language[$objects[$ship]] .
                            "</font></td></tr>";
                    } elseif ($espionage_tech >= 8) {
                        $pop_up .= "<tr><td width=50% align=left><font color=white>" .
                        $lang->language[$objects[$ship]] .
                        ":</font></td><td width=50% align=right><font color=white>" .
                        FormatLib::prettyNumber($amount) . "</font></td></tr>";
                    }
                }
            }
        }

        $pop_up .= "</table>";
        $pop_up .= "');\" onmouseout=\"return nd();\" class=\"" . $fleet_type . "\">" . $text . "</a>";

        return $pop_up;
    }

    /**
     * enemyLink
     *
     * @param array $fleet_row Fleet row
     *
     * @return string
     */
    public static function enemyLink($fleet_row)
    {
        $url = 'game.php?page=chat&playerId=' . $fleet_row['fleet_owner'];
        $image = Functions::setImage(DPATH . '/img/m.gif');
        $link = $fleet_row['start_planet_user'] . ' ' . UrlHelper::setUrl($url, $image);

        return $link;
    }

    /**
     * flyingFleetsTable
     *
     * @param array  $fleet_row    Fleet row
     * @param string $Status       Status
     * @param int    $Owner        Owner
     * @param string $Label        Label
     * @param string $Record       Record
     * @param string $current_user Current user
     *
     * @return void
     */
    public static function flyingFleetsTable($fleet_row, $Status, $Owner, $Label, $Record, $current_user, $acs_owner = false)
    {
        $lang = static::loadLanguage(['game/events', 'game/missions']);

        $FleetStyle = [
            1 => 'attack',
            2 => 'federation',
            3 => 'transport',
            4 => 'deploy',
            5 => 'hold',
            6 => 'espionage',
            7 => 'colony',
            8 => 'harvest',
            9 => 'destroy',
            10 => 'missile',
            15 => 'transport',
        ];
        $FleetPrefix = '';

        if ($Owner or $acs_owner) {
            $FleetPrefix = 'own';
        }

        $FleetStatus = [0 => 'flight', 1 => 'holding', 2 => 'return'];
        $MissionType = $fleet_row['fleet_mission'];
        if ($MissionType != Missions::MISSILE) {
            $FleetContent = self::fleetShipsPopup(
                $fleet_row,
                $lang->line('ev_fleet'),
                $FleetPrefix . $FleetStyle[$MissionType],
                $current_user
            );
        }

        $StartType = $fleet_row['fleet_start_type'];
        $TargetType = $fleet_row['fleet_end_type'];

        if ($Status != 2) {
            if ($StartType == 1) {
                $StartID = $lang->line('ev_from_the_planet');
            } elseif ($StartType == 3) {
                $StartID = $lang->line('ev_from_the_moon');
            }

            $StartID .= $fleet_row['start_planet_name'] . " ";
            $StartID .= FleetsLib::startLink($fleet_row, $FleetPrefix . $FleetStyle[$MissionType]);

            if ($MissionType != Missions::EXPEDITION) {
                switch ($TargetType) {
                    case 1:
                        $TargetID = $lang->line('ev_the_planet');
                        break;

                    case 2:
                        $TargetID = $lang->line('ev_debris_field');
                        break;

                    case 3:
                        $TargetID = $lang->line('ev_to_the_moon');
                        break;
                }
            } else {
                $TargetID = $lang->line('ev_the_position');
            }

            $TargetID .= $fleet_row['target_planet_name'] . " ";
            $TargetID .= FleetsLib::targetLink($fleet_row, $FleetPrefix . $FleetStyle[$MissionType]);
        } else {
            if ($StartType == 1) {
                $StartID = $lang->line('ev_to_the_planet');
            } elseif ($StartType == 3) {
                $StartID = $lang->line('ev_the_moon');
            }

            $StartID .= $fleet_row['start_planet_name'] . " ";
            $StartID .= FleetsLib::startLink($fleet_row, $FleetPrefix . $FleetStyle[$MissionType]);

            if ($MissionType != Missions::EXPEDITION) {
                switch ($TargetType) {
                    case 1:
                        $TargetID = $lang->line('ev_from_planet');
                        break;

                    case 2:
                        $TargetID = $lang->line('ev_from_debris_field');
                        break;

                    case 3:
                        $TargetID = $lang->line('ev_from_the_moon');
                        break;
                }
            } else {
                $TargetID = $lang->line('ev_from_position');
            }

            $TargetID .= $fleet_row['target_planet_name'] . " ";
            $TargetID .= FleetsLib::targetLink($fleet_row, $FleetPrefix . $FleetStyle[$MissionType]);
        }

        if ($MissionType == Missions::MISSILE) {
            $EventString = $lang->line('ev_missile_attack') .
            " ( " . FleetsLib::getFleetShipsArray($fleet_row['fleet_array'])[Defenses::defense_interplanetary_missile] . " ) ";
            $Time = $fleet_row['fleet_start_time'];
            $Rest = $Time - time();

            $EventString .= $StartID;
            $EventString .= $lang->line('ev_to');
            $EventString .= $TargetID;
            $EventString .= ".";
        } else {
            if ($Owner == true) {
                $EventString = $lang->line('ev_one_of_your');
                $EventString .= $FleetContent;
            } else {
                $EventString = $lang->line('ev_a');
                $EventString .= $FleetContent;
                $EventString .= $lang->line('ev_of');
                $EventString .= self::enemyLink($fleet_row);
            }

            switch ($Status) {
                case 0:
                    $Time = $fleet_row['fleet_start_time'];
                    $Rest = $Time - time();

                    $EventString .= $lang->line('ev_goes');
                    $EventString .= $StartID;
                    $EventString .= $lang->line('ev_toward');
                    $EventString .= $TargetID;
                    $EventString .= $lang->line('ev_with_the_mission_of');
                    break;

                case 1:
                    $Time = $fleet_row['fleet_end_stay'];
                    $Rest = $Time - time();

                    $EventString .= $lang->line('ev_goes');
                    $EventString .= $StartID;
                    $EventString .= $lang->line('ev_to_explore');
                    $EventString .= $TargetID;
                    $EventString .= $lang->line('ev_with_the_mission_of');
                    break;

                case 2:
                    $Time = $fleet_row['fleet_end_time'];
                    $Rest = $Time - time();

                    $EventString .= $lang->line('ev_comming_back');
                    $EventString .= $TargetID;
                    $EventString .= $StartID;
                    $EventString .= $lang->line('ev_with_the_mission_of');
                    break;
            }

            $EventString .= self::fleetResourcesPopup(
                $fleet_row,
                $lang->language['type_mission'][$MissionType],
                $FleetPrefix . $FleetStyle[$MissionType]
            );
        }

        $bloc['fleet_status'] = $FleetStatus[$Status];
        $bloc['fleet_prefix'] = $FleetPrefix;
        $bloc['fleet_style'] = $FleetStyle[$MissionType];
        $bloc['fleet_javai'] = Functions::chronoApplet($Label, $Record, $Rest, true);
        $bloc['fleet_order'] = $Label . $Record;
        $bloc['fleet_descr'] = $EventString;
        $bloc['fleet_javas'] = Functions::chronoApplet($Label, $Record, $Rest, false);
        $bloc['fleet_time'] = Timing::formatExtendedDate($Time);

        return self::getTemplate()->set(
            'overview.overview_fleet_event',
            $bloc
        );
    }

    /**
     * isFleetReturning
     *
     * @param array $fleet_mess Fleet mess
     *
     * @return boolean
     */
    public static function isFleetReturning($fleet_mess)
    {
        return ($fleet_mess == 1);
    }

    /**
     * Get max ship storage
     *
     * @param integer $ship_storage
     * @param integer $hyperspace_tech_level
     * @return integer
     */
    public static function getMaxStorage(int $ship_storage, int $hyperspace_tech_level): int
    {
        return ($ship_storage + ($ship_storage * 0.05 * $hyperspace_tech_level));
    }

    /**
     * Serialize the fleet array
     *
     * @param array $fleet_array Fleet array
     *
     * @return string
     */
    public static function setFleetShipsArray(array $fleet_array): string
    {
        return serialize($fleet_array);
    }

    /**
     * Un-serialize the fleet array
     *
     * @param string $fleet_array Fleet array
     *
     * @return array
     */
    public static function getFleetShipsArray(string $fleet_array): array
    {
        return unserialize($fleet_array);
    }

    /**
     * Check if the fleet has resources
     *
     * @param array $fleet
     * @return boolean
     */
    public static function hasResources(array $fleet): bool
    {
        return ($fleet['fleet_resource_metal'] != 0 or $fleet['fleet_resource_crystal'] != 0 or $fleet['fleet_resource_deuterium'] != 0);
    }

    /**
     * Return a new instance of Template
     *
     * @return Template
     */
    private static function getTemplate(): Template
    {
        return new Template();
    }

    /**
     * Load CI language
     *
     * @return void
     */
    private static function loadLanguage(array $required_lang): CI_Lang
    {
        $lang = new Language();

        return $lang->loadLang($required_lang, true);
    }
}
