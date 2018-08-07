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

use application\core\Entity;

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
class AllianceEntity extends Entity
{

    /**
     * Constructor
     * 
     * @param array $data Data
     * 
     * @return void
     */
    public function __construct($data)
    {
        parent::__construct($data);
    }
    
    /**
     * Return the alliance id
     * 
     * @return string
     */
    public function getAllianceId()
    {
        return $this->_data['alliance_id'];
    }

    /**
     * Return the alliance name
     * 
     * @return string
     */
    public function getAllianceName()
    {
        return $this->_data['alliance_name'];
    }

    /**
     * Return the alliance tag
     * 
     * @return string
     */
    public function getAllianceTag()
    {
        return $this->_data['alliance_tag'];
    }

    /**
     * Return the alliance owner
     * 
     * @return string
     */
    public function getAllianceOwner()
    {
        return $this->_data['alliance_owner'];
    }

    /**
     * Return the alliance register time
     * 
     * @return string
     */
    public function getAllianceRegisterTime()
    {
        return $this->_data['alliance_register_time'];
    }
    
    /**
     * Return the alliance description
     * 
     * @return string
     */
    public function getAllianceDescription()
    {
        return $this->_data['alliance_description'];
    }
    
    /**
     * Return the alliance web
     * 
     * @return string
     */
    public function getAllianceWeb()
    {
        return $this->_data['alliance_web'];
    }
    
    /**
     * Return the alliance text
     * 
     * @return string
     */
    public function getAllianceText()
    {
        return $this->_data['alliance_text'];
    }
    
    /**
     * Return the alliance image
     * 
     * @return string
     */
    public function getAllianceImage()
    {
        return $this->_data['alliance_image'];
    }
    
    /**
     * Return the alliance request
     * 
     * @return string
     */
    public function getAllianceRequest()
    {
        return $this->_data['alliance_request'];
    }
    
    /**
     * Return the alliance request not allow
     * 
     * @return string
     */
    public function getAllianceRequestNotAllow()
    {
        return $this->_data['alliance_request_notallow'];
    }
    
    /**
     * Return the alliance owner range
     * 
     * @return string
     */
    public function getAllianceOwnerRange()
    {
        return $this->_data['alliance_owner_range'];
    }

    /**
     * Return the alliance ranks
     * 
     * @return string
     */
    public function getAllianceRanks()
    {
        return $this->_data['alliance_ranks'];
    }
    
    /**
     * Return the alliance members
     * 
     * @return string
     */
    public function getAllianceMembers()
    {
        return $this->_data['alliance_members'];
    }
}

/* end of AllianceEntity.php */
