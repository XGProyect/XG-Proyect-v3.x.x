<?php
/**
 * Overview Model
 *
 * PHP Version 7.1+
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
     * Constructor
     *
     * @return void
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
     * Get own fleets
     *
     * @param type $user_id
     *
     * @return mixed
     */
    public function getOwnFleets($user_id)
    {
        if ((int) $user_id > 0) {
            return $this->db->queryFetchAll(
                "SELECT
                    f.*,
                    po.`planet_name` AS `start_planet_name`,
                    pt.`planet_name` AS `target_planet_name`,
                    uo.`user_name` AS `start_planet_user`,
                    ut.`user_name` AS `target_planet_user`
                FROM `" . FLEETS . "` f
                    INNER JOIN `" . USERS . "` uo
                    	ON uo.`user_id` = f.`fleet_owner`
                    LEFT JOIN `" . USERS . "` ut
                    	ON ut.`user_id` = f.`fleet_target_owner`
                    INNER JOIN `" . PLANETS . "` po
                	ON
                        (
                            po.planet_galaxy = f.fleet_start_galaxy AND
                            po.planet_system = f.fleet_start_system AND
                            po.planet_planet = f.fleet_start_planet AND
                            po.planet_type = f.fleet_start_type
                        )
                    LEFT JOIN `" . PLANETS . "` pt
                	ON
                        (
                            pt.planet_galaxy = f.fleet_end_galaxy AND
                            pt.planet_system = f.fleet_end_system AND
                            pt.planet_planet = f.fleet_end_planet AND
                            pt.planet_type = f.fleet_end_type
                        )
                WHERE f.`fleet_owner` = '" . $user_id . "' OR
                    f.`fleet_target_owner` = '" . $user_id . "'"
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
        if ((int) $user_id > 0) {
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
