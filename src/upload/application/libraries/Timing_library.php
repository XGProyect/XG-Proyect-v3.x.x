<?php
/**
 * Timing Library
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

use application\core\XGPCore;

/**
 * Timing_library Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
abstract class Timing_library extends XGPCore
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
        $color  = 'red';
        $status = parent::$lang['offline'];
        
        if ($online_time + 60 * 15 >= time()) {
            
            $color  = 'yellow';
            $status = parent::$lang['minutes'];
        }
        
        if ($online_time + 60 * 10 >= time()) {
            
            $color  = 'lime';
            $status = parent::$lang['online'];
        }

        return FormatLib::customColor($status, $color);
    }
    
    /**
     * Format time based on system config
     * 
     * @param string $time Time
     * 
     * @return string
     */
    public static function formatTime($time)
    {
        return date(FunctionsLib::readConfig('date_format_extended'), $time);
    }
}

/* end of Timing_library.php */
