<?php

declare(strict_types=1);

/**
 * Preferences Model
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
namespace application\models\game;

/**
 * Preferences Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Preferences
{

    private $db = null;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct($db)
    {
        // use this to make queries
        $this->db = $db;
    }

    /**
     * __destruct
     * 
     * @return void
     */
    public function __destruct()
    {
        $this->db->closeConnection();
    }

    /**
     * Get all preferences by a certain user
     * 
     * @param int $user_id
     * 
     * @return array
     */
    public function getAllPreferencesByUserId(int $user_id): array
    {
        return $this->db->queryFetchAll(
            "SELECT
                p.*
            FROM `" . PREFERENCES . "` p
            WHERE p.`preference_user_id` = '" . $user_id . "';"
        ) ?? [];
    }

    /**
     * Check if the nickname exists
     *
     * @param string $user_name
     * @return array
     */
    public function checkIfNicknameExists(string $nickname): array
    {
        return $this->db->queryFetch(
            "SELECT `user_id`
            FROM `" . USERS . "`
            WHERE `user_name` = '" . $this->db->escapeValue($nickname) . "'
            LIMIT 1;"
        ) ?? [];
    }

    /**
     * Check if the email exists
     *
     * @param string $email
     * @return array
     */
    public function checkIfEmailExists(string $email): array
    {
        return $this->db->queryFetch(
            "SELECT `user_email`
            FROM `" . USERS . "`
            WHERE `user_email` = '" . $this->db->escapeValue($email) . "'
            LIMIT 1;"
        ) ?? [];
    }

    /**
     * Update validated fields
     *
     * @param array $fields
     * @param integer $user_id
     * @return void
     */
    public function updateValidatedFields(array $fields, int $user_id): void
    {
        $columns_to_update  = [];

        foreach ($fields as $column => $value) {

            if (strpos($column, 'user_') !== false) {

                $columns_to_update[] = "u.`" . $column . "` = '" . $value . "'";
            }

            if (strpos($column, 'preference_') !== false) {

                $columns_to_update[] = "p.`" . $column . "` = '" . $value . "'";
            }
        }

        $this->db->query(
            "UPDATE " . USERS . " AS u, " . PREFERENCES . " AS p SET
            " . join($columns_to_update, ', ') . "
            WHERE u.`user_id` = '" . $user_id . "'
                AND p.`preference_user_id` = '" . $user_id . "';"
        );
    }
}

/* end of preferences.php */