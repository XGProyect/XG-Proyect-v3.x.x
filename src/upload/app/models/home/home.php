<?php declare (strict_types = 1);

/**
 * Home Model
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

/**
 * Home Class
 */
class Home extends Model
{
    /**
     * Get the user based on the provided credentials
     *
     * @param string $email
     * @return array|null
     */
    public function getUserWithProvidedCredentials(string $email): ?array
    {
        return $this->db->queryFetch(
            "SELECT
                u.`user_id`,
                u.`user_name`,
                u.`user_password`,
                b.`banned_longer`
            FROM `" . USERS . "` AS u
            LEFT JOIN `" . BANNED . "` AS b
                ON b.`banned_who` = u.`user_name`
            WHERE `user_email` = '" . $this->db->escapeValue($email) . "'
            LIMIT 1"
        );
    }

    /**
     * The the user home planet as the current planet
     *
     * @param integer $user_id
     * @return void
     */
    public function setUserHomeCurrentPlanet(int $user_id): void
    {
        $this->db->query(
            "UPDATE `" . USERS . "` SET
                `user_current_planet` = `user_home_planet_id`
            WHERE `user_id` ='" . $user_id . "'"
        );
    }

    /**
     * Remove ban
     *
     * @param string $user_name
     * @return void
     */
    public function removeBan(string $user_name): void
    {
        $this->db->query(
            "DELETE FROM `" . BANNED . "`
            WHERE `banned_who` = '" . $user_name . "'"
        );
    }
}
