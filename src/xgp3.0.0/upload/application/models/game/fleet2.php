<?php
/**
 * Fleet2 Model
 *
 * PHP Version 5.5+
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.2
 */

namespace application\models\game;

/**
 * Fleet2 Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.2
 */
class Fleet2_Model
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
     * Get ongoing ACS attacks
     * 
     * @param
     * 
     * @return mixed
     */
    public function getOngoingAcs()
    {
        return $this->db->query(
            "SELECT * FROM " . ACS_FLEETS
        );   
        
        return null;
    }
}

/* end of fleet2.php */
