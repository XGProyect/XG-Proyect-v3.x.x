<?php
/**
 * Buildings Model
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
 * Buildings Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Buildings
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
     * Insert a new building queue
     * 
     * @param type $building_id
     * @param type $queue
     * @param type $planet_id
     * 
     * @return void
     */
    public function updatePlanetBuildingQueue($queue, $building_id, $planet_id)
    {
        $this->db->query(
            "UPDATE " . PLANETS . " SET
                `planet_b_building_id` = '" . $queue . "',
                `planet_b_building` = '" . $building_id . "'
            WHERE `planet_id` = '" . $planet_id . "';"
        );
    }
}

/* end of buildings.php */