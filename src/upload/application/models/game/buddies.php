<?php
/**
 * Buddies Model
 *
 * PHP Version 7+
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
 * Buddies Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Buddies
{

    private $db = null;

    /**
     * __construct()
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
     * 
     * @param int $user_id User ID
     * 
     * @return array
     */
    public function getBuddiesByUserId($user_id)
    {
        return $this->db->queryFetchAll(
            "SELECT *
                FROM `" . BUDDY . "`
                WHERE `buddy_sender` = '" . (int)$user_id . "'
                    OR `buddy_receiver` = '" . (int)$user_id . "'"
        );
    }
    
    /**
     * Get Buddy data by Id
     * 
     * @param int $user_id User ID
     * 
     * @return array
     */
    public function getBuddyDataById($user_id)
    {
        return $this->db->queryFetch(
            "SELECT u.`user_id`, 
                    u.`user_name`, 
                    u.`user_galaxy`, 
                    u.`user_system`, 
                    u.`user_planet`, 
                    u.`user_onlinetime`, 
                    a.`alliance_id`, 
                    a.`alliance_name`
            FROM " . USERS . " AS u
            LEFT JOIN `" . ALLIANCE . "` AS a ON a.`alliance_id` = u.`user_ally_id`
            WHERE u.`user_id`='" . (int)$user_id . "'"
        );
    }
}

/* end of buddies.php */