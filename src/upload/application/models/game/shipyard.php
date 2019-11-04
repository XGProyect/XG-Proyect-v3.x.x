<?php
/**
 * Shipyard Model
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
 * Shipyard Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Shipyard
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
     * Update the planets table, set the items to build and reduce the resources
     *
     * @param array $planet Current planet data
     *
     * @return void
     */
    public function insertItemsToBuild($resources, $shipyard_queue, $planet_id)
    {
        $this->db->query(
            "UPDATE " . PLANETS . " AS p SET
                p.`planet_b_hangar_id` = CONCAT(p.`planet_b_hangar_id`, '" . $shipyard_queue . "'),
                p.`planet_metal` = '" . $resources['metal'] . "',
                p.`planet_crystal` = '" . $resources['crystal'] . "',
                p.`planet_deuterium` = '" . $resources['deuterium'] . "'
            WHERE p.`planet_id` = '" . $planet_id . "';"
        );
    }
}

/* end of shipyard.php */
