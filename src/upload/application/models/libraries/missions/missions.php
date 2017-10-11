<?php
/**
 * Missions Model
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

namespace application\models\libraries\missions;

/**
 * Missions Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Missions
{
    private $db = null;
    
    /**
     * __construct()
     */
    public function __construct($db)
    {        
        // use this to make queries
        $this->db   = $db;
        
        // lock tables
        //$this->lockTables();
    }

    /**
     * __destruct
     * 
     * @return void
     */
    public function __destruct()
    {
        // unlock tables
        //$this->unlockTables();
        
        // close connection
        $this->db->closeConnection();
    }

    /**
     * Lock all the required tables
     * 
     * @return void
     */
    private function lockTables()
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
    private function unlockTables()
    {
        $this->db->query("UNLOCK TABLES");
    }
    
    /**
     * Delete a fleet by its ID
     * 
     * @param int $fleet_id Fleet ID
     * 
     * @return void
     */
    public function deleteFleetById($fleet_id)
    {
        if ((int)$fleet_id > 0) {

            $this->db->query(
                "DELETE FROM " . FLEETS . " WHERE `fleet_id` = '" . $fleet_id . "'"
            );
        }
    }
    
    /**
     * Update fleet status by ID
     * 
     * @param int $fleet_id Fleet ID
     * 
     * @return void
     */
    public function updateFleetStatusById($fleet_id)
    {
        if ((int)$fleet_id > 0) {

            $this->db->query(
                "UPDATE " . FLEETS . " SET
                    `fleet_mess` = '1'
                WHERE `fleet_id` = '" . $fleet_id . "'"
            );
        }
    }
    
    /**
     * 
     * 
     * @param type $data
     */
    public function updatePlanetsShipsByCoords($data = [])
    {
        if (is_array($data)) {

            $this->db->query(
                "UPDATE " . PLANETS . " AS p
                INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id` SET
                    {$data['ships']}
                    `planet_metal` = `planet_metal` + '" . $data['resources']['metal'] . "',
                    `planet_crystal` = `planet_crystal` + '" . $data['resources']['crystal'] . "',
                    `planet_deuterium` = `planet_deuterium` + '" . $data['resources']['deuterium'] . "'
                WHERE `planet_galaxy` = '" . $data['coords']['galaxy'] . "' AND
                    `planet_system` = '" . $data['coords']['system'] . "' AND
                    `planet_planet` = '" . $data['coords']['planet'] . "' AND
                    `planet_type` = '" . $data['coords']['type'] . "'
                LIMIT 1;"
            );   
        }
    }
    
    public function updatePlanetResourcesByCoords($data = [])
    {
        if (is_array($data)) {

            $this->db->query(
                "UPDATE " . PLANETS . " SET
                    `planet_metal` = `planet_metal` + '" . $data['resources']['metal'] . "',
                    `planet_crystal` = `planet_crystal` + '" . $data['resources']['crystal'] . "',
                    `planet_deuterium` = `planet_deuterium` + '" . $data['resources']['deuterium'] . "'
                WHERE `planet_galaxy` = '" . $data['coords']['galaxy'] . "' AND
                    `planet_system` = '" . $data['coords']['system'] . "' AND
                    `planet_planet` = '" . $data['coords']['planet'] . "' AND
                    `planet_type` = '" . $data['coords']['type'] . "'
                LIMIT 1;"
            );
        }
    }
}

/* end of missions.php */
