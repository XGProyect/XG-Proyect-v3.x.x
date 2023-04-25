<?php

namespace App\Libraries;

use App\Core\Enumerators\DefensesEnumerator as Defenses;
use App\Core\Enumerators\MissionsEnumerator as Missions;
use App\Core\Language;
use App\Core\Objects;
use App\Core\Template;
use App\Helpers\UrlHelper;
use App\Libraries\TimingLibrary as Timing;
use CiLang;

class FleetsLib
{
    public static function shipConsumption(int $ship, array $user): int
    {
        if ($user['research_impulse_drive'] >= 5) {
            return Objects::getInstance()->getPrice($ship, 'consumption2');
        } else {
            return Objects::getInstance()->getPrice($ship, 'consumption');
        }
    }

    public static function targetDistance(int $origGalaxy, int $destGalaxy, int $origSystem, int $destSystem, int $origPlanet, int $destPlanet): int
    {
        $distance = 5;

        if (($origGalaxy - $destGalaxy) != 0) {
            $distance = abs($origGalaxy - $destGalaxy) * 20000;
        } elseif (($origSystem - $destSystem) != 0) {
            $distance = abs($origSystem - $destSystem) * 5 * 19 + 2700;
        } elseif (($origPlanet - $destPlanet) != 0) {
            $distance = abs($origPlanet - $destPlanet) * 5 + 1000;
        }

        return $distance;
    }

    public static function missionDuration(int $percentage, int $maxFleetSpeed, int $distance, int $speedFactor): float
    {
        // original formula: 3500 / Factor(percentage) * sqrt($distance * 10 / $max_fleet_speed) + 10)
        return (35000 / $percentage * sqrt($distance * 10 / $maxFleetSpeed) + 10) / $speedFactor;
    }

    /**
     * @return mixed
     */
    public static function fleetMaxSpeed(?array $fleetArray, int $fleet, array $user)
    {
        $pricelist = Objects::getInstance()->getPrice();
        $speed_all = [];

        if ($fleet != 0) {
            $fleetArray = [];
            $fleetArray[$fleet] = 1;
        }

        if (!empty($fleetArray) && !is_null($fleetArray)) {
            foreach ($fleetArray as $ship => $count) {
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
     * @param array $fleetArray      Fleet
     * @param int   $speed_factor     Speed factor
     * @param int   $mission_duration Mission duration
     * @param int   $mission_distance Mission distance
     * @param array $user             User
     *
     * @return int
     */
    public static function fleetConsumption($fleetArray, $speed_factor, $mission_duration, $mission_distance, $user)
    {
        $consumption = 0;
        $basic_consumption = 0;

        foreach ($fleetArray as $ship => $count) {
            if ($ship > 0) {
                $ship_speed = self::fleetMaxSpeed(null, $ship, $user);
                $ship_consumption = self::shipConsumption($ship, $user);
                $spd = 35000 / ($mission_duration * $speed_factor - 10) * sqrt($mission_distance * 10 / $ship_speed);

                $basic_consumption = $spd + $count * $ship_consumption * pow((($spd / 10) + 1), 2);
                $consumption += $basic_consumption * $mission_distance / 35000 + 1;
            }
        }

        return round($consumption);
    }

    public static function getMaxFleets($computerTech, $amiralLevel): int
    {
        return OfficiersLib::getMaxComputer($computerTech, $amiralLevel);
    }

    public static function getMaxExpeditions(int $astrophysicsTech): int
    {
        return floor(sqrt($astrophysicsTech));
    }

    public static function getMaxColonies($astrophysicsTech): int
    {
        return ceil($astrophysicsTech / 2);
    }

    public static function startLink(array $fleetRow, string $fleetType): string
    {
        $coords = FormatLib::prettyCoords(
            $fleetRow['fleet_start_galaxy'],
            $fleetRow['fleet_start_system'],
            $fleetRow['fleet_start_planet']
        );

        $link = 'game.php?page=galaxy&mode=3&galaxy=' .
            $fleetRow['fleet_start_galaxy'] . '&system=' . $fleetRow['fleet_start_system'];

        return UrlHelper::setUrl($link, $coords, '', $fleetType);
    }

    public static function targetLink(array $fleetRow, string $fleetType): string
    {
        $coords = FormatLib::prettyCoords(
            $fleetRow['fleet_end_galaxy'],
            $fleetRow['fleet_end_system'],
            $fleetRow['fleet_end_planet']
        );

        $link = 'game.php?page=galaxy&mode=3&galaxy=' .
            $fleetRow['fleet_end_galaxy'] . '&system=' . $fleetRow['fleet_end_system'];

        return UrlHelper::setUrl($link, $coords, '', $fleetType);
    }

    /**
     * fleetResourcesPopup
     *
     * @param array  $fleetRow  Fleet row
     * @param string $text       Text
     * @param string $fleet_type Fleet type
     *
     * @return void
     */
    public static function fleetResourcesPopup($fleetRow, $text, $fleet_type)
    {
        $total_resources = $fleetRow['fleet_resource_metal'] + $fleetRow['fleet_resource_crystal'] + $fleetRow['fleet_resource_deuterium'];

        if ($total_resources != 0) {
            $popup['fleet_resource_metal'] = FormatLib::prettyNumber($fleetRow['fleet_resource_metal']);
            $popup['fleet_resource_crystal'] = FormatLib::prettyNumber($fleetRow['fleet_resource_crystal']);
            $popup['fleet_resource_deuterium'] = FormatLib::prettyNumber($fleetRow['fleet_resource_deuterium']);

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
            $pop_up = "<a href='#' onmouseover=\"return overlib('" . strtr($resources_popup, ['"' => '']) . "');";
            $pop_up .= '" onmouseout="return nd();" class="' . $fleet_type . '">' . $text . '</a>';
        } else {
            $pop_up = $text . '';
        }

        return $pop_up;
    }

    /**
     * fleetShipsPopup
     *
     * @param array  $fleetRow    Fleet row
     * @param string $text         Text
     * @param string $fleet_type   Fleet type
     * @param array  $current_user Current user
     *
     * @return void
     */
    public static function fleetShipsPopup($fleetRow, $text, $fleet_type, $current_user = '')
    {
        $lang = static::loadLanguage(['game/events', 'game/ships']);
        $objects = Objects::getInstance()->getObjects();

        $ships = self::getFleetShipsArray($fleetRow['fleet_array']);
        $pop_up = "<a href='#' onmouseover=\"return overlib('";
        $pop_up .= '<table width=200>';

        $espionage_tech = OfficiersLib::getMaxEspionage(
            $current_user['research_espionage_technology'],
            $current_user['premium_officier_technocrat']
        );

        if ($espionage_tech < 2 && $fleetRow['fleet_owner'] != $current_user['user_id']) {
            $pop_up .= '<tr><td width=50% align=left><font color=white>' .
            $lang->line('ev_no_fleet_data') . '<font></td></tr>';
        } elseif ($espionage_tech >= 2 && $espionage_tech < 4 && $fleetRow['fleet_owner'] != $current_user['user_id']) {
            $pop_up .= '<tr><td width=50% align=left><font color=white>' .
            $lang->line('ev_aproaching') . $fleetRow['fleet_amount'] .
            $lang->line('ev_ships') . '<font></td></tr>';
        } else {
            if ($fleetRow['fleet_owner'] != $current_user['user_id']) {
                $pop_up .= '<tr><td width=100% align=left><font color=white>' .
                $lang->line('ev_aproaching') . $fleetRow['fleet_amount'] . $lang->line('ev_ships') .
                    ':<font></td></tr>';
            }

            foreach ($ships as $ship => $amount) {
                if ($fleetRow['fleet_owner'] == $current_user['user_id']) {
                    $pop_up .= '<tr><td width=50% align=left><font color=white>' .
                    $lang->language[$objects[$ship]] .
                    ':<font></td><td width=50% align=right><font color=white>' .
                    FormatLib::prettyNumber($amount) . '<font></td></tr>';
                } elseif ($fleetRow['fleet_owner'] != $current_user['user_id']) {
                    if ($espionage_tech >= 4 && $espionage_tech < 8) {
                        $pop_up .= '<tr><td width=50% align=left><font color=white>' .
                        $lang->language[$objects[$ship]] .
                            '<font></td></tr>';
                    } elseif ($espionage_tech >= 8) {
                        $pop_up .= '<tr><td width=50% align=left><font color=white>' .
                        $lang->language[$objects[$ship]] .
                        ':<font></td><td width=50% align=right><font color=white>' .
                        FormatLib::prettyNumber($amount) . '<font></td></tr>';
                    }
                }
            }
        }

        $pop_up .= '</table>';
        $pop_up .= "');\" onmouseout=\"return nd();\" class=\"" . $fleet_type . '">' . $text . '</a>';

        return $pop_up;
    }

    /**
     * enemyLink
     *
     * @param array $fleetRow Fleet row
     *
     * @return string
     */
    public static function enemyLink($fleetRow)
    {
        $url = 'game.php?page=chat&playerId=' . $fleetRow['fleet_owner'];
        $image = Functions::setImage(DPATH . '/img/m.gif');
        $link = $fleetRow['start_planet_user'] . ' ' . UrlHelper::setUrl($url, $image);

        return $link;
    }

    /**
     * flyingFleetsTable
     *
     * @param array  $fleetRow    Fleet row
     * @param string $Status       Status
     * @param int    $Owner        Owner
     * @param string $Label        Label
     * @param string $Record       Record
     * @param string $current_user Current user
     *
     * @return void
     */
    public static function flyingFleetsTable($fleetRow, $Status, $Owner, $Label, $Record, $current_user, $acs_owner = false)
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
        $MissionType = $fleetRow['fleet_mission'];
        if ($MissionType != Missions::MISSILE) {
            $FleetContent = self::fleetShipsPopup(
                $fleetRow,
                $lang->line('ev_fleet'),
                $FleetPrefix . $FleetStyle[$MissionType],
                $current_user
            );
        }

        $StartType = $fleetRow['fleet_start_type'];
        $TargetType = $fleetRow['fleet_end_type'];

        if ($Status != 2) {
            if ($StartType == 1) {
                $StartID = $lang->line('ev_from_the_planet');
            } elseif ($StartType == 3) {
                $StartID = $lang->line('ev_from_the_moon');
            }

            $StartID .= $fleetRow['start_planet_name'] . ' ';
            $StartID .= FleetsLib::startLink($fleetRow, $FleetPrefix . $FleetStyle[$MissionType]);

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

            $TargetID .= $fleetRow['target_planet_name'] . ' ';
            $TargetID .= FleetsLib::targetLink($fleetRow, $FleetPrefix . $FleetStyle[$MissionType]);
        } else {
            if ($StartType == 1) {
                $StartID = $lang->line('ev_to_the_planet');
            } elseif ($StartType == 3) {
                $StartID = $lang->line('ev_the_moon');
            }

            $StartID .= $fleetRow['start_planet_name'] . ' ';
            $StartID .= FleetsLib::startLink($fleetRow, $FleetPrefix . $FleetStyle[$MissionType]);

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

            $TargetID .= $fleetRow['target_planet_name'] . ' ';
            $TargetID .= FleetsLib::targetLink($fleetRow, $FleetPrefix . $FleetStyle[$MissionType]);
        }

        if ($MissionType == Missions::MISSILE) {
            $EventString = $lang->line('ev_missile_attack') .
            ' ( ' . FleetsLib::getFleetShipsArray($fleetRow['fleet_array'])[Defenses::defense_interplanetary_missile] . ' ) ';
            $Time = $fleetRow['fleet_start_time'];
            $Rest = $Time - time();

            $EventString .= $StartID;
            $EventString .= $lang->line('ev_to');
            $EventString .= $TargetID;
            $EventString .= '.';
        } else {
            if ($Owner == true) {
                $EventString = $lang->line('ev_one_of_your');
                $EventString .= $FleetContent;
            } else {
                $EventString = $lang->line('ev_a');
                $EventString .= $FleetContent;
                $EventString .= $lang->line('ev_of');
                $EventString .= self::enemyLink($fleetRow);
            }

            switch ($Status) {
                case 0:
                    $Time = $fleetRow['fleet_start_time'];
                    $Rest = $Time - time();

                    $EventString .= $lang->line('ev_goes');
                    $EventString .= $StartID;
                    $EventString .= $lang->line('ev_toward');
                    $EventString .= $TargetID;
                    $EventString .= $lang->line('ev_with_the_mission_of');
                    break;

                case 1:
                    $Time = $fleetRow['fleet_end_stay'];
                    $Rest = $Time - time();

                    $EventString .= $lang->line('ev_goes');
                    $EventString .= $StartID;
                    $EventString .= $lang->line('ev_to_explore');
                    $EventString .= $TargetID;
                    $EventString .= $lang->line('ev_with_the_mission_of');
                    break;

                case 2:
                    $Time = $fleetRow['fleet_end_time'];
                    $Rest = $Time - time();

                    $EventString .= $lang->line('ev_comming_back');
                    $EventString .= $TargetID;
                    $EventString .= $StartID;
                    $EventString .= $lang->line('ev_with_the_mission_of');
                    break;
            }

            $EventString .= self::fleetResourcesPopup(
                $fleetRow,
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
        return intval($ship_storage + ($ship_storage * 0.05 * $hyperspace_tech_level));
    }

    /**
     * Serialize the fleet array
     *
     * @param array $fleetArray Fleet array
     *
     * @return string
     */
    public static function setFleetShipsArray(array $fleetArray): string
    {
        return serialize($fleetArray);
    }

    /**
     * Un-serialize the fleet array
     *
     * @param string $fleetArray Fleet array
     *
     * @return array
     */
    public static function getFleetShipsArray(string $fleetArray): array
    {
        return unserialize($fleetArray);
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
    private static function loadLanguage(array $requiredLang): CiLang
    {
        $lang = new Language();

        return $lang->loadLang($requiredLang, true);
    }
}
