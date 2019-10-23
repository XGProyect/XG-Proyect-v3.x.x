<?php
/**
 * Planet entity
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
 * Planet Class
 *
 * @category Entity
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class PlanetEntity extends Entity
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
     *  Return the planet id
     */
    public function getPlanetId()
    {
        return $this->_data['planet_id'];
    }

    /**
     *  Return the planet name
     */
    public function getPlanetName()
    {
        return $this->_data['planet_name'];
    }

    /**
     *  Return the planet user id
     */
    public function getPlanetUserId()
    {
        return $this->_data['planet_user_id'];
    }

    /**
     *  Return the planet galaxy
     */
    public function getPlanetGalaxy()
    {
        return $this->_data['planet_galaxy'];
    }

    /**
     *  Return the planet system
     */
    public function getPlanetSystem()
    {
        return $this->_data['planet_system'];
    }

    /**
     *  Return the planet position
     */
    public function getPlanetPosition()
    {
        return $this->_data['planet_data'];
    }

    /**
     *  Return the planet latest activity
     */
    public function getPlanetLastUpdate()
    {
        return $this->_data['planet_last_update'];
    }

    /**
     *  Return the planet type
     */
    public function getPlanetType()
    {
        return $this->_data['planet_type'];
    }

    /**
     * Return the planet status
     */
    public function getPlanetStatus()
    {
        return $this->_data['planet_destroyed'];
    }

    /**
     * Return the planet building Queue
     */
    public function getPlanetBuildingQueue()
    {
        return $this->_data['planet_b_building'];
    }

    /**
     * Return the planet current building ID
     */
    public function getPlanetCurrentBuildingId()
    {
        return $this->_data['planet_b_building_id'];
    }

    /**
     * Return the planet technology queue
     */
    public function getPlanetTechnologyQueue()
    {
        return $this->_data['planet_b_tech'];
    }

    /**
     * Return the planet current technology ID
     */
    public function getPlanetCurrentTechnologyId()
    {
        return $this->_data['planet_b_tech_id'];
    }

    /**
     * Return the planet hangar queue
     */
    public function getPlanetHangarQueue()
    {
        return $this->_data['planet_b_hangar'];
    }

    /**
     * Return the planet current hangar ID
     */
    public function getPlanetCurrentHangarId()
    {
        return $this->_data['planet_b_hangar_id'];
    }

    /**
     * Return the planet image
     */
    public function getPlanetImage()
    {
        return $this->_data['planet_image'];
    }

    /**
     * Return the planet diameter
     */
    public function getPlanetDiameter()
    {
        return $this->_data['planet_diameter'];
    }

    /**
     * Return the planet busy fields
     */
    public function getPlanetAmountOfOcuppiedFields()
    {
        return $this->_data['planet_field_current'];
    }

    /**
     * Return the planet maximum amount of fields
     */
    public function getPlanetMaxAmountOfFields()
    {
        return $this->_data['planet_field_max'];
    }

    /**
     * Return the planet minimum temperature
     */
    public function getPlanetTempMin()
    {
        return $this->_data['planet_temp_min'];
    }

    /**
     * Return the planet maximum temperature
     */
    public function getPlanetTempMax()
    {
        return $this->_data['planet_temp_max'];
    }

    /**
     * Return the planet current amount of metal
     */
    public function getPlanetAmountOfMetal()
    {
        return $this->_data['planet_metal'];
    }

    /**
     * Return the planet current metal production per hour
     */
    public function getPlanetProductionPerHourMetal()
    {
        return $this->_data['planet_metal_perhour'];
    }

    /**
     * Return the planet metal capacity
     */
    public function getPlanetStorageCapacityMetal()
    {
        return $this->_data['planet_metal_max'];
    }

    /**
     * Return the planet current amount of crystal
     */
    public function getPlanetAmountOfCrystal()
    {
        return $this->_data['planet_crystal'];
    }

    /**
     * Return the planet current crystal production per hour
     */
    public function getPlanetProductionPerHourCrystal()
    {
        return $this->_data['planet_crystal_perhour'];
    }

    /**
     * Return the planet crystal capacity
     */
    public function getPlanetStorageCapacityCrystal()
    {
        return $this->_data['planet_crystal_max'];
    }

    /**
     * Return the planet current amount of deuterium
     */
    public function getPlanetAmountOfDeuterium()
    {
        return $this->_data['planet_deuterium'];
    }

    /**
     * Return the planet current deuterium production per hour
     */
    public function getPlanetProductionPerHourDeuterium()
    {
        return $this->_data['planet_deuterium_perhour'];
    }

    /**
     * Return the planet deuterium capacity
     */
    public function getPlanetStorageCapacityDeuterium()
    {
        return $this->_data['planet_deuterium_max'];
    }

    /**
     * Return the planet energy used
     */
    public function getPlanetUsedEnergy()
    {
        return $this->_data['planet_energy_used'];
    }

    /**
     * Return the planet max energy
     */
    public function getPlanetMaxEnergy()
    {
        return $this->_data['planet_energy_max'];
    }

    /**
     * Return the planet production percentage for the metal mine
     */
    public function getPlanetProductionPercentageMetal()
    {
        return $this->_data['planet_building_metal_mine_percent'];
    }

    /**
     * Return the planet production percentage for crystal mine
     */
    public function getPlanetProductionPercentageCrystal()
    {
        return $this->_data['planet_building_crystal_mine_percent'];
    }

    /**
     * Return the planet production percentage for deuterium sintetizer
     */
    public function getPlanetProductionPercentageDeuterium()
    {
        return $this->_data['planet_building_deuterium_sintetizer_percent'];
    }

    /**
     * Return the planet production percentage for the solar plant
     */
    public function getPlanetProductionPercentageSolarPlant()
    {
        return $this->_data['planet_building_solar_plant_percent'];
    }

    /**
     * Return the planet production percentage for the fusion reactor
     */
    public function getPlanetProductionPercentageFusion()
    {
        return $this->_data['planet_building_fusion_reactor_percent'];
    }

    /**
     * Return the planet production percentage for the solar satellite
     */
    public function getPlanetProductionPercentageSolarSatellite()
    {
        return $this->_data['planet_ship_solar_satellite_percent'];
    }

    /**
     * Return the planet latest jump time
     */
    public function getPlanetLatestJumpTime()
    {
        return $this->_data['planet_last_jump_time'];
    }

    /**
     * Return the planet amount of metal debris
     */
    public function getPlanetDebrisMetal()
    {
        return $this->_data['planet_debris_metal'];
    }

    /**
     * Return the planet amount of crystal debris
     */
    public function getPlanetDebrisCrystal()
    {
        return $this->_data['planet_debris_crystal'];
    }

    /**
     * Return the planet invisible start time
     */
    public function getPlanetInvisibleStartTime()
    {
        return $this->_data['planet_invisible_start_time'];
    }
}

/* end of PlanetEntity.php */
