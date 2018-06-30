<?php
/**
 * Alliance Model
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
 * Alliance Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Banned
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
     * Get banned users
     * 
     * @return array
     */
    public function getBannedUsers()
    {
        return $this->db->queryFetchAll(
                "SELECT *
            FROM " . BANNED . "
            ORDER BY `banned_id`;"
        );
    }
}

/* end of buildings.php */