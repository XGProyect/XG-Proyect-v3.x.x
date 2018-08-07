<?php
/**
 * Premium entity
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
 * Premium Entity Class
 *
 * @category Entity
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class PremiumEntity
{

    /**
     *
     * @var array
     */
    private $_premium = [];

    /**
     * Init with the premium data
     * 
     * @param array $premium Premium
     * 
     * @return void
     */
    public function __construct($premium)
    {
        $this->setPremium($premium);
    }

    /**
     * Set the current premium
     * 
     * @param array $premium Premium
     * 
     * @throws Exception
     * 
     * @return void
     */
    private function setPremium($premium)
    {
        try {
            
            if (!is_array($premium)) {
                
                return null;
            }
            
            $this->_premium = $premium;
        } catch (Exception $e) {

            die('Caught exception: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Return the premium user id
     * 
     * @return string
     */
    public function getPremiumUserId()
    {
        return $this->_premium['premium_user_id'];
    }
    
    /**
     * Return the premium dark matter
     * 
     * @return string
     */
    public function getPremiumDarkMatter()
    {
        return $this->_premium['premium_dark_matter'];
    }
    
    /**
     * Return the premium officier commander
     * 
     * @return string
     */
    public function getPremiumOfficierCommander()
    {
        return $this->_premium['premium_officier_commander'];
    }
    
    /**
     * Return the premium officier admiral
     * 
     * @return string
     */
    public function getPremiumOfficierAdmiral()
    {
        return $this->_premium['premium_officier_admiral'];
    }
    
    /**
     * Return the premium officier engineer
     * 
     * @return string
     */
    public function getPremiumOfficierEngineer()
    {
        return $this->_premium['premium_officier_engineer'];
    }
    
    /**
     * Return the premium officier geologist
     * 
     * @return string
     */
    public function getPremiumOfficierGeologist()
    {
        return $this->_premium['premium_officier_geologist'];
    }
    
    /**
     * Return the premium officier technocrat
     * 
     * @return string
     */
    public function getPremiumOfficierTechnocrat()
    {
        return $this->_premium['premium_officier_technocrat'];
    }
}

/* end of PremiumEntity.php */
