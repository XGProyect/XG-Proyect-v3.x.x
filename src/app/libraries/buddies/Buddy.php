<?php
/**
 * Buddy
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */

namespace App\libraries\buddies;

use App\core\entities\BuddyEntity;
use App\core\enumerators\BuddiesStatusEnumerator as BuddiesStatus;

/**
 * Buddy Class
 *
 * @category Classes
 * @package  buddy
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Buddy
{
    /**
     *
     * @var array
     */
    private $_buddies = [];

    /**
     *
     * @var int
     */
    private $_current_user_id = 0;

    /**
     * Constructor
     *
     * @param array $buddies         Buddies
     * @param int   $current_user_id Current User ID
     *
     * @return void
     */
    public function __construct($buddies, $current_user_id)
    {
        if (is_array($buddies)) {
            $this->setUp($buddies);
            $this->setUserId($current_user_id);
        }
    }

    /**
     * Get all the players that received a request from this user
     *
     * @return array
     */
    public function getSentRequests()
    {
        $list_of_buddies = [];

        foreach ($this->_buddies as $buddy) {
            if (($buddy instanceof BuddyEntity)
                && !$this->isBuddy($buddy)
                && $this->isOwnRequest($buddy)) {
                $list_of_buddies[] = $buddy;
            }
        }

        return $list_of_buddies;
    }

    /**
     * Get all the players that sent a request to this user
     *
     * @return array
     */
    public function getReceivedRequests()
    {
        $list_of_buddies = [];

        foreach ($this->_buddies as $buddy) {
            if (($buddy instanceof BuddyEntity)
                && !$this->isBuddy($buddy)
                && !$this->isOwnRequest($buddy)) {
                $list_of_buddies[] = $buddy;
            }
        }

        return $list_of_buddies;
    }

    /**
     * Get all the players that are the current user's buddies
     *
     * @return array
     */
    public function getBuddies()
    {
        $list_of_buddies = [];

        foreach ($this->_buddies as $buddy) {
            if (($buddy instanceof BuddyEntity) && $this->isBuddy($buddy)) {
                $list_of_buddies[] = $buddy;
            }
        }

        return $list_of_buddies;
    }

    /**
     * Check if is already a buddy
     *
     * @param BuddyEntity $buddy Buddy
     *
     * @return boolean
     */
    private function isBuddy(BuddyEntity $buddy)
    {
        return ($buddy->getBuddyStatus() == BuddiesStatus::isBuddy);
    }

    /**
     * Check if is the request owner
     *
     * @param BuddyEntity $buddy Buddy
     *
     * @return boolean
     */
    private function isOwnRequest(BuddyEntity $buddy)
    {
        return ($buddy->getBuddySender() == $this->getUserId());
    }

    /**
     * Set up the list of buddies
     *
     * @param array $buddies Buddies
     *
     * @return void
     */
    private function setUp($buddies)
    {
        foreach ($buddies as $buddy) {
            $this->_buddies[] = $this->createNewBuddyEntity($buddy);
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
     * Create a new instance of BuddyEntity
     *
     * @param array $buddy Buddy
     *
     * @return \BuddyEntity
     */
    private function createNewBuddyEntity($buddy)
    {
        return new BuddyEntity($buddy);
    }
}
