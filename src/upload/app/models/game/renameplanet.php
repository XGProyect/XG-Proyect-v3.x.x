<?php declare (strict_types = 1);

/**
 * Renameplanet Model
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\models\game;

use App\core\Model;

/**
 * Renameplanet Class
 */
class Renameplanet extends Model
{
    /**
     * Get all fleets incoming/outgoing to the planet
     *
     * @param integer $user_id
     * @param integer $galaxy
     * @param integer $system
     * @param integer $planet
     * @return array|null
     */
    public function getFleets(int $user_id, int $galaxy, int $system, int $planet): ?array
    {
        return $this->db->queryFetchAll(
            "SELECT
                `fleet_owner`,
                `fleet_target_owner`,
                `fleet_end_type`,
                `fleet_mess`
            FROM `" . FLEETS . "`
            WHERE (
                    fleet_owner = '" . $user_id . "' AND
                    fleet_start_galaxy = '" . $galaxy . "' AND
                    fleet_start_system = '" . $system . "' AND
                    fleet_start_planet = '" . $planet . "'
            )
            OR
            (
                fleet_target_owner = '" . $user_id . "' AND
                fleet_end_galaxy = '" . $galaxy . "' AND
                fleet_end_system = '" . $system . "' AND
                fleet_end_planet = '" . $planet . "'
            )"
        );
    }

    /**
     * Delete moon and planet
     *
     * @param integer $user_id
     * @param integer $planet_id
     * @param integer $galaxy
     * @param integer $system
     * @param integer $planet
     * @return void
     */
    public function deleteMoonAndPlanet(int $user_id, int $planet_id, int $galaxy, int $system, int $planet): void
    {
        $this->db->query(
            "UPDATE `" . PLANETS . "` AS p, `" . PLANETS . "` AS m, `" . USERS . "` AS u SET
                p.`planet_destroyed` = '" . (time() + (PLANETS_LIFE_TIME * 3600)) . "',
                m.`planet_destroyed` = '" . (time() + (PLANETS_LIFE_TIME * 3600)) . "',
                u.`user_current_planet` = u.`user_home_planet_id`
            WHERE p.`planet_id` = '" . $planet_id . "' AND
                m.`planet_galaxy` = '" . $galaxy . "' AND
                m.`planet_system` = '" . $system . "' AND
                m.`planet_planet` = '" . $planet . "' AND
                m.`planet_type` = '3' AND
                u.`user_id` = '" . $user_id . "';"
        );
    }

    /**
     * Delete planet
     *
     * @param integer $user_id
     * @param integer $planet_id
     * @return void
     */
    public function deletePlanet(int $user_id, int $planet_id): void
    {
        $this->db->query(
            "UPDATE `" . PLANETS . "` AS p, `" . USERS . "` AS u SET
                p.`planet_destroyed` = '" . (time() + (PLANETS_LIFE_TIME * 3600)) . "',
                u.`user_current_planet` = u.`user_home_planet_id`
            WHERE p.`planet_id` = '" . $planet_id . "' AND
                u.`user_id` = '" . $user_id . "';"
        );
    }

    /**
     * Update planet name
     *
     * @param string $new_name
     * @param integer $planet_id
     * @return void
     */
    public function updatePlanetName(string $new_name, int $planet_id): void
    {
        $this->db->query(
            "UPDATE `" . PLANETS . "` SET
                `planet_name` = '" . $this->db->escapeValue($new_name) . "'
            WHERE `planet_id` = '" . $planet_id . "'
            LIMIT 1;"
        );
    }
}
