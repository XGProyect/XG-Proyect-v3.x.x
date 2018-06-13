<?php
/**
 * Ranks
 *
 * PHP Version 5.5+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\libraries\alliance;

use application\libraries\enumerators\AllianceRanksEnumerator;
use application\libraries\enumerators\SwitchIntEnumerator;

/**
 * Ranks Class
 *
 * @category Classes
 * @package  alliance
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Ranks
{

    /**
     * Contains the ranks 
     * 
     * @var array
     */
    private $_ranks = [];

    /**
     * Constructor
     * 
     * @param type $alliance_ranks List of ranks as an array
     * 
     * @return void
     */
    public function __construct($alliance_ranks = [])
    {
        if (is_array($alliance_ranks) && !empty($alliance_ranks)) {

            $this->_ranks = $alliance_ranks;
        } else {

            $this->_ranks[] = [
                AllianceRanksEnumerator::name => '',
                AllianceRanksEnumerator::send_circular => SwitchIntEnumerator::off,
                AllianceRanksEnumerator::delete => SwitchIntEnumerator::off,
                AllianceRanksEnumerator::kick => SwitchIntEnumerator::off,
                AllianceRanksEnumerator::applications => SwitchIntEnumerator::off,
                AllianceRanksEnumerator::administration => SwitchIntEnumerator::off,
                AllianceRanksEnumerator::application_management => SwitchIntEnumerator::off,
                AllianceRanksEnumerator::view_member_list => SwitchIntEnumerator::off,
                AllianceRanksEnumerator::online_status => SwitchIntEnumerator::off,
                AllianceRanksEnumerator::right_hand => SwitchIntEnumerator::off,
            ];
        }
    }

    /**
     * 
     * @param RanksTypes $rank
     * @param SwitchTypes $value
     * 
     * @return void
     */
    public function addNew($ranks)
    {
        
    }

    /**
     * 
     * @param type $rank_id
     * @param type $permission
     * @param type $value
     */
    public function editRankById($rank_id, $ranks)
    {
        
    }

    /**
     * 
     * @param RanksTypes $rank
     * @param SwitchTypes $value
     * 
     * @return void
     */
    public function deleteRankById($rank_id)
    {
        array_splice($this->_ranks, $this->validateRankId($rank_id), 1);
    }

    /**
     * Get all the ranks permissions
     * 
     * @return array
     */
    public function getAllRanks()
    {
        return $this->_ranks;
    }
    
    /**
     * Get the permission for a certain rank
     * 
     * @param int $rank_id Rank ID
     * 
     * @return array
     */
    public function getRankById($rank_id)
    {
        return $this->_ranks[$this->validateRankId($rank_id)];
    }
    
    /**
     * Validate the rank ID
     * 
     * @param type $rank_id Rank ID
     * 
     * @return int
     */
    private function validateRankId($rank_id)
    {
        if ($rank_id <= 0) {
            
            return 0;
        }

        if ($rank_id > count($this->_ranks)) {
            
            return count($this->_ranks) - 1;
        }
        
        return $rank_id - 1;
    }
}

/* end of Ranks.php */
