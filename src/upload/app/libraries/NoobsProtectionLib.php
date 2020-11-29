<?php declare (strict_types = 1);

/**
 * Noobs Protection Library
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\libraries;

use App\core\XGPCore;

/**
 * NoobsProtectionLib Class
 */
class NoobsProtectionLib extends XGPCore
{
    /**
     * Protection on/off
     *
     * @var bool
     */
    private $protection;

    /**
     * Minimum amount of points (base points)
     *
     * @var int
     */
    private $protectiontime;

    /**
     * Protection multiplier
     *
     * @var int
     */
    private $protectionmulti;

    /**
     * Minimum allowed level
     *
     * @var int
     */
    private $allowed_level;

    /**
     * Constructor
     */
    public function __construct()
    {
        // load model
        parent::loadModel('libraries/noobsprotectionlib');

        // set configs
        $this->setAllSettings();
    }

    /**
     * Set all configs
     *
     * @return void
     */
    public function setAllSettings(): void
    {
        $configs = $this->Noobsprotectionlib_Model->readAllConfigs();

        $this->protection = (bool) $configs['noobprotection'];
        $this->protectiontime = (int) $configs['noobprotectiontime'];
        $this->protectionmulti = (int) $configs['noobprotectionmulti'];
        $this->allowed_level = (int) $configs['stat_admin_level'];
    }

    /**
     * Check if the first compared player is stronger than the second compared player
     *
     * @param integer $current_points
     * @param integer $other_points
     * @return boolean
     */
    public function isWeak(int $current_points, int $other_points): bool
    {
        if ($this->protection) {
            if ($this->protectionmulti == 0) {
                $this->protectionmulti = 1;
            }

            if ($current_points > $other_points * $this->protectionmulti) {
                if ($other_points > $this->protectiontime && $this->protectiontime > 0) {
                    return false;
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Check if the first compared player is stronger than the second compared player
     *
     * @param integer $current_points
     * @param integer $other_points
     * @return boolean
     */
    public function isStrong(int $current_points, int $other_points): bool
    {
        if ($this->protection) {
            if ($this->protectionmulti == 0) {
                $this->protectionmulti = 1;
            }

            if ($current_points * $this->protectionmulti < $other_points) {
                if ($current_points > $this->protectiontime && $this->protectiontime > 0) {
                    return false;
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Return points for both requested users
     *
     * @param integer $current_user_id
     * @param integer $other_user_id
     * @return array
     */
    public function returnPoints(int $current_user_id, int $other_user_id): array
    {
        return $this->Noobsprotectionlib_Model->returnBothPartiesPoints($current_user_id, $other_user_id);
    }

    /**
     * Determines if the rank can be shown or not
     *
     * @param integer $user_auth_level
     * @return boolean
     */
    public function isRankVisible(int $user_auth_level): bool
    {
        return ($user_auth_level <= $this->allowed_level);
    }
}
