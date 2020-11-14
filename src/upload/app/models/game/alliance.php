<?php
/**
 * Alliance Model
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */
namespace App\models\game;

use App\core\Model;

/**
 * Alliance Class
 */
class Alliance extends Model
{
    /**
     * Get Alliance Data By ID
     *
     * @param int $alliance_id Alliance ID
     *
     * @return array
     */
    public function getAllianceDataById($alliance_id)
    {
        $result[] = $this->db->queryFetch(
            "SELECT a.*,
                    (SELECT COUNT(user_id) AS `alliance_members`
                        FROM `" . USERS . "`
                        WHERE `user_ally_id` = a.`alliance_id`) AS `alliance_members`
            FROM `" . ALLIANCE . "` AS a
            WHERE a.`alliance_id` = '" . (int) $alliance_id . "'
            LIMIT 1;"
        );

        return $result;
    }

    /**
     * Create a new alliance with the provided params
     *
     * @param string $alliance_name Alliance Name
     * @param string $alliance_tag  Alliance Tag
     * @param int $user_id          User ID
     * @param string $founder_rank  Founder Rank
     * @param string $newcomer_rank  New member Rank
     *
     * @return void
     */
    public function createNewAlliance($alliance_name, $alliance_tag, $user_id, $founder_rank, $newcomer_rank)
    {
        try {
            $this->db->beginTransaction();

            $rights_string = '[{"rank":"Founder","rights":{"1":1,"2":1,"3":1,"4":1,"5":1,"6":1,"7":1,"8":1,"9":1}},{"rank":"Newcomer","rights":{"1":0,"2":0,"3":0,"4":0,"5":0,"6":0,"7":0,"8":0,"9":0}}]';

            $this->db->query(
                "INSERT INTO `" . ALLIANCE . "` SET
                `alliance_name` = '" . $alliance_name . "',
                `alliance_tag` = '" . $alliance_tag . "' ,
                `alliance_owner` = '" . (int) $user_id . "',
                `alliance_register_time` = '" . time() . "',
                `alliance_ranks` = '" . strtr($rights_string, ['Founder' => $founder_rank, 'Newcomer' => $newcomer_rank]) . "'"
            );

            $new_ally_id = $this->db->insertId();

            $this->db->query(
                "INSERT INTO " . ALLIANCE_STATISTICS . " SET
                `alliance_statistic_alliance_id`='" . $new_ally_id . "'"
            );

            $this->db->query(
                "UPDATE " . USERS . " SET
                `user_ally_id`='" . $new_ally_id . "',
                `user_ally_register_time`='" . time() . "'
                WHERE `user_id`='" . (int) $user_id . "'"
            );

            $this->db->commitTransaction();
        } catch (Exception $e) {
            $this->db->rollbackTransaction();
        }
    }

    /**
     * Search an alliance by name or tag
     *
     * @param string $name_tag Name or Tag
     *
     * @return array
     */
    public function searchAllianceByNameTag($name_tag)
    {
        return $this->db->queryFetchAll(
            "SELECT a.alliance_id,
                    a.alliance_tag,
                    a.alliance_name,
                (SELECT COUNT(user_id) AS `alliance_members`
                    FROM `" . USERS . "`
                    WHERE `user_ally_id` = a.`alliance_id`) AS `alliance_members`
            FROM " . ALLIANCE . " AS a
            WHERE a.alliance_name LIKE '%" . $this->db->escapeValue($name_tag) . "%' OR
                    a.alliance_tag LIKE '%" . $this->db->escapeValue($name_tag) . "%' LIMIT 30"
        );
    }

    /**
     * Update users table to set the alliance request
     *
     * @param int    $alliance_id  Alliance ID
     * @param string $text Request Text
     * @param int    $user_id      User ID
     *
     * @retun void
     */
    public function createNewUserRequest($alliance_id, $text, $user_id)
    {
        $this->db->query(
            "UPDATE `" . USERS . "` SET
            `user_ally_request` = '" . (int) $alliance_id . "' ,
            `user_ally_request_text` = '" . $text . "',
            `user_ally_register_time` = '" . time() . "',
            `user_ally_rank_id` = '1'
            WHERE `user_id`='" . (int) $user_id . "'"
        );
    }

    /**
     * Cancel user request
     *
     * @param int $user_id User ID
     *
     * @retun void
     */
    public function cancelUserRequestById($user_id)
    {
        $this->db->query(
            "UPDATE " . USERS . "
                SET `user_ally_request` = '0'
            WHERE `user_id`= '" . (int) $user_id . "'"
        );
    }

    /**
     * Exit alliance
     *
     * @param int $user_id User ID
     *
     * @retun void
     */
    public function exitAlliance($alliance_id, $user_id)
    {
        $this->db->query(
            "UPDATE `" . USERS . "` SET
                `user_ally_id` = '0',
                `user_ally_rank_id` = '0'
            WHERE `user_id` = '" . (int) $user_id . "'
                AND `user_ally_id` = '" . (int) $alliance_id . "'"
        );
    }

    /**
     *
     * @param type $alliance_id
     * @return type
     */
    public function getAllianceRequestsCount($alliance_id)
    {
        return $this->db->queryFetch(
            "SELECT COUNT(user_id) AS total_requests
                FROM `" . USERS . "`
                WHERE `user_ally_request` = '" . (int) $alliance_id . "'"
        );
    }

    /**
     * Get alliance members
     *
     * @param type $alliance_id
     * @param type $sort_by_field
     * @param type $sort_by_order
     * @return type
     */
    public function getAllianceMembers($alliance_id, $sort_by_field, $sort_by_order)
    {
        return $this->db->queryFetchAll(
            "SELECT u.user_id,
                    u.user_onlinetime,
                    u.user_name,
                    u.user_galaxy,
                    u.user_system,
                    u.user_planet,
                    u.user_ally_register_time,
                    u.user_ally_rank_id,
                    s.user_statistic_total_points
            FROM `" . USERS . "` AS u
            INNER JOIN `" . USERS_STATISTICS . "`AS s ON u.user_id = s.user_statistic_user_id
            WHERE u.user_ally_id='" . (int) $alliance_id . "'" . $this->returnSort($sort_by_field, $sort_by_order)
        );
    }

    /**
     * Get alliance members filtered by alliance ID
     *
     * @param int $alliance_id Alliance ID
     *
     * @return array
     */
    public function getAllianceMembersById($alliance_id)
    {
        return $this->db->queryFetchAll(
            "SELECT `user_id`, `user_name`, `user_ally_rank_id`
                FROM `" . USERS . "`
                WHERE `user_ally_id` = '" . (int) $alliance_id . "'"
        );
    }

    /**
     * Get alliance members filtered by alliance ID and Rank ID
     *
     * @param int $alliance_id Alliance ID
     * @param int $rank_id     Rank ID
     *
     * @return array
     */
    public function getAllianceMembersByIdAndRankId($alliance_id, $rank_id)
    {
        return $this->db->queryFetchAll(
            "SELECT `user_id`, `user_name`
            FROM `" . USERS . "`
            WHERE `user_ally_id` = '" . (int) $alliance_id . "' AND
                `user_ally_rank_id` = '" . (int) $rank_id . "'"
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

    /**
     * Update alliance settings
     *
     * @param int $alliance_id     Alliance ID
     * @param array $alliance_data Alliance Data
     *
     * @return void
     */
    public function updateAllianceSettings($alliance_id, $alliance_data)
    {
        $this->db->query(
            "UPDATE `" . ALLIANCE . "` SET
                `alliance_image` = '" . $alliance_data['alliance_image'] . "',
                `alliance_web` = '" . $alliance_data['alliance_web'] . "',
                `alliance_request_notallow` = '" . $alliance_data['alliance_request_notallow'] . "'
            WHERE `alliance_id` = '" . $alliance_id . "'"
        );
    }

    /**
     *
     * @param int    $alliance_id Alliance ID
     * @param string $text        Text
     *
     * @return void
     */
    public function updateAllianceRequestText($alliance_id, $text)
    {
        $this->db->query(
            "UPDATE " . ALLIANCE . " SET
                `alliance_request`='" . $text . "'
            WHERE `alliance_id` = '" . (int) $alliance_id . "'"
        );
    }

    /**
     *
     * @param int    $alliance_id Alliance ID
     * @param string $text        Text
     *
     * @return void
     */
    public function updateAllianceText($alliance_id, $text)
    {
        $this->db->query(
            "UPDATE " . ALLIANCE . " SET
                `alliance_text`='" . $text . "'
            WHERE `alliance_id` = '" . (int) $alliance_id . "'"
        );
    }

    /**
     *
     * @param int    $alliance_id Alliance ID
     * @param string $text        Text
     *
     * @return void
     */
    public function updateAllianceDescription($alliance_id, $text)
    {
        $this->db->query(
            "UPDATE " . ALLIANCE . " SET
                `alliance_description`='" . $text . "'
            WHERE `alliance_id` = '" . (int) $alliance_id . "'"
        );
    }

    /**
     *
     * @param int    $user_id User ID
     * @param string $rank    Rank
     */
    public function updateUserRank($user_id, $rank)
    {
        $this->db->query(
            "UPDATE " . USERS . " SET
                `user_ally_rank_id` = '" . $this->db->escapeValue($rank) . "'
            WHERE `user_id`='" . (int) $user_id . "'"
        );
    }

    /**
     * Add an user to the alliance
     *
     * @param int $user_id     User ID
     * @param int $alliance_id Alliance ID
     *
     * @return void
     */
    public function addUserToAlliance($user_id, $alliance_id)
    {
        $this->db->query(
            "UPDATE `" . USERS . "` SET
                `user_ally_request_text` = '',
                `user_ally_request` = '0',
                `user_ally_id` = '" . (int) $alliance_id . "'
            WHERE `user_id` = '" . (int) $user_id . "'"
        );
    }

    /**
     * Remove user from alliance
     *
     * @param int $user_id     User ID
     * @param int $alliance_id Alliance ID
     *
     * @return void
     */
    public function removeUserFromAlliance($user_id)
    {
        $this->addUserToAlliance($user_id, 0);
    }

    /**
     * Add an user to the alliance
     *
     * @param int $alliance_id Alliance ID
     *
     * @return array
     */
    public function getAllianceRequests($alliance_id)
    {
        return $this->db->queryFetchAll(
            "SELECT `user_id`,
                    `user_name`,
                    `user_ally_request_text`,
                    `user_ally_register_time`
            FROM `" . USERS . "`
            WHERE `user_ally_request` = '" . $alliance_id . "'"
        );
    }

    /**
     *
     * @param int    $alliance_id Alliance ID
     * @param string $alliance_name Alliance Name
     */
    public function updateAllianceName($alliance_id, $alliance_name)
    {
        $this->db->query(
            "UPDATE " . ALLIANCE . " AS a SET
                a.`alliance_name` = '" . $alliance_name . "'
            WHERE a.`alliance_id` = '" . $alliance_id . "';"
        );
    }

    /**
     *
     * @param int    $alliance_id  Alliance ID
     * @param string $alliance_tag Alliance Tag
     */
    public function updateAllianceTag($alliance_id, $alliance_tag)
    {
        $this->db->query(
            "UPDATE " . ALLIANCE . " SET
                `alliance_tag` = '" . $alliance_tag . "'
            WHERE `alliance_id` = '" . $alliance_id . "';"
        );
    }

    /**
     * @param int $alliance_id  Alliance ID
     */
    public function deleteAlliance($alliance_id)
    {
        try {
            $this->db->beginTransaction();

            $this->db->query(
                "UPDATE `" . USERS . "` SET
                    `user_ally_id` = '0'
                WHERE `user_ally_id` = '" . $alliance_id . "'"
            );

            $this->db->query(
                "DELETE FROM `" . ALLIANCE . "`
                WHERE `alliance_id` = '" . $alliance_id . "'
                LIMIT 1"
            );

            $this->db->commitTransaction();
        } catch (Exception $e) {
            $this->db->rollbackTransaction();
        }
    }

    /**
     *
     * @param int $alliance_id     Alliance ID
     * @param int $current_user_id Current User ID
     * @param int $new_leader      New Leader ID
     *
     * @return void
     */
    public function transferAlliance($alliance_id, $current_user_id, $new_leader)
    {
        $this->db->query(
            "UPDATE `" . USERS . "` AS u1, `" . ALLIANCE . "` AS a, `" . USERS . "` AS u2 SET
                u1.`user_ally_rank_id` = '1',
                a.`alliance_owner` = '" . (int) $new_leader . "',
                u2.`user_ally_rank_id` = '0'
            WHERE u1.`user_id` = " . $current_user_id . " AND
                a.`alliance_id` = " . $alliance_id . " AND
                u2.`user_id` = '" . (int) $new_leader . "'"
        );
    }

    /**
     * Check alliance name
     *
     * @param string $alliance_name Alliance Name
     *
     * @return array
     */
    public function checkAllianceName($alliance_name)
    {
        return $this->db->queryFetch(
            "SELECT `alliance_name`
            FROM `" . ALLIANCE . "`
            WHERE `alliance_name` = '" . $this->db->escapeValue($alliance_name) . "'"
        );
    }

    /**
     * Check alliance tag
     *
     * @param string $alliance_tag Alliance Tag
     *
     * @return array
     */
    public function checkAllianceTag($alliance_tag)
    {
        return $this->db->queryFetch(
            "SELECT `alliance_tag`
            FROM `" . ALLIANCE . "`
            WHERE `alliance_tag` = '" . $this->db->escapeValue($alliance_tag) . "'"
        );
    }

    /**
     * Return the sort method
     *
     * @param int $sort_field Sort by field
     * @param int $sort_order Sort by order [ASC|DESC]
     *
     * @return string
     */
    private function returnSort($sort_field, $sort_order)
    {
        // FIRST ORDER
        switch ($sort_field) {
            case 1:
                $sort = " ORDER BY `user_name`";
                break;
            case 2:
                $sort = " ORDER BY `user_ally_rank_id`";
                break;
            case 3:
                $sort = " ORDER BY `user_statistic_total_points`";
                break;
            case 4:
                $sort = " ORDER BY `user_ally_register_time`";
                break;
            case 5:
                $sort = " ORDER BY `user_onlinetime`";
                break;
            default:
                $sort = " ORDER BY `user_id`";
                break;
        }

        // SECOND ORDER
        if ($sort_order == 1) {
            $sort .= " DESC;";
        } elseif ($sort_order == 2) {
            $sort .= " ASC;";
        }

        return $sort;
    }
}
