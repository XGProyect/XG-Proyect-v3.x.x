<?php
/**
 * Production Library
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\libraries;

/**
 * ProductionLib Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class ProductionLib
{
    /**
     * maxStorable
     *
     * @param int $storage_level Storage level
     *
     * @return void
     */
    public static function maxStorable($storage_level)
    {
        return (int) (2.5 * pow(M_E, (20 * ($storage_level) / 33))) * 5000;
    }

    /**
     * maxProduction
     *
     * @param int $max_energy  Max energy
     * @param int $energy_used Energy used
     *
     * @return int
     */
    public static function maxProduction($max_energy, $energy_used)
    {
        if (($max_energy == 0) && ($energy_used > 0)) {
            $percentage = 0;
        } elseif (($max_energy > 0) && (($energy_used + $max_energy) < 0)) {
            $percentage = floor(($max_energy) / ($energy_used * -1) * 100);
        } else {
            $percentage = 100;
        }

        if ($percentage > 100) {
            $percentage = 100;
        }

        return $percentage;
    }

    /**
     * productionAmount
     *
     * @param int     $production Production amoint
     * @param int     $boost      Boost by officiers
     * @param int     $mult       Multiplier based on game speed
     * @param boolean $is_energy  Is energy?
     *
     * @return int
     */
    public static function productionAmount($production, $boost, $mult = 0, $is_energy = false)
    {
        if ($is_energy) {
            return floor($production * $boost);
        } else {
            return floor($production * $mult * $boost);
        }
    }

    /**
     * currentProduction
     *
     * @param int $resource       Resource amount
     * @param int $max_production Max production
     *
     * @return int
     */
    public static function currentProduction($resource, $max_production)
    {
        return ($resource * 0.01 * $max_production);
    }
}
