<?php
/**
 * Ranks
 *
 * PHP Version 7.1+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\libraries\alliance;

use application\core\enumerators\AllianceRanksEnumerator as AllianceRanks;
use application\core\enumerators\SwitchIntEnumerator as SwitchInt;
use application\libraries\FunctionsLib;
use Exception;

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
     * @param string $alliance_ranks List of ranks as a JSON string
     *
     * @return void
     *
     * @throws Exception
     */
    public function __construct($alliance_ranks)
    {
        try {
            if (is_array($alliance_ranks)) {

                throw new Exception('JSON Expected!');
            }

            $this->setRanks($alliance_ranks);
        } catch (Exception $e) {

            die('Caught exception: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Set the ranks
     *
     * @param string $ranks Ranks
     */
    private function setRanks($ranks)
    {
        $this->_ranks = json_decode($ranks, true);
    }

    /**
     * Get the ranks
     *
     * @return string
     */
    private function getRanks()
    {
        return $this->_ranks;
    }

    /**
     * Create a new rank
     *
     * @param string $name Rank Name
     *
     * @return array
     */
    public function addNew($name)
    {
        try {
            if (empty($name) or is_null($name)) {

                throw new Exception('Name cannot be empty or null');
            }

            $filtered_name = FunctionsLib::escapeString(strip_tags($name));

            $this->_ranks[] = [
                'rank' => $filtered_name,
                'rights' => [
                    AllianceRanks::delete => SwitchInt::off,
                    AllianceRanks::kick => SwitchInt::off,
                    AllianceRanks::applications => SwitchInt::off,
                    AllianceRanks::view_member_list => SwitchInt::off,
                    AllianceRanks::application_management => SwitchInt::off,
                    AllianceRanks::administration => SwitchInt::off,
                    AllianceRanks::online_status => SwitchInt::off,
                    AllianceRanks::send_circular => SwitchInt::off,
                    AllianceRanks::right_hand => SwitchInt::off,
                ],
            ];

            return $this->getRanks();
        } catch (Exception $e) {

            die('Caught exception: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Edit ranks by ID
     *
     * @param int   $rank_id
     * @param array $rights
     *
     * @throws Exception
     *
     * @return array
     */
    public function editRankById($rank_id, $rights)
    {
        try {
            if (!isset($this->getRanks()[$this->validateRankId($rank_id)])) {

                throw new Exception('Rank ID doesn\'t exists');
            }

            if (!is_array($rights) or count($rights) != 9) {

                throw new Exception('Array of rights is invalid, not an array or not 9 elements');
            }

            $this->_ranks[$rank_id]['rights'] = $rights;

            return $this->getRanks();
        } catch (Exception $e) {

            die('Caught exception: ' . $e->getMessage() . "\n");
        }
    }

    /**
     *
     * @param RanksTypes $rank
     * @param SwitchTypes $value
     *
     * @return array
     */
    public function deleteRankById($rank_id)
    {
        array_splice($this->_ranks, $this->validateRankId($rank_id), 1);

        return $this->getRanks();
    }

    /**
     * Get all the ranks permissions as an Array
     *
     * @return array
     */
    public function getAllRanksAsArray()
    {
        return $this->_ranks;
    }

    /**
     * Get all the ranks permissions as a JSON
     *
     * @return string
     */
    public function getAllRanksAsJsonString()
    {
        return json_encode($this->_ranks);
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
        return isset($this->_ranks[$rank_id]) ? $this->_ranks[$rank_id] : 0;
    }

    /**
     * Get the user rank by ID, it automatically decrements 1
     *
     * @param int $rank_id Rank ID
     *
     * @return array
     */
    public function getUserRankById($rank_id)
    {
        return $this->getRankById($rank_id - 1);
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
        if ($rank_id < 0) {

            return 0;
        }

        if ($rank_id > count($this->_ranks)) {

            return count($this->_ranks) - 1;
        }

        return $rank_id;
    }
}

/* end of Ranks.php */
