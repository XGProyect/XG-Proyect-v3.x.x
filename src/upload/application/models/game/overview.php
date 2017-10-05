<?php
/**
 * Overview Model
 *
 * PHP Version 5.5+
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.2
 */

namespace application\models\game;

/**
 * Overview Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.2
 */
class Overview
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
     * Get own fleets
     * 
     * @param type $user_id
     * 
     * @return mixed
     */
    public function getOwnFleets($user_id)
    {
        if ((int)$user_id > 0) {

            return $this->db->queryFetchAll(
                "SELECT *
                FROM " . FLEETS . "
                WHERE `fleet_owner` = '" . $user_id . "' OR 
                    `fleet_target_owner` = '" . $user_id . "';"
            );
        }
        
        return null;
    }
    
    /**
     * Get own fleets
     * 
     * @param type $user_id
     * 
     * @return mixed
     */
    public function getPlanets($user_id)
    {
        if ((int)$user_id > 0) {

            return $this->db->queryFetchAll(
                "SELECT *
                    FROM " . PLANETS . " AS p
                    INNER JOIN " . BUILDINGS . " AS b ON b.building_planet_id = p.`planet_id`
                    INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id`
                    INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id`
                    WHERE `planet_user_id` = '" . $user_id . "'
                            AND `planet_destroyed` = 0;"
            );
        }
        
        return null;
    }
}

/* end of overview.php */
