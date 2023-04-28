<?php

declare(strict_types=1);

namespace App\Models\Game;

use App\Core\Model;

class Infos extends Model
{
    public function getTargetGate(int $target_planet_id): array
    {
        return $this->db->queryFetch(
            'SELECT
                p.`planet_id`,
                b.`building_jump_gate`,
                p.`planet_last_jump_time`
            FROM `' . PLANETS . '` AS p
            INNER JOIN `' . BUILDINGS . "` AS b
                ON b.`building_planet_id` = p.`planet_id`
            WHERE p.`planet_id` = '" . $target_planet_id . "';"
        );
    }

    public function doJump(string $sub_query_origin, string $sub_query_destiny, int $jump_time, int $current_planet_id, int $target_planet_id, int $user_id): void
    {
        try {
            $this->db->beginTransaction();

            $this->db->query(
                'UPDATE `' . PLANETS . '`, `' . USERS . '`, `' . SHIPS . "` SET
                    $sub_query_origin
                    `planet_last_jump_time` = '" . $jump_time . "',
                    `user_current_planet` = '" . $target_planet_id . "'
                WHERE `planet_id` = '" . $current_planet_id . "'
                    AND `ship_planet_id` = '" . $current_planet_id . "'
                    AND `user_id` = '" . $user_id . "';"
            );

            $this->db->query(
                'UPDATE `' . PLANETS . '`, `' . SHIPS . "` SET
                $sub_query_destiny
                `planet_last_jump_time` = '" . $jump_time . "'
                WHERE `planet_id` = '" . $target_planet_id . "'
                    AND `ship_planet_id` = '" . $target_planet_id . "';"
            );

            $this->db->commitTransaction();
        } catch (Exception $e) {
            $this->db->rollbackTransaction();
        }
    }

    /**
     * Get a list of moons
     *
     * @param integer $user_id
     * @return array
     */
    public function getListOfMoons(int $user_id): array
    {
        return $this->db->queryFetchAll(
            'SELECT
                m.`planet_id`,
                m.`planet_galaxy`,
                m.`planet_system`,
                m.`planet_planet`,
                m.`planet_name`,
                m.`planet_last_jump_time`,
                b.`building_jump_gate`
            FROM `' . PLANETS . '` AS m
            INNER JOIN `' . BUILDINGS . "` AS b ON b.building_planet_id = m.planet_id
            WHERE m.`planet_type` = '3'
                AND m.`planet_user_id` = '" . $user_id . "';"
        );
    }
}
