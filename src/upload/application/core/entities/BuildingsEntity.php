<?php
/**
 * Buildings entity
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
 * BuildingsEntity Class
 *
 * @category Entity
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class BuildingsEntity extends Entity
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
     * Return the building id
     *
     * @return integer
     */
    public function getBuildingId(): int
    {
        return (int) $this->data['building_id'];
    }

    /**
     * Return the building planet id
     *
     * @return integer
     */
    public function getBuildingPlanetId(): int
    {
        return (int) $this->data['building_planet_id'];
    }

    /**
     * Return the building metal mine
     *
     * @return integer
     */
    public function getBuildingMetalMine(): int
    {
        return (int) $this->data['building_metal_mine'];
    }

    /**
     * Return the building crystal mine
     *
     * @return int
     */
    public function getBuildingCrystalMine(): int
    {
        return (int) $this->data['building_crystal_mine'];
    }

    /**
     * Return the building deuterium sintetizer
     *
     * @return integer
     */
    public function getBuildingDeuteriumSintetizer(): int
    {
        return (int) $this->data['building_deuterium_sintetizer'];
    }

    /**
     * Return the building solar plant
     *
     * @return integer
     */
    public function getBuildingSolarPlant(): int
    {
        return (int) $this->data['building_solar_plant'];
    }

    /**
     * Return the building fusion reactor
     *
     * @return integer
     */
    public function getBuildingFusionReactor(): int
    {
        return (int) $this->data['building_fusion_reactor'];
    }

    /**
     * Return the building robot factory
     *
     * @return integer
     */
    public function getBuildingRobotFactory(): int
    {
        return (int) $this->data['building_robot_factory'];
    }

    /**
     * Return the building nano factory
     *
     * @return integer
     */
    public function getBuildingNanoFactory(): int
    {
        return (int) $this->data['building_nano_factory'];
    }

    /**
     * Return the building hangar
     *
     * @return integer
     */
    public function getBuildingHangar(): int
    {
        return (int) $this->data['building hangar'];
    }

    /**
     * Return the building metal store
     *
     * @return integer
     */
    public function getBuildingMetalStore(): int
    {
        return (int) $this->data['building_metal_store'];
    }

    /**
     * Return the building crystal store
     *
     * @return integer
     */
    public function getBuildingCrystalStore(): int
    {
        return (int) $this->data['building_crystal_store'];
    }

    /**
     * Return the building deuterium tank
     *
     * @return int
     */
    public function getBuildingDeuteriumTank(): int
    {
        return (int) $this->data['building_deuterium_tank'];
    }

    /**
     * Return the building laboratory
     *
     * @return integer
     */
    public function getBuildingLaboratory(): int
    {
        return (int) $this->data['building_laboratory'];
    }

    /**
     * Return the building terraformer
     *
     * @return integer
     */
    public function getBuildingTerraformer(): int
    {
        return (int) $this->data['building_terraformer'];
    }

    /**
     * Return the building ally deposit
     *
     * @return integer
     */
    public function getBuildingAllyDeposit(): int
    {
        return (int) $this->data['building_ally_deposit'];
    }

    /**
     * Return the building missile silo
     *
     * @return int
     */
    public function getBuildingMissileSilo(): int
    {
        return (int) $this->data['building_missile_silo'];
    }

    /**
     * Return the building mondbasis
     *
     * @return int
     */
    public function getBuildingMondbasis(): int
    {
        return (int) $this->data['building_mondbasis'];
    }

    /**
     * Return the building phalanx
     *
     * @return integer
     */
    public function getBuildingPhalanx(): int
    {
        return (int) $this->data['building_phalanx'];
    }

    /**
     * Return the building jump gate
     *
     * @return integer
     */
    public function getBuildingJumpGate(): int
    {
        return (int) $this->data['building_jump_gate'];
    }
}

/* end of BuildingsEntity.php */
