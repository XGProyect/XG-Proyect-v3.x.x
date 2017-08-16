<?php
/**
 * Fleet1 Model
 *
 * PHP Version 5.5+
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */

namespace application\models\game;

/**
 * Fleet1 Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */
class Fleet1
{
    private $db = null;
    
    /**
     * __construct()
     */
    public function __construct($db)
    {        
        // use this to make queries
        $this->db   = $db;
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
     * Get on going fleet movements count
     * 
     * @param int $user_id User ID
     * 
     * @return mixed
     */
    public function getCounts($user_id)
    {
        if ((int)$user_id > 0) {

            return $this->db->queryFetch(
               "SELECT
                   (SELECT COUNT(fleet_owner) AS `actcnt`
                           FROM " . FLEETS . "
                           WHERE `fleet_owner` = '" . (int) $user_id . "') AS max_fleet,
                   (SELECT COUNT(fleet_owner) AS `expedi`
                           FROM " . FLEETS . "
                           WHERE `fleet_owner` = '" . (int) $user_id . "'
                                   AND `fleet_mission` = '15') AS max_expeditions"
           );
        }

        return null;
    }
}

/* end of fleet1.php */
