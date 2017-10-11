<?php
/**
 * Users_library Model
 *
 * PHP Version 5.5+
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */

namespace application\models\libraries;

/**
 * Users_library Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Mission_control_library
{
    private $db = null;
    
    /**
     * __construct()
     */
    public function __construct($db)
    {        
        // use this to make queries
        $this->db   = $db;
    }

    /**
     * __destruct
     * 
     * @return void
     */
    public function __destruct()
    {
        $this->db->closeConnection();
    }
    
    /**
     * Lock all the required tables
     * 
     * @return void
     */
    public function lockTables()
    {
        $this->db->query(
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
    }
    
    /**
     * Unlock previously locked tables
     * 
     * @return void
     */
    public function unlockTables()
    {
        $this->db->query("UNLOCK TABLES");
    }
    
    /**
     * Return a list of all the arriving fleets that are going 
     * to be processed at this time.
     * 
     * @return array
     */
    public function getArrivingFleets()
    {
        return $this->db->queryFetchAll(
            "SELECT f.*
            FROM `" . FLEETS . "` f, `" . FLEETS . "` sf
            WHERE 
            (
                (
                    f.`fleet_start_galaxy` = sf.`fleet_start_galaxy` AND
                    f.`fleet_start_system` = sf.`fleet_start_system` AND
                    f.`fleet_start_planet` = sf.`fleet_start_planet` AND
                    f.`fleet_start_type` = sf.`fleet_start_type`
                )
                OR
                (
                    f.`fleet_end_galaxy` = sf.`fleet_start_galaxy` AND
                    f.`fleet_end_system` = sf.`fleet_start_system` AND
                    f.`fleet_end_planet` = sf.`fleet_start_planet` AND 
                    f.`fleet_end_type`= sf.`fleet_start_type`
                )
            )
            AND
            (
                f.`fleet_start_time` < '" . time() . "'
                    OR f.`fleet_end_time` < '" . time() . "'
            )
            AND
            (
                sf.`fleet_start_time` <= '" . time() . "' 
                    AND sf.`fleet_mess` ='0' 
            )
            ORDER BY f.`fleet_id` ASC"
        );
    }
    
    /**
     * Return a list of all the returning fleets that are going 
     * to be processed at this time.
     * 
     * @return array
     */
    public function getReturningFleets()
    {
        return $this->db->queryFetchAll(
            "SELECT f.*
            FROM `" . FLEETS . "` f, `" . FLEETS . "` ef
            WHERE 
            (
                (
                    f.`fleet_start_galaxy` = ef.`fleet_end_galaxy` AND
                    f.`fleet_start_system` = ef.`fleet_end_system` AND
                    f.`fleet_start_planet` = ef.`fleet_end_planet` AND
                    f.`fleet_start_type` = ef.`fleet_end_type`
                )
                OR
                (
                    f.`fleet_end_galaxy` = ef.`fleet_end_galaxy` AND
                    f.`fleet_end_system` = ef.`fleet_end_system` AND
                    f.`fleet_end_planet` = ef.`fleet_end_planet` AND 
                    f.`fleet_end_type`= ef.`fleet_end_type`
                )
            )
            AND
            (
                f.`fleet_start_time` < '" . time() . "'
                    OR f.`fleet_end_time` < '" . time() . "'
            )
            AND
            (
                ef.`fleet_start_time` <= '" . time() . "'
            )
            ORDER BY f.`fleet_id` ASC"
        );
    }
}

/* end of mission_control_library.php */
