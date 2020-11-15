<?php namespace App\libraries;

use App\core\Language;
use App\libraries\Functions;

/**
 * Timing Library Class
 */
abstract class TimingLibrary
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
        $lang = $lang->loadLang('game/global', true);

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

        return date(Functions::readConfig('date_format_extended'), $time);
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

        return date(Functions::readConfig('date_format'), $time);
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

    /**
     * Get the amount of days left
     *
     * @param int $time
     * @return float
     */
    public static function getDaysLeft(int $time): float
    {
        return (($time - time()) / 24 / 3600);
    }

    /**
     * Get the amount of hours and minutes left
     *
     * @param int $time
     * @return string
     */
    public static function formatHoursMinutesLeft(int $time): string
    {
        return date('h:i', $time - time());
    }
}
