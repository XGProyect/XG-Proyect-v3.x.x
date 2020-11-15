<?php namespace App\models\libraries;

use App\core\Model;
use App\libraries\Functions;

/**
 * UsersLibrary Class
 */
class UsersLibrary extends Model
{
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
            "SELECT `user_ally_id` FROM `" . USERS . "` WHERE `user_id` = '" . $user_id . "';"
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
            FROM `" . ALLIANCE . "` AS a
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
            "DELETE r,f,n,p,pr,s,u FROM " . USERS . " AS u
            INNER JOIN " . RESEARCH . " AS r ON r.research_user_id = u.user_id
            LEFT JOIN " . FLEETS . " AS f ON f.fleet_owner = u.user_id
            LEFT JOIN " . NOTES . " AS n ON n.note_owner = u.user_id
            INNER JOIN " . PREMIUM . " AS p ON p.premium_user_id = u.user_id
            INNER JOIN " . PREFERENCES . " AS pr ON pr.preference_user_id = u.user_id
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
    public function setUserDataByUserId($user_id)
    {
        if (!defined('IN_ADMIN')) {
            return $this->db->queryFetch(
                "SELECT u.*,
                    pre.*,
                    pr.*,
                    usul.user_statistic_total_rank,
                    usul.user_statistic_total_points,
                    r.*,
                    a.alliance_name,
                    (
                        SELECT COUNT(`message_id`) AS `new_message`
                        FROM `" . MESSAGES . "`
                        WHERE `message_receiver` = u.`user_id` AND `message_read` = 0
                    ) AS `new_message`
                FROM `" . USERS . "` AS u
                INNER JOIN `" . PREFERENCES . "` AS pr ON pr.preference_user_id = u.user_id
                INNER JOIN `" . USERS_STATISTICS . "` AS usul ON usul.user_statistic_user_id = u.user_id
                INNER JOIN `" . PREMIUM . "` AS pre ON pre.premium_user_id = u.user_id
                INNER JOIN `" . RESEARCH . "` AS r ON r.research_user_id = u.user_id
                LEFT JOIN `" . ALLIANCE . "` AS a ON a.alliance_id = u.user_ally_id
                WHERE (u.`user_id` = '" . (int) $user_id . "')
                LIMIT 1;"
            );
        }

        return $this->db->queryFetch(
            "SELECT
                u.*
            FROM `" . USERS . "` AS u
            WHERE (u.`user_id` = '" . (int) $user_id . "')
            LIMIT 1;"
        );
    }

    /**
     * Update some data
     *
     * @param string $request_uri Requested URL
     * @param string $remote_addr Remote IP Address
     * @param string $user_agent  User agent/browser
     * @param int    $user_id     User ID
     *
     * @return void
     */
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

    /**
     * Set the user current planet data
     *
     * @param int $planet_id   Planet ID
     * @param int $admin_level Admin Level
     *
     * @return type
     */
    public function setPlanetData($planet_id, $admin_level)
    {
        return $this->db->queryFetch(
            "SELECT p.*, b.*, d.*, s.*,
            m.planet_id AS moon_id,
            m.planet_name AS moon_name,
            m.planet_image AS moon_image,
            m.planet_destroyed AS moon_destroyed,
            m.planet_image AS moon_image,
            (SELECT COUNT(user_statistic_user_id) AS stats_users
                FROM `" . USERS_STATISTICS . "` AS s
                INNER JOIN " . USERS . " AS u ON u.user_id = s.user_statistic_user_id
                WHERE u.`user_authlevel` <= " . $admin_level . ") AS stats_users
            FROM " . PLANETS . " AS p
            INNER JOIN " . BUILDINGS . " AS b ON b.building_planet_id = p.`planet_id`
            INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id`
            INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id`
            LEFT JOIN " . PLANETS . " AS m ON m.planet_id = (SELECT mp.`planet_id`
                FROM " . PLANETS . " AS mp
                WHERE (mp.planet_galaxy=p.planet_galaxy AND
                                mp.planet_system=p.planet_system AND
                                mp.planet_planet=p.planet_planet AND
                                mp.planet_type=3))
            WHERE p.`planet_id` = '" . $planet_id . "';"
        );
    }

    /**
     * Validate if the requested planet belongs to the current user
     *
     * @param int $planet_id Planet ID
     * @param int $user_id   User ID
     *
     * @return array
     */
    public function getUserPlanetByIdAndUserId($planet_id, $user_id)
    {
        return $this->db->queryFetch(
            "SELECT `planet_id`
            FROM " . PLANETS . "
            WHERE `planet_id` = '" . $planet_id . "'
            AND `planet_user_id` = '" . $user_id . "';"
        );
    }

    /**
     * Change the user current planet
     *
     * @param int $planet_id Planet ID
     * @param int $user_id   User ID
     *
     * @return void
     */
    public function changeUserPlanetByUserId($planet_id, $user_id)
    {
        $this->db->query(
            "UPDATE " . USERS . " SET
            `user_current_planet` = '" . $planet_id . "'
            WHERE `user_id` = '" . $user_id . "';"
        );
    }

    /**
     * Insert a new user and return their ID
     *
     * @param string $insert_query Insert Query
     *
     * @return int
     */
    public function createNewUser($insert_query)
    {
        $this->db->query($insert_query);

        return $this->db->insertId();
    }

    /**
     * Create premium record
     *
     * @param type $user_id The user id
     *
     * @return void
     */
    public function createPremium($user_id)
    {
        $this->db->query(
            "INSERT INTO `" . PREMIUM . "` (`premium_user_id`, `premium_dark_matter`)
            VALUES('" . $user_id . "', '" . Functions::readConfig('registration_dark_matter') . "');"
        );
    }

    /**
     * Create research record
     *
     * @param type $user_id The user id
     *
     * @return void
     */
    public function createResearch($user_id)
    {
        $this->db->query(
            "INSERT INTO " . RESEARCH . " SET `research_user_id` = '" . $user_id . "';"
        );
    }

    /**
     * Create settings record
     *
     * @param type $user_id The user id
     *
     * @return void
     */
    public function createSettings($user_id)
    {
        $this->db->query(
            "INSERT INTO " . PREFERENCES . " SET `preference_user_id` = '" . $user_id . "';"
        );
    }

    /**
     * Create statistics record
     *
     * @param type $user_id The user id
     *
     * @return void
     */
    public function createUserStatistics($user_id)
    {
        $this->db->query(
            "INSERT INTO " . USERS_STATISTICS . " SET `user_statistic_user_id` = '" . $user_id . "';"
        );
    }
}
