<?php
/**
 * Buddies Model
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
 * Buddies Class
 */
class Buddies extends Model
{
    /**
     * Get buddy data by ID
     *
     * @param int $buddy_id Buddy ID
     *
     * @return int $buddy_id Buddy ID
     */
    public function getBuddyDataByBuddyId($buddy_id)
    {
        return $this->db->queryFetch(
            "SELECT *
            FROM `" . BUDDY . "`
            WHERE `buddy_id` = '" . (int) $buddy_id . "'"
        );
    }

    /**
     *
     * @param int $user_id User ID
     *
     * @return array
     */
    public function getBuddiesByUserId($user_id)
    {
        return $this->db->queryFetchAll(
            "SELECT *
                FROM `" . BUDDY . "`
                WHERE `buddy_sender` = '" . (int) $user_id . "'
                    OR `buddy_receiver` = '" . (int) $user_id . "'"
        );
    }

    /**
     * Get Buddy data by Id
     *
     * @param int $user_id User ID
     *
     * @return array
     */
    public function getBuddyDataById($user_id)
    {
        return $this->db->queryFetch(
            "SELECT u.`user_id`,
                    u.`user_name`,
                    u.`user_galaxy`,
                    u.`user_system`,
                    u.`user_planet`,
                    u.`user_onlinetime`,
                    a.`alliance_id`,
                    a.`alliance_name`
            FROM " . USERS . " AS u
            LEFT JOIN `" . ALLIANCE . "` AS a ON a.`alliance_id` = u.`user_ally_id`
            WHERE u.`user_id`='" . (int) $user_id . "'"
        );
    }

    /**
     * Remove a buddy
     *
     * @param int $buddy_id Buddy Id
     * @param int $user_id  Current User Id
     *
     * @return void
     */
    public function removeBuddyById($buddy_id, $user_id)
    {
        $this->db->query(
            "DELETE FROM `" . BUDDY . "`
            WHERE `buddy_id` = '" . (int) $buddy_id . "'
                AND (`buddy_receiver` = '" . (int) $user_id . "'
                        OR `buddy_sender` = '" . (int) $user_id . "') "
        );
    }

    /**
     * Confirm player as a current user buddy
     *
     * @param int $buddy_id Buddy Id
     * @param int $user_id  Current User Id
     *
     * @return void
     */
    public function setBuddyStatusById($buddy_id, $user_id)
    {
        $this->db->query(
            "UPDATE `" . BUDDY . "`
                SET `buddy_status` = '1'
            WHERE `buddy_id` = '" . (int) $buddy_id . "' AND
                    `buddy_receiver` = '" . (int) $user_id . "'"
        );
    }

    /**
     * Get buddy ID based on receiver and sender, sort of validation
     *
     * @param type $send_to
     * @param type $user_id
     *
     * @return array
     */
    public function getBuddyIdByReceiverAndSender($send_to, $user_id)
    {
        return $this->db->queryFetch(
            "SELECT `buddy_id`
            FROM `" . BUDDY . "`
            WHERE (`buddy_receiver` = '" . (int) $user_id . "' AND
                    `buddy_sender` = '" . (int) $send_to . "') OR
                            (`buddy_receiver` = '" . (int) $send_to . "' AND
                                    `buddy_sender` = '" . (int) $user_id . "')"
        );
    }

    /**
     * Create a new buddy request
     *
     * @param type $user    User ID
     * @param type $user_id Current User ID
     * @param type $text    Request Text
     *
     * @return void
     */
    public function insertNewBuddyRequest($user, $user_id, $text)
    {
        $this->db->query(
            "INSERT INTO `" . BUDDY . "` SET
                `buddy_sender` = '" . (int) $user_id . "',
                `buddy_receiver` = '" . (int) $user . "',
                `buddy_status` = '0',
                `buddy_request_text` = '" . $this->db->escapeValue(strip_tags($text)) . "'"
        );
    }

    /**
     * Check if the user exists
     *
     * @param int $user_id
     *
     * @return string
     */
    public function checkIfBuddyExists($user_id)
    {
        return $this->db->queryFetch(
            "SELECT
                `user_id`,
                `user_name`
                FROM `" . USERS . "`
                WHERE `user_id` = '" . (int) $user_id . "'"
        );
    }

    /**
     * Get buddy details by ID
     *
     * @param int $user_id
     * @param int $group_id
     *
     * @return array
     */
    public function getBuddiesDetailsForAcsById(int $user_id, int $group_id): array
    {
        return $this->db->queryFetchAll(
            "SELECT DISTINCT
                u.`user_id`,
                u.`user_name`
            FROM `" . BUDDY . "` AS b
            LEFT JOIN `" . USERS . "` AS u
            	ON ((u.user_id = b.buddy_sender) OR (u.user_id = b.buddy_receiver))
            WHERE
            (
                b.`buddy_sender` = '" . $user_id . "'
            OR
                b.`buddy_receiver` = '" . $user_id . "'
            )
            AND b.`buddy_status` = '1'
            AND u.`user_id` NOT IN (
                SELECT
                    acs.`acs_user_id`
                FROM `" . ACS_MEMBERS . "` acs
                WHERE acs.`acs_group_id` = '" . $group_id . "'
            )"
        ) ?? [];
    }
}
