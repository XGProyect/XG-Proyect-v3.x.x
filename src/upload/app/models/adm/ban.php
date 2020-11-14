<?php declare (strict_types = 1);

/**
 * Ban Model
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

/**
 * Ban Class
 */
class Ban extends Model
{
    /**
     * Unban user by username
     *
     * @param string $username
     * @return void
     */
    public function unbanUser(string $user_name): void
    {
        $clean_user_name = $this->db->escapeValue($user_name);

        $this->db->query(
            "DELETE FROM `" . BANNED . "`
            WHERE `banned_who` = '" . $clean_user_name . "'"
        );

        $this->db->query(
            "UPDATE `" . USERS . "` SET
                `user_banned` = '0'
            WHERE `user_name` = '" . $clean_user_name . "'
            LIMIT 1"
        );
    }

    /**
     * Get banned user data
     *
     * @param string $ban_name
     * @return array|null
     */
    public function getBannedUserData(string $ban_name): ?array
    {
        $clean_user_name = $this->db->escapeValue($ban_name);

        return $this->db->queryFetch(
            "SELECT
                b.*,
                p.`preference_user_id`,
                p.`preference_vacation_mode`
            FROM `" . BANNED . "` AS b
            INNER JOIN `" . PREFERENCES . "` AS p
                ON p.`preference_user_id` = (
                    SELECT
                        `user_id`
                    FROM `" . USERS . "`
                    WHERE `user_name` = '" . $clean_user_name . "'
                    LIMIT 1
                )
            WHERE `banned_who` = '" . $clean_user_name . "'"
        );
    }

    /**
     * Ban user or update ban data
     *
     * @param array|null $banned_user
     * @param array $ban_data
     * @param string|null $vacation_mode
     * @return void
     */
    public function setOrUpdateBan(?array $banned_user, array $ban_data, ?string $vacation_mode): void
    {
        try {
            $this->db->beginTransaction();

            if (isset($banned_user)) {
                $this->db->query(
                    "UPDATE `" . BANNED . "`  SET
                        `banned_who` = '" . $ban_data['ban_name'] . "',
                        `banned_theme` = '" . $ban_data['ban_reason'] . "',
                        `banned_time` = '" . $ban_data['ban_time'] . "',
                        `banned_longer` = '" . $ban_data['ban_until'] . "',
                        `banned_author` = '" . $ban_data['ban_author'] . "',
                        `banned_email` = '" . $ban_data['ban_author_email'] . "'
                    WHERE `banned_who` = '" . $ban_data['ban_name'] . "';"
                );
            } else {
                $this->db->query(
                    "INSERT INTO `" . BANNED . "` SET
                        `banned_who` = '" . $ban_data['ban_name'] . "',
                        `banned_theme` = '" . $ban_data['ban_reason'] . "',
                        `banned_time` = '" . $ban_data['ban_time'] . "',
                        `banned_longer` = '" . $ban_data['ban_until'] . "',
                        `banned_author` = '" . $ban_data['ban_author'] . "',
                        `banned_email` = '" . $ban_data['ban_author_email'] . "';"
                );
            }

            $user_id = $this->db->queryFetch(
                "SELECT
                    `user_id`
                FROM `" . USERS . "`
                WHERE `user_name` = '" . $ban_data['ban_name'] . "' LIMIT 1"
            )['user_id'];

            $this->db->query(
                "UPDATE `" . USERS . "` AS u, `" . PREFERENCES . "` AS pr, `" . PLANETS . "` AS p SET
                    u.`user_banned` = '" . $ban_data['ban_until'] . "',
                    pr.`preference_vacation_mode` = " . (isset($vacation_mode) && $vacation_mode != '' ? "'" . time() . "'" : 'NULL') . ",
                    p.`planet_building_metal_mine_percent` = '0',
                    p.`planet_building_crystal_mine_percent` = '0',
                    p.`planet_building_deuterium_sintetizer_percent` = '0',
                    p.`planet_building_solar_plant_percent` = '0',
                    p.`planet_building_fusion_reactor_percent` = '0',
                    p.`planet_ship_solar_satellite_percent` = '0'
                WHERE u.`user_id` = " . $user_id . "
                        AND pr.`preference_user_id` = " . $user_id . "
                        AND p.`planet_user_id` = " . $user_id . ";"
            );

            $this->db->commitTransaction();
        } catch (Exception $e) {
            $this->db->rollbackTransaction();
        }
    }

    /**
     * Get list of users based on the provided conditions
     *
     * @param string $where_authlevel
     * @param string $where_banned
     * @param string $query_order
     * @return array
     */
    public function getListOfUsers(string $where_authlevel, string $where_banned, string $query_order): array
    {
        return $this->db->queryFetchAll(
            "SELECT
                `user_id`,
                `user_name`,
                `user_banned`
            FROM `" . USERS . "`
            " . $where_authlevel . " " . $where_banned . "
            ORDER BY " . $query_order . " ASC"
        );
    }

    /**
     * Get list of banned users
     *
     * @param string $order
     * @return array|null
     */
    public function getBannedUsers(string $order): ?array
    {
        return $this->db->queryFetchAll(
            "SELECT
                `user_id`,
                `user_name`
            FROM `" . USERS . "`
            WHERE `user_banned` <> '0'
            ORDER BY " . $order . " ASC"
        );
    }
}
