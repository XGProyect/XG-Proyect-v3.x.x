<?php
/**
 * Buildings Model
 *
 * PHP Version 7.1+
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
     * Insert a new building queue and deduct resources
     *
     * @param array $planet
     * @return void
     */
    public function updatePlanetBuildingQueue(array $planet): void
    {
        $this->db->query(
            "UPDATE `" . PLANETS . "` SET
                `planet_metal` = '" . $planet['planet_metal'] . "',
                `planet_crystal` = '" . $planet['planet_crystal'] . "',
                `planet_deuterium` = '" . $planet['planet_deuterium'] . "',
                `planet_b_building` = '" . $planet['planet_b_building'] . "',
                `planet_b_building_id` = '" . $planet['planet_b_building_id'] . "'
            WHERE `planet_id` = '" . $planet['planet_id'] . "';"
        );
    }
}

/* end of buildings.php */
