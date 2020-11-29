<?php
/**
 * Premium
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\libraries\premium;

use App\core\entities\PremiumEntity;

/**
 * Premium Class
 *
 * @category Classes
 * @package  premium
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Premium
{
    /**
     *
     * @var array
     */
    private $premium = [];

    /**
     *
     * @var int
     */
    private $current_user_id = 0;

    /**
     * Constructor
     *
     * @param array $premium         Premium
     * @param int   $current_user_id Current User ID
     *
     * @return void
     */
    public function __construct($premium, $current_user_id)
    {
        if (is_array($premium)) {
            $this->setUp($premium);
            $this->setUserId($current_user_id);
        }
    }

    /**
     * Get all the premium
     *
     * @return array
     */
    public function getPremium()
    {
        $list_of_premium = [];

        foreach ($this->premium as $premium) {
            if (($premium instanceof PremiumEntity)) {
                $list_of_premium[] = $premium;
            }
        }

        return $list_of_premium;
    }

    /**
     * Get current premium
     *
     * @return array
     */
    public function getCurrentPremium()
    {
        return $this->getPremium()[0];
    }

    /**
     * Set up the list of premium
     *
     * @param array $premiums Premiums
     *
     * @return void
     */
    private function setUp($premiums)
    {
        foreach ($premiums as $premium) {
            $this->premium[] = $this->createNewPremiumEntity($premium);
        }
    }

    /**
     *
     * @param int $user_id User Id
     */
    private function setUserId($user_id)
    {
        $this->current_user_id = $user_id;
    }

    /**
     *
     * @return int
     */
    private function getUserId()
    {
        return $this->current_user_id;
    }

    /**
     * Create a new instance of PremiumEntity
     *
     * @param array $premium Premium
     *
     * @return \PremiumEntity
     */
    private function createNewPremiumEntity($premium)
    {
        return new PremiumEntity($premium);
    }
}
