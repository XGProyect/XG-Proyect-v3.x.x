<?php
/**
 * Production Library
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
     * method max_resource
     * param $storage_level
     * return max storage capacity
     */
    public static function max_storable($storage_level)
    {
        return (int)(2.5 * pow(M_E, (20 * ($storage_level) / 33))) * 5000;
    }

    /**
     * method max_production
     * param1 $max_energy
     * param2 $energy_used
     * return validated production factor (0%-100%)
     */
    public static function max_production($max_energy, $energy_used)
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
     * method production_amount
     * param1 $production
     * param2 $boost
     * return production amount
     */
    public static function production_amount($production, $boost, $is_energy = false)
    {
        if ($is_energy) {

            return floor($production * $boost);
        } else {

            return floor($production * Functions_Lib::read_config('resource_multiplier') * $boost);
        }
    }

    /**
     * method current_production
     * param $resource
     * return amount of resource production
     */
    public static function current_production($resource, $max_production)
    {
        return ($resource * 0.01 * $max_production);
    }
}
/* end of ProductionLib.php */
