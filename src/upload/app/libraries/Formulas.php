<?php
/**
 * Formulas.php
 *
 * @author   XG Proyect Team
 * @license  https://www.xgproyect.org XG Proyect
 * @link     https://www.xgproyect.org
 * @version  3.2.0
 */
namespace App\libraries;

use App\core\enumerators\BuildingsEnumerator as Buildings;
use App\libraries\Functions;

/**
 * Formulas Class
 */
abstract class Formulas
{
    /**
     * phalanxRange
     *
     * @param int $phalanx_level Phalanx level
     *
     * return int
     */
    public static function phalanxRange($phalanx_level)
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
    public static function missileRange($impulse_drive_level)
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
    public static function getPlanetSize($position, $main = false)
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

        $fields = self::calculatePlanetFields($diameter);

        if ($main) {
            $diameter = '12800';
            $fields = Functions::readConfig('initial_fields');
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
    public static function calculatePlanetFields($diameter)
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
    public static function setPlanetImage($system, $position)
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
    public static function setPlanetTemp($position)
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
    public static function getMoonDestructionChance(int $planet_diameter, int $death_stars): int
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
    public static function getDeathStarsDestructionChance(int $planet_diameter)
    {
        return round(sqrt($planet_diameter) / 2);
    }

    /**
     * Get the Ion Technology Bonus
     * @param float $ion_technology_level
     */
    public static function getIonTechnologyBonus(int $ion_technology_level): float
    {
        return $ion_technology_level * 0.04;
    }

    /**
     * Get the base cost to tear down, without influence of the ion technology
     *
     * @param int $price
     * @param float $factor
     * @param int $level
     */
    public static function getTearDownBaseCost(int $price, float $factor, int $level): int
    {
        return floor(self::getDevelopmentCost($price, $factor, ($level - 2)));
    }

    /**
     * Get the cost to tear down
     *
     * @param int $price
     * @param float $factor
     * @param int $level
     * @param int $ion_technology_level
     */
    public static function getTearDownCost(int $price, float $factor, int $level, int $ion_technology_level): int
    {
        return max(floor(self::getTearDownBaseCost($price, $factor, $level) * (1 - self::getIonTechnologyBonus($ion_technology_level))), 0);
    }

    /**
     * Get the cost to develop something
     *
     * @param int $price
     * @param float $factor
     * @param int $level
     */
    public static function getDevelopmentCost(int $price, float $factor, int $level): float
    {
        return round($price * pow($factor, $level));
    }

    /**
     * Check if the building is for destroy and calculate
     *
     * @param integer $time
     * @return integer
     */
    public static function getTearDownTime(int $metal_cost, int $cystal_cost, int $building, int $robotics_factory, int $nanite_factory, int $level): float
    {
        $tear_down_time = self::getDevelopmentTime($metal_cost, $cystal_cost, $building, $robotics_factory, $nanite_factory, $level - 2);

        return ($tear_down_time < 1 ? 1 : $tear_down_time);
    }

    /**
     * Get the time to produce ships and defenses
     *
     * @param integer $metal_cost
     * @param integer $cystal_cost
     * @param integer $ship_defense
     * @param integer $shipyard_level
     * @param integer $nanite_factory_level
     * @return float
     */
    public static function getShipyardProductionTime(int $metal_cost, int $cystal_cost, int $ship_defense, int $shipyard_level, int $nanite_factory_level): float
    {
        return self::getDevelopmentTime($metal_cost, $cystal_cost, $ship_defense, $shipyard_level, $nanite_factory_level, 0, false);
    }

    /**
     * Get the time to build
     *
     * @param integer $metal_cost
     * @param integer $cystal_cost
     * @param integer $building
     * @param integer $robotics_factory
     * @param integer $nanite_factory
     * @param integer $level
     * @return float
     */
    public static function getBuildingTime(int $metal_cost, int $cystal_cost, int $building, int $robotics_factory, int $nanite_factory, int $level): float
    {
        return self::getDevelopmentTime($metal_cost, $cystal_cost, $building, $robotics_factory, $nanite_factory, $level);
    }

    /**
     * Get research time
     *
     * @param integer $metal_cost
     * @param integer $cystal_cost
     * @param integer $total_lab_level
     * @param integer $expedition_level
     * @return float
     */
    public static function getResearchTime(int $metal_cost, int $cystal_cost, int $total_lab_level, int $expedition_level): float
    {
        $universe_speed = Functions::readConfig('game_speed') / 2500;

        return ($metal_cost + $cystal_cost) / ($universe_speed * 1000 * (1 + $total_lab_level) * (1 + $expedition_level)) * 3600;
    }

    /**
     * Get the time to develop something
     *
     * @param integer $metal_cost
     * @param integer $cystal_cost
     * @param integer $object
     * @param integer $first_boost
     * @param integer $second_boost
     * @param integer $level
     * @param boolean $reduce
     * @return float
     */
    private static function getDevelopmentTime(int $metal_cost, int $cystal_cost, int $object, int $first_boost, int $second_boost, int $level = 0, bool $reduce = true): float
    {
        $resources_needed = $metal_cost + $cystal_cost;
        $reduction = max(4 - ($level + 1) / 2, 1);
        $robotics = 1 + $first_boost;
        $nanite = pow(2, $second_boost);
        $universe_speed = Functions::readConfig('game_speed') / 2500;
        $without_reduction = [
            Buildings::BUILDING_NANO_FACTORY,
            Buildings::BUILDING_MONDBASIS,
            Buildings::BUILDING_PHALANX,
            Buildings::BUILDING_JUMP_GATE,
        ];

        if (in_array($object, $without_reduction) or $reduce == false) {
            $reduction = 1;
        }

        return $resources_needed / (2500 * $reduction * $robotics * $nanite * $universe_speed) * 3600;
    }
}
