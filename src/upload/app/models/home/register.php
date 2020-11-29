<?php declare (strict_types = 1);

/**
 * Register Model
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
use App\libraries\PlanetLib;
use App\libraries\UsersLibrary;

/**
 * Register Class
 */
class Register extends Model
{
    /**
     * Contains the ID of the new user
     *
     * @var integer
     */
    private $user_id = 0;

    /**
     * Contains the user name of the new user
     *
     * @var string
     */
    private $user_name = '';

    /**
     * Contains the email of the new user
     *
     * @var string
     */
    private $user_email = '';

    /**
     * Contains the password of the new user
     *
     * @var string
     */
    private $user_password = '';

    /**
     * Get planet by coords
     *
     * @param integer $galaxy
     * @param integer $system
     * @param integer $planet
     * @return bool
     */
    public function checkIfPlanetExists(int $galaxy, int $system, int $planet): bool
    {
        $planet = $this->db->queryFetch(
            "SELECT
                `planet_id`
            FROM `" . PLANETS . "`
            WHERE `planet_galaxy` = '" . $galaxy . "'
                AND `planet_system` = '" . $system . "'
                AND `planet_planet` = '" . $planet . "'
            LIMIT 1;"
        );

        return isset($planet['planet_id']);
    }

    /**
     * Register a new user
     *
     * @param UsersLibrary $user
     * @param array $new_user_data
     * @param array $coords
     * @return void
     */
    public function createNewUser(UsersLibrary $user, array $new_user_data, array $coords): void
    {
        try {
            $this->db->beginTransaction();

            $this->user_name = $this->db->escapeValue(strip_tags($new_user_data['new_user_name']));
            $this->user_email = $this->db->escapeValue($new_user_data['new_user_email']);
            $this->user_password = Functions::hash($new_user_data['new_user_password']);

            // create the new user
            $this->user_id = $user->createUserWithOptions(
                [
                    'user_name' => $this->user_name,
                    'user_password' => $this->user_password,
                    'user_email' => $this->user_email,
                    'user_lastip' => $_SERVER['REMOTE_ADDR'],
                    'user_ip_at_reg' => $_SERVER['REMOTE_ADDR'],
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                    'user_current_page' => $this->db->escapeValue($_SERVER['REQUEST_URI']),
                    'user_register_time' => time(),
                    'user_onlinetime' => time(),
                ]
            );

            // create a new planet
            $this->createNewPlanet($coords, $this->user_id);

            // assign the new planet to the new user
            $this->updateUserPlanet($coords, $this->user_id);

            $this->db->commitTransaction();
        } catch (Exception $e) {
            $this->db->rollbackTransaction();
        }
    }

    /**
     * Create a new planet
     *
     * @param array $coords
     * @param integer $new_user_id
     * @return void
     */
    private function createNewPlanet(array $coords, int $new_user_id): void
    {
        $creator = new PlanetLib;
        $creator->setNewPlanet($coords['galaxy'], $coords['system'], $coords['planet'], $new_user_id, '', true);
    }

    /**
     * Assign the newly created planet to the newly registered user
     *
     * @param array $coords
     * @param integer $new_user_id
     * @return void
     */
    private function updateUserPlanet(array $coords, int $new_user_id): void
    {
        $this->db->query(
            "UPDATE `" . USERS . "` SET
            `user_home_planet_id` = (
                SELECT
                    `planet_id`
                FROM `" . PLANETS . "`
                WHERE `planet_user_id` = '" . $new_user_id . "'
                LIMIT 1
            ),
            `user_current_planet` = (
                SELECT
                    `planet_id`
                FROM `" . PLANETS . "`
                WHERE `planet_user_id` = '" . $new_user_id . "'
                LIMIT 1
            ),
            `user_galaxy` = '" . $coords['galaxy'] . "',
            `user_system` = '" . $coords['system'] . "',
            `user_planet` = '" . $coords['planet'] . "'
             WHERE `user_id` = '" . $new_user_id . "' LIMIT 1;"
        );
    }

    /**
     * Get the new user ID
     *
     * @return array
     */
    public function getNewUserData(): array
    {
        return [
            'user_id' => $this->user_id,
            'user_name' => $this->user_name,
            'user_email' => $this->user_email,
            'user_hashed_password' => $this->user_password,
        ];
    }

    /**
     * Check if the username exists
     *
     * @param string $user_name
     * @return array|null
     */
    public function checkUser(string $user_name): ?array
    {
        return $this->db->queryFetch(
            "SELECT
                u.`user_name`
            FROM `" . USERS . "` AS u
            WHERE `user_name` = '" . $this->db->escapeValue($user_name) . "'
            LIMIT 1;"
        );
    }

    /**
     * Check if the email exists
     *
     * @param string $email
     * @return array|null
     */
    public function checkEmail(string $email): ?array
    {
        return $this->db->queryFetch(
            "SELECT
                u.`user_email`
            FROM `" . USERS . "` AS u
            WHERE `user_email` = '" . $this->db->escapeValue($email) . "'
            LIMIT 1;"
        );
    }
}
