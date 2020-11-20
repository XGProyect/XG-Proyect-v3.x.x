<?php declare (strict_types = 1);
/**
 * XG Proyect
 *
 * Open-source OGame Clon
 *
 * This content is released under the GPL-3.0 License
 *
 * Copyright (c) 2008-2020 XG Proyect
 *
 * @package    XG Proyect
 * @author     XG Proyect Team
 * @copyright  2008-2020 XG Proyect
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0 License
 * @link       https://github.com/XGProyect/
 * @since      Version 3.0.0
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
