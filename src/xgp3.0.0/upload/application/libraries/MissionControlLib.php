<?php
/**
 * Mission Control Library
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
 * MissionControlLib Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class MissionControlLib extends XGPCore
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(&$planet)
    {
        parent::__construct();
        
        include_once XGP_ROOT . 'application/libraries/missions/missions.php';
        
        parent::$db->query(
            "LOCK TABLE " . ACS_FLEETS . " WRITE,
            " . ALLIANCE . " AS a WRITE,
            " . REPORTS . " WRITE,
            " . MESSAGES . " WRITE,
            " . FLEETS . " WRITE,
            " . FLEETS . " AS f WRITE,
            " . FLEETS . " AS f1 WRITE,
            " . FLEETS . " AS f2 WRITE,
            " . PLANETS . " WRITE,
            " . PLANETS . " AS pc1 WRITE,
            " . PLANETS . " AS pc2 WRITE,
            " . PLANETS . " AS p WRITE,
            " . PLANETS . " AS m WRITE,
            " . PLANETS . " AS mp WRITE,
            " . PLANETS . " AS pm WRITE,
            " . PLANETS . " AS pm2 WRITE,
            " . PREMIUM . " WRITE,
            " . PREMIUM . " AS pr WRITE,
            " . PREMIUM . " AS pre WRITE,
            " . SETTINGS . " WRITE,
            " . SETTINGS . " AS se WRITE,
            " . SHIPS . " WRITE,
            " . SHIPS . " AS s WRITE,
            " . BUILDINGS . " WRITE,
            " . BUILDINGS . " AS b WRITE,
            " . DEFENSES . " WRITE,
            " . DEFENSES . " AS d WRITE,
            " . RESEARCH . " WRITE,
            " . RESEARCH . " AS r WRITE,
            " . USERS_STATISTICS . " WRITE,
            " . USERS_STATISTICS . " AS us WRITE,
            " . USERS_STATISTICS . " AS usul WRITE,
            " . USERS . " WRITE,
            " . USERS . " AS u WRITE"
        );

        $all_fleets = parent::$db->query(
            "SELECT *
            FROM " . FLEETS . "
            WHERE (
            (
                `fleet_start_galaxy` = " . $planet['planet_galaxy'] . " AND
                `fleet_start_system` = " . $planet['planet_system'] . " AND
                `fleet_start_planet` = " . $planet['planet_planet'] . " AND
                `fleet_start_type` = " . $planet['planet_type'] . "
            )
            OR
            (
                `fleet_end_galaxy` = " . $planet['planet_galaxy'] . " AND
                `fleet_end_system` = " . $planet['planet_system'] . " AND
                `fleet_end_planet` = " . $planet['planet_planet'] . "
            )

            AND
                `fleet_end_type`= " . $planet['planet_type'] . "
            )

            AND

            (
                `fleet_start_time` < '" . time() . "' OR
                `fleet_end_time` < '" . time() . "'
            );"
        );


        // missions list
        $missions   = array(
            1   => 'Attack',
            2   => 'Acs',
            3   => 'Transport',
            4   => 'Deploy',
            5   => 'Stay',
            6   => 'Spy',
            7   => 'Colonize',
            8   => 'Recycle',
            9   => 'Destroy',
            10  => 'Missile',
            15  => 'Expedition',
        );

        // Process missions
        while ($fleet = parent::$db->fetchArray($all_fleets)) {

            $name           = $missions[$fleet['fleet_mission']];
            $file_name      = strtolower($name);
            $mission_name   = strtolower($name) . 'Mission';
            $class_name     = '\application\libraries\missions\\' . $name;
            
            include_once XGP_ROOT . 'application/libraries/missions/' . $file_name . '.php';
            
            $mission    = new $class_name();
            $mission->$mission_name($fleet);
        }

        parent::$db->query("UNLOCK TABLES");
    }
}

/* end of MissionControlLib.php */
