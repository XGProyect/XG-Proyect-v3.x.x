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
use application\libraries\fleets\Fleets;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use const ACS_FLEETS;
use const FLEETS;
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
        /*
        $mode = filter_input(
            INPUT_GET,
            'mode',
            FILTER_CALLBACK,
            [
                'options' => function($value) {
                    
                    if (in_array($value, ['add', 'edit', 'delete', 'a'])) {
                        
                        return $value;
                    }
                    
                    return false;
                }
            ]
        );
            
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
        /**
         * Parse the items
         */
        $page = [
            'js_path' => JS_PATH,
            'acs_code' => $this->generateRandomAcsCode(),
            'buddies_list' => $this->buildBuddiesList(),
            'members_list' => $this->buildMembersList(),
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
        $this->_fleet_id = isset($_GET['fleet']) ? (int) $_GET['fleet'] : NULL;
        $union = isset($_GET['union']) ? (int) $_GET['union'] : NULL;
        $acs_user_message = '';

        if (!is_numeric($this->_fleet_id) or empty($this->_fleet_id) or ! is_numeric($union)) {
            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }

        // QUERY
        $fleet = $this->db->queryFetch(
            "SELECT `fleet_id`,
            `fleet_start_time`,
            `fleet_end_time`,
            `fleet_mess`,
            `fleet_group`,
            `fleet_end_galaxy`,
            `fleet_end_system`,
            `fleet_end_planet`,
            `fleet_end_type`
            FROM " . FLEETS . "
            WHERE fleet_id = '" . intval($this->_fleet_id) . "'"
        );

        if ($fleet['fleet_id'] == '') {
            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }

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

        if ($fleet['fleet_start_time'] <= time() or $fleet['fleet_end_time'] < time() or $fleet['fleet_mess'] == 1) {
            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }

        if (empty($fleet['fleet_group'])) {


            $federation_invited = intval($this->_user['user_id']);

            $this->db->query("INSERT INTO " . ACS_FLEETS . " SET
                `acs_fleet_name` = '" . $acs_code . "',
                `acs_fleet_members` = '" . $this->_user['user_id'] . "',
                `acs_fleet_fleets` = '" . $this->_fleet_id . "',
                `acs_fleet_galaxy` = '" . $fleet['fleet_end_galaxy'] . "',
                `acs_fleet_system` = '" . $fleet['fleet_end_system'] . "',
                `acs_fleet_planet` = '" . $fleet['fleet_end_planet'] . "',
                `acs_fleet_planet_type` = '" . $fleet['fleet_end_type'] . "',
                `acs_fleet_invited` = '" . $federation_invited . "'"
            );

            $acs_id = $this->db->insertId();
            $acs_fleet = $this->db->query(
                "SELECT `acs_fleet_invited`, `acs_fleet_name`
            FROM " . ACS_FLEETS . "
            WHERE `acs_fleet_name` = '" . $acs_code . "' AND
                            `acs_fleet_members` = '" . $this->_user['user_id'] . "' AND
                            `acs_fleet_fleets` = '" . $this->_fleet_id . "' AND
                            `acs_fleet_galaxy` = '" . $fleet['fleet_end_galaxy'] . "' AND
                            `acs_fleet_system` = '" . $fleet['fleet_end_system'] . "' AND
                            `acs_fleet_planet` = '" . $fleet['fleet_end_planet'] . "' AND
                            `acs_fleet_invited` = '" . $this->_user['user_id'] . "'"
            );

            $this->db->query(
                "UPDATE " . FLEETS . "
                SET fleet_group = '" . $acs_id . "'
                WHERE fleet_id = '" . intval($this->_fleet_id) . "'"
            );
        } else {

            $acs_fleet = $this->db->query(
                "SELECT `acs_fleet_invited`, `acs_fleet_name`
                FROM " . ACS_FLEETS . "
                WHERE acs_fleet_id = '" . intval($fleet['fleet_group']) . "'"
            );
        }

        $row = $this->db->fetchArray($acs_fleet);
        $federation_invited = $row['acs_fleet_invited'];
        $parse['acs_code'] = $row['acs_fleet_name'];
        $members = explode(",", $federation_invited);
        $members_count = 0;
        $members_row = '';

        foreach ($members as $a => $b) {

            if ($b != '') {

                $member_qry = $this->db->query(
                    "SELECT `user_name`
                    FROM " . USERS . "
                    WHERE `user_id` ='" . intval($b) . "' ;"
                );

                while ($row = $this->db->fetchArray($member_qry)) {
                    $members_option['value'] = $row['user_name'];
                    $members_option['title'] = $row['user_name'];
                }
            }
            $members_count++;
        }

        $parse['invited_count'] = $members_count;
        $parse['invited_members'] = $members_row;
        $parse['federation_invited'] = $federation_invited;
        $parse['add_user_message'] = $acs_user_message;
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
                    'value' => $buddy['user_name'],
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
        
        
        
        return $list_of_members;
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
									WHERE acs_fleet_members = '" . intval($this->_user['user_id']) . "';");
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
