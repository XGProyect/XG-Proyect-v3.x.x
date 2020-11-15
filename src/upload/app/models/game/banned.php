<?php
/**
 * Banned Model
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */
namespace App\models\game;

use App\core\Model;

/**
 * Banned Class
 */
class Banned extends Model
{
    /**
     * Get banned users
     *
     * @return array
     */
    public function getBannedUsers()
    {
        return $this->db->queryFetchAll(
            "SELECT *
            FROM " . BANNED . "
            ORDER BY `banned_id`;"
        );
    }
}
