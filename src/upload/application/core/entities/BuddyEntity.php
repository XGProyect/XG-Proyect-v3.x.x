<?php
/**
 * Buddy entity
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

/**
 * Buddy Class
 *
 * @category Entity
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class BuddyEntity
{

    /**
     *
     * @var array
     */
    private $_buddy = [];

    /**
     * Init with the buddy data
     * 
     * @param array $buddy Buddy
     */
    public function __construct($buddy)
    {
        $this->setBuddy($buddy);
    }

    /**
     * Set the current planet
     * 
     * @param array $buddy Buddy
     * @throws Exception
     */
    private function setBuddy($buddy)
    {
        try {

            if (!is_array($buddy)) {
                throw new Exception('Must be an array');
            }

            $this->_buddy = $buddy;
        } catch (Exception $e) {

            die('Caught exception: ' . $e->getMessage() . "\n");
        }
    }

    /**
     *  Return the buddy id
     */
    public function getBuddyId()
    {
        return $this->_buddy['buddy_id'];
    }

    /**
     *  Return the buddy sender
     */
    public function getBuddySender()
    {
        return $this->_buddy['buddy_sender'];
    }

    /**
     *  Return the buddy receiver
     */
    public function getBuddyReceiver()
    {
        return $this->_buddy['buddy_receiver'];
    }

    /**
     *  Return the buddy status
     */
    public function getBuddyStatus()
    {
        return $this->_buddy['buddy_status'];
    }

    /**
     *  Return the buddy request text
     */
    public function getRequestText()
    {
        return $this->_buddy['buddy_request_text'];
    }
}

/* end of BuddyEntity.php */
