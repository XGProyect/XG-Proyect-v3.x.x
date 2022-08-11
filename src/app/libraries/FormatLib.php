<?php
/**
 * FormatLib.php
 *
 * @author   XG Proyect Team
 * @license  https://www.xgproyect.org XG Proyect
 * @link     https://www.xgproyect.org
 * @version  3.2.0
 */

namespace App\libraries;

use App\core\enumerators\ImportanceEnumerator as Importance;
use App\helpers\UrlHelper;
use DateTime;

/**
 * FormatLib class
 */
class FormatLib
{
    /**
     * Convert or format a time in seconds to its string representation. Ex.: weeks, days, hours, minutes, seconds
     *
     * @param int $input_seconds
     *
     * @return string
     */
    public static function prettyTime(float $input_seconds): string
    {
        $sec_min = 60;
        $sec_hour = 60 * $sec_min;
        $sec_day = 24 * $sec_hour;
        $sec_week = 7 * $sec_day;

        // Extract weeks
        $weeks = floor($input_seconds / $sec_week);

        // Extract days
        $daysSeconds = (int)$input_seconds % $sec_week;
        $days = floor($daysSeconds / $sec_day);

        // Extract hours
        $hourSeconds = (int)$input_seconds % $sec_day;
        $hours = floor($hourSeconds / $sec_hour);

        // Extract minutes
        $minuteSeconds = (int)$hourSeconds % $sec_hour;
        $minutes = floor($minuteSeconds / $sec_min);

        // Extract the remaining seconds
        $remainingSeconds = (int)$minuteSeconds % $sec_min;
        $seconds = ceil($remainingSeconds);

        // Format and return
        $timeParts = [];
        $sections = [
            'w' => (int) $weeks,
            'd' => (int) $days,
            'h' => (int) $hours,
            'm' => (int) $minutes,
            's' => (int) $seconds,
        ];

        foreach ($sections as $name => $value) {
            if ($value > 0) {
                $timeParts[] = $value . $name;
            }
        }

        return implode(' ', $timeParts);
    }

    /**
     * prettyTimeHour
     *
     * @param int $seconds Seconds
     *
     * @return string
     */
    public static function prettyTimeHour($seconds)
    {
        $min = floor($seconds / 60 % 60);
        $time = '';

        if ($min != 0) {
            $time .= $min . 'min ';
        }

        return $time;
    }

    /**
     * prettyTimeAgo
     *
     * @param int $datetime DateTime
     * @param $full
     *
     * @return string
     */
    public static function prettyTimeAgo($datetime, $full = false)
    {
        $now = new DateTime();
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = [
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) {
            $string = array_slice($string, 0, 1);
        }

        return $string ? implode(', ', $string) : '';
    }

    /**
     * colorNumber
     *
     * @param int    $n Number
     * @param string $s String
     *
     * @return string
     */
    public static function colorNumber($n, $s = '')
    {
        if ($n >= 0) {
            if ($s != '') {
                $s = self::colorGreen($s);
            } else {
                $s = self::colorGreen($n);
            }
        } elseif ($n < 0) {
            if ($s != '') {
                $s = self::colorRed($s);
            } else {
                $s = self::colorRed($n);
            }
        } else {
            if ($s != '') {
                $s = $s;
            } else {
                $s = $n;
            }
        }

        return $s;
    }

    /**
     * Set a red color
     *
     * @param string $string String
     *
     * @return string
     */
    public static function colorRed($string)
    {
        return '<font color="#ff0000">' . $string . '</font>';
    }

    /**
     * Set a green color
     *
     * @param string $string String
     *
     * @return string
     */
    public static function colorGreen($string)
    {
        return '<font color="#00ff00">' . $string . '</font>';
    }

    /**
     * Return a provided color
     *
     * @param string $string String
     * @param string $color  Color
     *
     * @return string
     */
    public static function customColor($string, $color)
    {
        return '<font color="' . $color . '">' . $string . '</font>';
    }

    /**
     * Create a new span HTML element
     *
     * @param string $content
     * @param string|null $class
     * @return string
     */
    public static function spanElement(string $content, ?string $class = ''): string
    {
        return '<span class="' . $class . '">' . $content . '</span>';
    }

    /**
     * prettyNumber
     *
     * @param int     $n     Number
     * @param boolean $floor Floor
     *
     * @return string
     */
    public static function prettyNumber($n, $floor = true)
    {
        if ($floor) {
            $n = floor($n??0.0);
        }

        return number_format($n, 0, ",", ".");
    }

    /**
     * shortlyNumber
     *
     * @param $number
     *
     * @return string
     */
    public static function shortlyNumber($number)
    {
        // MAS DEL TRILLON
        if ($number >= 1000000000000000000000000) {
            return self::prettyNumber(($number / 1000000000000000000)) . ' T+';
        } elseif ($number >= 1000000000000000000 && $number < 1000000000000000000000000) {
            return self::prettyNumber(($number / 1000000000000000000)) . ' T';
        } elseif ($number >= 1000000000000 && $number < 1000000000000000000) {
            return self::prettyNumber(($number / 1000000000000)) . ' B';
        } elseif ($number >= 1000000 && $number < 1000000000000) {
            return self::prettyNumber(($number / 1000000)) . ' M';
        } elseif ($number >= 10000 && $number < 1000000) {
            return self::prettyNumber(($number / 1000)) . ' K';
        } else {
            return self::prettyNumber($number);
        }
    }

    /**
     * method floatToString
     *
     * @param int     $numeric Number
     * @param int     $pro     Pro
     * @param boolean $output  Output
     *
     * @return string
     */
    public static function floatToString($numeric, $pro = 0, $output = false)
    {
        return ($output) ? str_replace(
            ",",
            ".",
            sprintf("%." . $pro . "f", $numeric)
        ) : sprintf("%." . $pro . "f", $numeric);
    }

    /**
     * roundUp
     *
     * @param int $value     Value
     * @param int $precision Precision
     *
     * @return int
     */
    public static function roundUp($value, $precision = 0)
    {
        if ($precision == 0) {
            $precisionFactor = 1;
        } else {
            $precisionFactor = pow(10, $precision);
        }

        return ceil($value * $precisionFactor) / $precisionFactor;
    }

    /**
     * Return the coords in format [g:s:p] and links them to the galaxy
     *
     * @param int $galaxy
     * @param int $system
     * @param int $planet
     * @return void
     */
    public static function prettyCoords(int $galaxy, int $system, int $planet): string
    {
        return UrlHelper::setUrl(
            'game.php?page=galaxy&mode=3&galaxy=' . $galaxy . '&system=' . $system,
            self::formatCoords($galaxy, $system, $planet)
        );
    }

    /**
     * Return the coords in format [g:s:p]
     *
     * @param integer $galaxy
     * @param integer $system
     * @param integer $planet
     * @return string
     */
    public static function formatCoords(int $galaxy, int $system, int $planet): string
    {
        return sprintf('[%d:%d:%d]', $galaxy, $system, $planet);
    }

    /**
     * strongText
     *
     * @param string $value Value
     *
     * @return string
     */
    public static function strongText($value)
    {
        return '<strong>' . $value . '</strong>';
    }

    /**
     * prettyBytes
     *
     * @param int     $bytes     Bytes
     * @param int     $precision Precision
     * @param boolean $bitwise   Bitwise Arithmetic
     *
     * @return int
     */
    public static function prettyBytes($bytes, $precision = 2, $bitwise = false)
    {
        $units = ['Bytes', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        if ($bitwise) {
            $bytes /= (1 << (10 * $pow));
        } else {
            $bytes /= pow(1024, $pow);
        }

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Return the color based on the importance
     *
     * @param int $priority
     *
     * @return string
     */
    public static function getImportanceColor(int $priority): string
    {
        switch ($priority) {
            case Importance::unimportant:
                return 'lime';
            case Importance::normal:
                return 'yellow';
            case Importance::important:
                return 'red';
        }

        return 'lime';
    }

    /**
     * Format the level
     *
     * @param string $object
     * @param string $lvl_string
     * @param int $level
     *
     * @return string
     */
    public static function formatLevel(string $object, string $lvl_string, int $level): string
    {
        return $object . ' (' . $lvl_string . $level . ')';
    }

    /**
     * Get a list of all html colors names
     *
     * @return array
     */
    public static function getHTMLColorsNameList(): array
    {
        return [
            'AliceBlue',
            'AntiqueWhite',
            'Aqua',
            'Aquamarine',
            'Azure',
            'Beige',
            'Bisque',
            'Black',
            'BlanchedAlmond',
            'Blue',
            'BlueViolet',
            'Brown',
            'BurlyWood',
            'CadetBlue',
            'Chartreuse',
            'Chocolate',
            'Coral',
            'CornflowerBlue',
            'Cornsilk',
            'Crimson',
            'Cyan',
            'DarkBlue',
            'DarkCyan',
            'DarkGoldenRod',
            'DarkGray',
            'DarkGrey',
            'DarkGreen',
            'DarkKhaki',
            'DarkMagenta',
            'DarkOliveGreen',
            'DarkOrange',
            'DarkOrchid',
            'DarkRed',
            'DarkSalmon',
            'DarkSeaGreen',
            'DarkSlateBlue',
            'DarkSlateGray',
            'DarkSlateGrey',
            'DarkTurquoise',
            'DarkViolet',
            'DeepPink',
            'DeepSkyBlue',
            'DimGray',
            'DimGrey',
            'DodgerBlue',
            'FireBrick',
            'FloralWhite',
            'ForestGreen',
            'Fuchsia',
            'Gainsboro',
            'GhostWhite',
            'Gold',
            'GoldenRod',
            'Gray',
            'Grey',
            'Green',
            'GreenYellow',
            'HoneyDew',
            'HotPink',
            'IndianRed',
            'Indigo',
            'Ivory',
            'Khaki',
            'Lavender',
            'LavenderBlush',
            'LawnGreen',
            'LemonChiffon',
            'LightBlue',
            'LightCoral',
            'LightCyan',
            'LightGoldenRodYellow',
            'LightGray',
            'LightGrey',
            'LightGreen',
            'LightPink',
            'LightSalmon',
            'LightSeaGreen',
            'LightSkyBlue',
            'LightSlateGray',
            'LightSlateGrey',
            'LightSteelBlue',
            'LightYellow',
            'Lime',
            'LimeGreen',
            'Linen',
            'Magenta',
            'Maroon',
            'MediumAquaMarine',
            'MediumBlue',
            'MediumOrchid',
            'MediumPurple',
            'MediumSeaGreen',
            'MediumSlateBlue',
            'MediumSpringGreen',
            'MediumTurquoise',
            'MediumVioletRed',
            'MidnightBlue',
            'MintCream',
            'MistyRose',
            'Moccasin',
            'NavajoWhite',
            'Navy',
            'OldLace',
            'Olive',
            'OliveDrab',
            'Orange',
            'OrangeRed',
            'Orchid',
            'PaleGoldenRod',
            'PaleGreen',
            'PaleTurquoise',
            'PaleVioletRed',
            'PapayaWhip',
            'PeachPuff',
            'Peru',
            'Pink',
            'Plum',
            'PowderBlue',
            'Purple',
            'RebeccaPurple',
            'Red',
            'RosyBrown',
            'RoyalBlue',
            'SaddleBrown',
            'Salmon',
            'SandyBrown',
            'SeaGreen',
            'SeaShell',
            'Sienna',
            'Silver',
            'SkyBlue',
            'SlateBlue',
            'SlateGray',
            'SlateGrey',
            'Snow',
            'SpringGreen',
            'SteelBlue',
            'Tan',
            'Teal',
            'Thistle',
            'Tomato',
            'Turquoise',
            'Violet',
            'Wheat',
            'White',
            'WhiteSmoke',
            'Yellow',
            'YellowGreen',
        ];
    }
}
