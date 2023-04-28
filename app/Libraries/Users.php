<?php

namespace App\Libraries;

use App\Core\Enumerators\AllianceRanksEnumerator as AllianceRanks;
use App\Core\Enumerators\SwitchIntEnumerator as SwitchInt;
use App\Libraries\Alliance\Ranks;
use App\Models\Libraries\UsersLibrary;

class Users
{
    private $user_data;
    private $planet_data;
    private UsersLibrary $usersModel;

    public function __construct()
    {
        $this->usersModel = new UsersLibrary();

        if (self::isSessionSet()) {
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
    public static function checkSession()
    {
        if (!self::isSessionSet()) {
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
        $user_data = $this->usersModel->getAllyIdByUserId($user_id);

        if ($user_data['user_ally_id'] != 0) {
            $alliance = $this->usersModel->getAllianceDataByAllianceId($user_data['user_ally_id']);

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
                    $this->usersModel->updateAllianceOwner($alliance['alliance_id'], $userRank);
                } else {
                    $this->usersModel->deleteAllianceById($alliance['alliance_id']);
                }
            } else {
                $this->usersModel->deleteAllianceById($alliance['alliance_id']);
            }
        }

        $this->usersModel->deletePlanetsAndRelatedDataByUserId($user_id);
        $this->usersModel->deleteMessagesByUserId($user_id);
        $this->usersModel->deleteBuddysByUserId($user_id);
        $this->usersModel->deleteUserDataById($user_id);
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
    //##########################################################################
    //
    // Private Methods
    //
    //##########################################################################

    /**
     * isSessionSet
     *
     * @return boolean
     */
    private static function isSessionSet()
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
        $user_row = $this->usersModel->setUserDataByUserId($_SESSION['user_id']);

        $this->displayLoginErrors($user_row);

        // update user activity data
        $this->usersModel->updateUserActivityData(
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

        if (!password_verify(($user_row['user_password'] . '-' . SECRETWORD), $_SESSION['user_password']) && !defined('IN_LOGIN')) {
            Functions::redirect(SYSTEM_ROOT);
        }
    }

    /**
     * setPlanetData
     *
     * @return void
     */
    private function setPlanetData()
    {
        $this->planet_data = $this->usersModel->setPlanetData(
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
            $owned = $this->usersModel->getUserPlanetByIdAndUserId($select, $this->user_data['user_id']);

            if ($owned) {
                $this->user_data['current_planet'] = $select;
                $this->usersModel->changeUserPlanetByUserId($select, $this->user_data['user_id']);
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
                $insert_query .= '`' . $column . "` = '" . $value . "', ";
            }

            // Remove last comma
            $insert_query = substr_replace($insert_query, '', -2) . ';';

            // get the last inserted user id
            $user_id = $this->usersModel->createNewUser($insert_query);

            // insert extra required tables
            if ($full_insert) {
                // create the buildings, defenses and ships tables
                $this->usersModel->createPremium($user_id);
                $this->usersModel->createResearch($user_id);
                $this->usersModel->createSettings($user_id);
                $this->usersModel->createUserStatistics($user_id);
            }

            return $user_id;
        }
    }
}
