<?php

declare(strict_types=1);

namespace App\Models\Game;

use App\Core\Model;

class Galaxy extends Model
{
    public function getGalaxyDataByGalaxyAndSystem(int $galaxy, int $system, int $user_id): array
    {
        return $this->db->queryFetchAll(
            "SELECT
                (
                    SELECT
                        CONCAT (GROUP_CONCAT(`buddy_receiver`), ',', GROUP_CONCAT(`buddy_sender`)) AS `buddys`
                    FROM `" . BUDDY . '` AS b
                    WHERE
                    (
                        b.`buddy_receiver` = ' . $user_id . '
                        OR
                        b.`buddy_sender` = ' . $user_id . '
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
                    FROM `' . USERS . '`
                    WHERE `user_ally_id` = a.`alliance_id`
                ) AS `ally_members`
            FROM `' . PLANETS . '` AS p
                INNER JOIN `' . USERS . '` AS u
                    ON p.`planet_user_id` = u.`user_id`
                INNER JOIN `' . PREFERENCES . '` AS pr
                    ON pr.`preference_user_id` = u.`user_id`
                INNER JOIN `' . USERS_STATISTICS . '` AS s
                    ON s.`user_statistic_user_id` = u.`user_id`
                LEFT JOIN `' . ALLIANCE . '` AS a
                    ON a.`alliance_id` = u.`user_ally_id`
                LEFT JOIN `' . PLANETS . '` AS m
                    ON m.`planet_id` = (
                        SELECT mp.`planet_id`
                        FROM `' . PLANETS . "` AS mp
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

    public function countAmountFleetsByUserId(int $user_id): int
    {
        return (int) $this->db->queryFetch(
            'SELECT
                COUNT(`fleet_id`) AS total_fleets
            FROM `' . FLEETS . "`
            WHERE `fleet_owner` = '" . $user_id . "';"
        )['total_fleets'];
    }

    public function getTargetUserDataByCoords(int $galaxy, int $system, int $planet, int $planet_type = 1): ?array
    {
        return $this->db->queryFetch(
            'SELECT
                u.`user_id`,
                u.`user_onlinetime`,
                u.`user_authlevel`,
                pr.`preference_vacation_mode`
            FROM `' . USERS . '` AS u
            INNER JOIN `' . PREFERENCES . '` AS pr ON pr.preference_user_id = u.user_id
            WHERE u.user_id = (
                SELECT `planet_user_id`
                FROM `' . PLANETS . '`
                WHERE planet_galaxy = ' . $galaxy . '  AND
                    planet_system = ' . $system . ' AND
                    planet_planet = ' . $planet . ' AND
                    planet_type = ' . $planet_type . '
                LIMIT 1
                )
            LIMIT 1'
        );
    }

    public function getPlanetDebrisByCoords(int $galaxy, int $system, int $planet): ?array
    {
        return $this->db->queryFetch(
            'SELECT
                `planet_invisible_start_time`,
                `planet_debris_metal`,
                `planet_debris_crystal`
            FROM `' . PLANETS . "`
            WHERE `planet_galaxy` = '" . $galaxy . "'
                AND `planet_system` = '" . $system . "'
                AND `planet_planet` = '" . $planet . "'
                AND `planet_type` = 1;"
        );
    }
}
