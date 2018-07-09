<?php
/**
 * Fleet1 Model
 *
 * PHP Version 5.5+
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */
namespace application\models\game;

/**
 * Fleet1 Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */
class Fleet1
{

    private $db = null;

    /**
     * __construct()
     */
    public function __construct($db)
    {
        // use this to make queries
        $this->db = $db;
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
     * Get ships by planet id
     * 
     * @param int $planet_id Planet ID
     * 
     * @return array
     */
    public function getShipsByPlanetId($planet_id)
    {
        if ((int) $planet_id > 0) {

            return $this->db->queryFetch(
                    "SELECT 
                        s.`ship_small_cargo_ship`,
                        s.`ship_big_cargo_ship`,
                        s.`ship_light_fighter`,
                        s.`ship_heavy_fighter`,
                        s.`ship_cruiser`,
                        s.`ship_battleship`,
                        s.`ship_colony_ship`,
                        s.`ship_recycler`,
                        s.`ship_espionage_probe`,
                        s.`ship_bomber`,
                        s.`ship_solar_satellite`,
                        s.`ship_destroyer`,
                        s.`ship_deathstar`,
                        s.`ship_battlecruiser`
                    FROM `" . SHIPS . "` AS s 
                    WHERE s.`ship_planet_id` = '" . $planet_id . "';"
            );
        }

        return [];   
    }
    
    /**
     * Get all fleets by user id or owner
     * 
     * @param int $user_id User ID
     * 
     * @return array
     */
    public function getAllFleetsByUserId($user_id)
    {
        if ((int) $user_id > 0) {

            return $this->db->queryFetchAll(
                    "SELECT f.*
                    FROM `" . FLEETS . "` AS f
                    WHERE f.`fleet_owner` = '" . $user_id . "';"
            );
        }

        return [];  
    }
}

/* end of fleet1.php */
