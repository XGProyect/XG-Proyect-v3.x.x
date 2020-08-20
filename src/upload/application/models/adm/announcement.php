<?php
/**
 * Announcement Model
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

use application\core\Model;

/**
 * Announcement Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
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
        return $this->db->query(
            "SELECT
                `user_id`,
                `user_name`,
                `user_email`
            FROM `" . USERS . "`;"
        )->result_array();
    }
}

/* end of announcement.php */
