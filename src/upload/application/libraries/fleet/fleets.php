<?php
/**
 * Fleets
 *
 * PHP Version 5.5+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\libraries\fleets;

use application\core\entities\FleetEntity;

/**
 * Fleets Class
 *
 * @category Classes
 * @package  fleets
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Fleets
{
    /**
     *
     * @var array 
     */
    private $_fleets = [];
    
    /**
     *
     * @var int 
     */
    private $_current_user_id = 0;
    
    /**
     * Constructor
     * 
     * @param array $fleets          Fleets
     * @param int   $current_user_id Current User ID
     * 
     * @return void
     */
    public function __construct($fleets, $current_user_id)
    {
        if (is_array($fleets)) {
            
            $this->setUp($fleets);
            $this->setUserId($current_user_id);
        }
    }
    
    /**
     * Get all the fleets
     * 
     * @return array
     */
    public function getFleets()
    {
        $list_of_fleets = [];
        
        foreach($this->_fleets as $fleets) {
            
            if (($fleets instanceof FleetEntity)) {
                
                $list_of_fleets[] = $fleets;
            }
        }
        
        return $list_of_fleets;
    }
    
    /**
     * Set up the list of fleets
     * 
     * @param array $fleets Fleets
     * 
     * @return void
     */
    private function setUp($fleets)
    {
        foreach ($fleets as $fleet) {

            $this->_fleets[] = $this->createNewFleetEntity($fleet);
        }
    }
    
    /**
     * 
     * @param int $user_id User Id
     */
    private function setUserId($user_id)
    {
        $this->_current_user_id = $user_id;
    }
    
    /**
     * 
     * @return int
     */
    private function getUserId()
    {
        return $this->_current_user_id;
    }
    
    /**
     * Create a new instance of FleetEntity
     * 
     * @param array $fleet Fleet
     * 
     * @return \FleetEntity
     */
    private function createNewFleetEntity($fleet)
    {   
        return new FleetEntity($fleet);
    }
}

/* end of fleets.php */
