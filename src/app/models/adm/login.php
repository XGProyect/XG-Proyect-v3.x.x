<?php
/**
 * Login Model
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

class Login extends Model
{
    public function getLoginData(string $userEmail): array
    {
        $result = $this->db->queryFetch(
            "SELECT
                `user_id`,
                `user_name`,
                `user_password`
            FROM `" . USERS . "`
            WHERE `user_email` = '" . $this->db->escapeValue($userEmail) . "'
                AND `user_authlevel` >= '1'
            LIMIT 1"
        );

        if ($result) {
            return $result;
        }

        return [];
    }
}
