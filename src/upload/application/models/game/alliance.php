<?php
/**
 * Alliance Model
 *
 * PHP Version 5.5+
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */
namespace application\models\game;

/**
 * Alliance Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Alliance
{

    private $db = null;

    /**
     * __construct()
     */
    public function __construct($db)
    {
        // use this to make queries
        $this->db = $db;
    }

    /**
     * __destruct
     * 
     * @return void
     */
    public function __destruct()
    {
        $this->db->closeConnection();
    }
    
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
            WHERE a.`alliance_id` = '" . (int)$alliance_id . "'
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
     * 
     * @return void
     */
    public function createNewAlliance($alliance_name, $alliance_tag, $user_id, $founder_rank)
    {
        $this->db->query(
            "INSERT INTO " . ALLIANCE . " SET
            `alliance_name`='" . $alliance_name . "',
            `alliance_tag`='" . $alliance_tag . "' ,
            `alliance_owner`='" . (int)$user_id . "',
            `alliance_owner_range` = '" . $founder_rank . "',
            `alliance_register_time`='" . time() . "'"
        );

        $new_ally_id = $this->db->insertId();

        $this->db->query(
            "INSERT INTO " . ALLIANCE_STATISTICS . " SET
            `alliance_statistic_alliance_id`='" . $new_ally_id . "'"
        );

        $this->db->query("UPDATE " . USERS . " SET
            `user_ally_id`='" . $new_ally_id . "',
            `user_ally_register_time`='" . time() . "'
            WHERE `user_id`='" . (int)$user_id . "'"
        );
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
            "UPDATE " . USERS . " SET
            `user_ally_request` = '" . (int)$alliance_id . "' ,
            `user_ally_request_text` = '" . $text . "',
            `user_ally_register_time` = '" . time() . "'
            WHERE `user_id`='" . (int)$user_id . "'"
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
            WHERE `user_id`= '" . (int)$user_id . "'"
        );
    }
    
    /**
     * Exit alliance
     * 
     * @param int $user_id User ID
     * 
     * @retun void
     */
    public function exitAlliance($user_id)
    {
        $this->db->query(
            "UPDATE `" . USERS . "` SET
                `user_ally_id` = '0',
                `user_ally_rank_id` = '0'
            WHERE `user_id`='" . (int)$user_id . "'"
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
                WHERE `user_ally_request` = '" . (int)$alliance_id . "'"
        );
    }
    
    /**
     * Get alliance members
     * 
     * @param type $user_alliance_id
     * 
     * @return type
     */
    public function getAllianceMembers($alliance_id, $sort1, $sort2)
    {
        if ($sort2) {

            $sort = $this->returnSort($sort1, $sort2);
        } else {

            $sort = '';
        }
        
        return $this->db->query(
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
            WHERE u.user_ally_id='" . (int)$alliance_id . "'" . $sort
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
        return $this->db->query(
            "SELECT `user_id`, `user_name`, `user_ally_rank_id`
                FROM `" . USERS . "`
                WHERE `user_ally_id` = '" . (int)$alliance_id . "'"
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
        return $this->db->query(
            "SELECT `user_id`, `user_name`
            FROM `" . USERS . "`
            WHERE `user_ally_id` = '" . (int)$alliance_id . "' AND
                `user_ally_rank_id` = '" . (int)$rank_id . "'"
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
            WHERE `alliance_id` = '" . (int)$alliance_id . "'"
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
            "UPDATE " . ALLIANCE . " SET
                `alliance_owner_range`='" . $alliance_data['alliance_owner_range'] . "',
                `alliance_image`='" . $alliance_data['alliance_image'] . "',
                `alliance_web`='" . $alliance_data['alliance_web'] . "',
                `alliance_request_notallow`='" . $alliance_data['alliance_request_notallow'] . "'
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
                `alliance_request`='" .$text . "'
            WHERE `alliance_id` = '" . (int)$alliance_id . "'"
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
            WHERE `alliance_id` = '" . (int)$alliance_id . "'"
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
            WHERE `alliance_id` = '" . (int)$alliance_id . "'"
        );
    }
    
    /**
     * 
     * @param int $user_id User ID
     * 
     * @return array
     */
    public function getUserToBeKickedById($user_id)
    {
        return $this->db->queryFetch(
            "SELECT `user_ally_id`, `user_id`
            FROM `" . USERS . "`
            WHERE `user_id` = '" . (int)$user_id . "'
            LIMIT 1"
        );
    }
    
    /**
     * 
     * @param int $user_id User ID
     * 
     * @return array
     */
    public function getUserById($user_id)
    {
        return $this->db->queryFetch(
            "SELECT `user_id`
            FROM " . USERS . "
            WHERE `user_id` = '" . (int)$user_id . "'
            LIMIT 1"
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
            WHERE `user_id`='" . (int)$user_id . "'"
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
                `user_ally_id` = '" . (int)$alliance_id . "'
            WHERE `user_id` = '" . (int)$user_id . "'"
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
        return $this->db->query(
            "SELECT `user_id`, `user_name`, `user_ally_request_text`, `user_ally_register_time`
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
                a.`alliance_name` = '" . $alliance_name . "',
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
                u1.`user_ally_rank_id` = '0',
                a.`alliance_owner` = '" . (int)$new_leader . "',
                u2.`user_ally_rank_id` = '0'
            WHERE u1.`user_id`=" . $current_user_id . " AND
                a.`alliance_id`=" . $alliance_id . " AND
                u2.user_id`='" . (int)$new_leader . "'"
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
            WHERE `alliance_tag` = '" . $this->db->escapeValue($alliance_name) . "'"
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
     * @param int $sort1 Sort 1
     * @param int $sort2 Sort 2
     * 
     * @return string
     */
    private function returnSort($sort1, $sort2)
    {
        // FIRST ORDER
        switch ($sort1) {
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
        if ($sort2 == 1) {

            $sort .= " DESC;";
        } elseif ($sort2 == 2) {

            $sort .= " ASC;";
        }

        return $sort;
    }
}

/* end of buildings.php */