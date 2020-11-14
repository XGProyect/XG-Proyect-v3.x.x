<?php namespace App\libraries;

use App\helpers\StringsHelper;
use App\libraries\FormatLib as Format;
use App\libraries\TimingLibrary as Timing;

/**
 * OfficiersLib Class
 */
class OfficiersLib
{
    /**
     * isOfficierActive
     *
     * @param int $expire_time Expiration time
     *
     * @return int
     */
    public static function isOfficierActive($expire_time)
    {
        return ($expire_time > time() && $expire_time != 0);
    }

    /**
     * getMaxEspionage
     *
     * @param int $espionage_tech    Espionage tech level
     * @param int $technocrate_level Technocrate level
     *
     * @return int
     */
    public static function getMaxEspionage($espionage_tech, $technocrate_level)
    {
        return $espionage_tech + (1 * (self::isOfficierActive($technocrate_level) ? TECHNOCRATE_SPY : 0));
    }

    /**
     * getMaxComputer
     *
     * @param int $computer_tech Computer tech level
     * @param int $amiral_level  Amiral level
     *
     * @return int
     */
    public static function getMaxComputer($computer_tech, $amiral_level)
    {
        return 1 + $computer_tech + (1 * (self::isOfficierActive($amiral_level) ? AMIRAL : 0));
    }

    /**
     * Get the officer time left as string
     *
     * @param int $expiration
     * @param array $lang
     * @return string
     */
    public static function getOfficierTimeLeft(int $expiration, array $lang): string
    {
        $lang_line = 'of_time_remaining_many';
        $time_left = strtr(
            Format::prettyTimeAgo(Timing::formatShortDate($expiration)),
            $lang['timing']
        );

        if (Timing::getDaysLeft($expiration) <= 1) {
            $lang_line = 'of_time_remaining_less';
            $time_left = Timing::formatHoursMinutesLeft($expiration);
        }

        if (Timing::getDaysLeft($expiration) > 1 && Timing::getDaysLeft($expiration) < 2) {
            $lang_line = 'of_time_remaining_one';
            $time_left = '';
        }

        return StringsHelper::parseReplacements(
            $lang[$lang_line],
            [$time_left]
        );
    }
}
