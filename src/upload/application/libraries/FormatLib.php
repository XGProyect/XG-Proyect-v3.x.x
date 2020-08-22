<?php
/**
 * Format Library
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

use application\core\enumerators\ImportanceEnumerator as Importance;
use application\helpers\UrlHelper;

/**
 * FormatLib Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class FormatLib
{

    /**
     * prettyTime
     *
     * @param int $seconds Seconds
     *
     * @return string
     */
    public static function prettyTime($seconds)
    {
        $day = floor($seconds / (24 * 3600));
        $hs = floor($seconds / 3600 % 24);
        $ms = floor($seconds / 60 % 60);
        $sr = floor($seconds / 1 % 60);

        if ($hs < 10) {
            $hh = "0" . $hs;
        } else {
            $hh = $hs;
        }

        if ($ms < 10) {
            $mm = "0" . $ms;
        } else {
            $mm = $ms;
        }

        if ($sr < 10) {
            $ss = "0" . $sr;
        } else {
            $ss = $sr;
        }

        $time = '';

        if ($day != 0) {
            $time .= $day . 'd ';
        }

        if ($hs != 0) {
            $time .= $hh . 'h ';
        } else {
            $time .= '00h ';
        }

        if ($ms != 0) {
            $time .= $mm . 'm ';
        } else {
            $time .= '00m ';
        }

        $time .= $ss . 's';

        return $time;
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
     * colorNumber
     *
     * @param int    $n Number
     * @param string $s String
     *
     * @return string
     */
    public static function colorNumber($n, $s = '')
    {
        if ($n > 0) {
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
     * prettyNumber
     *
     * @param int     $n     Number
     * @param boolean $floor Floor
     *
     * @return
     */
    public static function prettyNumber($n, $floor = true)
    {
        if ($floor) {
            $n = floor($n);
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
        $units = array('Bytes', 'KB', 'MB', 'GB', 'TB');

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

/* end of FormatLib.php */
