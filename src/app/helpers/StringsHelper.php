<?php

declare(strict_types=1);
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
 * @since      3.0.0
 */

namespace App\helpers;

use Exception;

/**
 * StringsHelper Class
 */
abstract class StringsHelper
{
    /**
     * Generate a random string, using a cryptographically secure
     * pseudorandom number generator (random_int)
     *
     * For PHP 7, random_int is a PHP core function
     * For PHP 5.x, depends on https://github.com/paragonie/random_compat
     *
     * @param int $length      How many characters do we want?
     * @param string $keyspace A string of all possible characters to select from
     *
     * @return string
     *
     * @link https://stackoverflow.com/questions/6101956/generating-a-random-password-in-php/31284266#31284266
     */
    public static function randomString(int $length, string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string
    {
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        if ($max < 1) {
            throw new Exception('$keyspace must be at least two characters long');
        }
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }

    /**
     * Replicates the behavior of mysql_real_escape_string
     *
     * @param string $value
     * @return string
     */
    public static function escapeString(string $value): string
    {
        return strtr($value, [
            "\\" => "\\\\",
            "\x00" => "\\0",
            "\n" => "\\n",
            "\r" => "\\r",
            "'" => "\'",
            '"' => '\"',
            "\x1a" => "\\Z",
        ]);
    }

    /**
     * Parse a line of text and replace its variables with the provided replacements
     *
     * @param string $text
     * @param array ...$replacements
     * @return string
     */
    public static function parseReplacements(string $text, array $replacements): string
    {
        return sprintf(
            $text,
            ...$replacements
        );
    }
}
