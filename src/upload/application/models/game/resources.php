<?php

declare (strict_types = 1);

/**
 * Resources Model
 *
 * PHP Version 7.1+
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\models\game;

use application\core\Database;

/**
 * Resources Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Resources
{
    private $db = null;

    /**
     * Constructor
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        // use this to make queries
        $this->db = $db;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->db->closeConnection();
    }

    /**
     * Update current planet
     *
     * @param array $planet
     * @param string $sub_query
     * @return void
     */
    public function updateCurrentPlanet(array $planet, string $sub_query): void
    {
        $this->db->query(
            "UPDATE `" . PLANETS . "` SET
                `planet_id` = '" . $planet['planet_id'] . "'
                $sub_query
                WHERE `planet_id` = '" . $planet['planet_id'] . "';"
        );
    }
}

/* end of resources.php */
