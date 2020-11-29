<?php
/**
 * Acs Fleet entity
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\core\entities;

use App\core\Entity;

/**
 * AcsFleetEntity Class
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
     * Get the acs id
     *
     * @return string
     */
    public function getAcsFleetId()
    {
        return $this->data['acs_id'];
    }

    /**
     * Get the acs name
     *
     * @return string
     */
    public function getAcsFleetName()
    {
        return $this->data['acs_name'];
    }

    /**
     * Get the acs owner
     *
     * @return string
     */
    public function getAcsFleetOwner()
    {
        return $this->data['acs_owner'];
    }

    /**
     * Get the acs galaxy
     *
     * @return string
     */
    public function getAcsFleetGalaxy()
    {
        return $this->data['acs_galaxy'];
    }

    /**
     * Get the acs system
     *
     * @return string
     */
    public function getAcsFleetSystem()
    {
        return $this->data['acs_system'];
    }

    /**
     * Get the acs planet
     *
     * @return string
     */
    public function getAcsFleetPlanet()
    {
        return $this->data['acs_planet'];
    }

    /**
     * Get the acs planet type
     *
     * @return string
     */
    public function getAcsFleetPlanetType()
    {
        return $this->data['acs_planet_type'];
    }
}
