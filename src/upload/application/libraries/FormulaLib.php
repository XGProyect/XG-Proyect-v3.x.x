<?php
/**
 * Formula Library
 *
 * PHP Version 7.1+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\libraries;

/**
 * FormulaLib Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class FormulaLib
{

    /**
     * phalanxRange
     *
     * @param int $phalanx_level Phalanx level
     *
     * return int
     */
    public function phalanxRange($phalanx_level)
    {
        $range = 0;

        if ($phalanx_level > 1) {
            $range = pow($phalanx_level, 2) - 1;
        } elseif ($phalanx_level == 1) {
            $range = 1;
        }

        return $range;
    }

    /**
     * missileRange
     *
     * @param int $impulse_drive_level Impulse drive level
     *
     * return int
     */
    public function missileRange($impulse_drive_level)
    {
        if ($impulse_drive_level > 0) {
            return ($impulse_drive_level * 5) - 1;
        }

        return 0;
    }

    /**
     * getPlanetSize
     *
     * @param int     $position Position
     * @param boolean $main     Home world
     *
     * @return void
     */
    public function getPlanetSize($position, $main = false)
    {
        // THIS DIAMETERS ARE CALCULATED TO RETURN THE CORRECT AMOUNT OF FIELDS, IT SHOULD WORK AS OGAME.
        $min = [
            9747, 9849, 9899, 11091, 12166,
            12166, 11874, 12921, 12689, 12410,
            12083, 11662, 10392, 9000, 8062,
        ];

        $max = [
            10392, 10488, 11747, 14491, 14900,
            15748, 15588, 15905, 15588, 15000,
            14318, 13416, 11000, 9644, 8602,
        ];

        $diameter = mt_rand($min[$position - 1], $max[$position - 1]);
        $diameter *= PLANETSIZE_MULTIPLER;

        $fields = $this->calculatePlanetFields($diameter);

        if ($main) {
            $diameter = '12800';
            $fields = FunctionsLib::readConfig('initial_fields');
        }

        $return['planet_diameter'] = $diameter;
        $return['planet_field_max'] = $fields;

        return $return;
    }

    /**
     * getPlanetFields
     *
     * @param int $diameter Diameter
     *
     * @return int
     */
    public function calculatePlanetFields($diameter)
    {
        return (int) pow(($diameter / 1000), 2);
    }

    /**
     * setPlanetImage
     *
     * @param int $system   Planet system
     * @param int $position Planet position
     *
     * @return string
     */
    public function setPlanetImage($system, $position)
    {
        // Formula based on original game values
        // How many images do we have for each planet type
        $planets_availables = [
            'dschjungel' => 10, // jungle
            'eis' => 10, // ice
            'gas' => 8, // gas
            'normaltemp' => 7, // normal
            'trocken' => 10, // dry
            'wasser' => 9, // water
            'wuesten' => 4, // desert
        ];

        if ($position >= 1 && $position <= 3) {
            $type = ['trocken', 'wuesten'];
        }

        if ($position >= 4 && $position <= 5) {
            $type = ['normaltemp', 'trocken'];
        }

        if ($position >= 6 && $position <= 7) {
            $type = ['dschjungel', 'normaltemp'];
        }

        if ($position >= 8 && $position <= 9) {
            $type = ['wasser', 'dschjungel'];
        }

        if ($position >= 10 && $position <= 11) {
            $type = ['eis', 'wasser'];
        }

        if ($position >= 12 && $position <= 13) {
            $type = ['gas', 'eis'];
        }

        if ($position >= 14 && $position <= 15) {
            $type = ['normaltemp', 'gas'];
        }

        // if it's an even number, we will get second element postion in the array
        if ($system % 2 == 0) {
            $even = 1;
        } else {
            $even = 0;
        }

        $image_id = mt_rand(1, $planets_availables[$type[$even]]);

        if ($image_id < 10) {
            $image_id = '0' . $image_id;
        }

        return $type[$even] . 'planet' . $image_id;
    }

    /**
     * setPlanetTemp
     *
     * @param int $position Planet position
     *
     * @return array
     */
    public function setPlanetTemp($position)
    {
        // Based on original game values
        $temp_avilable = [
            1 => [220, 260],
            2 => [170, 210],
            3 => [120, 160],
            4 => [70, 110],
            5 => [60, 100],
            6 => [50, 90],
            7 => [40, 80],
            8 => [30, 70],
            9 => [20, 60],
            10 => [10, 50],
            11 => [0, 40],
            12 => [-10, 30],
            13 => [-50, -10],
            14 => [-90, -50],
            15 => [-130, -90],
        ];

        $temperature = mt_rand($temp_avilable[$position][0], $temp_avilable[$position][1]);

        $temp['min'] = $temperature - 40;
        $temp['max'] = $temperature;

        return $temp;
    }

    /**
     * Get moon destruction chance
     *
     * @param int $planet_diameter
     * @param int $death_stars
     *
     * @return int
     */
    public function getMoonDestructionChance(int $planet_diameter, int $death_stars): int
    {
        $prob = (100 - sqrt($planet_diameter)) * sqrt($death_stars);

        return ($prob > 100) ? 100 : round($prob);
    }

    /**
     * Get Death Stars destruction chance
     *
     * @param int $planet_diameter
     * @return type
     */
    public function getDeathStarsDestructionChance(int $planet_diameter)
    {
        return round(sqrt($planet_diameter) / 2);
    }

    /**
     *10%
     *echo floor((10000 * 10 / 10000) * 4500 / 10);
     *
     *50%
     *echo floor((10000 * 50 / 10000) * 4500 / 10);
     *
     *100%
     * ((max_storage - current_resources) * percentage /  max_storage) * base_dm / base_percentage
     *echo floor(((10000 - 1549.33) * 100 / 10000) * 4500 / 10);// = 38083
     *
     *
     *10000 * 0.10 |     1000 - 4500
     *20000 * 0.10 | 2000 - 9000
     *40000 * 0.10 | 4000 - 18000
     *75000 * 0.10 | 7500 - 18000
     *
     *(max_storage * 0.10) *  base_resource / (max_storage_level_1 * 0.10)
     */

    public function getTraderRefill($max_storage, $base_resource, $max_storage_level)
    {
        return ($max_storage * 0.10) * $base_resource / ($max_storage_level * 0.10);
    }

    /**
     * Get the price to refill the storage a 10%
     *
     * @param integer $max_storage
     * @param integer $base_dm
     * @return integer
     */
    public function getPriceToFillBy10Percent(int $max_storage, int $base_dm): int
    {
        return calculateRefillStoragePrice($max_storage, $base_dm, 10);
    }

    /**
     * Get the price to refill the storage a 50%
     *
     * @param integer $max_storage
     * @param integer $base_dm
     * @return integer
     */
    public function getPriceToFillBy50Percent(int $max_storage, int $base_dm): int
    {
        return calculateRefillStoragePrice($max_storage, $base_dm, 50);
    }

    /**
     * Get the price to completely refill the storage
     *
     * @param integer $max_storage
     * @param integer $base_dm
     * @param float $current_resources
     * @return integer
     */
    public function getPriceToFillTo100Percent(int $max_storage, int $base_dm, float $current_resources): int
    {
        return calculateRefillStoragePrice($max_storage, $base_dm, 100, $current_resources);
    }

    /**
     * Get the price to refill the storage
     *
     * @param integer $max_storage
     * @param integer $base_dm
     * @param integer $percentage
     * @param float $current_resources
     * @return integer
     */
    public function calculateRefillStoragePrice(int $max_storage, int $base_dm, int $percentage, float $current_resources = 0): int
    {
        return (($max_storage - $current_resources) * $percentage / $max_storage) * $base_dm / 10;
    }
}

/* end of FormulaLib.php */
