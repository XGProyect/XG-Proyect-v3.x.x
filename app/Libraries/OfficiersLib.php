<?php

namespace App\Libraries;

use App\Helpers\StringsHelper;
use App\Libraries\TimingLibrary as Timing;

class OfficiersLib
{
    public static function isOfficierActive(int $expireTime): int
    {
        return ($expireTime > time() && $expireTime != 0);
    }

    public static function getMaxEspionage(int $espionageTech, int $technocrateLevel): int
    {
        return $espionageTech + (1 * (self::isOfficierActive($technocrateLevel) ? TECHNOCRATE_SPY : 0));
    }

    public static function getMaxComputer(int $computerTech, int $admiralLevel): int
    {
        return 1 + $computerTech + (1 * (self::isOfficierActive($admiralLevel) ? AMIRAL : 0));
    }

    public static function getOfficierTimeLeft(int $expiration, array $lang): string
    {
        $lang_line = 'of_time_remaining_many';
        $time_left = round(Timing::getDaysLeft($expiration));

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
