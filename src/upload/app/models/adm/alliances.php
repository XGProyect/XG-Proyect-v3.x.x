<?php
/**
 * Alliances Model
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
 * Alliances Class
 */
class Alliances extends Model
{
    /**
     * Get all alliance data
     *
     * @return array
     */
    public function getAllAllianceDataById(int $id): array
    {
        return $this->db->queryFetch(
            "SELECT
                a.*,
                als.*
            FROM `" . ALLIANCE . "` AS a
            INNER JOIN `" . ALLIANCE_STATISTICS . "` AS als ON als.alliance_statistic_alliance_id = a.alliance_id
            WHERE (a.`alliance_id` = '{$id}')
            LIMIT 1;"
        );
    }

    /**
     * Check if an alliance exists verifying its name or tag
     *
     * @param string $alliance
     * @return array|null
     */
    public function checkAllianceByNameOrTag(string $alliance): ?array
    {
        return $this->db->queryFetch(
            "SELECT `alliance_id`
                FROM `" . ALLIANCE . "`
                WHERE `alliance_name` = '" . $alliance . "' OR
                    `alliance_tag` = '" . $alliance . "';"
        );
    }

    /**
     * Check if the alliance tag exists
     *
     * @param string $alliance_tag
     * @return boolean
     */
    public function checkAllianceTag(string $alliance_tag): bool
    {
        $alliance_tag = trim($alliance_tag);
        $alliance_tag = htmlspecialchars_decode($alliance_tag, ENT_QUOTES);

        if ($alliance_tag == '' or is_null($alliance_tag) or (strlen($alliance_tag) < 3) or (strlen($alliance_tag) > 8)) {
            return false;
        }

        $alliance_tag = $this->db->escapeValue($alliance_tag);

        $check_tag = $this->db->queryFetch(
            "SELECT `alliance_tag`
            FROM `" . ALLIANCE . "`
            WHERE `alliance_tag` = '" . $alliance_tag . "'"
        );

        if ($check_tag) {
            return false;
        }

        return true;
    }

    /**
     * Check if the alliance name exists
     *
     * @param string $alliance_name
     * @return boolean
     */
    public function checkAllianceName(string $alliance_name): bool
    {
        $alliance_name = trim($alliance_name);
        $alliance_name = htmlspecialchars_decode($alliance_name, ENT_QUOTES);

        if ($alliance_name == '' or is_null($alliance_name) or (strlen($alliance_name) < 3) or (strlen($alliance_name) > 30)) {
            return false;
        }

        $alliance_name = $this->db->escapeValue($alliance_name);

        $check_name = $this->db->queryFetch(
            "SELECT
                `alliance_name`
            FROM `" . ALLIANCE . "`
            WHERE `alliance_name` = '" . $alliance_name . "'"
        );

        if ($check_name) {
            return false;
        }

        return true;
    }

    /**
     * Check if it is the alliance founder
     *
     * @param integer $user_id
     * @return boolean
     */
    public function checkAllianceFounder(int $user_id): bool
    {
        $ally_data = $this->db->queryFetch(
            "SELECT
                `user_ally_id`,
                `user_ally_request`
            FROM `" . USERS . "`
            WHERE `user_id` = '" . $user_id . "';"
        );

        return ($ally_data['user_ally_id'] > 0 && !empty($ally_data['user_ally_id']) && $ally_data['user_ally_request'] > 0 && !empty($ally_data['user_ally_request']));
    }

    /**
     * Get all the alliance members
     *
     * @param integer $alliance_id
     * @return array
     */
    public function getAllianceMembers(int $alliance_id): array
    {
        return $this->db->queryFetchAll(
            "SELECT u.`user_id`,
                u.`user_name`,
                u.`user_ally_request`,
                u.`user_ally_request_text`,
                u.`user_ally_register_time`,
                u.`user_ally_rank_id`,
                a.`alliance_owner`,
                a.`alliance_ranks`
            FROM `" . USERS . "` AS u
            LEFT JOIN `" . ALLIANCE . "` AS a ON a.`alliance_id` = u.`user_ally_id`
            WHERE u.`user_ally_id` = '" . $alliance_id . "';"
        );
    }

    /**
     * Remove alliance members
     *
     * @param string $ids_string
     * @return void
     */
    public function removeAllianceMembers(string $ids_string): void
    {
        $this->db->query(
            "UPDATE `" . USERS . "` SET
                `user_ally_id` = 0,
                `user_ally_request` = 0,
                `user_ally_request_text` = '',
                `user_ally_rank_id` = 0
            WHERE `user_id` IN (" . rtrim($ids_string, ',') . ")"
        );
    }

    /**
     * Get all users
     *
     * @return array
     */
    public function getAllUsers(): array
    {
        return $this->db->queryFetchAll(
            "SELECT
                `user_id`,
                `user_name`
            FROM `" . USERS . "`;"
        );
    }

    /**
     * Update the alliance data
     *
     * @param array $alliance_data
     * @return void
     */
    public function updateAllianceData(array $alliance_data): void
    {
        $this->db->query(
            "UPDATE `" . ALLIANCE . "` SET
                `alliance_name` = '" . $this->db->escapeValue($alliance_data['alliance_name']) . "',
                `alliance_tag` = '" . $this->db->escapeValue($alliance_data['alliance_tag']) . "',
                `alliance_owner` = '" . $alliance_data['alliance_owner'] . "',
                `alliance_web` = '" . $this->db->escapeValue($alliance_data['alliance_web']) . "',
                `alliance_image` = '" . $this->db->escapeValue($alliance_data['alliance_image']) . "',
                `alliance_description` = '" . $this->db->escapeValue($alliance_data['alliance_description']) . "',
                `alliance_text` = '" . $this->db->escapeValue($alliance_data['alliance_text']) . "',
                `alliance_request` = '" . $this->db->escapeValue($alliance_data['alliance_request']) . "',
                `alliance_request_notallow` = '" . $this->db->escapeValue($alliance_data['alliance_request_notallow']) . "'
                WHERE `alliance_id` = '" . $alliance_data['alliance_id'] . "';"
        );
    }

    /**
     * Get an alliance members count
     *
     * @param integer $alliance_id
     * @return array
     */
    public function countAllianceMembers(int $alliance_id): array
    {
        return $this->db->queryFetch(
            "SELECT
                COUNT(`user_id`) AS `Amount`
            FROM `" . USERS . "`
            WHERE `user_ally_id` = '" . $alliance_id . "';"
        );
    }

    /**
     * Update alliance ranks
     *
     * @param int    $alliance_id Alliance ID
     * @param string $ranks       Ranks
     */
    public function updateAllianceRanks($alliance_id, $ranks)
    {
        $this->db->query(
            "UPDATE `" . ALLIANCE . "` SET
                `alliance_ranks` = '" . $ranks . "'
            WHERE `alliance_id` = '" . (int) $alliance_id . "'"
        );
    }
}
