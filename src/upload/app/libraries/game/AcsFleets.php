<?php
/**
 * AcsFleets
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\libraries\game;

use App\core\entities\AcsFleetEntity;

/**
 * AcsFleets Class
 *
 * @category Classes
 * @package  fleets
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class AcsFleets
{
    /**
     *
     * @var array
     */
    private $_acs = [];

    /**
     *
     * @var int
     */
    private $_current_user_id = 0;

    /**
     * Constructor
     *
     * @param array $acs             Acs
     * @param int   $current_user_id Current User ID
     *
     * @return void
     */
    public function __construct($acs, $current_user_id)
    {
        if (is_array($acs)) {
            $this->setUp($acs);
            $this->setUserId($current_user_id);
        }
    }

    /**
     * Get all the acs
     *
     * @return array
     */
    public function getAcs()
    {
        $list_of_acs = [];

        foreach ($this->_acs as $acs) {
            if (($acs instanceof AcsFleetEntity)) {
                $list_of_acs[] = $acs;
            }
        }

        return $list_of_acs;
    }

    /**
     * Get the first acs result
     *
     * @return array
     */
    public function getFirstAcs()
    {
        return $this->getAcs()[0];
    }

    /**
     * Set up the list of acs
     *
     * @param array $acsFleets Acs Fleets
     *
     * @return void
     */
    private function setUp($acsFleets)
    {
        foreach ($acsFleets as $acs) {
            $data = $this->createNewAcsFleetEntity($acs);

            $this->_acs[] = $data;
        }
    }

    /**
     *
     * @param int $user_id User Id
     */
    private function setUserId($user_id)
    {
        $this->_current_user_id = $user_id;
    }

    /**
     *
     * @return int
     */
    private function getUserId()
    {
        return $this->_current_user_id;
    }

    /**
     * Create a new instance of AcsFleetEntity
     *
     * @param array $fleet Fleet
     *
     * @return \AcsFleetEntity
     */
    private function createNewAcsFleetEntity($fleet)
    {
        return new AcsFleetEntity($fleet);
    }
}
