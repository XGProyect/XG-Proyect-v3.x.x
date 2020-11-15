<?php
/**
 * Researches
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\libraries\research;

use App\core\entities\ResearchEntity;

/**
 * researches Class
 *
 * @category Classes
 * @package  research
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Researches
{
    /**
     *
     * @var array
     */
    private $_research = [];

    /**
     *
     * @var int
     */
    private $_current_user_id = 0;

    /**
     * Constructor
     *
     * @param array $research        Research
     * @param int   $current_user_id Current User ID
     *
     * @return void
     */
    public function __construct(array $research, int $current_user_id)
    {
        if (is_array($research)) {
            $this->setUp($research);
            $this->setUserId($current_user_id);
        }
    }

    /**
     * Get all the research
     *
     * @return array
     */
    public function getResearch(): array
    {
        $list_of_research = [];

        foreach ($this->_research as $research) {
            if (($research instanceof ResearchEntity)) {
                $list_of_research[] = $research;
            }
        }

        return $list_of_research;
    }

    /**
     * Get current research
     *
     * @return array
     */
    public function getCurrentResearch()
    {
        return $this->getResearch()[0];
    }

    /**
     * Set up the list of researches
     *
     * @param array $researches Researches
     *
     * @return void
     */
    private function setUp($researches)
    {
        foreach ($researches as $research) {
            $this->_research[] = $this->createNewResearchEntity($research);
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
     * Create a new instance of ResearchEntity
     *
     * @param array $research Research
     *
     * @return \ResearchEntity
     */
    private function createNewResearchEntity($research)
    {
        return new ResearchEntity($research);
    }
}
