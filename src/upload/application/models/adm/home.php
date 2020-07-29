<?php
/**
 * Home Model
 *
 * PHP Version 7.1+
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\models\adm;

use application\core\Database;
use application\core\Model;

/**
 * Home Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Home extends Model
{
    /**
     * Get database version
     *
     * @return string
     */
    public function getDbVersion(): string
    {
        return $this->db->version();
    }

    /**
     * Get database size
     *
     * @return array
     */
    public function getDbSize(): array
    {
        return $this->db->query(
            "SELECT
                SUM(data_length + index_length) AS 'db_size'
            FROM information_schema.TABLES
            WHERE table_schema = " . $this->db->escape(DB_NAME) . ";"
        )->row_array();
    }

    /**
     * Get server general counts
     *
     * @return array
     */
    public function getUsersStats(): array
    {
        return $this->db->query(
            "SELECT
                (
                    SELECT
                        COUNT(u.`user_id`) AS `total_users`
                    FROM
                        `" . USERS . "` u
                ) AS `number_users`,
                (
                    SELECT
                        COUNT(a.`alliance_id`) AS `total_alliances`
                    FROM
                        `" . ALLIANCE . "` a
                ) AS `number_alliances`,
                (
                    SELECT
                        COUNT(p.`planet_id`) AS `total_planets`
                    FROM
                        `" . PLANETS . "` p
                    WHERE
                        p.`planet_type` = '1'
                ) AS `number_planets`,
                (
                    SELECT
                        COUNT(m.`planet_id`) AS `total_moons`
                    FROM
                        `" . PLANETS . "` m
                    WHERE
                        m.`planet_type` = '3'
                ) AS `number_moons`,
                (
                    SELECT
                        COUNT(f.`fleet_id`) AS `total_fleets`
                    FROM
                        `" . FLEETS . "` f
                ) AS `number_fleets`,
                (
                    SELECT
                        COUNT(r.`report_rid`) AS `total_reports`
                    FROM
                        `" . REPORTS . "` r
                ) AS `number_reports`,
                (
                    SELECT
                        FLOOR(AVG(s.`user_statistic_total_points`)) AS `average_user_total_points`
                    FROM
                        `" . USERS_STATISTICS . "` s
                ) AS `average_user_points`,
                (
                    SELECT
                        FLOOR(AVG(s.`alliance_statistic_total_points`)) AS `average_alliance_total_points`
                    FROM
                        `" . ALLIANCE_STATISTICS . "` s
                ) AS `average_alliance_points`"
        )->row_array();
    }
}

/* end of home.php */
