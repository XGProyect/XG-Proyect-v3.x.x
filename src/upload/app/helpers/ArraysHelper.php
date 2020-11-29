<?php declare (strict_types = 1);

/**
 * Arrays Helper
 *
 * @category Helper
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\helpers;

/**
 * ArraysHelper Class
 */
abstract class ArraysHelper
{
    /**
     * Like in_array but going deeper, for multidimensional arrays
     *
     * @param string $needle
     * @param array $haystack
     * @return boolean
     */
    public static function inMultiArray(string $needle, array $haystack): bool
    {
        foreach ($haystack as $key => $value) {
            if ($value == $needle) {
                return true;
            } elseif (is_array($value)) {
                if (self::inMultiArray($needle, $value)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Like array_search but going deeper, for multidimensional arrays
     *
     * @param string $needle
     * @param array $haystack
     * @return array|null
     */
    public static function multiArraySearch(string $needle, array $haystack): ?int
    {
        foreach ($haystack as $key => $value) {
            $current_key = $key;

            if ($needle === $value or (is_array($value) && self::multiArraySearch($needle, $value) !== null)) {
                return (int) $current_key;
            }
        }

        return null;
    }
}
