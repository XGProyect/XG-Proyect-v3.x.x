<?php
/**
 * Updates_library Model
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
 * Updates_library Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Updates_library
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
     * Delete deleted users and inactive users
     * 
     * @param int $del_deleted  Delete deleted users
     * @param int $del_inactive Delete inactive users
     * 
     * @return void
     */
    public function deleteUsersByDeletedAndInactive($del_deleted, $del_inactive)
    {
        return $this->db->queryFetchAll (
            "SELECT u.`user_id`
            FROM `" . USERS . "` AS u
            INNER JOIN `" . SETTINGS . "` AS s ON s.setting_user_id = u.user_id
            WHERE (s.`setting_delete_account` < '".$del_deleted."' AND s.`setting_delete_account` <> 0) OR
            (u.`user_onlinetime` < '".$del_inactive."' AND u.`user_onlinetime` <> 0 AND u.`user_authlevel` <> 3)"
        );
    }
    
    /**
     * Delete old messages
     * 
     * @param int $del_before Delete time
     * 
     * @return void
     */
    public function deleteMessages($del_before)
    {
        $this->db->query("DELETE FROM " . MESSAGES . " WHERE `message_time` < '". $del_before ."';");
    }
    
    /**
     * Delete old reports
     * 
     * @param int $del_before Delete time
     * 
     * @return void
     */
    public function deleteReports($del_before)
    {
        $this->db->query("DELETE FROM " . REPORTS . " WHERE `report_time` < '". $del_before ."';");
    }
    
    /**
     * Delete old sessions
     * 
     * @param int $del_before Delete time
     * 
     * @return void
     */
    public function deleteSessions($del_before)
    {
        $this->db->query("DELETE FROM " . SESSIONS . " WHERE `session_last_accessed` < '". $del_before ."';");
    }
    
    /**
     * Delete old planets
     * 
     * @param int $del_before Delete time
     * 
     * @return void
     */
    public function deleteDestroyedPlanets($del_before)
    {
        $this->db->query(
            "DELETE p,b,d,s FROM " . PLANETS . " AS p
            INNER JOIN " . BUILDINGS . " AS b ON b.building_planet_id = p.`planet_id`
            INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id`
            INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id`
            WHERE `planet_destroyed` < '" . $del_before . "' AND `planet_destroyed` <> 0;"
        );
    } 
    
    /**
     * Generate an SQL Backup
     * 
     * @return void
     */
    public function generateBackUp()
    {
        $this->db->query->backupDb();
    }
    
    /**
     * Get start fleets
     * 
     * @return array
     */
    public function getStartFleets()
    {
        return $this->db->queryFetchAll(
            "SELECT 
            fleet_start_galaxy, 
            fleet_start_system, 
            fleet_start_planet, 
            fleet_start_type
            FROM " . FLEETS . "
            WHERE `fleet_start_time` <= '" . time() . "' AND `fleet_mess` ='0'
            ORDER BY fleet_id ASC;"
        );  
    }
    
    /**
     * Get end fleets
     * 
     * @return array
     */
    public function getEndFleets()
    {
        return $this->db->queryFetchAll(
            "SELECT 
            fleet_end_galaxy, 
            fleet_end_system, 
            fleet_end_planet, 
            fleet_end_type
            FROM " . FLEETS . "
            WHERE `fleet_end_time` <= '" . time() . "
            ORDER BY fleet_id ASC';"
        );
    }
    
    /**
     * Update planet buildings, queue, fields and statistics
     * 
     * @param string $building_name Building Name
     * @param int    $amount        Amount
     * @param array  $planet        Planet
     * 
     * @return void
     */
    public function updatePlanet($building_name, $amount, $planet)
    {
        $this->db->query(
            "UPDATE " . PLANETS . " AS p
            INNER JOIN " . USERS_STATISTICS . " AS s ON s.user_statistic_user_id = p.planet_user_id
            INNER JOIN " . BUILDINGS . " AS b ON b.building_planet_id = p.`planet_id` SET
            `".$building_name."` = '".$amount."',
            `user_statistic_buildings_points` = `user_statistic_buildings_points` + '" .
                $planet['building_points'] . "',
            `planet_b_building` = '". $planet['planet_b_building'] ."',
            `planet_b_building_id` = '". $planet['planet_b_building_id'] ."',
            `planet_field_current` = '" . $planet['planet_field_current'] . "',
            `planet_field_max` = '" . $planet['planet_field_max'] . "'
            WHERE `planet_id` = '" . $planet['planet_id'] . "';"
        );
    }
    
    /**
     * Update planet building queue
     * 
     * @param array $planet Planet
     * 
     * @return void
     */
    public function updateBuildingsQueue($planet)
    {
        $this->db->query(
            "UPDATE " . PLANETS . " SET
            `planet_b_building` = '". $planet['planet_b_building'] ."',
            `planet_b_building_id` = '". $planet['planet_b_building_id'] ."'
            WHERE `planet_id` = '" . $planet['planet_id'] . "';"
        );
    }
}

/* end of update.php */
