<?php declare (strict_types = 1);

/**
 * Statistics Model
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
use App\libraries\Functions;

/**
 * Statistics Class
 */
class Statistics extends Model
{
    /**
     * Get the amount of alliances
     *
     * @return integer
     */
    public function countAlliances(): int
    {
        return (int) $this->db->queryFetch(
            "SELECT
                COUNT(`alliance_id`) AS `count`
            FROM `" . ALLIANCE . "`;"
        )['count'];
    }

    /**
     * Get list of alliances and their statistics
     *
     * @param string $order
     * @param integer $start
     * @return array|null
     */
    public function getAlliances(string $order, int $start): ?array
    {
        return $this->db->queryFetchAll(
            'SELECT
                s.*,
                a.`alliance_id`,
                a.`alliance_tag`,
                a.`alliance_name`,
                a.`alliance_request_notallow`,
                (
                    SELECT
                        COUNT(user_id) AS `ally_members`
                    FROM `' . USERS . '`
                    WHERE `user_ally_id` = a.`alliance_id`
                ) AS `ally_members`
            FROM `' . ALLIANCE_STATISTICS . '` AS s
            INNER JOIN  `' . ALLIANCE . '` AS a ON a.`alliance_id` = s.`alliance_statistic_alliance_id`
            ORDER BY `alliance_statistic_' . $order . '` DESC, `alliance_statistic_total_rank` ASC
            LIMIT ' . $start . ',100;'
        );
    }

    /**
     * Get list of users and their statistics
     *
     * @param string $order
     * @param integer $start
     * @return array|null
     */
    public function getUsers(string $order, int $start): ?array
    {
        return $this->db->queryFetchAll(
            'SELECT
                s.*,
                u.`user_id`,
                u.`user_name`,
                u.`user_ally_id`,
                a.`alliance_name`
            FROM `' . USERS_STATISTICS . '` as s
            INNER JOIN `' . USERS . '` as u ON u.`user_id` = s.`user_statistic_user_id`
            LEFT JOIN `' . ALLIANCE . '` AS a ON a.`alliance_id` = u.`user_ally_id`
            WHERE `user_authlevel` <= ' . Functions::readConfig('stat_admin_level') . '
            ORDER BY `user_statistic_' . $order . '` DESC, `user_statistic_total_rank` ASC
            LIMIT ' . $start . ',100;'
        );
    }
}
