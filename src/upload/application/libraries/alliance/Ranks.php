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
namespace application\libraries\buildings;

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
     * @var array rank => flag
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

            $this->_ranks = [
                AllianceRanksEnumerator::name => SwitchIntEnumerator::off,
                AllianceRanksEnumerator::mail => SwitchIntEnumerator::off,
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
    public function addNew(AllianceRanksEnumerator $rank, SwitchIntEnumerator $value)
    {
        
    }

    /**
     * 
     * @param RanksTypes $rank
     * @param SwitchTypes $value
     * 
     * @return void
     */
    public function editRank(AllianceRanksEnumerator $rank, SwitchIntEnumerator $value)
    {
        
    }

    /**
     * 
     * @param RanksTypes $rank
     * @param SwitchTypes $value
     * 
     * @return void
     */
    public function deleteRank(AllianceRanksEnumerator $rank, SwitchIntEnumerator $value)
    {
        
    }

    /**
     * Get all the ranks
     * 
     * @return array
     */
    public function getRanks()
    {
        return $this->_ranks;
    }

    /**
     * Get the ranks by rank name
     * 
     * @param RanksTypes $rank Rank
     * 
     * @return int
     */
    public function getRankStatusByName(AllianceRanksEnumerator $rank)
    {
        if (in_array($rank, $this->_ranks)) {

            return $this->_ranks[$rank];
        }

        return 0;
    }
}

/* end of Buildings.php */
