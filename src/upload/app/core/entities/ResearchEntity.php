<?php
/**
 * Research entity
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
 * ResearchEntity Class
 *
 * @category Entity
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class ResearchEntity extends Entity
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
     * Return the research id
     *
     * @return string
     */
    public function getResearchId()
    {
        return $this->data['research_id'];
    }

    /**
     * Return the research user id
     *
     * @return string
     */
    public function getResearchUserId()
    {
        return $this->data['research_user_id'];
    }

    /**
     * Return the research current research
     *
     * @return string
     */
    public function getResearchCurrentResearch()
    {
        return $this->data['research_current_research'];
    }

    /**
     * Return the research espionage technology
     *
     * @return string
     */
    public function getResearchEspionageTechnology()
    {
        return $this->data['research_espionage_technology'];
    }

    /**
     * Return the research computer technology
     *
     * @return string
     */
    public function getResearchComputerTechnology()
    {
        return $this->data['research_computer_technology'];
    }

    /**
     * Return the research weapons technology
     *
     * @return string
     */
    public function getResearchWeaponsTechnology()
    {
        return $this->data['research_weapons_technology'];
    }

    /**
     * Return the research id
     *
     * @return string
     */
    public function getResearchShieldingTechnology()
    {
        return $this->data['research_shielding_technology'];
    }

    /**
     * Return the research armour technology
     *
     * @return string
     */
    public function getResearchArmourTechnology()
    {
        return $this->data['research_armour_technology'];
    }

    /**
     * Return the research energy technology
     *
     * @return string
     */
    public function getResearchEnergyTechnology()
    {
        return $this->data['research_energy_technology'];
    }

    /**
     * Return the research hyperspace technology
     *
     * @return string
     */
    public function getResearchHyperspaceTechnology()
    {
        return $this->data['research_hyperspace_technology'];
    }

    /**
     * Return the research combustion drive
     *
     * @return string
     */
    public function getResearchCombustionDrive()
    {
        return $this->data['research_combustion_drive'];
    }

    /**
     * Return the research impulse drive
     *
     * @return string
     */
    public function getResearchImpulseDrive()
    {
        return $this->data['research_impulse_drive'];
    }

    /**
     * Return the research hyperspace drive
     *
     * @return string
     */
    public function getResearchHyperspaceDrive()
    {
        return $this->data['research_hyperspace_drive'];
    }

    /**
     * Return the research laser technology
     *
     * @return string
     */
    public function getResearchLaserTechnology()
    {
        return $this->data['research_laser_technology'];
    }

    /**
     * Return the research ionic technology
     *
     * @return string
     */
    public function getResearchIonicTechnology()
    {
        return $this->data['research_ionic_technology'];
    }

    /**
     * Return the research plasma technology
     *
     * @return string
     */
    public function getResearchPlasmaTechnology()
    {
        return $this->data['research_plasma_technology'];
    }

    /**
     * Return the research intergalactic research network
     *
     * @return string
     */
    public function getResearchIntergalacticResearchNetwork()
    {
        return $this->data['research_intergalactic_research_network'];
    }

    /**
     * Return the research astrophysics
     *
     * @return string
     */
    public function getResearchAstrophysics()
    {
        return $this->data['research_astrophysics'];
    }

    /**
     * Return the research graviton technology
     *
     * @return string
     */
    public function getResearchGravitonTechnology()
    {
        return $this->data['research_graviton_technology'];
    }
}
