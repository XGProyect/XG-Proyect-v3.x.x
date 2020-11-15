<?php
/**
 * Fleets Model
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\models\adm;

use App\core\Model;

/**
 * Fleets Class
 */
class Fleets extends Model
{
    /**
     * Get all fleets
     *
     * @return array
     */
    public function getAllFleets(): array
    {
        return $this->db->queryFetchAll(
            "SELECT
                f.*,
                (
                    SELECT `user_name`
                    FROM `" . USERS . "`
                    WHERE `user_id` = f.`fleet_owner`
                ) AS fleet_username,
                (
                    SELECT `user_name`
                    FROM `" . USERS . "`
                    WHERE `user_id` = f.`fleet_target_owner`
                ) AS `target_username`
            FROM `" . FLEETS . "` AS f
            ORDER BY f.`fleet_end_time` ASC;"
        );
    }

    /**
     * Delete fleet by ID
     *
     * @param integer $fleet_id
     * @return void
     */
    public function restartFleetById(int $fleet): void
    {
        try {
            $this->db->beginTransaction();

            $base_time = time();

            $times = $this->db->queryFetch(
                "SELECT
                    (f.`fleet_end_time` - f.`fleet_start_time`) AS `mission_time`
                FROM `" . FLEETS . "` f
                WHERE f.`fleet_id` = '" . $fleet . "';"
            );

            $start_time = $base_time + $times['mission_time'];
            $end_time = $base_time + $times['mission_time'] * 2;

            $this->db->query(
                "UPDATE `" . FLEETS . "` f SET
                    f.`fleet_start_time` = '" . $start_time . "',
                    f.`fleet_end_stay` = '0',
                    f.`fleet_end_time` = '" . $end_time . "'
                WHERE f.`fleet_id` = '" . $fleet . "';"
            );

            $this->db->commitTransaction();
        } catch (Exception $e) {
            $this->db->rollbackTransaction();
        }
    }

    /**
     * Delete fleet by ID
     *
     * @param integer $fleet_id
     * @return void
     */
    public function endFleetById(int $fleet_id): void
    {
        $this->db->query(
            "UPDATE
                `" . FLEETS . "` f
            SET
                f.`fleet_start_time` = '" . time() . "',
                f.`fleet_end_time` = '" . time() . "',
                f.`fleet_end_stay` = '0'
            WHERE f.`fleet_id` = '" . $fleet_id . "';"
        );
    }

    /**
     * Delete fleet by ID
     *
     * @param integer $fleet_id
     * @return void
     */
    public function returnFleetById(int $fleet_id): void
    {
        $this->db->query(
            "UPDATE `" . FLEETS . "` f SET
                f.`fleet_start_time` = '" . time() . "',
                f.`fleet_end_stay` = '0',
                f.`fleet_end_time` = '" . (time() * 2) . "' - f.`fleet_creation`,
                f.`fleet_target_owner` = f.`fleet_owner`,
                f.`fleet_mess` = '1'
            WHERE f.`fleet_id` = '" . $fleet_id . "';"
        );
    }

    /**
     * Delete fleet by ID
     *
     * @param integer $fleet_id
     * @return void
     */
    public function deleteFleetById(int $fleet_id): void
    {
        $this->db->query(
            "DELETE f.*
            FROM `" . FLEETS . "` f
            WHERE f.`fleet_id` = '" . $fleet_id . "';"
        );
    }
}
