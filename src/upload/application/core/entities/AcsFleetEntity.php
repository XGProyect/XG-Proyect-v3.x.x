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

use Exception;

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
class AcsFleetEntity
{

    /**
     *
     * @var array
     */
    private $_acs = [];

    /**
     * Init with the acs data
     * 
     * @param array $acs Acs
     * 
     * @return void
     */
    public function __construct($acs)
    {
        $this->setAcsFleet($acs);
    }

    /**
     * Set the current acs
     * 
     * @param array $acs Acs
     * 
     * @throws Exception
     * 
     * @return void
     */
    private function setAcsFleet($acs)
    {
        try {

            if (!is_array($acs)) {
                
                return null;
            }
            
            $this->_acs = $acs;
        } catch (Exception $e) {

            die('Caught exception: ' . $e->getMessage() . "\n");
        }
    }
    
    /**
     * Get the acs fleet id
     * 
     * @return string
     */
    public function getAcsFleetId()
    {
        return $this->_acs['acs_fleet_id'];
    }

    /**
     * Get the acs fleet name
     * 
     * @return string
     */
    public function getAcsFleetName()
    {
        return $this->_acs['acs_fleet_name'];
    }

    /**
     * Get the acs fleet members
     * 
     * @return string
     */
    public function getAcsFleetMembers()
    {
        return $this->_acs['acs_fleet_members'];
    }

    /**
     * Get the acs fleet fleets
     * 
     * @return string
     */
    public function getAcsFleetFleets()
    {
        return $this->_acs['acs_fleet_fleets'];
    }

    /**
     * Get the acs fleet galaxy
     * 
     * @return string
     */
    public function getAcsFleetGalaxy()
    {
        return $this->_acs['acs_fleet_galaxy'];
    }

    /**
     * Get the acs fleet system
     * 
     * @return string
     */
    public function getAcsFleetSystem()
    {
        return $this->_acs['acs_fleet_system'];
    }

    /**
     * Get the acs fleet planet
     * 
     * @return string
     */
    public function getAcsFleetPlanet()
    {
        return $this->_acs['acs_fleet_planet'];
    }

    /**
     * Get the acs fleet planet type
     * 
     * @return string
     */
    public function getAcsFleetPlanetType()
    {
        return $this->_acs['acs_fleet_planet_type'];
    }

    /**
     * Get the acs fleet invited
     * 
     * @return string
     */
    public function getAcsFleetInvited()
    {
        return $this->_acs['acs_fleet_invited'];
    }
}
/* end of AcsFleetEntity.php */
