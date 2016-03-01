<?php
/**
 * Developments Library
 *
 * PHP Version 5.5+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */

namespace application\libraries;

use application\core\XGPCore;

/**
 * DevelopmentsLib Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class DevelopmentsLib extends XGPCore
{
    /**
     * setBuildingPage
     *
     * @param int $element Element
     *
     * @return string
     */
    public static function setBuildingPage($element)
    {
        $resources_array    = [1, 2, 3, 4, 12, 22, 23, 24];
        $station_array      = [14, 15, 21, 31, 33, 34, 44];

        if (in_array($element, $resources_array)) {

            return 'resources';
        }

        if (in_array($element, $station_array)) {

            return 'station';
        }

        // IN CASE THE ELEMENT DOESN'T EXISTS
        return 'overview';
    }

    /**
     * maxFields
     *
     * @param array $current_planet Current planet
     *
     * @return void
     */
    public static function maxFields(&$current_planet)
    {
        return $current_planet['planet_field_max'] + (
            $current_planet[parent::$objects->getObjects(33)] * FIELDS_BY_TERRAFORMER
        );
    }

    /**
     * developmentPrice
     *
     * @param array   $current_user   Current user
     * @param array   $current_planet Current planet
     * @param string  $element        Element
     * @param boolean $incremental    Incremental
     * @param boolean $destroy        Destroy
     *
     * @return int
     */
    public static function developmentPrice(
        $current_user,
        $current_planet,
        $element,
        $incremental = true,
        $destroy = false
    ) {
        $resource   = parent::$objects->getObjects();
        $pricelist  = parent::$objects->getPrice();

        if ($incremental) {
            $level = (isset($current_planet[$resource[$element]]))
                ? $current_planet[$resource[$element]] : $current_user[$resource[$element]];
        }

        $array  = ['metal', 'crystal', 'deuterium', 'energy_max'];

        foreach ($array as $res_type) {

            if (isset($pricelist[$element][$res_type])) {

                if ($incremental) {

                    if ($element == 124) {

                        $cost[$res_type] = round(
                            ($pricelist[$element][$res_type] * pow($pricelist[$element]['factor'], $level)) / 100
                        ) * 100;
                    } else {

                        $cost[$res_type] = floor(
                            $pricelist[$element][$res_type] * pow($pricelist[$element]['factor'], $level)
                        );
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
     * isDevelopmentPayable
     *
     * @param array   $current_user   Current user
     * @param array   $current_planet Current planet
     * @param string  $element        Element
     * @param boolean $incremental    Incremental
     * @param boolean $destroy        Destroy
     *
     * @return boolean
     */
    public static function isDevelopmentPayable(
        $current_user,
        $current_planet,
        $element,
        $incremental = true,
        $destroy = false
    ) {

        $return = true;
        $costs  = self::developmentPrice($current_user, $current_planet, $element, $incremental, $destroy);

        foreach ($costs as $resource => $amount) {

            if ($costs[$resource] > $current_planet['planet_' . $resource]) {

                $return = false;
            }
        }

        return $return;
    }

    /**
     * formatedDevelopmentPrice
     *
     * @param array   $current_user   Current user
     * @param array   $current_planet Current planet
     * @param string  $element        Element
     * @param boolean $userfactor     User factor
     * @param boolean $level          Level
     *
     * @return string
     */
    public static function formatedDevelopmentPrice(
        $current_user,
        $current_planet,
        $element,
        $userfactor = true,
        $level = false
    ) {
        $resource   = parent::$objects->getObjects();
        $pricelist  = parent::$objects->getPrice();
        $lang       = parent::$lang;

        if ($userfactor && ($level === false )) {

            $level  = (isset($current_planet[$resource[$element]]))
                ? $current_planet[$resource[$element]] : $current_user[$resource[$element]];
        }

        $is_buyeable    = true;
        $text           = $lang['fgp_require'];
        $array          = [
            'metal'         => $lang['Metal'],
            'crystal'       => $lang['Crystal'],
            'deuterium'     => $lang['Deuterium'],
            'energy_max'    => $lang['Energy']
        ];

        foreach ($array as $res_type => $ResTitle) {

            if (isset($pricelist[$element][$res_type]) && $pricelist[$element][$res_type] != 0) {

                $text .= $ResTitle . ": ";

                if ($userfactor) {

                    if ($element == 124) {

                        $cost = round(
                            ($pricelist[$element][$res_type] * pow($pricelist[$element]['factor'], $level)) / 100
                        ) * 100;
                    } else {

                        $cost = floor(
                            $pricelist[$element][$res_type] * pow($pricelist[$element]['factor'], $level)
                        );
                    }
                } else {

                    $cost = floor($pricelist[$element][$res_type]);
                }

                if ($cost > $current_planet['planet_' . $res_type]) {

                    $text .= "<b style=\"color:red;\"> <t title=\"-" . FormatLib::prettyNumber(
                        $cost - $current_planet['planet_' . $res_type]
                    ) . "\">";
                    $text .= "<span class=\"noresources\">" . FormatLib::prettyNumber($cost) . "</span></t></b> ";
                    $is_buyeable = false;
                } else {

                    $text .= "<b style=\"color:lime;\">" . FormatLib::prettyNumber($cost) . "</b> ";
                }
            }
        }

        return $text;
    }

    /**
     * developmentTime
     *
     * @param array   $current_user    Current user
     * @param array   $current_planet  Current planet
     * @param string  $element         Element
     * @param boolean $level           Level
     * @param int     $total_lab_level Total lab level
     *
     * @return int
     */
    public static function developmentTime(
        $current_user,
        $current_planet,
        $element,
        $level = false,
        $total_lab_level = 0
    ) {
        $resource   = parent::$objects->getObjects();
        $pricelist  = parent::$objects->getPrice();
        $reslist    = parent::$objects->getObjectsList();

        // IF ROUTINE FIX BY JSTAR
        if ($level === false) {

            $level  = (isset($current_planet[$resource[$element]]))
                ? $current_planet[$resource[$element]] : $current_user[$resource[$element]];
        }

        if (in_array($element, $reslist['build'])) {

            $cost_metal     = floor($pricelist[$element]['metal'] * pow($pricelist[$element]['factor'], $level));
            $cost_crystal   = floor($pricelist[$element]['crystal'] * pow($pricelist[$element]['factor'], $level));
            $time           = (($cost_crystal + $cost_metal) / FunctionsLib::readConfig('game_speed'))
                * (1 / ( $current_planet[$resource['14']] + 1))
                * pow(0.5, $current_planet[$resource['15']]);
            $time           = floor(( $time * 60 * 60));
        } elseif (in_array($element, $reslist['tech'])) {

            $cost_metal = floor($pricelist[$element]['metal'] * pow($pricelist[$element]['factor'], $level));
            $cost_crystal   = floor($pricelist[$element]['crystal'] * pow($pricelist[$element]['factor'], $level));
            $intergal_lab   = $current_user[$resource[123]];

            if ($intergal_lab < 1) {

                $lablevel   = $current_planet[$resource['31']];
            } else {

                $lablevel   = $total_lab_level;
            }

            $time   = (($cost_metal + $cost_crystal)
                / FunctionsLib::readConfig('game_speed')) / (($lablevel + 1) * 2);
            $time   = floor(
                ($time * 60 * 60) * (1 - ((OfficiersLib::isOfficierActive(
                    $current_user['premium_officier_technocrat']
                )) ? TECHNOCRATE_SPEED : 0))
            );
        } elseif (in_array($element, $reslist['defense'])) {

            $time   = (($pricelist[$element]['metal'] + $pricelist[$element]['crystal'] )
                / FunctionsLib::readConfig('game_speed'))
                * (1 / ($current_planet[$resource['21']] + 1))
                * pow(1 / 2, $current_planet[$resource['15']]);
            $time   = floor(($time * 60 * 60));
        } elseif (in_array($element, $reslist['fleet'])) {

            $time   = (($pricelist[$element]['metal'] + $pricelist[$element]['crystal'])
                / FunctionsLib::readConfig('game_speed'))
                * (1 / ($current_planet[$resource['21']] + 1))
                * pow(1 / 2, $current_planet[$resource['15']]);
            $time   = floor(($time * 60 * 60));
        }

        if ($time <= 0) {

            $time   = 1;
        }

        return $time;
    }

    /**
     * formatedDevelopmentTime
     *
     * @param int $time Time
     *
     * @return string
     */
    public static function formatedDevelopmentTime($time)
    {
        return "<br>" . parent::$lang['fgf_time'] . FormatLib::prettyTime($time);
    }

    /**
     * isDevelopmentAllowed
     *
     * @param array $current_user   Current user
     * @param array $current_planet Current planet
     * @param string $element       Element
     *
     * @return boolean
     */
    public static function isDevelopmentAllowed($current_user, $current_planet, $element)
    {
        $resource       = parent::$objects->getObjects();
        $requeriments   = parent::$objects->getRelations();

        if (isset($requeriments[$element])) {

            $enabled    = true;

            foreach ($requeriments[$element] as $ReqElement => $EleLevel) {

                if (isset($current_user[$resource[$ReqElement]])
                    && $current_user[$resource[$ReqElement]] >= $EleLevel) {

                    $enabled    = true;
                } elseif (isset($current_planet[$resource[$ReqElement]])
                    && $current_planet[$resource[$ReqElement]] >= $EleLevel) {

                    $enabled    = true;
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
     * currentBuilding
     *
     * @param string $call_program Call program
     * @param int    $element_id   Element ID
     *
     * @return string
     */
    public static function currentBuilding($call_program, $element_id = 0)
    {
        $parse = parent::$lang;

        $parse['call_program'] = $call_program;
        $parse['current_page'] = ($element_id != 0) ? DevelopmentsLib::setBuildingPage($element_id) : $call_program;

        return parent::$page->parseTemplate(parent::$page->getTemplate('buildings/buildings_buildlist_script'), $parse);
    }

    /**
     * setFirstElement
     *
     * @param array $current_planet Current planet
     * @param array $current_user   Current user
     *
     * @return void
     */
    public static function setFirstElement(&$current_planet, $current_user)
    {
        $lang       = parent::$lang;
        $resource   = parent::$objects->getObjects();

        if ($current_planet['planet_b_building'] == 0) {

            $current_queue = $current_planet['planet_b_building_id'];

            if ($current_queue != 0) {

                $queue_array    = explode(";", $current_queue);
                $loop           = true;

                while ($loop == true) {

                    $list_id_array  = explode(",", $queue_array[0]);
                    $element        = $list_id_array[0];
                    $level          = $list_id_array[1];
                    $build_time     = $list_id_array[2];
                    $build_end_time = $list_id_array[3];
                    $build_mode     = $list_id_array[4];
                    $no_more_level  = false;

                    if ($build_mode == 'destroy') {

                        $for_destroy    = true;
                    } else {

                        $for_destroy    = false;
                    }

                    $is_payable = self::isDevelopmentPayable(
                        $current_user,
                        $current_planet,
                        $element,
                        true,
                        $for_destroy
                    );

                    if ($for_destroy) {

                        if ($current_planet[$resource[$element]] == 0) {

                            $is_payable     = false;
                            $no_more_level  = true;
                        }
                    }

                    if ($is_payable == true) {

                        $price  = self::developmentPrice($current_user, $current_planet, $element, true, $for_destroy);
                        
                        $current_planet['planet_metal']     -= $price['metal'];
                        $current_planet['planet_crystal']   -= $price['crystal'];
                        $current_planet['planet_deuterium'] -= $price['deuterium'];
                        
                        $current_time   = time();
                        $build_end_time = $build_end_time;
                        $new_queue      = implode(";", $queue_array);

                        if ($new_queue == '') {

                            $new_queue  = '0';
                        }

                        $loop   = false;
                    } else {

                        $element_name   = $lang['tech'][$element];

                        if ($no_more_level == true) {

                            $message    = sprintf($lang['sys_nomore_level'], $element_name);
                        } else {

                            $price      = self::developmentPrice(
                                $current_user,
                                $current_planet,
                                $element,
                                true,
                                $for_destroy
                            );

                            $message    = sprintf(
                                $lang['sys_notenough_money'],
                                $element_name,
                                FormatLib::prettyNumber($current_planet['planet_metal']),
                                $lang['Metal'],
                                FormatLib::prettyNumber($current_planet['planet_crystal']),
                                $lang['Crystal'],
                                FormatLib::prettyNumber($current_planet['planet_deuterium']),
                                $lang['Deuterium'],
                                FormatLib::prettyNumber($price['metal']),
                                $lang['Metal'],
                                FormatLib::prettyNumber($price['crystal']),
                                $lang['Crystal'],
                                FormatLib::prettyNumber($price['deuterium']),
                                $lang['Deuterium']
                            );
                        }

                        FunctionsLib::sendMessage(
                            $current_user['user_id'],
                            '',
                            '',
                            5,
                            $lang['sys_buildlist'],
                            $lang['sys_buildlist_fail'],
                            $message
                        );

                        array_shift($queue_array);

                        foreach ($queue_array as $num => $info) {

                            $fix_ele            = explode(",", $info);
                            $fix_ele[3]         = $fix_ele[3] - $build_time;
                            $queue_array[$num]  = implode(",", $fix_ele);
                        }

                        $actual_count   = count($queue_array);

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

            parent::$db->query(
                "UPDATE " . PLANETS . " SET
                `planet_metal` = '" . $current_planet['planet_metal'] . "',
                `planet_crystal` = '" . $current_planet['planet_crystal'] . "',
                `planet_deuterium` = '" . $current_planet['planet_deuterium'] . "',
                `planet_b_building` = '" . $current_planet['planet_b_building'] . "',
                `planet_b_building_id` = '" . $current_planet['planet_b_building_id'] . "'
                WHERE `planet_id` = '" . $current_planet['planet_id'] . "';"
            );
        }
        return;
    }

    /**
     * setLevelFormat
     *
     * @param int    $level        Level
     * @param string $element      Element
     * @param string $current_user Current user
     *
     * @return void
     */
    public static function setLevelFormat($level, $element = '', $current_user = '')
    {
        $return_level   = '';

        // check if is base level
        if ($level != 0) {

            $return_level   = ' (' . parent::$lang['bd_lvl'] . ' ' . $level . ')';
        }

        // check a commander plus
        switch ($element) {
            case 106:
                if (OfficiersLib::isOfficierActive($current_user['premium_officier_technocrat'])) {

                    $return_level .= FormatLib::strongText(
                        FormatLib::colorGreen(' +' . TECHNOCRATE_SPY . parent::$lang['bd_spy'])
                    );
                }

                break;

            case 108:
                if (OfficiersLib::isOfficierActive($current_user['premium_officier_admiral'])) {

                    $return_level .= FormatLib::strongText(
                        FormatLib::colorGreen(' +' . AMIRAL . parent::$lang['bd_commander'])
                    );
                }

                break;
        }

        return $return_level;
    }

    /**
     * isLabWorking
     *
     * @param array $current_user Current user
     *
     * @return boolean
     */
    public static function isLabWorking($current_user)
    {
        return ($current_user['research_current_research'] != 0);
    }

    /**
     * isShipyardWorking
     *
     * @param array $current_planet Current planet
     *
     * @return boolean
     */
    public static function isShipyardWorking($current_planet)
    {
        return ($current_planet['planet_b_hangar'] != 0);
    }
}

/* end of DevelopmentsLib.php */
