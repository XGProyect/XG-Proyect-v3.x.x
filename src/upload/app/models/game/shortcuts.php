<?php
/**
 * Shortcuts Model
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
 * Shortcuts Class
 */
class Shortcuts extends Model
{
    /**
     * Update user shortcuts
     *
     * @param int    $user_id   User ID
     * @param string $shortcuts Shortcuts
     *
     * @return void
     */
    public function updateShortcuts(int $user_id, string $shortcuts): void
    {
        $this->db->query(
            "UPDATE `" . USERS . "` u SET
                u.`user_fleet_shortcuts` = '" . $shortcuts . "'
            WHERE u.`user_id` = '" . $user_id . "'"
        );
    }
}
