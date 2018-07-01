<?php
/**
 * Alliance entity
 *
 * PHP Version 5.5+
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\core\entities;

use Exception;

/**
 * Alliance Entity Class
 *
 * @category Entity
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class AllianceEntity
{

    /**
     *
     * @var array
     */
    private $_alliance = [];

    /**
     * Init with the alliance data
     * 
     * @param array $alliance Alliance
     * 
     * @return void
     */
    public function __construct($alliance)
    {
        $this->setAlliance($alliance);
    }

    /**
     * Set the current alliance
     * 
     * @param array $alliance Alliance
     * 
     * @throws Exception
     * 
     * @return void
     */
    private function setAlliance($alliance)
    {
        try {

            if (!is_array($alliance)) {
                
                return null;
            }
            
            $this->_alliance = $alliance;
        } catch (Exception $e) {

            die('Caught exception: ' . $e->getMessage() . "\n");
        }
    }
    
    /**
     * Return the alliance id
     * 
     * @return string
     */
    public function getAllianceId()
    {
        return $this->_alliance['alliance_id'];
    }

    /**
     * Return the alliance name
     * 
     * @return string
     */
    public function getAllianceName()
    {
        return $this->_alliance['alliance_name'];
    }

    /**
     * Return the alliance tag
     * 
     * @return string
     */
    public function getAllianceTag()
    {
        return $this->_alliance['alliance_tag'];
    }

    /**
     * Return the alliance owner
     * 
     * @return string
     */
    public function getAllianceOwner()
    {
        return $this->_alliance['alliance_owner'];
    }

    /**
     * Return the alliance register time
     * 
     * @return string
     */
    public function getAllianceRegisterTime()
    {
        return $this->_alliance['alliance_register_time'];
    }
    
    /**
     * Return the alliance description
     * 
     * @return string
     */
    public function getAllianceDescription()
    {
        return $this->_alliance['alliance_description'];
    }
    
    /**
     * Return the alliance web
     * 
     * @return string
     */
    public function getAllianceWeb()
    {
        return $this->_alliance['alliance_web'];
    }
    
    /**
     * Return the alliance text
     * 
     * @return string
     */
    public function getAllianceText()
    {
        return $this->_alliance['alliance_text'];
    }
    
    /**
     * Return the alliance image
     * 
     * @return string
     */
    public function getAllianceImage()
    {
        return $this->_alliance['alliance_image'];
    }
    
    /**
     * Return the alliance request
     * 
     * @return string
     */
    public function getAllianceRequest()
    {
        return $this->_alliance['alliance_request'];
    }
    
    /**
     * Return the alliance request not allow
     * 
     * @return string
     */
    public function getAllianceRequestNotAllow()
    {
        return $this->_alliance['alliance_request_notallow'];
    }
    
    /**
     * Return the alliance owner range
     * 
     * @return string
     */
    public function getAllianceOwnerRange()
    {
        return $this->_alliance['alliance_owner_range'];
    }

    /**
     * Return the alliance ranks
     * 
     * @return string
     */
    public function getAllianceRanks()
    {
        return $this->_alliance['alliance_ranks'];
    }
    
    /**
     * Return the alliance members
     * 
     * @return string
     */
    public function getAllianceMembers()
    {
        return $this->_alliance['alliance_members'];
    }
}

/* end of AllianceEntity.php */
