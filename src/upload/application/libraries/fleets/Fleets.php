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
use application\core\enumerators\MissionsEnumerator as Missions;

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
     * 
     * @var int
     */
    private $_fleet_count = 0;

    /**
     * 
     * @var int
     */
    private $_expedition_count = 0;
    
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
                
                $this->setFleetCount();
                
                if ($fleets->getFleetMission() == Missions::expedition) {
                    
                    $this->setExpeditionsCount();
                }
                
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
     * Increase the fleets count
     * 
     * @return void
     */
    private function setFleetsCount()
    {
        ++$this->_fleet_count;
    }
    
    /**
     * Increase the expeditions count
     * 
     * @return void
     */
    private function setExpeditionsCount()
    {
        ++$this->_expedition_count;
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
     * 
     * @return int
     */
    public function getFleetsCount()
    {
        return $this->_fleet_count;
    }
    
    /**
     * 
     * @return int
     */
    public function getExpeditionsCount()
    {
        return $this->_expedition_count;
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