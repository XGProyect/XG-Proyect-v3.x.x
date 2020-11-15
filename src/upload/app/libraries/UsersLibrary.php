<?php
/**
 * Users Library
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\libraries;

use App\core\enumerators\AllianceRanksEnumerator as AllianceRanks;
use App\core\enumerators\SwitchIntEnumerator as SwitchInt;
use App\core\Language;
use App\core\Template;
use App\libraries\alliance\Ranks;
use App\libraries\Functions;
use App\libraries\TimingLibrary as Timing;

/**
 * Users Class
 */
class UsersLibrary
{
    /**
     * @var mixed
     */
    private $user_data;
    /**
     * @var mixed
     */
    private $planet_data;
    /**
     * @var mixed
     */
    private $Users_Model;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->Users_Model = Functions::modelLoader('libraries/UsersLibrary');

        if ($this->isSessionSet()) {
            // Get user data and check it
            $this->setUserData();

            if (!defined('IN_ADMIN')) {
                // Set the changed planet
                $this->setPlanet();

                // Get planet data and check it
                $this->setPlanetData();

                // Update resources, ships, defenses & technologies
                UpdatesLibrary::updatePlanetResources($this->user_data, $this->planet_data, time());

                // Update buildings queue
                UpdatesLibrary::updateBuildingsQueue($this->planet_data, $this->user_data);
            }
        }
    }

    /**
     * userLogin
     *
     * @param int    $user_id   User ID
     * @param string $password  Password
     *
     * @return void
     */
    public function userLogin($user_id = 0, $password = '')
    {
        if ($user_id != 0 && !empty($password) && (strlen($password) == 60)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_password'] = Functions::hash($password . '-' . SECRETWORD);

            return true;
        } else {
            return false;
        }
    }

    /**
     * getUserData
     *
     * @return array
     */
    public function getUserData()
    {
        return $this->user_data;
    }

    /**
     * getPlanetData
     *
     * @return array
     */
    public function getPlanetData()
    {
        return $this->planet_data;
    }

    /**
     * checkSession
     *
     * @return void
     */
    public function checkSession()
    {
        if (!$this->isSessionSet()) {
            Functions::redirect(SYSTEM_ROOT);
        }
    }

    /**
     * deleteUser
     *
     * @param int $user_id User ID
     *
     * @return void
     */
    public function deleteUser($user_id)
    {
        $user_data = $this->Users_Model->getAllyIdByUserId($user_id);

        if ($user_data['user_ally_id'] != 0) {
            $alliance = $this->Users_Model->getAllianceDataByAllianceId($user_data['user_ally_id']);

            if ($alliance['ally_members'] > 1 && (isset($alliance['alliance_ranks']) && !is_null($alliance['alliance_ranks']))) {
                $ranks = new Ranks($alliance['alliance_ranks']);
                $userRank = null;

                // search for an user that has permission to receive the alliance.
                foreach ($ranks->getAllRanksAsArray() as $id => $rank) {
                    if (isset($rank['rights'][AllianceRanks::RIGHT_HAND]) && $rank['rights'][AllianceRanks::RIGHT_HAND] == SwitchInt::on) {
                        $userRank = $id;
                        break;
                    }
                }

                // check and update
                if (is_numeric($userRank)) {
                    $this->Users_Model->updateAllianceOwner($alliance['alliance_id'], $userRank);
                } else {
                    $this->Users_Model->deleteAlliance($alliance['alliance_id']);
                }
            } else {
                $this->Users_Model->deleteAlliance($alliance['alliance_id']);
            }
        }

        $this->Users_Model->deletePlanetsAndRelatedDataByUserId($user_id);
        $this->Users_Model->deleteMessagesByUserId($user_id);
        $this->Users_Model->deleteBuddysByUserId($user_id);
        $this->Users_Model->deleteUserDataById($user_id);
    }

    /**
     * Check if user is on vacations
     *
     * @param array $user User data
     *
     * @return boolean
     */
    public function isOnVacations($user)
    {
        return ($user['preference_vacation_mode'] > 0);
    }

    /**
     * Check if user is inactive
     *
     * @param array $user User data
     *
     * @return boolean
     */
    public function isInactive($user)
    {
        return ($user['user_onlinetime'] < (time() - ONE_WEEK));
    }
    ###########################################################################
    #
    # Private Methods
    #
    ###########################################################################

    /**
     * isSessionSet
     *
     * @return boolean
     */
    private function isSessionSet()
    {
        return !(!isset($_SESSION['user_id']) or !isset($_SESSION['user_password']));
    }

    /**
     * Set the user data after some session and security validations
     *
     * @return void
     */
    private function setUserData()
    {
        $user_row = $this->Users_Model->setUserDataByUserId($_SESSION['user_id']);

        $this->displayLoginErrors($user_row);

        // update user activity data
        $this->Users_Model->updateUserActivityData(
            $_SERVER['REQUEST_URI'],
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT'],
            $_SESSION['user_id']
        );

        // pass the data
        $this->user_data = $user_row;

        // unset the old data
        unset($user_row);
    }

    /**
     * Display login errors
     *
     * @param array $user_row User Row
     *
     * @return void
     */
    private function displayLoginErrors($user_row)
    {
        if ($user_row['user_id'] != $_SESSION['user_id'] && !defined('IN_LOGIN')) {
            Functions::redirect(SYSTEM_ROOT);
        }

        if (!password_verify(($user_row['user_password'] . "-" . SECRETWORD), $_SESSION['user_password']) && !defined('IN_LOGIN')) {
            Functions::redirect(SYSTEM_ROOT);
        }

        if ($user_row['user_banned'] > 0) {
            $core = new Language();
            $ci_lang = $core->loadLang('game/global', true);

            $parse = $ci_lang->language;
            $parse['banned_until'] = Timing::formatExtendedDate($user_row['user_banned']);

            $template = new Template();
            die($template->set('home/banned_message', $parse));
        }
    }

    /**
     * setPlanetData
     *
     * @return void
     */
    private function setPlanetData()
    {
        $this->planet_data = $this->Users_Model->setPlanetData(
            $this->user_data['user_current_planet'],
            Functions::readConfig('stat_admin_level')
        );
    }

    /**
     * setPlanet
     *
     * @return void
     */
    private function setPlanet()
    {
        $select = isset($_GET['cp']) ? (int) $_GET['cp'] : '';
        $restore = isset($_GET['re']) ? (int) $_GET['re'] : '';

        if (isset($select) && is_numeric($select) && isset($restore) && $restore == 0 && $select != 0) {
            $owned = $this->Users_Model->getUserPlanetByIdAndUserId($select, $this->user_data['user_id']);

            if ($owned) {
                $this->user_data['current_planet'] = $select;
                $this->Users_Model->changeUserPlanetByUserId($select, $this->user_data['user_id']);
            }
        }
    }

    /**
     * createUserWithOptions
     *
     * @param array   $data        The data as an array
     * @param boolean $full_insert Insert all the required tables
     *
     * @return void
     */
    public function createUserWithOptions($data, $full_insert = true)
    {
        if (is_array($data)) {
            $insert_query = 'INSERT INTO ' . USERS . ' SET ';

            foreach ($data as $column => $value) {
                $insert_query .= "`" . $column . "` = '" . $value . "', ";
            }

            // Remove last comma
            $insert_query = substr_replace($insert_query, '', -2) . ';';

            // get the last inserted user id
            $user_id = $this->Users_Model->createNewUser($insert_query);

            // insert extra required tables
            if ($full_insert) {
                // create the buildings, defenses and ships tables
                $this->Users_Model->createPremium($user_id);
                $this->Users_Model->createResearch($user_id);
                $this->Users_Model->createSettings($user_id);
                $this->Users_Model->createUserStatistics($user_id);
            }

            return $user_id;
        }
    }
}
