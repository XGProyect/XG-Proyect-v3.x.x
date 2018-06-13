<?php
/**
 * Alliance
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
namespace application\libraries\alliance;

use application\core\entities\AllianceEntity;

/**
 * Alliance Class
 *
 * @category Classes
 * @package  alliance
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Alliances
{
    /**
     *
     * @var array 
     */
    private $_alliances = [];
    
    /**
     *
     * @var int 
     */
    private $_current_user_id = 0;

    /**
     * Constructor
     * 
     * @param array $alliances       Alliances
     * @param int   $current_user_id Current User ID
     * 
     * @return void
     */
    public function __construct($alliances, $current_user_id)
    {
        if (is_array($alliances)) {
            
            $this->setUp($alliances);
            $this->setUserId($current_user_id);
        }
    }
    
    /**
     * Get all the alliances
     * 
     * @return array
     */
    public function getAlliance()
    {
        $list_of_alliances = [];
        
        foreach($this->_alliances as $alliance) {
            
            if (($alliance instanceof AllianceEntity)) {
                
                $list_of_alliances[] = $alliance;
            }
        }
        
        return $list_of_alliances;
    }
    
    /**
     * Return current alliance data
     * 
     * @return array
     */
    public function getCurrentAlliance()
    {
        return $this->_alliances[0];
    }
    
    /**
     * Check if is the alliance owner
     * 
     * @return string
     */
    public function isOwner()
    {
        return ($this->getCurrentAlliance()->getAllianceOwner() === $this->getUserId());
    }
    
    /**
     * 
     * @return boolean
     */
    public function checkRank($rank)
    {
        return true;
    }
    
    /**
     * Check if the user has access to certain section of the alliance
     * 
     * @param int $rank Rank
     * 
     * @return boolean
     */
    public function hasAccess($rank)
    {
        return ($this->isOwner() or $this->checkRank($rank));
    }
    
    /**
     * Set up the list of alliances
     * 
     * @param array $alliances Alliances
     */
    private function setUp($alliances)
    {
        foreach ($alliances as $alliance) {

            $this->_alliances[] = $this->createNewAllianceEntity($alliance);
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
     * Create a new instance of AllianceEntity
     * 
     * @param array $alliance Alliance
     * 
     * @return \AllianceEntity
     */
    private function createNewAllianceEntity($alliance)
    {   
        return new AllianceEntity($alliance);
    }
}

/* end of Alliance.php */
