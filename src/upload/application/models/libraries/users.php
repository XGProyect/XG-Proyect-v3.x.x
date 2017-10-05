<?php
/**
 * Users Model
 *
 * PHP Version 5.5+
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.2
 */

namespace application\models\libraries;

/**
 * Users Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Users
{
    private $db = null;
    
    /**
     * __construct()
     */
    public function __construct($db)
    {        
        // use this to make queries
        $this->db   = $db;
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
     * Get alliance ID
     * 
     * @param int $user_id User ID
     * 
     * @return array
     */
    public function getAllyIdByUserId($user_id)
    {
        return $this->db->queryFetch(
            "SELECT `user_ally_id` FROM " . USERS . " WHERE `user_id` = '" . $user_id . "';"
        );
    }
    
    /**
     * Get alliance data
     * 
     * @param array $alliance_id Alliance ID
     * 
     * @return array
     */
    public function getAllianceDataByAllianceId($alliance_id)
    {
        return $this->db->queryFetch(
            "SELECT a.`alliance_id`, a.`alliance_ranks`,
                (SELECT COUNT(user_id) AS `ally_members` 
                    FROM `" . USERS . "` 
                    WHERE `user_ally_id` = '" . $alliance_id . "') AS `ally_members`
            FROM " . ALLIANCE . " AS a
            WHERE a.`alliance_id` = '" . $alliance_id . "';"
        );
    }
    
    /**
     * Update the alliance owner
     * 
     * @param array $alliance_id Alliance ID
     * @param int   $user_rank   Rank ID
     * 
     * @return type
     */
    public function updateAllianceOwner($alliance_id, $user_rank)
    {
        return $this->db->query(
            "UPDATE `" . ALLIANCE . "` SET 
                `alliance_owner` = 
                (
                    SELECT `user_id` 
                    FROM `" . USERS . "`
                    WHERE `user_ally_rank_id` = '" . $user_rank . "'
                        AND `user_ally_id` = '" . $alliance_id . "'
                    LIMIT 1
                )
            WHERE `alliance_id` = '" . $alliance_id . "';"
        );
    }
    
    /**
     * Delete alliance
     * 
     * @param Int $alliance_id Alliance ID
     * 
     * @return void
     */
    public function deleteAllianceById($alliance_id)
    {
        $this->db->query(
            "DELETE ass, a FROM " . ALLIANCE . " AS a
            INNER JOIN " . ALLIANCE_STATISTICS . " AS ass ON ass.alliance_statistic_alliance_id = a.alliance_id
            WHERE a.`alliance_id` = '" . $alliance_id . "';"
        );

        $this->db->query(
            "UPDATE `" . USERS . "` SET 
                `user_ally_id` = '0',
                `user_ally_request` = '0',
                `user_ally_request_text` = '',
                `user_ally_register_time` = '',
                `user_ally_rank_id` = '0'
            WHERE `user_ally_id` = '" . $alliance_id . "';"
        );
    }
    
    /**
     * Delete the planet and its related data like buildings, defenses and ships.
     * 
     * @param int $user_id User ID
     * 
     * @return void
     */
    public function deletePlanetsAndRelatedDataByUserId($user_id)
    {
        $this->db->query(
            "DELETE p,b,d,s FROM " . PLANETS . " AS p
            INNER JOIN " . BUILDINGS . " AS b ON b.building_planet_id = p.`planet_id`
            INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id`
            INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id`
            WHERE `planet_user_id` = '" . $user_id . "';"
        );
    }
    
    /**
     * Delete the planet and its related data like buildings, defenses and ships.
     * 
     * @param int $user_id User ID
     * 
     * @return void
     */
    public function deleteMessagesByUserId($user_id)
    {
        $this->db->query(
            "DELETE FROM " . MESSAGES . " 
                WHERE `message_sender` = '" . $user_id . "' OR `message_receiver` = '" . $user_id . "';"
        );
    }
    
    /**
     * Delete the planet and its related data like buildings, defenses and ships.
     * 
     * @param int $user_id User ID
     * 
     * @return void
     */
    public function deleteBuddysByUserId($user_id)
    {
        $this->db->query(
            "DELETE FROM " . BUDDY . " 
                WHERE `buddy_sender` = '" . $user_id . "' OR `buddy_receiver` = '" . $user_id . "';"
        );
    }
    
    /**
     * Delete the planet and its related data like buildings, defenses and ships.
     * 
     * @param int $user_id User ID
     * 
     * @return void
     */
    public function deleteUserDataById($user_id)
    {
        $this->db->query(
            "DELETE r,f,n,p,se,s,u FROM " . USERS . " AS u
            INNER JOIN " . RESEARCH . " AS r ON r.research_user_id = u.user_id
            LEFT JOIN " . FLEETS . " AS f ON f.fleet_owner = u.user_id
            LEFT JOIN " . NOTES . " AS n ON n.note_owner = u.user_id
            INNER JOIN " . PREMIUM . " AS p ON p.premium_user_id = u.user_id
            INNER JOIN " . SETTINGS . " AS se ON se.setting_user_id = u.user_id
            INNER JOIN " . USERS_STATISTICS . " AS s ON s.user_statistic_user_id = u.user_id
            WHERE u.`user_id` = '" . $user_id . "';"
        );
    }
    
    /**
     * Get the user data by user name
     * 
     * @param string $user_name User Name
     * 
     * @return array
     */
    public function setUserDataByUserName($user_name)
    {
        return $this->db->queryFetch(
            "SELECT u.*,
                pre.*,
                se.*,
                usul.user_statistic_total_rank,
                usul.user_statistic_total_points,
                r.*,
                a.alliance_name,
                (SELECT COUNT(`message_id`) AS `new_message` 
                FROM `" . MESSAGES . "` 
                WHERE `message_receiver` = u.`user_id` AND `message_read` = 0) AS `new_message`
            FROM " . USERS . " AS u
            INNER JOIN " . SETTINGS . " AS se ON se.setting_user_id = u.user_id
            INNER JOIN " . USERS_STATISTICS . " AS usul ON usul.user_statistic_user_id = u.user_id
            INNER JOIN " . PREMIUM . " AS pre ON pre.premium_user_id = u.user_id
            INNER JOIN " . RESEARCH . " AS r ON r.research_user_id = u.user_id
            LEFT JOIN " . ALLIANCE . " AS a ON a.alliance_id = u.user_ally_id
            WHERE (u.user_name = '" . $this->db->escapeValue($user_name) . "')
            LIMIT 1;"
        );
    }
    
    public function updateUserActivityData($request_uri, $remote_addr, $user_agent, $user_id)
    {
        $this->db->query(
            "UPDATE " . USERS . " SET
                `user_onlinetime` = '" . time() . "',
                `user_current_page` = '" . $this->db->escapeValue($request_uri) . "',
                `user_lastip` = '" . $this->db->escapeValue($remote_addr) . "',
                `user_agent` = '" . $this->db->escapeValue($user_agent) . "'
            WHERE `user_id` = '" . $this->db->escapeValue($user_id) . "'
            LIMIT 1;"
        );
    }
}

/* end of users.php */
