<?php
/**
 * Maker Model
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\models\adm;

use App\core\Model;
use App\libraries\Functions;
use App\libraries\PlanetLib;

/**
 * Maker Class
 */
class Maker extends Model
{
    /**
     * Get list of users without an alliance or without pending requests
     *
     * @return array
     */
    public function getUsersWithoutAlliance(): array
    {
        return $this->db->queryFetchAll(
            "SELECT
                `user_id`,
                `user_name`
            FROM `" . USERS . "`
            WHERE `user_ally_id` = '0'
                AND `user_ally_request` = '0';"
        ) ?? [];
    }

    /**
     * Get all server users
     *
     * @return array
     */
    public function getAllServerUsers(): array
    {
        return $this->db->queryFetchAll(
            "SELECT
                `user_id`,
                `user_name`
            FROM `" . USERS . "`;"
        ) ?? [];
    }

    /**
     * Get all planets that their status is not destroyed
     *
     * @return array
     */
    public function getAllActivePlanets(): array
    {
        return $this->db->queryFetchAll(
            "SELECT
                `planet_id`,
                `planet_name`,
                `planet_galaxy`,
                `planet_system`,
                `planet_planet`
            FROM `" . PLANETS . "`
            WHERE `planet_destroyed` = '0'
                AND `planet_type` = '1';"
        ) ?? [];
    }

    /**
     * Check if the username exists
     *
     * @param string $username
     * @return array
     */
    public function checkUserName(string $username): array
    {
        return $this->db->queryFetch(
            "SELECT
                `user_name`
            FROM `" . USERS . "`
            WHERE `user_name` = '" . $this->db->escapeValue($username) . "'
            LIMIT 1"
        ) ?? [];
    }

    /**
     * Check if the email exists
     *
     * @param string $email
     * @return array
     */
    public function checkUserEmail(string $email): array
    {
        return $this->db->queryFetch(
            "SELECT
                `user_email`
            FROM `" . USERS . "`
            WHERE `user_email` = '" . $this->db->escapeValue($email) . "'
            LIMIT 1"
        ) ?? [];
    }

    /**
     * Check if the planet exists
     *
     * @param string $email
     * @return array
     */
    public function checkPlanet(int $galaxy, int $system, int $planet): array
    {
        return $this->db->queryFetch(
            "SELECT COUNT(`planet_id`) AS `count`
            FROM `" . PLANETS . "`
            WHERE
                `planet_galaxy` = '" . $galaxy . "'
            AND
                `planet_system` = '" . $system . "'
            AND
                `planet_planet` = '" . $planet . "'
            LIMIT 1;"
        ) ?? [];
    }

    /**
     * Create new user and set their new planet
     *
     * @param string $name
     * @param string $email
     * @param integer $auth
     * @param string $pass
     * @param integer $galaxy
     * @param integer $system
     * @param integer $planet
     * @return void
     */
    public function createNewUser(string $name, string $email, int $auth, string $pass, int $galaxy, int $system, int $planet): void
    {
        try {
            $time = time();

            $this->db->beginTransaction();

            $this->db->query(
                "INSERT INTO `" . USERS . "` SET
                    `user_name` = '" . $this->db->escapeValue($name) . "',
                    `user_email` = '" . $this->db->escapeValue($email) . "',
                    `user_ip_at_reg` = '" . $_SERVER['REMOTE_ADDR'] . "',
                    `user_home_planet_id` = '0',
                    `user_register_time` = '" . $time . "',
                    `user_onlinetime` = '" . $time . "',
                    `user_authlevel` = '" . $auth . "',
                    `user_password` = '" . Functions::hash($pass) . "';"
            );

            $last_user_id = $this->db->insertId();

            $this->db->query(
                "INSERT INTO `" . RESEARCH . "` SET
                    `research_user_id` = '" . $last_user_id . "';"
            );

            $this->db->query(
                "INSERT INTO `" . USERS_STATISTICS . "` SET
                    `user_statistic_user_id` = '" . $last_user_id . "';"
            );

            $this->db->query(
                "INSERT INTO `" . PREMIUM . "` (`premium_user_id`, `premium_dark_matter`)
                VALUES('" . $last_user_id . "', '" . Functions::readConfig('registration_dark_matter') . "');"
            );

            $this->db->query(
                "INSERT INTO `" . PREFERENCES . "` SET
                    `preference_user_id` = '" . $last_user_id . "';"
            );

            $creator = new PlanetLib;
            $creator->setNewPlanet($galaxy, $system, $planet, $last_user_id, '', true);

            $last_planet_id = $this->db->insertId();

            $this->db->query(
                "UPDATE `" . USERS . "` SET
                    `user_home_planet_id` = '" . $last_planet_id . "',
                    `user_current_planet` = '" . $last_planet_id . "',
                    `user_galaxy` = '" . $galaxy . "',
                    `user_system` = '" . $system . "',
                    `user_planet` = '" . $planet . "'
                WHERE
                    `user_id` = '" . $last_user_id . "'
                LIMIT 1;"
            );

            $this->db->commitTransaction();
        } catch (Exception $e) {
            $this->db->rollbackTransaction();
        }
    }

    /**
     * Check if the alliance exists
     *
     * @param string $alliance_name
     * @param string $alliance_tag
     * @return array
     */
    public function checkAlliance(string $alliance_name, string $alliance_tag): array
    {
        return $this->db->queryFetch(
            "SELECT
                `alliance_id`
            FROM `" . ALLIANCE . "`
            WHERE `alliance_name` = '" . $this->db->escapeValue($alliance_name) . "'
                OR `alliance_tag` = '" . $this->db->escapeValue($alliance_tag) . "';"
        ) ?? [];
    }

    /**
     * Create a new alliance
     *
     * @param string $alliance_name
     * @param string $alliance_tag
     * @param int $alliance_founder
     * @param string $rank
     * @return array
     */
    public function createAlliance(string $alliance_name, string $alliance_tag, int $alliance_founder, string $rank): void
    {
        try {
            $time = time();

            $this->db->beginTransaction();

            $rights_string = '[{"rank":"Founder","rights":{"1":1,"2":1,"3":1,"4":1,"5":1,"6":1,"7":1,"8":1,"9":1}},{"rank":"Newcomer","rights":{"1":0,"2":0,"3":0,"4":0,"5":0,"6":0,"7":0,"8":0,"9":0}}]';

            $this->db->query(
                "INSERT INTO `" . ALLIANCE . "` SET
                `alliance_name` = '" . $alliance_name . "',
                `alliance_tag` = '" . $alliance_tag . "' ,
                `alliance_owner` = '" . (int) $user_id . "',
                `alliance_register_time` = '" . time() . "',
                `alliance_ranks` = '" . $rights_string . "'"
            );

            $new_alliance_id = $this->db->insertId();

            $this->db->query(
                "INSERT INTO `" . ALLIANCE_STATISTICS . "` SET
                    `alliance_statistic_alliance_id`='" . $new_alliance_id . "'"
            );

            $this->db->query(
                "UPDATE `" . USERS . "` SET
                    `user_ally_id` = '" . $new_alliance_id . "',
                    `user_ally_register_time` = '" . $time . "'
                WHERE `user_id` = '" . $alliance_founder . "'"
            );

            $this->db->commitTransaction();
        } catch (Exception $e) {
            $this->db->rollbackTransaction();
        }
    }

    /**
     * Check if user exists by its ID
     *
     * @return array
     */
    public function checkUserById(int $user_id): array
    {
        return $this->db->queryFetch(
            "SELECT *
            FROM `" . USERS . "`
            WHERE `user_id` = '" . $user_id . "'"
        ) ?? [];
    }

    /**
     * Create new planet with the provided details
     *
     * @param integer $galaxy
     * @param integer $system
     * @param integer $planet
     * @param integer $user_id
     * @param integer $field_max
     * @param integer $name
     * @return void
     */
    public function createNewPlanet(int $galaxy, int $system, int $planet, int $user_id, int $field_max, string $name): void
    {
        try {
            $this->db->beginTransaction();

            $creator = new PlanetLib;
            $creator->setNewPlanet($galaxy, $system, $planet, $user_id, '', '', false);

            $this->db->query(
                "UPDATE `" . PLANETS . "` SET
                    `planet_field_max` = '" . $field_max . "',
                    `planet_name` = '" . $name . "'
                    WHERE `planet_galaxy` = '" . $galaxy . "'
                        AND `planet_system` = '" . $system . "'
                        AND `planet_planet` = '" . $planet . "'
                        AND `planet_type` = '1'"
            );

            $this->db->commitTransaction();
        } catch (Exception $e) {
            $this->db->rollbackTransaction();
        }
    }

    /**
     * @param int $planet_id
     * @return mixed
     */
    public function checkMoon(int $planet_id): array
    {
        return $this->db->queryFetch(
            "SELECT
                p.*,
                (
                    SELECT
                        `planet_id`
                    FROM
                        `" . PLANETS . "`
                    WHERE
                            `planet_galaxy` = (
                                SELECT `planet_galaxy`
                                FROM `" . PLANETS . "`
                                WHERE `planet_id` = '" . $planet_id . "'
                                        AND `planet_type` = 1
                        )
                        AND `planet_system` = (
                                                SELECT `planet_system`
                                                FROM `" . PLANETS . "`
                                                WHERE `planet_id` = '" . $planet_id . "'
                                                    AND `planet_type` = 1
                        )
                        AND `planet_planet` = (
                                                SELECT `planet_planet`
                                                FROM `" . PLANETS . "`
                                                WHERE `planet_id` = '" . $planet_id . "'
                                                    AND `planet_type` = 1
                        )
                        AND `planet_type` = 3
                ) AS `id_moon`
            FROM `" . PLANETS . "` AS p
            WHERE
                p.`planet_id` = '" . $planet_id . "'
            AND
                p.`planet_type` = '1'"
        ) ?? [];
    }

    /**
     * Create a new moon
     *
     * @param integer $galaxy
     * @param integer $system
     * @param integer $planet
     * @param integer $owner
     * @param string $moon_name
     * @param integer $size
     * @param integer $max_fields
     * @param integer $mintemp
     * @param integer $maxtemp
     * @return void
     */
    public function createNewMoon(int $galaxy, int $system, int $planet, int $owner, string $moon_name, int $size, int $max_fields, int $mintemp, int $maxtemp): void
    {
        $creator = new PlanetLib;
        $creator->setNewMoon($galaxy, $system, $planet, $owner, $moon_name, 0, $size, $max_fields, $mintemp, $maxtemp);
    }
}
