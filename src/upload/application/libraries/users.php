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
 * @version  3.0.0
 */

namespace application\libraries;

use application\core\XGPCore;

/**
 * Users Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Users extends XGPCore
{

    private $user_data;
    private $planet_data;
    private $langs;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->langs    = parent::$lang;

        if ($this->isSessionSet()) {

            parent::loadModel('libraries/users');
            
            // Get user data and check it
            $this->setUserData();

            // Check game close
            FunctionsLib::checkServer($this->user_data);

            // Set the changed planet
            $this->setPlanet();

            // Get planet data and check it
            $this->setPlanetData();

            // Update resources, ships, defenses & technologies
            UpdateResourcesLib::updateResources($this->user_data, $this->planet_data, time());

            // Update buildings queue
            Update::updateBuildingsQueue($this->planet_data, $this->user_data);
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

            $_SESSION['user_id']        = $user_id;
            $_SESSION['user_name']      = $user_name;
            $_SESSION['user_password']  = sha1($password . '-' . SECRETWORD);

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

            FunctionsLib::redirect(XGP_ROOT);
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
        $user_data  = $this->Users_Model->getAllyIdByUserId($user_id);
        
        if ($user_data['user_ally_id'] != 0) {

            $alliance = $this->Users_Model->getAllianceDataByAllianceId($user_data['user_ally_id']);

            if ($alliance['ally_members'] > 1 
                && (isset($alliance['alliance_ranks']) && !is_null($alliance['alliance_ranks']))) {
                
                $ranks      = unserialize($alliance['alliance_ranks']);
                $userRank   = null;
                
                // search for an user that has permission to receive the alliance.
                foreach ($ranks as $id => $rank) {

                    if ($rank['rechtehand'] == 1) {

                        $userRank   = $id;
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
     * isOnVacations
     *
     * @param array $user User data
     *
     * @return boolean
     */
    public function isOnVacations($user)
    {
        if ($user['setting_vacations_status'] == 1) {

            return true;
        } else {

            return false;
        }
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
        if (!isset($_SESSION['user_id']) or !isset($_SESSION['user_name']) or !isset($_SESSION['user_password'])) {

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

        if ($user_row['user_id'] != $_SESSION['user_id'] && !defined('IN_LOGIN')) {

            FunctionsLib::message($this->langs['ccs_other_user'], XGP_ROOT, 3, false, false);
        }

        if (sha1($user_row['user_password'] . "-" . SECRETWORD) != $_SESSION['user_password'] && !defined('IN_LOGIN')) {

            FunctionsLib::message($this->langs['css_different_password'], XGP_ROOT, 5, false, false);
        }

        if ($user_row['user_banned'] > 0) {

            $parse                  = $this->langs;
            $parse['banned_until']  = date(FunctionsLib::readConfig('date_format_extended'), $user_row['user_banned']);

            die(parent::$page->get('home/banned_message')->parse($parse));
        }

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
     * setPlanetData
     *
     * @return void
     */
    private function setPlanetData()
    {
        $this->planet_data = parent::$db->queryFetch(
            "SELECT p.*, b.*, d.*, s.*,
            m.planet_id AS moon_id,
            m.planet_name AS moon_name,
            m.planet_image AS moon_image,
            m.planet_destroyed AS moon_destroyed,
            m.planet_image AS moon_image,
            (SELECT COUNT(user_statistic_user_id) AS stats_users 
                FROM `" . USERS_STATISTICS . "` AS s
                INNER JOIN " . USERS . " AS u ON u.user_id = s.user_statistic_user_id
                WHERE u.`user_authlevel` <= " . FunctionsLib::readConfig('stat_admin_level') . ") AS stats_users
            FROM " . PLANETS . " AS p
            INNER JOIN " . BUILDINGS . " AS b ON b.building_planet_id = p.`planet_id`
            INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id`
            INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id`
            LEFT JOIN " . PLANETS . " AS m ON m.planet_id = (SELECT mp.`planet_id`
                FROM " . PLANETS . " AS mp
                WHERE (mp.planet_galaxy=p.planet_galaxy AND
                                mp.planet_system=p.planet_system AND
                                mp.planet_planet=p.planet_planet AND
                                mp.planet_type=3))
            WHERE p.`planet_id` = '" . $this->user_data['user_current_planet'] . "';"
        );
    }

    /**
     * setPlanet
     *
     * @return void
     */
    private function setPlanet()
    {
        $select     = isset($_GET['cp']) ? (int)$_GET['cp'] : '';
        $restore    = isset($_GET['re']) ? (int)$_GET['re'] : '';

        if (isset($select) && is_numeric($select) && isset($restore) && $restore == 0 && $select != 0) {

            $owned = parent::$db->queryFetch(
                "SELECT `planet_id`
                FROM " . PLANETS . "
                WHERE `planet_id` = '" . $select . "'
                AND `planet_user_id` = '" . $this->user_data['user_id'] . "';"
            );

            if ($owned) {

                $this->user_data['current_planet'] = $select;

                parent::$db->query(
                    "UPDATE " . USERS . " SET
                    `user_current_planet` = '" . $select . "'
                    WHERE `user_id` = '" . $this->user_data['user_id'] . "';"
                );
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
            
            $insert_query   = 'INSERT INTO ' . USERS . ' SET ';
            
            foreach ($data as $column => $value) {
                $insert_query .= "`" . $column . "` = '" . $value . "', ";
            }
                
            // Remove last comma
            $insert_query   = substr_replace($insert_query, '', -2) . ';';
            
            parent::$db->query($insert_query);
            
            // get the last inserted user id
            $user_id  = parent::$db->insertId();
            
            // insert extra required tables
            if ($full_insert) {
                
                // create the buildings, defenses and ships tables
                self::createPremium($user_id);
                self::createResearch($user_id);
                self::createSettings($user_id);
                self::createUserStatistics($user_id);
            }
            
            return $user_id;
        }
    }
    
    /**
     * createPremium
     * 
     * @param type $user_id The user id
     * 
     * @return void
     */
    public function createPremium($user_id)
    {
        parent::$db->query(
            "INSERT INTO " . PREMIUM . " SET `premium_user_id` = '" . $user_id . "';"
        );
    }
    
    /**
     * createResearch
     * 
     * @param type $user_id The user id
     * 
     * @return void
     */
    public function createResearch($user_id)
    {
        parent::$db->query(
            "INSERT INTO " . RESEARCH . " SET `research_user_id` = '" . $user_id . "';"
        );
    }
    
    /**
     * createSettings
     * 
     * @param type $user_id The user id
     * 
     * @return void
     */
    public function createSettings($user_id)
    {
        parent::$db->query(
            "INSERT INTO " . SETTINGS . " SET `setting_user_id` = '" . $user_id . "';"
        );
    }
    
    /**
     * createUserStatistics
     * 
     * @param type $user_id The user id
     * 
     * @return void
     */
    public function createUserStatistics($user_id)
    {
        parent::$db->query(
            "INSERT INTO " . USERS_STATISTICS . " SET `user_statistic_user_id` = '" . $user_id . "';"
        );
    }
}

/* end of Users.php */
