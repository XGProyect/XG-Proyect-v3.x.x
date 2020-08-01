<?php

declare (strict_types = 1);

/**
 * Galaxy Model
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
namespace application\models\game;

use application\core\Database;

/**
 * Galaxy Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Galaxy
{
    private $db = null;

    /**
     * Constructor
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        // use this to make queries
        $this->db = $db;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->db->closeConnection();
    }

    /**
     * Get galaxy data by galaxy and system
     *
     * @param integer $galaxy
     * @param integer $system
     * @return array
     */
    public function getGalaxyDataByGalaxyAndSystem(int $galaxy, int $system): array
    {
        return $this->db->queryFetchAll(
            "SELECT
                (
                    SELECT
                        CONCAT (GROUP_CONCAT(`buddy_receiver`), ',', GROUP_CONCAT(`buddy_sender`)) AS `buddys`
                    FROM `" . BUDDY . "` AS b
                    WHERE
                    (
                        b.`buddy_receiver` = u.`user_id`
                        OR
                        b.`buddy_sender` = u.`user_id`
                    )
                ) AS buddys,
                p.`planet_debris_metal` AS `metal`,
                p.`planet_debris_crystal` AS `crystal`,
                p.`planet_id` AS `id_planet`,
                p.`planet_galaxy`,
                p.`planet_system`,
                p.`planet_planet`,
                p.`planet_type`,
                p.`planet_destroyed`,
                p.`planet_name`,
                p.`planet_image`,
                p.`planet_last_update`,
                p.`planet_user_id`,
                u.`user_id`,
                u.`user_ally_id`,
                u.`user_banned`,
                pr.`preference_vacation_mode`,
                u.`user_onlinetime`,
                u.`user_name`,
                u.`user_authlevel`,
                s.`user_statistic_total_rank`,
                s.`user_statistic_total_points`,
                m.`planet_id` AS `id_luna`,
                m.`planet_diameter`,
                m.`planet_temp_min`,
                m.`planet_destroyed` AS `destroyed_moon`,
                m.`planet_name` AS `name_moon`,
                a.`alliance_name`,
                a.`alliance_tag`,
                a.`alliance_web`,
                (
                    SELECT
                        COUNT(`user_id`) AS `ally_members`
                    FROM `" . USERS . "`
                    WHERE `user_ally_id` = a.`alliance_id`
                ) AS `ally_members`
            FROM `" . PLANETS . "` AS p
                INNER JOIN `" . USERS . "` AS u
                    ON p.`planet_user_id` = u.`user_id`
                INNER JOIN `" . PREFERENCES . "` AS pr
                    ON pr.`preference_user_id` = u.`user_id`
                INNER JOIN `" . USERS_STATISTICS . "` AS s
                    ON s.`user_statistic_user_id` = u.`user_id`
                LEFT JOIN `" . ALLIANCE . "` AS a
                    ON a.`alliance_id` = u.`user_ally_id`
                LEFT JOIN `" . PLANETS . "` AS m
                    ON m.`planet_id` = (
                        SELECT mp.`planet_id`
                        FROM `" . PLANETS . "` AS mp
                        WHERE (
                            mp.`planet_galaxy` = p.`planet_galaxy`
                            AND
                            mp.`planet_system` = p.`planet_system`
                            AND
                            mp.`planet_planet` = p.`planet_planet`
                            AND
                            mp.`planet_type` = '3'
                        )
                    )
            WHERE (
                    p.planet_galaxy = '" . $galaxy . "'
                    AND
                    p.planet_system = '" . $system . "'
                    AND
                    p.planet_type = '1'
                    AND
                    (
                        p.planet_planet > '0'
                        AND
                        p.planet_planet <= '" . MAX_PLANET_IN_SYSTEM . "'
                    )
            )
            ORDER BY p.planet_planet;"
        );
    }

    /**
     * Get amount of fleets that the user has
     *
     * @param integer $user_id
     * @return array
     */
    public function countAmountFleetsByUserId(int $user_id): int
    {
        return $this->db->queryFetch(
            "SELECT
                COUNT(`fleet_id`) AS total_fleets
            FROM `" . FLEETS . "`
            WHERE `fleet_owner` = '" . $user_id . "';"
        )['total_fleets'];
    }

    /**
     * Get target user data by coords
     *
     * @param integer $galaxy
     * @param integer $system
     * @param integer $planet
     * @return array
     */
    public function getTargetUserDataByCoords(int $galaxy, int $system, int $planet): array
    {
        return $this->db->queryFetch(
            "SELECT
                u.`user_id`,
                u.`user_onlinetime`,
                pr.`preference_vacation_mode`
            FROM `" . USERS . "` AS u
            INNER JOIN `" . PREFERENCES . "` AS pr ON pr.preference_user_id = u.user_id
            WHERE u.user_id = (
                SELECT `planet_user_id`
                FROM `" . PLANETS . "`
                WHERE planet_galaxy = " . $galaxy . "  AND
                    planet_system = " . $system . " AND
                    planet_planet = " . $planet . " AND
                    planet_type = 1
                LIMIT 1
                )
            LIMIT 1"
        );
    }

    /**
     * Insert a new missiles mission into the fleets table
     *
     * @param array $data
     * @return void
     */
    public function insertNewMissilesMission(array $data): void
    {
        try {
            $this->db->beginTransaction();

            $this->db->query(
                "INSERT INTO `" . FLEETS . "` SET
                `fleet_owner` = '" . $data['fleet_owner'] . "',
                `fleet_mission` = '10',
                `fleet_amount` = " . $data['fleet_amount'] . ",
                `fleet_array` = '" . $data['fleet_array'] . "',
                `fleet_start_time` = '" . $data['fleet_start_time'] . "',
                `fleet_start_galaxy` = '" . $data['fleet_start_galaxy'] . "',
                `fleet_start_system` = '" . $data['fleet_start_system'] . "',
                `fleet_start_planet` ='" . $data['fleet_start_planet'] . "',
                `fleet_start_type` = '1',
                `fleet_end_time` = '" . $data['fleet_end_time'] . "',
                `fleet_end_stay` = '0',
                `fleet_end_galaxy` = '" . $data['fleet_end_galaxy'] . "',
                `fleet_end_system` = '" . $data['fleet_end_system'] . "',
                `fleet_end_planet` = '" . $data['fleet_end_planet'] . "',
                `fleet_end_type` = '1',
                `fleet_target_obj` = '" . $data['fleet_target_obj'] . "',
                `fleet_resource_metal` = '0',
                `fleet_resource_crystal` = '0',
                `fleet_resource_deuterium` = '0',
                `fleet_target_owner` = '" . $data['fleet_target_owner'] . "',
                `fleet_group` = '0',
                `fleet_mess` = '0',
                `fleet_creation` = '" . time() . "';"
            );

            $this->db->query(
                "UPDATE `" . DEFENSES . "` SET
                    `defense_interplanetary_missile` = `defense_interplanetary_missile` - " . $data['fleet_amount'] . "
                WHERE `defense_planet_id` =  '" . $data['user_current_planet'] . "'"
            );

            $this->db->commitTransaction();
        } catch (Exception $e) {
            $this->db->rollbackTransaction();
        }
    }
}

/* end of galaxy.php */
