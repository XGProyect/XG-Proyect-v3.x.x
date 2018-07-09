<?php
/**
 * Acs Fleet entity
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
 * Acs Fleet entity Class
 *
 * @category Entity
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class AcsFleetEntity extends Entity
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
     * Get the acs fleet id
     * 
     * @return string
     */
    public function getAcsFleetId()
    {
        return $this->_data['acs_fleet_id'];
    }

    /**
     * Get the acs fleet name
     * 
     * @return string
     */
    public function getAcsFleetName()
    {
        return $this->_data['acs_fleet_name'];
    }

    /**
     * Get the acs fleet members
     * 
     * @return string
     */
    public function getAcsFleetMembers()
    {
        return $this->_data['acs_fleet_members'];
    }

    /**
     * Get the acs fleet fleets
     * 
     * @return string
     */
    public function getAcsFleetFleets()
    {
        return $this->_data['acs_fleet_fleets'];
    }

    /**
     * Get the acs fleet galaxy
     * 
     * @return string
     */
    public function getAcsFleetGalaxy()
    {
        return $this->_data['acs_fleet_galaxy'];
    }

    /**
     * Get the acs fleet system
     * 
     * @return string
     */
    public function getAcsFleetSystem()
    {
        return $this->_data['acs_fleet_system'];
    }

    /**
     * Get the acs fleet planet
     * 
     * @return string
     */
    public function getAcsFleetPlanet()
    {
        return $this->_data['acs_fleet_planet'];
    }

    /**
     * Get the acs fleet planet type
     * 
     * @return string
     */
    public function getAcsFleetPlanetType()
    {
        return $this->_data['acs_fleet_planet_type'];
    }

    /**
     * Get the acs fleet invited
     * 
     * @return string
     */
    public function getAcsFleetInvited()
    {
        return $this->_data['acs_fleet_invited'];
    }
}
/* end of AcsFleetEntity.php */
