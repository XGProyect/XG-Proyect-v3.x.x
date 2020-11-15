<?php
/**
 * Messages Model
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.2
 */
namespace App\models\game;

use App\core\Model;

/**
 * Messages Class
 */
class Messages extends Model
{
    /**
     * Get the list of messages by user id and type
     *
     * @param int    $user_id         User id
     * @param string $msg_type_string Message types
     *
     * @return mixed
     */
    public function getByUserIdAndType($user_id, $msg_type_string)
    {
        if ((int) $user_id > 0 && !empty($msg_type_string)) {
            return $this->db->queryFetchAll(
                "SELECT *
                FROM `" . MESSAGES . "`
                WHERE `message_receiver` = " . $user_id . "
                        AND `message_type` IN (" . rtrim($msg_type_string, ',') . ")
                ORDER BY `message_time` DESC;"
            );
        }

        return null;
    }

    /**
     * Get the list of messages by user id
     *
     * @param int $user_id User id
     *
     * @return mixed
     */
    public function getByUserId($user_id)
    {
        if ((int) $user_id > 0) {
            return $this->db->queryFetchAll(
                "SELECT
                    *
                FROM `" . MESSAGES . "`
                WHERE `message_receiver` = '" . $user_id . "'
                ORDER BY `message_time` DESC;"
            );
        }

        return null;
    }

    /**
     * Mark messages as read by user id and type
     *
     * @param int    $user_id         User id
     * @param string $msg_type_string Message types
     *
     * @return mixed
     */
    public function markAsReadByType($user_id, $msg_type_string)
    {
        if ((int) $user_id > 0 && !empty($msg_type_string)) {
            return $this->db->query(
                "UPDATE `" . MESSAGES . "` SET
                    `message_read` = '1'
                WHERE `message_receiver` = " . $user_id . "
                        AND `message_type` IN (" . rtrim($msg_type_string, ',') . ");"
            );
        }

        return null;
    }

    /**
     * Mark messages as read by user id and type
     *
     * @param int    $user_id         User id
     * @param string $msg_type_string Message types
     *
     * @return mixed
     */
    public function markAsRead($user_id)
    {
        if ((int) $user_id > 0) {
            return $this->db->query(
                "UPDATE `" . MESSAGES . "` SET
                    `message_read` = '1'
                WHERE `message_receiver` = " . $user_id . ";"
            );
        }

        return null;
    }

    /**
     * Get home planet details
     *
     * @param int $planet_id Planet ID
     *
     * @return mixed
     */
    public function getHomePlanet($planet_id)
    {
        if ((int) $planet_id > 0) {
            return $this->db->queryFetch(
                "SELECT u.`user_id`, u.`user_name`, p.`planet_galaxy`, p.`planet_system`, p.`planet_planet`
                FROM " . PLANETS . " AS p
                INNER JOIN " . USERS . " as u ON p.planet_user_id = u.user_id
                WHERE p.`planet_user_id` = '" . $planet_id . "';"
            );
        }

        return null;
    }

    /**
     * Delete all messages for the current user
     *
     * @param type $user_id User ID
     *
     * @return void
     */
    public function deleteAllByOwner($user_id)
    {
        if ((int) $user_id > 0) {
            return $this->db->query(
                "DELETE FROM " . MESSAGES . "
                WHERE `message_receiver` = '" . $user_id . "';"
            );
        }

        return null;
    }

    /**
     * Delete message by id and current user
     *
     * @param int $user_id       The user ID
     * @param int $messages_ids  The messages ID
     *
     * @return mixed
     */
    public function deleteByOwnerAndIds($user_id, $messages_ids)
    {
        if ((int) $user_id > 0) {
            return $this->db->query(
                "DELETE FROM " . MESSAGES . "
                WHERE `message_id` IN (" . $messages_ids . ")
                    AND `message_receiver` = '" . $user_id . "';"
            );
        }

        return null;
    }

    /**
     * Delete message by id and current user
     *
     * @param int $user_id       The user ID
     * @param int $message_type  The messages type
     *
     * @return mixed
     */
    public function deleteByOwnerAndMessageType($user_id, $message_type)
    {
        if ((int) $user_id > 0 && (int) $message_type >= 0) {
            return $this->db->query(
                "DELETE FROM " . MESSAGES . "
                WHERE `message_type` IN (" . $message_type . ")
                    AND `message_receiver` = '" . $user_id . "';"
            );
        }

        return null;
    }

    /**
     * Count messages sum by type
     *
     * @param int $user_id User ID
     *
     * @return mixed
     */
    public function countMessagesByType($user_id)
    {
        if ((int) $user_id > 0) {
            return $this->db->queryFetchAll(
                "SELECT
                    `message_type`,
                    COUNT(`message_type`) AS message_type_count,
                    SUM(`message_read` = 0) AS unread_count
                FROM `" . MESSAGES . "`
                WHERE `message_receiver` = '" . $user_id . "'
                GROUP BY `message_type`"
            );
        }

        return null;
    }

    /**
     * Count alliance members, buddys, operators and notes
     *
     * @param int $user_id      User ID
     * @param int $user_ally_id User Alliance ID
     *
     * @return mixed
     */
    public function countAddressBookAndNotes($user_id, $user_ally_id)
    {
        if ((int) $user_id > 0 && (int) $user_ally_id >= 0) {
            return $this->db->queryFetch(
                "SELECT
                ( SELECT COUNT(`user_id`)
                    FROM `" . USERS . "`
                    WHERE `user_ally_id` = '" . $user_ally_id . "'
                        AND `user_ally_id` <> 0
                        AND `user_id` <> '" . $user_id . "'
                    ) AS alliance_count,

                    ( SELECT COUNT(`buddy_id`)
                    FROM `" . BUDDY . "`
                    WHERE `buddy_sender` = '" . $user_id . "'
                        OR `buddy_receiver` = '" . $user_id . "'
                    ) AS buddys_count,

                    ( SELECT COUNT(`note_id`)
                    FROM `" . NOTES . "`
                    WHERE `note_owner` = '" . $user_id . "'
                    ) AS notes_count,

                    ( SELECT COUNT(`user_id`)
                    FROM " . USERS . "
                    WHERE user_authlevel <> 0
                        AND `user_id` <> '" . $user_id . "'
                    ) AS operators_count"
            );
        }

        return null;
    }

    /**
     * Get all the user friends
     *
     * @param int $user_id User ID
     *
     * @return mixed
     */
    public function getFriends($user_id)
    {
        if ((int) $user_id > 0) {
            return $this->db->query(
                "SELECT
                    u.`user_id`,
                    u.`user_name`,
                    u.`user_email`
                FROM `" . BUDDY . "` b
                LEFT JOIN `" . USERS . "` u
                    ON u.user_id = IF(`buddy_sender` = '" . $user_id . "', `buddy_receiver`, `buddy_sender`)
                WHERE `buddy_sender`='" . $user_id . "'
                    OR `buddy_receiver`='" . $user_id . "'"
            );
        }

        return null;
    }

    /**
     * Get all alliance members that the user can contact
     *
     * @param int $user_id      User ID
     * @param int $user_ally_id User Alliance ID
     *
     * @return mixed
     */
    public function getAllianceMembers($user_id, $user_ally_id)
    {
        if ((int) $user_id > 0 && (int) $user_ally_id > 0) {
            return $this->db->query(
                "SELECT `user_id`, `user_name`, `user_email`
                FROM " . USERS . "
                WHERE user_ally_id = '" . $user_ally_id . "'
                    AND `user_id` <> '" . $user_id . "';"
            );
        }

        return null;
    }

    /**
     * Get all the game operators
     *
     * @param int $user_id User ID
     *
     * @return mixed
     */
    public function getOperators($user_id)
    {
        if ((int) $user_id > 0) {
            return $this->db->queryFetchAll(
                "SELECT `user_name`, `user_email`
                FROM " . USERS . "
                WHERE user_authlevel > '0'
                    AND `user_id` <> '" . $user_id . "';"
            );
        }

        return null;
    }

    /**
     * Get all the user notes
     *
     * @param int $user_id User ID
     *
     * @return mixed
     */
    public function getNotes($user_id)
    {
        if ((int) $user_id > 0) {
            return $this->db->query(
                "SELECT `note_id`, `note_priority`, `note_title`
                FROM `" . NOTES . "`
                WHERE `note_owner` = '" . $user_id . "';"
            );
        }

        return null;
    }
}
