<?php
/**
 * DevelopmentsLib.php
 *
 * @author   XG Proyect Team
 * @license  https://www.xgproyect.org XG Proyect
 * @link     https://www.xgproyect.org
 * @version  3.2.0
 */
namespace application\libraries;

use application\core\enumerators\ResearchEnumerator as Research;
use application\core\Template;
use application\core\XGPCore;
use application\libraries\DevelopmentsLib;
use application\libraries\FormatLib;
use application\libraries\Formulas;
use application\libraries\FunctionsLib;
use application\libraries\OfficiersLib;

/**
 * DevelopmentsLib Class
 */
class DevelopmentsLib extends XGPCore
{
    /**
     * Return a new instance of Template
     *
     * @return Template
     */
    public static function getTemplate(): Template
    {
        return new Template;
    }

    /**
     * setBuildingPage
     *
     * @param int $element Element
     *
     * @return string
     */
    public static function setBuildingPage($element)
    {
        $resources_array = [1, 2, 3, 4, 12, 22, 23, 24];
        $station_array = [14, 15, 21, 31, 33, 34, 44];

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
    public static function maxFields($current_planet)
    {
        return $current_planet['planet_field_max'] + (
            $current_planet[parent::$objects->getObjects(33)] * FIELDS_BY_TERRAFORMER
        );
    }

    /**
     * Get the development price
     *
     * @param array $current_user
     * @param array $current_planet
     * @param integer $element
     * @param boolean $incremental
     * @param boolean $destroy
     * @return void
     */
    public static function developmentPrice(array $current_user, array $current_planet, int $element, $incremental = true, $destroy = false)
    {
        $resource = parent::$objects->getObjects();
        $pricelist = parent::$objects->getPrice();

        if ($incremental) {
            $level = (isset($current_planet[$resource[$element]])) ? $current_planet[$resource[$element]] : $current_user[$resource[$element]];
        }

        foreach (['metal', 'crystal', 'deuterium', 'energy_max'] as $type) {
            if (isset($pricelist[$element][$type])) {
                if ($incremental) {
                    $cost[$type] = Formulas::getDevelopmentCost($pricelist[$element][$type], $pricelist[$element]['factor'], $level);
                } else {
                    $cost[$type] = floor($pricelist[$element][$type]);
                }

                if ($destroy == true) {
                    $cost[$type] = Formulas::getTearDownCost(
                        $pricelist[$element][$type],
                        $pricelist[$element]['factor'],
                        $level,
                        $current_user[$resource[Research::research_ionic_technology]]
                    );
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
    public static function isDevelopmentPayable($current_user, $current_planet, $element, $incremental = true, $destroy = false)
    {
        $return = true;
        $costs = self::developmentPrice($current_user, $current_planet, $element, $incremental, $destroy);

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
    public static function formatedDevelopmentPrice($current_user, $current_planet, $element, $lang, $userfactor = true, $level = false)
    {
        $resource = parent::$objects->getObjects();
        $pricelist = parent::$objects->getPrice();

        if ($userfactor && ($level === false)) {
            $level = (isset($current_planet[$resource[$element]])) ? $current_planet[$resource[$element]] : $current_user[$resource[$element]];
        }

        $is_buyeable = true;
        $text = $lang->line('require');
        $array = [
            'metal' => $lang->line('metal'),
            'crystal' => $lang->line('crystal'),
            'deuterium' => $lang->line('deuterium'),
            'energy_max' => $lang->line('energy'),
        ];

        foreach ($array as $res_type => $ResTitle) {
            if (isset($pricelist[$element][$res_type]) && $pricelist[$element][$res_type] != 0) {
                $text .= $ResTitle . ": ";

                if ($userfactor) {
                    $cost = Formulas::getDevelopmentCost($pricelist[$element][$res_type], $pricelist[$element]['factor'], $level);
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
    public static function developmentTime($current_user, $current_planet, $element, $level = false, $total_lab_level = 0)
    {
        $resource = parent::$objects->getObjects();
        $pricelist = parent::$objects->getPrice();
        $reslist = parent::$objects->getObjectsList();

        // IF ROUTINE FIX BY JSTAR
        if ($level === false) {
            $level = (isset($current_planet[$resource[$element]])) ? $current_planet[$resource[$element]] : $current_user[$resource[$element]];
        }

        if (in_array($element, $reslist['build'])) {
            $cost_metal = Formulas::getDevelopmentCost($pricelist[$element]['metal'], $pricelist[$element]['factor'], $level);
            $cost_crystal = Formulas::getDevelopmentCost($pricelist[$element]['crystal'], $pricelist[$element]['factor'], $level);
            $time = Formulas::getBuildingTime($cost_metal, $cost_crystal, $element, $current_planet[$resource['14']], $current_planet[$resource['15']], $level);
        } elseif (in_array($element, $reslist['tech'])) {
            $cost_metal = Formulas::getDevelopmentCost($pricelist[$element]['metal'], $pricelist[$element]['factor'], $level);
            $cost_crystal = Formulas::getDevelopmentCost($pricelist[$element]['crystal'], $pricelist[$element]['factor'], $level);
            $intergal_lab = $current_user[$resource[123]];

            if ($intergal_lab < 1) {
                $lablevel = $current_planet[$resource['31']];
            } else {
                $lablevel = $total_lab_level;
            }

            $time = (($cost_metal + $cost_crystal) / FunctionsLib::readConfig('game_speed')) / (($lablevel + 1) * 2);
            $time = floor(
                ($time * 3600) * (1 - ((OfficiersLib::isOfficierActive(
                    $current_user['premium_officier_technocrat']
                )) ? TECHNOCRATE_SPEED : 0))
            );
        } elseif (in_array($element, $reslist['defense']) or in_array($element, $reslist['fleet'])) {
            $time = Formulas::getShipyardProductionTime(
                $pricelist[$element]['metal'],
                $pricelist[$element]['crystal'],
                $element,
                $current_planet[$resource['21']],
                $current_planet[$resource['15']]
            );
        }

        return ($time < 1 ? 1 : $time);
    }

    /**
     * formatedDevelopmentTime
     *
     * @param int $time Time
     *
     * @return string
     */
    public static function formatedDevelopmentTime($time, $lang_line)
    {
        return "<br>" . $lang_line . FormatLib::prettyTime($time);
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
        $resource = parent::$objects->getObjects();
        $requeriments = parent::$objects->getRelations();

        if (isset($requeriments[$element])) {
            $enabled = true;

            foreach ($requeriments[$element] as $ReqElement => $EleLevel) {
                if (isset($current_user[$resource[$ReqElement]]) && $current_user[$resource[$ReqElement]] >= $EleLevel) {
                    $enabled = true;
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
     * currentBuilding
     *
     * @param string $call_program Call program
     * @param int    $element_id   Element ID
     *
     * @return string
     */
    public static function currentBuilding($call_program, $lang, $element_id = 0)
    {
        $parse['call_program'] = $call_program;
        $parse['current_page'] = ($element_id != 0) ? DevelopmentsLib::setBuildingPage($element_id) : $call_program;

        return self::getTemplate()->set(
            'buildings/buildings_buildlist_script',
            array_merge($parse, $lang)
        );
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
    public static function setLevelFormat($level, $lang, $element = '', $current_user = '')
    {
        $return_level = '';

        // check if is base level
        if ($level != 0) {
            $return_level = ' (' . $lang->line('level') . $level . ')';
        }

        // check a commander plus
        switch ($element) {
            case 106:
                if (OfficiersLib::isOfficierActive($current_user['premium_officier_technocrat'])) {
                    $return_level .= FormatLib::strongText(
                        FormatLib::colorGreen(' +' . TECHNOCRATE_SPY . $lang->line('re_spy'))
                    );
                }

                break;

            case 108:
                if (OfficiersLib::isOfficierActive($current_user['premium_officier_admiral'])) {
                    $return_level .= FormatLib::strongText(
                        FormatLib::colorGreen(' +' . AMIRAL . $lang->line('re_commander'))
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

    /**
     * Check if there are any fields available
     *
     * @param type $current_planet
     *
     * @return boolean
     */
    public static function areFieldsAvailable($current_planet)
    {
        return ($current_planet['planet_field_current'] < self::maxFields($current_planet));
    }
}
