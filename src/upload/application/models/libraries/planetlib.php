<?php

declare (strict_types = 1);

/**
 * PlanetLib Model
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
namespace application\models\libraries;

use application\core\Database;

/**
 * PlanetLib Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class PlanetLib
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
     * Check if a planet exists
     *
     * @param integer $galaxy
     * @param integer $system
     * @param integer $position
     * @return array
     */
    public function checkPlanetExists(int $galaxy, int $system, int $position): ?array
    {
        return $this->db->queryFetch(
            "SELECT
                `planet_id`
            FROM `" . PLANETS . "`
            WHERE `planet_galaxy` = '" . $galaxy . "' AND
                `planet_system` = '" . $system . "' AND
                `planet_planet` = '" . $position . "';"
        );
    }

    /**
     * Check if a moon exists
     *
     * @param integer $galaxy
     * @param integer $system
     * @param integer $position
     * @return array
     */
    public function checkMoonExists(int $galaxy, int $system, int $position): ?array
    {
        return $this->db->queryFetch(
            "SELECT pm2.`planet_id`,
                pm2.`planet_name`,
                pm2.`planet_temp_max`,
                pm2.`planet_temp_min`,
                (
                    SELECT
                        pm.`planet_id` AS `id_moon`
                    FROM `" . PLANETS . "` AS pm
                        WHERE pm.`planet_galaxy` = '" . $galaxy . "' AND
                                pm.`planet_system` = '" . $system . "' AND
                                pm.`planet_planet` = '" . $position . "' AND
                                pm.`planet_type` = 3) AS `id_moon`
                FROM `" . PLANETS . "` AS pm2
                WHERE pm2.`planet_galaxy` = '" . $galaxy . "' AND
                        pm2.`planet_system` = '" . $system . "' AND
                        pm2.`planet_planet` = '" . $position . "';"
        );
    }

    /**
     * Create a new planet
     *
     * @param array $data
     * @param boolean $full_insert
     * @return void
     */
    public function createNewPlanet(array $data, bool $full_insert = true): void
    {
        if (is_array($data)) {
            $insert_query = 'INSERT INTO `' . PLANETS . '` SET ';

            foreach ($data as $column => $value) {
                $insert_query .= "`" . $column . "` = '" . $value . "', ";
            }

            // Remove last comma
            $insert_query = substr_replace($insert_query, '', -2) . ';';

            $this->db->query($insert_query);

            // insert extra required tables
            if ($full_insert) {
                // get the last inserted planet id
                $planet_id = $this->db->insertId();

                // create the buildings, defenses and ships tables
                $this->insertPlanetBuildings($planet_id);
                $this->insertPlanetDefenses($planet_id);
                $this->insertPlanetShips($planet_id);
            }
        }
    }

    /**
     * Insert a new record into buildings table
     *
     * @param integer $planet_id
     * @return void
     */
    private function insertPlanetBuildings(int $planet_id): void
    {
        $this->db->query(
            "INSERT INTO `" . BUILDINGS . "` SET `building_planet_id` = '" . $planet_id . "';"
        );
    }

    /**
     * Insert a new record into defenses table
     *
     * @param integer $planet_id
     * @return void
     */
    private function insertPlanetDefenses(int $planet_id): void
    {
        $this->db->query(
            "INSERT INTO `" . DEFENSES . "` SET `defense_planet_id` = '" . $planet_id . "';"
        );
    }

    /**
     * Insert a new record into ships table
     *
     * @param integer $planet_id
     * @return void
     */
    private function insertPlanetShips(int $planet_id): void
    {
        $this->db->query(
            "INSERT INTO `" . SHIPS . "` SET `ship_planet_id` = '" . $planet_id . "';"
        );
    }
}

/* end of planetlib.php */
