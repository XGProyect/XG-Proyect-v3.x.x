<?php
/**
 * Format Library
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
namespace application\libraries;

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
     * colorRed
     *
     * @param string $n String
     *
     * @return string
     */
    public static function colorRed($n)
    {
        return '<font color="#ff0000">' . $n . '</font>';
    }

    /**
     * colorGreen
     *
     * @param string $n String
     *
     * @return string
     */
    public static function colorGreen($n)
    {
        return '<font color="#00ff00">' . $n . '</font>';
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

            return self::prettyNumber(( $number / 1000000000000000000)) . "&nbsp;<font color=lime>T+</font>";
        } elseif ($number >= 1000000000000000000 && $number < 1000000000000000000000000) {

            return self::prettyNumber(( $number / 1000000000000000000)) . "&nbsp;<font color=lime>T</font>";
        } elseif ($number >= 1000000000000 && $number < 1000000000000000000) {

            return self::prettyNumber(( $number / 1000000000000)) . "&nbsp;<font color=lime>B</font>";
        } elseif ($number >= 1000000 && $number < 1000000000000) {

            return self::prettyNumber(( $number / 1000000)) . "&nbsp;<font color=lime>M</font>";
        } elseif ($number >= 1000 && $number < 1000000) {

            return self::prettyNumber(( $number / 1000)) . "&nbsp;<font color=lime>K</font>";
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
                ",", ".", sprintf("%." . $pro . "f", $numeric)
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
     * prettyCoords
     *
     * @param int $galaxy Galaxy
     * @param int $system System
     * @param int $planet Planet
     *
     * @return
     */
    public static function prettyCoords($galaxy, $system, $planet)
    {
        return "[$galaxy:$system:$planet]";
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
}

/* end of FormatLib.php */
