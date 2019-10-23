<?php
/**
 * Fleet entity
 *
 * PHP Version 7.1+
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
 * Fleet Entity Class
 *
 * @category Entity
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class FleetEntity extends Entity
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
     * Get the fleet id
     * 
     * @return string
     */
    public function getFleetId()
    {
        return $this->_data['fleet_id'];
    }
    
    /**
     * Get the fleet owner
     * 
     * @return string
     */
    public function getFleetOwner()
    {
        return $this->_data['fleet_owner'];
    }
    
    /**
     * Get the fleet mission
     * 
     * @return string
     */
    public function getFleetMission()
    {
        return $this->_data['fleet_mission'];
    }
    
    /**
     * Get the fleet amount
     * 
     * @return string
     */
    public function getFleetAmount()
    {
        return $this->_data['fleet_amount'];
    }
    
    /**
     * Get the fleet array
     * 
     * @return string
     */
    public function getFleetArray()
    {
        return $this->_data['fleet_array'];
    }
    
    /**
     * Get the fleet start time
     * 
     * @return string
     */
    public function getFleetStartTime()
    {
        return $this->_data['fleet_start_time'];
    }
    
    /**
     * Get the fleet start galaxy
     * 
     * @return string
     */
    public function getFleetStartGalaxy()
    {
        return $this->_data['fleet_start_galaxy'];
    }
    
    /**
     * Get the fleet start system
     * 
     * @return string
     */
    public function getFleetStartSystem()
    {
        return $this->_data['fleet_start_system'];
    }
    
    /**
     * Get the fleet start planet
     * 
     * @return string
     */
    public function getFleetStartPlanet()
    {
        return $this->_data['fleet_start_planet'];
    }
    
    /**
     * Get the fleet start type
     * 
     * @return string
     */
    public function getFleetStartType()
    {
        return $this->_data['fleet_start_type'];
    }
    
    /**
     * Get the fleet end time
     * 
     * @return string
     */
    public function getFleetEndTime()
    {
        return $this->_data['fleet_end_time'];
    }
    
    /**
     * Get the fleet end stay
     * 
     * @return string
     */
    public function getFleetEndStay()
    {
        return $this->_data['fleet_end_stay'];
    }
    
    /**
     * Get the fleet end galaxy
     * 
     * @return string
     */
    public function getFleetEndGalaxy()
    {
        return $this->_data['fleet_end_galaxy'];
    }
    
    /**
     * Get the fleet end system
     * 
     * @return string
     */
    public function getFleetEndSystem()
    {
        return $this->_data['fleet_end_system'];
    }
    
    /**
     * Get the fleet end planet
     * 
     * @return string
     */
    public function getFleetEndPlanet()
    {
        return $this->_data['fleet_end_planet'];
    }
    
    /**
     * Get the fleet end type
     * 
     * @return string
     */
    public function getFleetEndType()
    {
        return $this->_data['fleet_end_type'];
    }
    
    /**
     * Get the fleet target obj
     * 
     * @return string
     */
    public function getFleetTargetObj()
    {
        return $this->_data['fleet_target_obj'];
    }
    
    /**
     * Get the fleet resource metal
     * 
     * @return string
     */
    public function getFleetResourceMetal()
    {
        return $this->_data['fleet_resource_metal'];
    }
    
    /**
     * Get the fleet resource crystal
     * 
     * @return string
     */
    public function getFleetResourceCrystal()
    {
        return $this->_data['fleet_resource_crystal'];
    }
    
    /**
     * Get the fleet resource deuterium
     * 
     * @return string
     */
    public function getFleetResourceDeuterium()
    {
        return $this->_data['fleet_resource_deuterium'];
    }
    
    /**
     * Get the fleet fuel
     * 
     * @return string
     */
    public function getFleetFuel()
    {
        return $this->_data['fleet_fuel'];
    }
    
    /**
     * Get the fleet target owner
     * 
     * @return string
     */
    public function getFleetTargetOwner()
    {
        return $this->_data['fleet_target_owner'];
    }
    
    /**
     * Get the fleet group
     * 
     * @return string
     */
    public function getFleetGroup()
    {
        return $this->_data['fleet_group'];
    }
    
    /**
     * Get the fleet mess
     * 
     * @return string
     */
    public function getFleetMess()
    {
        return $this->_data['fleet_mess'];
    }
    
    /**
     * Get the fleet creation 
     * 
     * @return string
     */
    public function getFleetCreation()
    {
        return $this->_data['fleet_creation'];
    }
}

/* end of FleetEntity.php */
