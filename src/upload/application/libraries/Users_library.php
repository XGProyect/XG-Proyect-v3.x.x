<?php
/**
 * Users Library
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
namespace application\libraries;

/**
 * Users Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Users_library
{

    private $user_data;
    private $planet_data;
    private $Users_Model;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->Users_Model = FunctionsLib::modelLoader('libraries/users_library');

        if ($this->isSessionSet()) {

            // Get user data and check it
            $this->setUserData();

            // Check game close
            FunctionsLib::checkServer($this->user_data);

            // Set the changed planet
            $this->setPlanet();

            // Get planet data and check it
            $this->setPlanetData();

            // Update resources, ships, defenses & technologies
            Updates_library::updatePlanetResources($this->user_data, $this->planet_data, time());

            // Update buildings queue
            Updates_library::updateBuildingsQueue($this->planet_data, $this->user_data);
        }
    }

    /**
     * userLogin
     *
     * @param int    $user_id   User ID
     * @param string $user_name User name
     * @param string $password  Password
     *
     * @return void
     */
    public function userLogin($user_id = 0, $user_name = '', $password = '')
    {
        if ($user_id != 0 && !empty($user_name) && !empty($password)) {

            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $user_name;
            $_SESSION['user_password'] = sha1($password . '-' . SECRETWORD);

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

            FunctionsLib::redirect(SYSTEM_ROOT);
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

                $ranks = unserialize($alliance['alliance_ranks']);
                $userRank = null;

                // search for an user that has permission to receive the alliance.
                foreach ($ranks as $id => $rank) {

                    if ($rank['rechtehand'] == 1) {

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
        return ($user['setting_vacations_status'] == 1);
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
        if (!isset($_SESSION['user_id']) or ! isset($_SESSION['user_name']) or ! isset($_SESSION['user_password'])) {

            return false;
        } else {

            return true;
        }
    }

    /**
     * Set the user data after some session and security validations
     *
     * @return void
     */
    private function setUserData()
    {
        $user_row = $this->Users_Model->setUserDataByUserName($_SESSION['user_name']);

        FunctionsLib::displayLoginErrors($user_row);

        // update user activity data
        $this->Users_Model->updateUserActivityData(
            $_SERVER['REQUEST_URI'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $_SESSION['user_id']
        );

        // pass the data
        $this->user_data = $user_row;

        // unset the old data
        unset($user_row);
    }

    /**
     * setPlanetData
     *
     * @return void
     */
    private function setPlanetData()
    {
        $this->planet_data = $this->Users_Model->setPlanetData(
            $this->user_data['user_current_planet'], FunctionsLib::readConfig('stat_admin_level')
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

/* end of Users.php */
