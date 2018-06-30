<?php
/**
 * Fleet entity
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
 * Fleet Entity Class
 *
 * @category Entity
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class FleetEntity
{

    /**
     *
     * @var array
     */
    private $_fleet = [];

    /**
     * Init with the buddy data
     * 
     * @param array $fleet Fleet
     * 
     * @return void
     */
    public function __construct($fleet)
    {
        $this->setFleet($fleet);
    }

    /**
     * Set the current planet
     * 
     * @param array $fleet Fleet
     * 
     * @throws Exception
     * 
     * @return void
     */
    private function setFleet($fleet)
    {
        try {

            if (!is_array($fleet)) {
                
                return  null;
            }
            
            $this->_fleet = $fleet;
        } catch (Exception $e) {

            die('Caught exception: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Return the buddy id
     * 
     * @return string
     */
    public function getBuddyId()
    {
        return $this->_buddy['buddy_id'];
    }

    /**
     * Return the buddy sender
     * 
     * @return string
     */
    public function getBuddySender()
    {
        return $this->_buddy['buddy_sender'];
    }

    /**
     * Return the buddy receiver
     * 
     * @return string
     */
    public function getBuddyReceiver()
    {
        return $this->_buddy['buddy_receiver'];
    }

    /**
     * Return the buddy status
     * 
     * @return string
     */
    public function getBuddyStatus()
    {
        return $this->_buddy['buddy_status'];
    }

    /**
     * Return the buddy request text
     * 
     * @return string
     */
    public function getRequestText()
    {
        return $this->_buddy['buddy_request_text'];
    }
}

/* end of FleetEntity.php */
