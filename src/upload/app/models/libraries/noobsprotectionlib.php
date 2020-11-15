<?php declare (strict_types = 1);

/**
 * NoobsProtectionLib Model
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\models\libraries;

use App\core\Model;
use App\libraries\Functions;

/**
 * NoobsProtectionLib Class
 */
class NoobsProtectionLib extends Model
{
    /**
     * Read all server configurations
     *
     * @return array
     */
    public function readAllConfigs(): array
    {
        return Functions::readConfig('', true);
    }

    /**
     * Return points for current user and the other user
     *
     * @param integer $current_user_id
     * @param integer $other_user_id
     * @return array
     */
    public function returnBothPartiesPoints(int $current_user_id, int $other_user_id): array
    {
        return $this->db->queryFetch(
            "SELECT
                (
                    SELECT
                        `user_statistic_total_points`
                    FROM `" . USERS_STATISTICS . "`
                    WHERE `user_statistic_user_id` = " . $current_user_id . "
                ) AS user_points,
                (
                    SELECT
                        `user_statistic_total_points`
                    FROM `" . USERS_STATISTICS . "`
                    WHERE `user_statistic_user_id` = " . $other_user_id . "
                ) AS target_points"
        );
    }
}
