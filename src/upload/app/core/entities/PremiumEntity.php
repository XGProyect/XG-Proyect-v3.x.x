<?php
/**
 * Premium entity
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
 * PremiumEntity Class
 *
 * @category Entity
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class PremiumEntity extends Entity
{
    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct($data)
    {
        parent::__construct($data);
    }

    /**
     * Return the premium user id
     *
     * @return string
     */
    public function getPremiumUserId()
    {
        return $this->data['premium_user_id'];
    }

    /**
     * Return the premium dark matter
     *
     * @return string
     */
    public function getPremiumDarkMatter()
    {
        return $this->data['premium_dark_matter'];
    }

    /**
     * Return the premium officier commander
     *
     * @return string
     */
    public function getPremiumOfficierCommander()
    {
        return $this->data['premium_officier_commander'];
    }

    /**
     * Return the premium officier admiral
     *
     * @return string
     */
    public function getPremiumOfficierAdmiral()
    {
        return $this->data['premium_officier_admiral'];
    }

    /**
     * Return the premium officier engineer
     *
     * @return string
     */
    public function getPremiumOfficierEngineer()
    {
        return $this->data['premium_officier_engineer'];
    }

    /**
     * Return the premium officier geologist
     *
     * @return string
     */
    public function getPremiumOfficierGeologist()
    {
        return $this->data['premium_officier_geologist'];
    }

    /**
     * Return the premium officier technocrat
     *
     * @return string
     */
    public function getPremiumOfficierTechnocrat()
    {
        return $this->data['premium_officier_technocrat'];
    }
}
