<?php
/**
 * Fleet Model
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
namespace application\models\game;

/**
 * Fleet Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Fleet
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
    
    /**
     * Get all user planets
     * 
     * @param int $user_id User ID
     * 
     * @return array
     */
    public function getAllPlanetsByUserId($user_id)
    {
        if ((int) $user_id > 0) {

            return $this->db->queryFetchAll(
                "SELECT 
                    p.`planet_id`,
                    p.`planet_name`,
                    p.`planet_galaxy`,
                    p.`planet_system`,
                    p.`planet_planet`,
                    p.`planet_type`
                FROM `" . PLANETS . "` AS p
                WHERE p.`planet_user_id` = '" . $user_id . "';"
            );
        }

        return [];
    }
    
    /**
     * Get ongoing ACS attacks
     * 
     * @param
     * 
     * @return mixed
     */
    public function getOngoingAcs()
    {
        return $this->db->queryFetchAll(
                "SELECT * FROM `" . ACS_FLEETS . "`"
        );
    }
    
    /**
     * Check if an acs exists
     *
     * @param int $fleet_acs    Fleet ACS ID
     * @param int $galaxy       Galaxy
     * @param int $system       System
     * @param int $planet       Planet
     * @param int $planet_type  Planet Type
     *
     * @return boolean
     */
    public function acsExists($fleet_acs, $galaxy, $system, $planet, $planet_type)
    {
        return $this->db->queryFetch(
            "SELECT 
                COUNT(`acs_fleet_id`) AS `amount`
            FROM `" . ACS_FLEETS . "`
            WHERE `acs_fleet_id` = '" . $fleet_acs . "' AND 
                `acs_fleet_galaxy` = '" . $galaxy . "' AND 
                `acs_fleet_system` = '" . $system . "' AND 
                `acs_fleet_planet` = '" . $planet . "' AND 
                `acs_fleet_planet_type` = '" . $planet_type . "';"
        )['amount'] > 0;
    }
    
    /**
     * Get planet owner by coords
     * 
     * @param int $g    Galaxy
     * @param int $s    System
     * @param int $p    Planet
     * @param int $pt   Planet Type
     * 
     * @return bool
     */
    public function getPlanetOwnerByCoords($g, $s, $p, $pt)
    {
        return $this->db->queryFetch(
            "SELECT `planet_user_id`
            FROM `" . PLANETS . "`
            WHERE `planet_galaxy` = '" . $g . "'
                AND `planet_system` = '" . $s . "'
                AND `planet_planet` = '" . $p . "'
                AND `planet_type` = '" . $pt . "';"
        );
    }
    
    /**
     * Get ACS count
     * 
     * @param type $acs_fleet_id
     * 
     * @return int
     */
    public function getAcsCount($acs_fleet_id): int
    {
        return $this->db->queryFetch(
            "SELECT COUNT(`acs_fleet_id`) AS `acs_amount`
            FROM `" . ACS_FLEETS . "`
            WHERE `acs_fleet_id` = '" . $acs_fleet_id . "'"
        )['acs_amount'];
    }

    /**
     * Insert a new fleet
     * 
     * @param array $fleet_data
     * @param array $planet_data
     * 
     * @return boolean
     */
    public function insertNewFleet(array $fleet_data, array $planet_data): bool
    {
        try {
            
            $this->db->beginTransaction();
            
            // prepare the query
            foreach ($fleet_data as $field => $value) {
               
                $sql[] = "`" . $field . "` = '" . $value . "'";
            }

            $this->db->query(
                "INSERT INTO `" . FLEETS . "` SET" . join(',', $sql) . ';'
            );
            
            // remove ships and resources
            $this->updatePlanet($planet_data);   
            
            $this->db->commitTransaction();

            return true;
            
        } catch(Exception $e) {
            
            $this->db->rollbackTransaction();
            
            return false;
        }
    }
    
    /**
     * Update planet based on the received values
     * 
     * @param array $planet_data Planet Data
     * 
     * @return void
     */
    public function updatePlanet(array $planet_data)
    {
        $this->db->query(
            "UPDATE `" . PLANETS . "` AS p
            INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id` SET
            {$planet_data['sub_query']}
            `planet_metal` = `planet_metal` - " . $planet_data['planet_metal'] . ",
            `planet_crystal` = `planet_crystal` - " . $planet_data['planet_crystal'] . ",
            `planet_deuterium` = `planet_deuterium` - " . ($planet_data['planet_deuterium'] + $planet_data['consumption']) . "
            WHERE `planet_id` = " . $planet_data['planet_id'] . ";"
        );
    }
}

/* end of fleet.php */
