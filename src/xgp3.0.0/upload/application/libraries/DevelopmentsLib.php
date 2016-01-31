<?php

/**
 * Developments Library.
 *
 * PHP Version 5.5+
 *
 * @category Library
 *
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 *
 * @link     http://www.xgproyect.org
 *
 * @version  3.0.0
 */

namespace application\libraries;

use application\core\XGPCore;

/**
 * DevelopmentsLib Class.
 *
 * @category Classes
 *
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 *
 * @link     http://www.xgproyect.org
 *
 * @version  3.0.0
 */
class DevelopmentsLib extends XGPCore
{
    /**
     * method set_building_page
     * param $element
     * return the page for the current element.
     */
    public static function set_building_page($element)
    {
        $resources_array = array(1,  2,  3,  4, 12, 22, 23, 24);
        $station_array   = array(14, 15, 21, 31, 33, 34, 44);

        if (in_array($element, $resources_array)) {
            return 'resources';
        }

        if (in_array($element, $station_array)) {
            return 'station';
        }

        return 'overview'; // IN CASE THE ELEMENT DOESN'T EXISTS
    }

    /**
     * method max_fields
     * param $current_planet
     * return the max amount of planet fields that can be occupied.
     */
    public static function max_fields(&$current_planet)
    {
        return $current_planet['planet_field_max'] + ($current_planet[parent::$objects->getObjects(33)] * FIELDS_BY_TERRAFORMER);
    }

    /**
     * method development_price
     * param $current_user
     * param $current_planet
     * param $element
     * param $incremental
     * param $destroy
     * return the price of a building or research, also used to destroy buildings, cancel technologies, etc.
     */
    public static function development_price($current_user, $current_planet, $element, $incremental = true, $destroy = false)
    {
        $resource  = parent::$objects->getObjects();
        $pricelist = parent::$objects->getPrice();

        if ($incremental) {
            $level = (isset($current_planet[$resource[$element]])) ? $current_planet[$resource[$element]] : $current_user[$resource[$element]];
        }

        $array = array('metal', 'crystal', 'deuterium', 'energy_max');

        foreach ($array as $res_type) {
            if (isset($pricelist[$element][$res_type])) {
                if ($incremental) {
                    if ($element == 124) {
                        $cost[$res_type] = round(($pricelist[$element][$res_type] * pow($pricelist[$element]['factor'], $level)) / 100) * 100;
                    } else {
                        $cost[$res_type] = floor($pricelist[$element][$res_type] * pow($pricelist[$element]['factor'], $level));
                    }
                } else {
                    $cost[$res_type] = floor($pricelist[$element][$res_type]);
                }

                if ($destroy == true) {
                    $cost[$res_type] = floor($cost[$res_type] / 4);
                }
            }
        }

        return $cost;
    }

    /**
     * method is_development_payable
     * param $current_user
     * param $current_planet
     * param $element
     * param $incremental
     * param $destroy
     * return TRUE is you can afford the building, research, ship or defense, or FALSE if you can't.
     */
    public static function is_development_payable($current_user, $current_planet, $element, $incremental = true, $destroy = false)
    {
        $return = true;
        $costs  = self::development_price($current_user, $current_planet, $element, $incremental, $destroy);

        foreach ($costs as $resource => $amount) {
            if ($costs[$resource] > $current_planet['planet_' . $resource]) {
                $return = false;
            }
        }

        return $return;
    }

    /**
     * method formated_development_price
     * param $current_user
     * param $current_planet
     * param $element
     * param $userfactor
     * param $level
     * return the price of a building or research.
     */
    public static function formated_development_price($current_user, $current_planet, $element, $userfactor = true, $level = false)
    {
        $resource  = parent::$objects->getObjects();
        $pricelist = parent::$objects->getPrice();
        $lang      = parent::$lang;

        if ($userfactor && ($level === false)) {
            $level = (isset($current_planet[$resource[$element]])) ? $current_planet[$resource[$element]] : $current_user[$resource[$element]];
        }

        $is_buyeable = true;
        $text        = $lang['fgp_require'];
        $array       = array(
                                    'metal'      => $lang['Metal'],
                                    'crystal'    => $lang['Crystal'],
                                    'deuterium'  => $lang['Deuterium'],
                                    'energy_max' => $lang['Energy'],
                                );

        foreach ($array as $res_type => $ResTitle) {
            if (isset($pricelist[$element][$res_type]) && $pricelist[$element][$res_type] != 0) {
                $text .= $ResTitle . ': ';

                if ($userfactor) {
                    if ($element == 124) {
                        $cost = round(($pricelist[$element][$res_type] * pow($pricelist[$element]['factor'], $level)) / 100) * 100;
                    } else {
                        $cost = floor($pricelist[$element][$res_type] * pow($pricelist[$element]['factor'], $level));
                    }
                } else {
                    $cost = floor($pricelist[$element][$res_type]);
                }

                if ($cost > $current_planet['planet_' . $res_type]) {
                    $text .= '<b style="color:red;"> <t title="-' . FormatLib::pretty_number($cost - $current_planet['planet_' . $res_type]) . '">';
                    $text .= '<span class="noresources">' . FormatLib::pretty_number($cost) . '</span></t></b> ';
                    $is_buyeable = false;
                } else {
                    $text .= '<b style="color:lime;">' . FormatLib::pretty_number($cost) . '</b> ';
                }
            }
        }

        return $text;
    }

    /**
     * method development_time
     * param $current_user
     * param $current_planet
     * param $element
     * param $level
     * param $total_lab_level
     * return the development time for buildings, research, ships and defenses.
     */
    public static function development_time($current_user, $current_planet, $element, $level = false, $total_lab_level = 0)
    {
        $resource  = parent::$objects->getObjects();
        $pricelist = parent::$objects->getPrice();
        $reslist   = parent::$objects->getObjectsList();

        // IF ROUTINE FIX BY JSTAR
        if ($level === false) {
            $level = (isset($current_planet[$resource[$element]])) ? $current_planet[$resource[$element]] : $current_user[$resource[$element]];
        }

        if (in_array($element, $reslist['build'])) {
            $cost_metal   = floor($pricelist[$element]['metal']   * pow($pricelist[$element]['factor'], $level));
            $cost_crystal = floor($pricelist[$element]['crystal'] * pow($pricelist[$element]['factor'], $level));
            $time         = (($cost_crystal + $cost_metal) / FunctionsLib::read_config('game_speed')) * (1 / ($current_planet[$resource['14']] + 1)) * pow(0.5, $current_planet[$resource['15']]);
            $time         = floor(($time * 60 * 60));
        } elseif (in_array($element, $reslist['tech'])) {
            $cost_metal   = floor($pricelist[$element]['metal']   * pow($pricelist[$element]['factor'], $level));
            $cost_crystal = floor($pricelist[$element]['crystal'] * pow($pricelist[$element]['factor'], $level));
            $intergal_lab = $current_user[$resource[123]];

            if ($intergal_lab < 1) {
                $lablevel = $current_planet[$resource['31']];
            } else {
                $lablevel = $total_lab_level;
            }

            $time = (($cost_metal + $cost_crystal) / FunctionsLib::read_config('game_speed')) / (($lablevel + 1) * 2);
            $time = floor(($time * 60 * 60) * (1 - ((OfficiersLib::isOfficierActive($current_user['premium_officier_technocrat'])) ? TECHNOCRATE_SPEED : 0)));
        } elseif (in_array($element, $reslist['defense'])) {
            $time = (($pricelist[$element]['metal'] + $pricelist[$element]['crystal']) / FunctionsLib::read_config('game_speed')) * (1 / ($current_planet[$resource['21'] ] + 1)) * pow(1 / 2, $current_planet[$resource['15']]);
            $time = floor(($time * 60 * 60));
        } elseif (in_array($element, $reslist['fleet'])) {
            $time = (($pricelist[$element]['metal'] + $pricelist[$element]['crystal']) / FunctionsLib::read_config('game_speed')) * (1 / ($current_planet[$resource['21']] + 1)) * pow(1 / 2, $current_planet[$resource['15']]);
            $time = floor(($time * 60 * 60));
        }

        if ($time <= 0) {
            $time = 1;
        }

        return $time;
    }

    /**
     * method formated_development_time
     * param $time
     * return the formated time transcript.
     */
    public static function formated_development_time($time)
    {
        return '<br>' . parent::$lang['fgf_time'] . FormatLib::pretty_time($time);
    }

    /**
     * method is_development_allowed
     * param $current_user
     * param $current_planet
     * param $element
     * return if is possible develop a new building, research, ship or defense.
     */
    public static function is_development_allowed($current_user, $current_planet, $element)
    {
        $resource     = parent::$objects->getObjects();
        $requeriments = parent::$objects->getRelations();

        if (isset($requeriments[$element])) {
            $enabled = true;

            foreach ($requeriments[$element] as $ReqElement => $EleLevel) {
                if (@$current_user[$resource[$ReqElement]] && $current_user[$resource[$ReqElement]] >= $EleLevel) {
                    //BREAK
                } elseif (isset($current_planet[$resource[$ReqElement]]) && $current_planet[$resource[$ReqElement]] >= $EleLevel) {
                    $enabled = true;
                } else {
                    return false;
                }
            }

            return $enabled;
        } else {
            return true;
        }
    }

    /**
     * method current_building
     * param $call_program
     * param $element_id
     * return if is possible develop a new building, research, ship or defense.
     */
    public static function current_building($call_program, $element_id = 0)
    {
        $parse                 = parent::$lang;
        $parse['call_program'] = $call_program;
        $parse['current_page'] = ($element_id != 0) ? self::set_building_page($element_id) : $call_program;

        return parent::$page->parse_template(parent::$page->get_template('buildings/buildings_buildlist_script'), $parse);
    }

    /**
     * method set_first_element
     * param $current_planet
     * param $current_user
     * return set the next element in the buildings queue to the top.
     */
    public static function set_first_element(&$current_planet, $current_user)
    {
        $lang     = parent::$lang;
        $resource = parent::$objects->getObjects();

        if ($current_planet['planet_b_building'] == 0) {
            $current_queue = $current_planet['planet_b_building_id'];

            if ($current_queue != 0) {
                $queue_array = explode(';', $current_queue);
                $loop        = true;

                while ($loop == true) {
                    $list_id_array  = explode(',', $queue_array[0]);
                    $element        = $list_id_array[0];
                    $level          = $list_id_array[1];
                    $build_time     = $list_id_array[2];
                    $build_end_time = $list_id_array[3];
                    $build_mode     = $list_id_array[4];
                    $no_more_level  = false;

                    if ($build_mode == 'destroy') {
                        $for_destroy = true;
                    } else {
                        $for_destroy = false;
                    }

                    $is_payable = self::is_development_payable($current_user, $current_planet, $element, true, $for_destroy);

                    if ($for_destroy) {
                        if ($current_planet[$resource[$element]] == 0) {
                            $is_payable    = false;
                            $no_more_level = true;
                        }
                    }

                    if ($is_payable == true) {
                        $price = self::development_price($current_user, $current_planet, $element, true, $for_destroy);
                        $current_planet['planet_metal']        -= $price['metal'];
                        $current_planet['planet_crystal']      -= $price['crystal'];
                        $current_planet['planet_deuterium']    -= $price['deuterium'];
                        $current_time   = time();
                        $build_end_time = $build_end_time;
                        $new_queue      = implode(';', $queue_array);

                        if ($new_queue == '') {
                            $new_queue = '0';
                        }

                        $loop = false;
                    } else {
                        $element_name = $lang['tech'][$element];

                        if ($no_more_level == true) {
                            $message = sprintf($lang['sys_nomore_level'], $element_name);
                        } else {
                            $price   = self::development_price($current_user, $current_planet, $element, true, $for_destroy);
                            $message = sprintf(
                                                        $lang['sys_notenough_money'],
                                                        $element_name,
                                                        FormatLib::pretty_number($current_planet['planet_metal']), $lang['Metal'],
                                                        FormatLib::pretty_number($current_planet['planet_crystal']), $lang['Crystal'],
                                                        FormatLib::pretty_number($current_planet['planet_deuterium']), $lang['Deuterium'],
                                                        FormatLib::pretty_number($price['metal']), $lang['Metal'],
                                                        FormatLib::pretty_number($price['crystal']), $lang['Crystal'],
                                                        FormatLib::pretty_number($price['deuterium']), $lang['Deuterium']
                                                    );
                        }

                        FunctionsLib::send_message($current_user['user_id'], '', '', 5, $lang['sys_buildlist'], $lang['sys_buildlist_fail'], $message);

                        array_shift($queue_array);

                        foreach ($queue_array as $num => $info) {
                            $fix_ele           = explode(',', $info);
                            $fix_ele[3]        = $fix_ele[3] - $build_time;
                            $queue_array[$num] = implode(',', $fix_ele);
                        }

                        $actual_count = count($queue_array);

                        if ($actual_count == 0) {
                            $build_end_time = '0';
                            $new_queue      = '0';
                            $loop           = false;
                        }
                    }
                }
            } else {
                $build_end_time = '0';
                $new_queue      = '0';
            }

            $current_planet['planet_b_building']    = $build_end_time;
            $current_planet['planet_b_building_id'] = $new_queue;

            parent::$db->query('UPDATE ' . PLANETS . " SET
									`planet_metal` = '" . $current_planet['planet_metal'] . "',
									`planet_crystal` = '" . $current_planet['planet_crystal'] . "',
									`planet_deuterium` = '" . $current_planet['planet_deuterium'] . "',
									`planet_b_building` = '" . $current_planet['planet_b_building'] . "',
									`planet_b_building_id` = '" . $current_planet['planet_b_building_id'] . "'
									WHERE `planet_id` = '" . $current_planet['planet_id'] . "';");
        }

        return;
    }

    /**
     * method set_level_format
     * param $level
     * param $element
     * return return the level with format.
     */
    public static function set_level_format($level, $element = '', $current_user = '')
    {
        $return_level = '';

        // check if is base level
        if ($level != 0) {
            $return_level = ' (' . parent::$lang['bd_lvl'] . ' ' . $level . ')';
        }

        // check a commander plus
        switch ($element) {
            case 106:

                if (OfficiersLib::isOfficierActive($current_user['premium_officier_technocrat'])) {
                    $return_level    .= FormatLib::strong_text(FormatLib::color_green(' +' . TECHNOCRATE_SPY . parent::$lang['bd_spy']));
                }

            break;

            case 108:

                if (OfficiersLib::isOfficierActive($current_user['premium_officier_admiral'])) {
                    $return_level    .= FormatLib::strong_text(FormatLib::color_green(' +' . AMIRAL . parent::$lang['bd_commander']));
                }

            break;
        }

        return $return_level;
    }

    /**
     * method is_lab_working
     * param (array) $current_user
     * return (bool) true if lab is working.
     */
    public static function is_lab_working($current_user)
    {
        return  $current_user['research_current_research'] != 0;
    }

    /**
     * method is_shipyard_working
     * param (array) $current_planet
     * return (bool) true if shipyard is working.
     */
    public static function is_shipyard_working($current_planet)
    {
        return  $current_planet['planet_b_hangar'] != 0;
    }
}

/* end of DevelopmentsLib.php */
