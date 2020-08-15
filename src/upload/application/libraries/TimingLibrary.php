<?php
/**
 * Timing Library
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

use application\core\Language;
use application\core\XGPCore;

/**
 * Timing Library Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
abstract class TimingLibrary extends XGPCore
{
    /**
     * Return an string with the online time formatted
     *
     * @param int $online_time Online Time
     *
     * @return string
     */
    public static function setOnlineStatus($online_time)
    {
        $lang = new Language;
        $lang = $lang->loadLang('global', true);

        $color = 'red';
        $status = $lang->line('offline');

        if ($online_time + 60 * 15 >= time()) {
            $color = 'yellow';
            $status = $lang->line('minutes');
        }

        if ($online_time + 60 * 10 >= time()) {
            $color = 'lime';
            $status = $lang->line('online');
        }

        return FormatLib::customColor($status, $color);
    }

    /**
     * Format time based on system default extended date config
     *
     * @param string $time Time
     *
     * @return string
     */
    public static function formatExtendedDate($time)
    {
        if (!is_numeric($time)) {
            $time = strtotime($time);
        }

        return date(FunctionsLib::readConfig('date_format_extended'), $time);
    }

    /**
     * Format time based on system default short date config
     *
     * @param string $time Time
     *
     * @return string
     */
    public static function formatShortDate($time)
    {
        if (!is_numeric($time)) {
            $time = strtotime($time);
        }

        return date(FunctionsLib::readConfig('date_format'), $time);
    }

    /**
     * Format time on days format
     *
     * @param string $time Time
     *
     * @return string
     */
    public static function formatDaysTime($time)
    {
        $days = floor((time() - $time) / (3600 * 24));

        return strtr("%s d", ["%s" => $days]);
    }
}

/* end of Timing_library.php */
