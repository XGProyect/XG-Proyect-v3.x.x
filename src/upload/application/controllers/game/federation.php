<?php
/**
 * Federation Controller
 *
 * PHP Version 5.5+
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\controllers\game;

use application\core\Controller;
use application\libraries\fleets\AcsFleets;
use application\libraries\fleets\Fleets;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use const ACS_FLEETS;
use const JS_PATH;
use const USERS;

/**
 * Federation Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Federation extends Controller
{

    /**
     * 
     * @var int
     */
    const MODULE_ID = 8;

    /**
     * 
     * @var string
     */
    const REDIRECT_TARGET = 'game.php?page=fleet1';
    
    /**
     *
     * @var array
     */
    private $_user;

    /**
     *
     * @var \Fleets
     */
    private $_fleets = null;
    
    /**
     *
     * @var \AcsFleets
     */
    private $_group = null;
    
    /**
     *
     * @var string
     */
    private $_acs_code = '';
    
    /**
     *
     * @var int 
     */
    private $_members_count = 0;
    
    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // load Model
        parent::loadModel('game/fleet');
        parent::loadModel('game/buddies');
        
        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        // set data
        $this->_user = $this->getUserData();

        // init a new fleets object
        $this->setUpFleets();

        // time to do something
        $this->runAction();
        
        // build the page
        $this->buildPage();
    }

    /**
     * Creates a new fleets object that will handle all the fleets
     * creation methods and actions
     * 
     * @return void
     */
    private function setUpFleets()
    {
        $this->_fleets = new Fleets(
            $this->Fleet_Model->getAllFleetsByUserId($this->_user['user_id']),
            $this->_user['user_id']
        );
    }

    /**
     * Run an action
     * 
     * @return void
     */
    private function runAction()
    {
        $data = filter_input_array(INPUT_POST);
            
        if (isset($data['add']) && isset($data['friends_list'])) {
            
        }
        
        if (isset($data['remove']) && isset($data['members_list'])) {
            
        }
        
        if (isset($data['search']) && isset($data['addtogroup'])) {
            
        }
        
        if (isset($data['save']) && isset($data['name_acs'])) {
            
        }
            
            
        /*
        $data = filter_input_array(INPUT_POST, [
            'name' => FILTER_SANITIZE_STRING,
            'galaxy' => [
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => 1, 'max_range' => MAX_GALAXY_IN_WORLD]
            ],
            'system' => [
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => 1, 'max_range' => MAX_SYSTEM_IN_GALAXY]
            ],
            'planet' => [
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => 1, 'max_range' => (MAX_PLANET_IN_SYSTEM + 1)]
            ],
            'type' => [
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => 1, 'max_range' => 3]
            ]
        ]);

        $action = filter_input(INPUT_GET, 'a', FILTER_VALIDATE_INT);
            
        if ($mode) {
            
            $this->_clean_data['mode'] = $mode;
            $this->_clean_data['data'] = $data;
            $this->_clean_data['action'] = $action;
            
            $this->{$mode .'Shortcut'}();
        }*/
    }
    
    /**
     * Build the page
     * 
     * @return void
     */
    private function buildPage()
    {
        $this->validateData();
        
        /**
         * Parse the items
         */
        $page = [
            'js_path' => JS_PATH,
            'acs_code' => $this->_acs_code,
            'buddies_list' => $this->buildBuddiesList(),
            'members_list' => $this->buildMembersList(),
            'invited_count' => $this->_members_count,
            'add_error_messages' => $this->buildErrorMessagesBlock()
        ];

        // display the page
        parent::$page->display(
            $this->getTemplate()->set(
                'fleet/fleet_federation_view',
                array_merge(
                    $this->getLang(), $page
                )
            ), false, '', false
        );
        

        // OTHER VALUES
        $acs_user_message = '';

        // ACTIONS
        if (isset($_POST['save_acs']) && $_POST['save_acs']) {
            $this->set_name($_POST['name_acs']);
        }

        // REMOVE A MEMBER
        if (isset($_POST['remove']) && $_POST['remove']) {
            $this->remove_user($_POST['members_list']);
        }

        // ADD A MEMBER
        if (isset($_POST['search_user']) or isset($_POST['add'])) {
            $user_to_add = isset($_POST['search_user']) ? '' : $_POST['add'] ? $_POST['friends_list'] : '';

            if ($this->add_user($user_to_add)) {
                $acs_user_message = FormatLib::customColor($this->getLang()['fl_player'] . " " . $_POST['addtogroup'] . " " . $this->getLang()['fl_add_to_attack'], 'lime');
            } else {
                $acs_user_message = FormatLib::colorRed($this->getLang()['fl_player'] . " " . $_POST['addtogroup'] . " " . $this->getLang()['fl_dont_exist']);
            }
        }

        $parse['add_user_message'] = $acs_user_message;
    }
    
    /**
     * Validate data
     * 
     * @return void
     */
    private function validateData()
    {
        $fleet_id = filter_input(INPUT_GET, 'fleet', FILTER_VALIDATE_INT);
        
        if ($fleet_id) {
            
            $own_fleet = $this->_fleets->getOwnValidFleetById($fleet_id);

            if (!is_null($own_fleet)) {
               
                if ($own_fleet->getFleetGroup() <= 0) {

                    // create a new acs, and get its group ID
                    $group_id = $this->Fleet_Model->createNewAcs(
                        $this->generateRandomAcsCode(),
                        $own_fleet
                    );
                } else {
                    
                    $group_id = $own_fleet->getFleetGroup();
                }
                
                $this->_group = new AcsFleets(
                    $this->Fleet_Model->getAcsDataByGroupId($group_id),
                    $this->_user['user_id']
                );

                $this->_acs_code = $this->_group->getFirstAcs()->getAcsFleetName();
            }
        } else {
            
            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }
    }
    
    /**
     * Generates a random ACS code
     * 
     * @return string
     */
    private function generateRandomAcsCode(): string
    {
        return 'AG' . mt_rand(100000, 999999999);
    }
    
    /**
     * Build the list of friends
     * 
     * @return array
     */
    private function buildBuddiesList(): array
    {
        $list_of_buddies = [];
        
        $buddies = $this->Buddies_Model->getBuddiesDetailsById(
            $this->_user['user_id']
        );
        
        if (count($buddies) > 0) {
            
            foreach ($buddies as $buddy) {
                
                $list_of_buddies[] = [
                    'value' => $buddy['user_id'],
                    'title' => $buddy['user_name']
                ];
            }
        }
        
        return $list_of_buddies;
    }
    
    /**
     * Build the list of members
     * 
     * @return array
     */
    private function buildMembersList(): array
    {
        $list_of_members = [];

        $members = $this->Fleet_Model->getListOfAcsMembers(
            join(',', unserialize($this->_group->getFirstAcs()->getAcsFleetInvited()))
        );
        
        if (count($members) > 0) {
            
            foreach ($members as $member) {
                
                ++$this->_members_count;
                
                $list_of_members[] = [
                    'value' => $member['user_id'],
                    'title' => $member['user_name']
                ];
            }
        }
        
        return $list_of_members;
    }

    private function buildErrorMessagesBlock()
    {
        return '';
    }
    
    /**
     * method set_name
     * param $acs_name
     * return set acs name
     */
    private function set_name($acs_name)
    {
        $name_len = strlen($acs_name);

        if ($name_len >= 3 && $name_len <= 20) {
            $this->db->query("UPDATE " . ACS_FLEETS . "
									SET `acs_fleet_name` = '" . $this->db->escapeValue($acs_name) . "'
									WHERE acs_fleet_owner = '" . intval($this->_user['user_id']) . "';");
        }

        return true;
    }

    /**
     * method add_user
     * param
     * return search and add the user
     */
    private function add_user($member_name = '')
    {
        if ($member_name == '') {
            $member_name = $_POST['addtogroup'];
        }

        $added_user_id = 0;
        $member_qry = $this->db->queryFetch("SELECT `user_id`
															FROM " . USERS . "
															WHERE `user_name` ='" . $this->db->escapeValue($member_name) . "';");

        if (( $member_qry['user_id'] != NULL ) && ( $this->members_count($_POST['federation_invited']) < 5 ) && ( $member_qry['user_id'] != $this->_user['user_id'] )) {
            $new_member_string = $this->db->escapeValue($_POST['federation_invited']) . ',' . $member_qry['user_id'];

            $this->db->query("UPDATE " . ACS_FLEETS . " SET
									`acs_fleet_invited` = '" . $new_member_string . "'
									WHERE `acs_fleet_fleets` = '" . $this->_fleet_id . "';");

            $invite_message = $this->getLang()['fl_player'] . $this->_user['user_name'] . $this->getLang()['fl_acs_invitation_message'];
            FunctionsLib::sendMessage($member_qry['user_id'], $this->_user['user_id'], '', 5, $this->_user['user_name'], $this->getLang()['fl_acs_invitation_title'], $invite_message);

            return true;
        } else {
            return false;
        }
    }

    /**
     * method add_user
     * param
     * return search and add the user
     */
    private function remove_user($member_name = '')
    {
        $remove_user_id = 0;
        $member_qry = $this->db->queryFetch("SELECT `user_id`
															FROM " . USERS . "
															WHERE `user_name` ='" . $this->db->escapeValue($member_name) . "';");

        if (( $member_qry['user_id'] != NULL ) && ( $this->members_count($_POST['federation_invited']) >= 1 ) && ( $member_qry['user_id'] != $this->_user['user_id'] )) {

            $members = explode(',', $_POST['federation_invited']);
            $new_member_string = '';

            foreach ($members as $member_id) {
                if ($member_qry['user_id'] != $member_id) {
                    $new_member_string .= $member_id . ',';
                }
            }

            $new_member_string = substr_replace($new_member_string, '', -1);

            $this->db->query("UPDATE " . ACS_FLEETS . " SET
									`acs_fleet_invited` = '" . $new_member_string . "'
									WHERE `acs_fleet_fleets` = '" . $this->_fleet_id . "';");

            $invite_message = $this->getLang()['fl_player'] . $this->_user['user_name'] . $this->getLang()['fl_acs_invitation_message'];
            FunctionsLib::sendMessage($member_qry['user_id'], $this->_user['user_id'], '', 5, $this->_user['user_name'], $this->getLang()['fl_acs_invitation_title'], $invite_message);

            return true;
        } else {
            return false;
        }
    }

    /**
     * method can_add_members
     * param $members_array
     * return true if can add members, false if queue is full
     */
    private function members_count($members_array)
    {
        $member_id = explode(',', $members_array);

        return count($member_id);
    }
}

/* end of federation.php */
