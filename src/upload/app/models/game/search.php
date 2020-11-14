<?php
/**
 * Search Model
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
 * Search Class
 */
class Search extends Model
{
    /**
     * Search results by player name
     *
     * @param string $playerName
     * @return array
     */
    public function getResultsByPlayerName(string $playerName): array
    {
        if (!empty($playerName)) {
            return $this->db->queryFetchAll(
                "SELECT
                    u.`user_id`,
                    u.`user_name`,
                    u.`user_authlevel`,
                    p.`planet_name`,
                    p.`planet_galaxy`,
                    p.`planet_system`,
                    p.`planet_planet`,
                    s.`user_statistic_total_rank` AS `user_rank`,
                    a.`alliance_id`,
                    a.`alliance_name`
                FROM `" . USERS . "` AS u
                    INNER JOIN `" . USERS_STATISTICS . "` AS s ON s.`user_statistic_user_id` = u.`user_id`
                    INNER JOIN `" . PLANETS . "` AS p ON p.`planet_id` = u.`user_home_planet_id`
                    LEFT JOIN `" . ALLIANCE . "` AS a ON a.alliance_id = u.`user_ally_id`
                WHERE u.`user_name` LIKE '%" . $this->db->escapeValue($playerName) . "%'
                LIMIT " . MAX_SEARCH_RESULTS . ";"
            );
        }

        return [];
    }

    /**
     * Search results by alliance name or alliance tag
     *
     * @param string $allianceTag
     * @return array
     */
    public function getResultsByAllianceTag(string $allianceTag): array
    {
        if (!empty($allianceTag)) {
            return $this->db->queryFetchAll(
                "SELECT
                    a.`alliance_id`,
                    a.`alliance_name`,
                    a.`alliance_tag`,
                    a.`alliance_request_notallow` AS `alliance_requests`,
                    s.`alliance_statistic_total_points` AS `alliance_points`,
                    (SELECT
                        COUNT(user_id) AS `ally_members`
                        FROM `" . USERS . "`
                        WHERE `user_ally_id` = a.`alliance_id`
                    ) AS `alliance_members`
                FROM `" . ALLIANCE . "` AS a
                    LEFT JOIN `" . ALLIANCE_STATISTICS . "` AS s ON a.`alliance_id` = s.`alliance_statistic_alliance_id`
                WHERE (a.alliance_name LIKE '%" . $this->db->escapeValue($allianceTag) . "%')
                    OR (a.alliance_tag LIKE '%" . $this->db->escapeValue($allianceTag) . "%')
                LIMIT " . MAX_SEARCH_RESULTS . ";"
            );
        }

        return [];
    }

    /**
     * Search results by planet name
     *
     * @param string $planetName
     * @return array
     */
    public function getResultsByPlanetName(string $planetName): array
    {
        if (!empty($planetName)) {
            return $this->db->queryFetchAll(
                "SELECT
                    u.`user_id`,
                    u.`user_name`,
                    u.`user_authlevel`,
                    p.`planet_name`,
                    p.`planet_galaxy`,
                    p.`planet_system`,
                    p.`planet_planet`,
                    s.`user_statistic_total_rank` AS `user_rank`,
                    a.`alliance_id`,
                    a.`alliance_name`
                FROM `" . USERS . "` AS u
                    INNER JOIN `" . USERS_STATISTICS . "` AS s ON s.`user_statistic_user_id` = u.`user_id`
                    INNER JOIN `" . PLANETS . "` AS p ON p.`planet_user_id` = u.`user_id`
                    LEFT JOIN `" . ALLIANCE . "` AS a ON a.`alliance_id` = u.`user_ally_id`
                WHERE p.`planet_name` LIKE '%" . $this->db->escapeValue($planetName) . "%'
                LIMIT " . MAX_SEARCH_RESULTS . ";"
            );
        }

        return [];
    }
}
