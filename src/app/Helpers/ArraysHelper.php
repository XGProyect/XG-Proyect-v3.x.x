<?php

declare(strict_types=1);

namespace App\Helpers;

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
