<?php declare (strict_types = 1);

/**
 * Preferences Model
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
 * Preferences Class
 */
class Preferences extends Model
{
    /**
     * Get all preferences by a certain user
     *
     * @param int $user_id
     *
     * @return array
     */
    public function getAllPreferencesByUserId(int $user_id): array
    {
        return $this->db->queryFetchAll(
            "SELECT
                p.*
            FROM `" . PREFERENCES . "` p
            WHERE p.`preference_user_id` = '" . $user_id . "';"
        ) ?? [];
    }

    /**
     * Check if the nickname exists
     *
     * @param string $user_name
     * @return array
     */
    public function checkIfNicknameExists(string $nickname): array
    {
        return $this->db->queryFetch(
            "SELECT `user_id`
            FROM `" . USERS . "`
            WHERE `user_name` = '" . $this->db->escapeValue($nickname) . "'
            LIMIT 1;"
        ) ?? [];
    }

    /**
     * Check if the email exists
     *
     * @param string $email
     * @return array
     */
    public function checkIfEmailExists(string $email): array
    {
        return $this->db->queryFetch(
            "SELECT `user_email`
            FROM `" . USERS . "`
            WHERE `user_email` = '" . $this->db->escapeValue($email) . "'
            LIMIT 1;"
        ) ?? [];
    }

    /**
     * Update validated fields
     *
     * @param array $fields
     * @param integer $user_id
     * @return void
     */
    public function updateValidatedFields(array $fields, int $user_id): void
    {
        $columns_to_update = [];

        foreach ($fields as $column => $value) {
            if (strpos($column, 'user_') !== false) {
                $columns_to_update[] = "u.`" . $column . "` = '" . $value . "'";
            }

            if (strpos($column, 'preference_') !== false) {
                $columns_to_update[] = "p.`" . $column . "` = " . (is_null($value) ? 'NULL' : "'" . $value . "'");
            }
        }

        $this->db->query(
            "UPDATE " . USERS . " AS u, " . PREFERENCES . " AS p SET
            " . join(', ', $columns_to_update) . "
            WHERE u.`user_id` = '" . $user_id . "'
                AND p.`preference_user_id` = '" . $user_id . "';"
        );
    }

    /**
     * Check the empire current activity
     *
     * @param integer $user_id
     * @return boolean
     */
    public function isEmpireActive(int $user_id): bool
    {
        if ($user_id > 0) {
            $activity = $this->db->queryFetch(
                "SELECT (
                    (
                        SELECT
                            COUNT(f.`fleet_id`) AS quantity
                        FROM `" . FLEETS . "` f
                        WHERE f.`fleet_owner` = '" . $user_id . "'
                    )
                +
                    (
                        SELECT
                            COUNT(p.`planet_id`) AS quantity
                        FROM `" . PLANETS . "` p
                        WHERE p.`planet_user_id` = '" . $user_id . "'
                            AND (p.`planet_b_building` <> 0
                                OR `planet_b_tech` <> 0
                                OR `planet_b_hangar` <> 0
                            )
                    )
                ) as total"
            );

            return ($activity['total'] > 0);
        }

        return false;
    }

    /**
     * Start vacation mode first checking if it's possible to set
     *
     * @param integer $user_id
     * @return boolean
     */
    public function startVacation(int $user_id): bool
    {
        if (!$this->isEmpireActive($user_id)) {
            $this->db->query(
                "UPDATE `" . PREFERENCES . "` pr, `" . PLANETS . "` p SET
                    pr.`preference_vacation_mode` = '" . time() . "',
                    p.`planet_building_metal_mine_percent` = '0',
                    p.`planet_building_crystal_mine_percent` = '0',
                    p.`planet_building_deuterium_sintetizer_percent` = '0',
                    p.`planet_building_solar_plant_percent` = '0',
                    p.`planet_building_fusion_reactor_percent` = '0',
                    p.`planet_ship_solar_satellite_percent` = '0'
                WHERE pr.`preference_user_id` = '" . $user_id . "'
                    AND p.`planet_user_id` = '" . $user_id . "';"
            );

            return true;
        }

        return false;
    }

    /**
     * Remove vacation mode and set production to maximum
     *
     * @param integer $user_id
     * @return void
     */
    public function endVacation(int $user_id): void
    {
        if ($user_id > 0) {
            $this->db->query(
                "UPDATE `" . PREFERENCES . "` pr, `" . PLANETS . "` p SET
                    pr.`preference_vacation_mode` = NULL,
                    p.`planet_last_update` = '" . time() . "',
                    p.`planet_building_metal_mine_percent` = '10',
                    p.`planet_building_crystal_mine_percent` = '10',
                    p.`planet_building_deuterium_sintetizer_percent` = '10',
                    p.`planet_building_solar_plant_percent` = '10',
                    p.`planet_building_fusion_reactor_percent` = '10',
                    p.`planet_ship_solar_satellite_percent` = '10'
                WHERE pr.`preference_user_id` = '" . $user_id . "'
                    AND p.`planet_user_id` = '" . $user_id . "';"
            );
        }
    }
}
