<?php declare (strict_types = 1);

/**
 * Mail Model
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\models\home;

use App\core\Model;
use App\libraries\Functions;

/**
 * Mail Class
 */
class Mail extends Model
{
    /**
     * Check if email exists returning the user name
     *
     * @param string $email
     * @return string|null
     */
    public function getEmailUsername(string $email): ?string
    {
        return $this->db->queryFetch(
            "SELECT
                `user_name`
            FROM `" . USERS . "`
            WHERE `user_email` = '" . $this->db->escapeValue($email) . "'
            LIMIT 1;"
        )['user_name'];
    }

    /**
     * Set a new password for the user
     *
     * @param string $email
     * @param string $new_password
     * @return void
     */
    public function setUserNewPassword(string $email, string $new_password): void
    {
        $this->db->query(
            "UPDATE `" . USERS . "` SET
                `user_password` = '" . Functions::hash($new_password) . "'
            WHERE `user_email` = '" . $this->db->escapeValue($email) . "'
            LIMIT 1;"
        );
    }
}
