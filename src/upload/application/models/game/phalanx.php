<?php

declare (strict_types = 1);

/**
 * Phalanx Model
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

use application\core\Model;

/**
 * Phalanx Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Phalanx extends Model
{
    /**
     * Reduce the phalanx cost from the planet
     *
     * @param integer $planet_id
     * @return void
     */
    public function reduceDeuterium(int $planet_id): void
    {
        $this->db->query(
            "UPDATE `" . PLANETS . "` SET
                `planet_deuterium` = `planet_deuterium` - '" . PHALANX_COST . "'
            WHERE `planet_id` = '" . $planet_id . "';"
        );
    }

    /**
     * Get the current planet ID and name
     *
     * @param integer $galaxy
     * @param integer $system
     * @param integer $planet
     * @return array
     */
    public function getTargetPlanetIdAndName(int $galaxy, int $system, int $planet): array
    {
        return $this->db->queryFetch(
            "SELECT
                `planet_name`,
                `planet_user_id`
            FROM `" . PLANETS . "`
            WHERE `planet_galaxy` = '" . $galaxy . "' AND
                    `planet_system` = '" . $system . "' AND
                    `planet_planet` = '" . $planet . "' AND
                    `planet_type` = 1"
        );
    }

    /**
     * Get the current planet moon status
     *
     * @param integer $galaxy
     * @param integer $system
     * @param integer $planet
     * @return array|null
     */
    public function getTargetMoonStatus(int $galaxy, int $system, int $planet): ?array
    {
        return $this->db->queryFetch(
            "SELECT
                `planet_destroyed`
            FROM `" . PLANETS . "`
            WHERE `planet_galaxy` = '" . $galaxy . "' AND
                    `planet_system` = '" . $system . "' AND
                    `planet_planet` = '" . $planet . "' AND
                    `planet_type` = 3 "
        );
    }

    /**
     * Get fleets from/to selected planet
     *
     * @param integer $galaxy
     * @param integer $system
     * @param integer $planet
     * @return array|null
     */
    public function getFleetsToTarget(int $galaxy, int $system, int $planet): ?array
    {
        return $this->db->queryFetchAll(
            "SELECT
                f.*
            FROM `" . FLEETS . "` AS f
            WHERE (
                    (
                        f.`fleet_start_galaxy` = '" . $galaxy . "' AND
                        f.`fleet_start_system` = '" . $system . "' AND
                        f.`fleet_start_planet` = '" . $planet . "'
                    ) OR
                    (
                        f.`fleet_end_galaxy` = '" . $galaxy . "' AND
                        f.`fleet_end_system` = '" . $system . "' AND
                        f.`fleet_end_planet` = '" . $planet . "'
                    )
            ) ;"
        );
    }
}

/* end of phalanx.php */
