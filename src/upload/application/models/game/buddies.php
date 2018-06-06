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
}

/* end of buddies.php */