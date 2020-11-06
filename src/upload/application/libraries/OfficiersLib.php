<?php
/**
 * Officiers Library
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
 * OfficiersLib Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
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
}
