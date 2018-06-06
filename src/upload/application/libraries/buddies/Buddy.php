<?php
/**
 * Buddy
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
namespace application\libraries\buddies;

use application\core\entities\BuddyEntity;
use application\libraries\enumerators\BuddiesStatusEnumerator as BuddiesStatus;

/**
 * Buddy Class
 *
 * @category Classes
 * @package  buddy
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Buddy
{
    
    /**
     * Constructor
     * 
     * @param array $buddies Buddies
     * 
     * @return void
     */
    public function __construct($buddies)
    {
        if (is_array($buddies)) {
            
            $this->setUp($buddies);
        }
    }
    
    public function getReceivedRequests()
    {

    }
    
    public function getSentRequests()
    {
        
    }
    
    /**
     * Get all the players that are the current user's buddies
     * 
     * @return array
     */
    public function getBuddies()
    {
        $list_of_buddies = [];
        
        foreach($this->_buddies as $buddy) {
            
            if (($buddy instanceof BuddyEntity) && $this->isBuddy($buddy)) {
                
                $list_of_buddies[] = $buddy;
            }
        }
        
        return $list_of_buddies;
    }
    
    /**
     * Check if is already a buddy
     * 
     * @param BuddyEntity $buddy Buddy
     * 
     * @return boolean
     */
    private function isBuddy($buddy)
    {
        return ($buddy->getBuddyStatus() == BuddiesStatus::isBuddy);
    }
    
    /**
     * Set up the list of buddies
     * 
     * @param array $buddies Buddies
     */
    private function setUp($buddies)
    {
        foreach ($buddies as $buddy) {

            $this->_buddies[] = $this->createNewBuddyEntity($buddy);
        }
    }
    
    /**
     * Create a new instance of BuddyEntity
     * 
     * @param array $buddy Buddy
     * 
     * @return \BuddyEntity
     */
    private function createNewBuddyEntity($buddy)
    {   
        return new BuddyEntity($buddy);
    }
}

/* end of Buddy.php */
