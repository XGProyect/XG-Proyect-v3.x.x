<?php
/**
 * Planet entity
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
 * PlanetEntity Class
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
     * Return the planet id
     *
     * @return integer
     */
    public function getPlanetId(): int
    {
        return (int) $this->data['planet_id'];
    }

    /**
     * Return the planet name
     *
     * @return string
     */
    public function getPlanetName(): string
    {
        return (string) $this->data['planet_name'];
    }

    /**
     * Return the planet user id
     *
     * @return integer
     */
    public function getPlanetUserId(): int
    {
        return (int) $this->data['planet_user_id'];
    }

    /**
     * Return the planet galaxy
     *
     * @return integer
     */
    public function getPlanetGalaxy(): int
    {
        return (int) $this->data['planet_galaxy'];
    }

    /**
     * Return the planet system
     *
     * @return integer
     */
    public function getPlanetSystem(): int
    {
        return (int) $this->data['planet_system'];
    }

    /**
     * Return the planet position
     *
     * @return integer
     */
    public function getPlanetPosition(): int
    {
        return (int) $this->data['planet_planet'];
    }

    /**
     * Return the planet latest activity
     *
     * @return integer
     */
    public function getPlanetLastUpdate(): int
    {
        return (int) $this->data['planet_last_update'];
    }

    /**
     * Return the planet type
     *
     * @return integer
     */
    public function getPlanetType(): int
    {
        return (int) $this->data['planet_type'];
    }

    /**
     * Return the planet status
     *
     * @return integer
     */
    public function getPlanetDestroyed(): int
    {
        return (int) $this->data['planet_destroyed'];
    }

    /**
     * Return the planet building queue time
     *
     * @return integer
     */
    public function getPlanetBuildingTime(): int
    {
        return (int) $this->data['planet_b_building'];
    }

    /**
     * Return the planet current building queue
     *
     * @return string
     */
    public function getPlanetCurrentBuildingQueue(): string
    {
        return (string) $this->data['planet_b_building_id'];
    }

    /**
     * Return the planet technology queue
     *
     * @return integer
     */
    public function getPlanetTechnologyQueue(): int
    {
        return (int) $this->data['planet_b_tech'];
    }

    /**
     * Return the planet current technology ID
     *
     * @return integer
     */
    public function getPlanetCurrentTechnologyId(): int
    {
        return (int) $this->data['planet_b_tech_id'];
    }

    /**
     * Return the planet hangar queue
     *
     * @return integer
     */
    public function getPlanetHangarQueue(): int
    {
        return (int) $this->data['planet_b_hangar'];
    }

    /**
     * Return the planet current hangar ID
     *
     * @return string
     */
    public function getPlanetCurrentHangarId(): string
    {
        return (string) $this->data['planet_b_hangar_id'];
    }

    /**
     * Return the planet image
     *
     * @return string
     */
    public function getPlanetImage(): string
    {
        return (string) $this->data['planet_image'];
    }

    /**
     * Return the planet diameter
     *
     * @return integer
     */
    public function getPlanetDiameter(): int
    {
        return (int) $this->data['planet_diameter'];
    }

    /**
     * Return the planet busy fields
     *
     * @return integer
     */
    public function getPlanetAmountOfOcuppiedFields(): int
    {
        return (int) $this->data['planet_field_current'];
    }

    /**
     * Return the planet maximum amount of fields
     *
     * @return integer
     */
    public function getPlanetMaxAmountOfFields(): int
    {
        return (int) $this->data['planet_field_max'];
    }

    /**
     * Return the planet minimum temperature
     *
     * @return integer
     */
    public function getPlanetTempMin(): int
    {
        return (int) $this->data['planet_temp_min'];
    }

    /**
     * Return the planet maximum temperature
     *
     * @return integer
     */
    public function getPlanetTempMax(): int
    {
        return (int) $this->data['planet_temp_max'];
    }

    /**
     * Return the planet current amount of metal
     *
     * @return float
     */
    public function getPlanetAmountOfMetal(): float
    {
        return (float) $this->data['planet_metal'];
    }

    /**
     * Return the planet current metal production per hour
     *
     * @return integer
     */
    public function getPlanetProductionPerHourMetal(): int
    {
        return (int) $this->data['planet_metal_perhour'];
    }

    /**
     * Return the planet current amount of crystal
     *
     * @return float
     */
    public function getPlanetAmountOfCrystal(): float
    {
        return (float) $this->data['planet_crystal'];
    }

    /**
     * Return the planet current crystal production per hour
     *
     * @return integer
     */
    public function getPlanetProductionPerHourCrystal(): int
    {
        return (int) $this->data['planet_crystal_perhour'];
    }

    /**
     * Return the planet current amount of deuterium
     *
     * @return float
     */
    public function getPlanetAmountOfDeuterium(): float
    {
        return (float) $this->data['planet_deuterium'];
    }

    /**
     * Return the planet current deuterium production per hour
     *
     * @return integer
     */
    public function getPlanetProductionPerHourDeuterium(): int
    {
        return (int) $this->data['planet_deuterium_perhour'];
    }

    /**
     * Return the planet energy used
     *
     * @return integer
     */
    public function getPlanetUsedEnergy(): int
    {
        return (int) $this->data['planet_energy_used'];
    }

    /**
     * Return the planet max energy
     *
     * @return integer
     */
    public function getPlanetMaxEnergy(): int
    {
        return (int) $this->data['planet_energy_max'];
    }

    /**
     * Return the planet production percentage for the metal mine
     *
     * @return integer
     */
    public function getPlanetProductionPercentageMetal(): int
    {
        return (int) $this->data['planet_building_metal_mine_percent'];
    }

    /**
     * Return the planet production percentage for crystal mine
     *
     * @return integer
     */
    public function getPlanetProductionPercentageCrystal(): int
    {
        return (int) $this->data['planet_building_crystal_mine_percent'];
    }

    /**
     * Return the planet production percentage for deuterium sintetizer
     *
     * @return integer
     */
    public function getPlanetProductionPercentageDeuterium(): int
    {
        return (int) $this->data['planet_building_deuterium_sintetizer_percent'];
    }

    /**
     * Return the planet production percentage for the solar plant
     *
     * @return integer
     */
    public function getPlanetProductionPercentageSolarPlant(): int
    {
        return (int) $this->data['planet_building_solar_plant_percent'];
    }

    /**
     * Return the planet production percentage for the fusion reactor
     *
     * @return integer
     */
    public function getPlanetProductionPercentageFusion(): int
    {
        return (int) $this->data['planet_building_fusion_reactor_percent'];
    }

    /**
     * Return the planet production percentage for the solar satellite
     *
     * @return integer
     */
    public function getPlanetProductionPercentageSolarSatellite(): int
    {
        return (int) $this->data['planet_ship_solar_satellite_percent'];
    }

    /**
     * Return the planet latest jump time
     *
     * @return integer
     */
    public function getPlanetLatestJumpTime(): int
    {
        return (int) $this->data['planet_last_jump_time'];
    }

    /**
     * Return the planet amount of metal debris
     *
     * @return integer
     */
    public function getPlanetDebrisMetal(): int
    {
        return (int) $this->data['planet_debris_metal'];
    }

    /**
     * Return the planet amount of crystal debris
     *
     * @return integer
     */
    public function getPlanetDebrisCrystal(): int
    {
        return (int) $this->data['planet_debris_crystal'];
    }

    /**
     * Return the planet invisible start time
     *
     * @return integer
     */
    public function getPlanetInvisibleStartTime(): int
    {
        return (int) $this->data['planet_invisible_start_time'];
    }
}
