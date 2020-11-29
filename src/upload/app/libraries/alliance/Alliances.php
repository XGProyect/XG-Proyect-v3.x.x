<?php
/**
 * Alliance
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\libraries\alliance;

use App\core\entities\AllianceEntity;
use App\core\enumerators\SwitchIntEnumerator;
use App\libraries\alliance\Ranks;

/**
 * Alliance Class
 *
 * @category Classes
 * @package  alliance
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Alliances
{
    /**
     *
     * @var array
     */
    private $_alliances = [];

    /**
     *
     * @var int
     */
    private $_current_user_id = 0;

    /**
     *
     * @var int
     */
    private $_current_user_rank_id = 0;

    /**
     * Constructor
     *
     * @param array $alliances            Alliances
     * @param int   $current_user_id      Current User ID
     * @param int   $current_user_rank_id Current User Rank Id
     *
     * @return void
     */
    public function __construct($alliances, $current_user_id, $current_user_rank_id = 0)
    {
        if (is_array($alliances)) {
            $this->setUp($alliances);
            $this->setUserId($current_user_id);
            $this->setUserRankId($current_user_rank_id);
        }
    }

    /**
     * Get all the alliances
     *
     * @return array
     */
    public function getAlliances()
    {
        $list_of_alliances = [];

        foreach ($this->_alliances as $alliance) {
            if (($alliance instanceof AllianceEntity)) {
                $list_of_alliances[] = $alliance;
            }
        }

        return $list_of_alliances;
    }

    /**
     * Return current alliance data
     *
     * @return array
     */
    public function getCurrentAlliance()
    {
        return $this->_alliances[0];
    }

    /**
     * Get current alliance rank
     *
     * @return Ranks
     */
    public function getCurrentAllianceRankObject()
    {
        return new Ranks($this->getCurrentAlliance()->getAllianceRanks());
    }

    /**
     * Check if is the alliance owner
     *
     * @return string
     */
    public function isOwner()
    {
        return ($this->getCurrentAlliance()->getAllianceOwner() === $this->getUserId());
    }

    /**
     * Check the rank for the current user
     *
     * @return boolean
     */
    public function checkRank($rank)
    {
        $ranks = $this->getCurrentAllianceRankObject();

        return ($rank != null
            && $ranks->getAllRanksAsArray() != null
            && $ranks->getRankById($this->getUserRankId())['rights'][$rank] == SwitchIntEnumerator::on);
    }

    /**
     * Check if the user has access to certain section of the alliance
     *
     * @param int $rank Rank
     *
     * @return boolean
     */
    public function hasAccess($rank)
    {
        return ($this->isOwner() or $this->checkRank($rank));
    }

    /**
     * Set up the list of alliances
     *
     * @param array $alliances Alliances
     *
     * @return void
     */
    private function setUp($alliances)
    {
        foreach ($alliances as $alliance) {
            $this->_alliances[] = $this->createNewAllianceEntity($alliance);
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
     *
     * @param int $user_rank_id User Rank Id
     */
    private function setUserRankId($user_rank_id)
    {
        $this->_current_user_rank_id = $user_rank_id;
    }

    /**
     *
     * @return int
     */
    private function getUserRankId()
    {
        return $this->_current_user_rank_id;
    }

    /**
     * Create a new instance of AllianceEntity
     *
     * @param array $alliance Alliance
     *
     * @return \AllianceEntity
     */
    private function createNewAllianceEntity($alliance)
    {
        return new AllianceEntity($alliance);
    }
}
