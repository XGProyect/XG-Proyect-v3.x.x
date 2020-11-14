<?php
/**
 * Announcement Model
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
 * Announcement Class
 */
class Announcement extends Model
{
    /**
     * Get all server users
     *
     * @return array
     */
    public function getAllPlayers(): array
    {
        return $this->db->queryFetchAll(
            "SELECT
                `user_id`,
                `user_name`,
                `user_email`
            FROM `" . USERS . "`;"
        );
    }
}
