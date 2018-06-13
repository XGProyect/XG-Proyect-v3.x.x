<?php
/**
 * Alliance Controller
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
use application\libraries\alliance\Alliances;
use application\libraries\enumerators\AllianceRanksEnumerator as AllianceRanks;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;

use const DPATH;

/**
 * Alliance Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Alliance extends Controller
{

    const MODULE_ID = 13;

    /**
     *
     * @var type \Users_library
     */
    private $_user;

    /**
     *
     * @var \Alliance
     */
    private $_alliance = null;

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
        parent::loadModel('game/alliance');

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        // set data
        $this->_user = $this->getUserData();

        // init a new buddy object
        $this->setUpAlliances();

        // time to do something
        $this->runAction();

        // build the page
        $this->buildPage();
    }

    /**
     * Creates a new alliance object that will handle all the alliance
     * creation methods and actions
     * 
     * @return void
     */
    private function setUpAlliances()
    {
        $this->_alliance = new Alliances(
            $this->Alliance_Model->getAllianceDataById($this->getAllianceId()), $this->_user['user_id']
        );
    }

    /**
     * Check if the page that the user is trying to access is allowed
     * 
     * @return boolean
     */
    private function isPageAllowed()
    {
        $allowed_pages = [
            'public' => [
                'default', 'ainfo', 'make', 'search', 'apply'
            ],
            'awaitingApproval' => [
                'default', 'ainfo'
            ],
            'isMember' => [
                'default', 'ainfo', 'exit', 'memberslist', 'circular', 'admin'
            ]
        ];

        return in_array(
            $this->getCurrentSection(), array_filter($allowed_pages[$this->getUserAccess()], function($value) {
                return $value;
            })
        );
    }

    /**
     * Get user level based on their alliance status
     * 
     * @return string
     */
    private function getUserAccess()
    {
        // not in an alliance
        if ((int) $this->_user['user_ally_id'] === 0) {

            // doesn't have a request
            if ((int) $this->_user['user_ally_request'] === 0) {

                // it's public then
                return 'public';
            }

            // supposedly a request was sent
            return 'awaitingApproval';
        }

        // any other case
        return 'isMember';
    }

    /**
     * Get current alliance ID
     * 
     * @return int
     */
    private function getAllianceId()
    {
        $alliance_id = filter_input(INPUT_GET, 'allyid', FILTER_VALIDATE_INT);

        return (!empty($alliance_id) && (int) $alliance_id != 0) ? $alliance_id : $this->_user['user_ally_id'];
    }

    /**
     * Determine the current page and validate it
     * 
     * @return string
     */
    private function getCurrentSection()
    {
        $mode = filter_input(INPUT_GET, 'mode');

        return (isset($mode) ? $mode : 'default');
    }

    /**
     * Run an action
     * 
     * @return void
     */
    private function runAction()
    {
        
    }

    /**
     * Build the page
     * 
     * @return void
     */
    private function buildPage()
    {
        if (!$this->isPageAllowed()) {

            FunctionsLib::redirect('game.php?page=alliance');
        }

        parent::$page->display(
            $this->{'get' . ucfirst($this->getCurrentSection()) . 'Section'}()
        );
    }

    /**
     * Get alliance default section, calling the right method
     * 
     * @return string
     */
    public function getDefaultSection()
    {
        return $this->{'getDefault' . ucfirst($this->getUserAccess()) . 'Section'}();
    }

    /**
     * Get alliance default menu for search and create
     * 
     * @return type
     */
    public function getDefaultPublicSection()
    {
        return $this->getTemplate()->set('alliance/alliance_default_menu_view', $this->getLang());
    }

    /**
     * Get alliance default message for awaiting approval
     * 
     * @return string
     */
    public function getDefaultAwaitingApprovalSection()
    {
        return 'getDefaultSectionForNotMembers';
    }

    /**
     * Get default section for a member user
     * 
     * @return string
     */
    public function getDefaultIsMemberSection()
    {
        return $this->getTemplate()->set(
            'alliance/alliance_front_page_view', array_merge([
                'image' => $this->buildImageBlock(),
                'tag' => $this->buildTagBlock(),
                'name' => $this->buildNameBlock(),
                'members' => $this->buildMembersBlock(),
                'rank' => $this->buildRankBlock(),
                'requests' => $this->buildRequestsBlock(),
                'circular' => $this->buildCircularBlock(),
                'description' => $this->buildTagBlock(),
                'web' => $this->buildTagBlock(),
                'text' => $this->buildTagBlock(),
                'owner' => $this->buildTagBlock()
            ], $this->getLang()));
        
        $alliance_ranks = unserialize($this->_alliance->getFirstAllianceRanks());

        // REQUESTS
        $request_count = $this->Alliance_Model->getAllianceRequestsCount(
                $this->_ally['alliance_id']
            )['total_requests'];

        $this->getLang()['requests'] = '';
        if ($request_count != 0) {
            if ($this->_ally['alliance_owner'] == $this->_user['user_id'] or $alliance_ranks[$this->_user['user_ally_rank_id'] - 1]['bewerbungen'] != 0) {
                $parse['request_count'] = $request_count;
                $this->getLang()['requests'] = $this->getTemplate()->set('alliance/alliance_requests_row', $parse);
            }
        }
        // EXIT ALLIANCE
        if ($this->_ally['alliance_owner'] != $this->_user['user_id']) {
            $this->getLang()['alliance_owner'] = $this->getTemplate()->set('alliance/alliance_abandon_alliance', $parse);
        } else {
            $this->getLang()['alliance_owner'] = '';
        }

        // GENERAL INFORMATION
        
        $this->getLang()['range'] = $range;
        $this->getLang()['alliance_description'] = $this->description_block($this->_ally['alliance_description']);
        $this->getLang()['alliance_text'] = nl2br($this->bbcode->bbCode($this->_ally['alliance_text']));
        $this->getLang()['alliance_web'] = $this->web_block($this->_ally['alliance_web']);
    }

    /**
     * Get alliance information section
     * 
     * @return string
     */
    public function getAinfoSection()
    {
        return $this->getTemplate()->set(
            'alliance/alliance_ainfo', 
            array_merge(
                $this->getLang(), 
                [
                    'alliance_image' => $this->buildImageBlock(),
                    'alliance_tag' => $this->_alliance->getCurrentAlliance()->getAllianceTag(),
                    'alliance_name' => $this->_alliance->getCurrentAlliance()->getAllianceImage(),
                    'ally_member_scount' => $this->_alliance->getCurrentAlliance()->getAllianceMembers(),
                    'alliance_description' => $this->description_block($this->_alliance->getCurrentAlliance()->getAllianceDescription()),
                    'alliance_web' => $this->web_block($this->_alliance->getCurrentAlliance()->getAllianceWeb()),
                    'alliance_request' => $this->request_block(
                        $this->_alliance->getCurrentAlliance()->getAllianceId(), $this->_alliance->getCurrentAlliance()->getAllianceRequestNotAllow()
                    )
                ]
            )
        );
    }

    /**
     * Get alliance search section
     * 
     * @return string
     */
    public function getSearchSection()
    {
        $search_string = filter_input(INPUT_POST, 'searchtext');
        $search_page = $this->getTemplate()->set(
            'alliance/alliance_search_form_view',
            array_merge(['searchtext' => $search_string], $this->getLang())
        );

        if (!empty($search_string)) {

            $list_of_results = [];
            $results = new Alliances(
                $this->Alliance_Model->searchAllianceByNameTag($search_string), $this->_user['user_id']
            );

            foreach ($results->getAlliance() as $result) {

                $list_of_results[] = [
                    'ally_tag' => FunctionsLib::setUrl('game.php?page=alliance&mode=apply&allyid=' . $result->getAllianceId(), '', $result->getAllianceTag()),
                    'alliance_name' => $result->getAllianceName(),
                    'ally_members' => $result->getAllianceMembers()
                ];
            }

            $search_page .= $this->getTemplate()->set(
                'alliance/alliance_search_results_view',
                array_merge(['list_of_results' => $list_of_results], $this->getLang())
            );
        }

        return $search_page;
    }

    /**
     * Get alliance make section
     * 
     * @return string
     */
    public function getMakeSection()
    {
        $action = filter_input_array(INPUT_POST);
        
        if (is_array($action)) {
            
            $alliance_tag = $this->validateTag($action['atag']);
            $alliance_name = $this->validateName($action['aname']);

            $this->Alliance_Model->createNewAlliance(
                $alliance_name, $alliance_tag, $this->_user['user_id'], $this->getLang()['al_alliance_founder_rank']
            );

            $message = str_replace(['%s', '%d'], [$alliance_name, $alliance_tag], $this->getLang()['al_created']);
            return $this->messageBox(
                $message, 
                $message . "<br/><br/>", 
                'game.php?page=alliance', 
                $this->getLang()['al_continue']
            );
        } else {
         
            return $this->getTemplate()->set('alliance/alliance_make_view', $this->getLang());
        }
    }

    /**
     * Get alliance apply section
     * 
     * @return string
     */
    public function getApplySection()
    {
        return 'getApplySection';
    }

    /**
     * Get alliance member list section
     * 
     * @return string
     */
    public function getMemberlistSection()
    {
        return 'getMemberlistSection';
    }

    /**
     * Get alliance circular section
     * 
     * @return string
     */
    public function getCircularSection()
    {
        return 'getCircularSection';
    }

    /**
     * Get alliance exit section
     * 
     * @return string
     */
    public function getExitSection()
    {
        return 'getExitSection';
    }

    /**
     * Get alliance admin section
     * 
     * @return string
     */
    public function getAdminSection()
    {
        return 'getAdminSection';
    }

    /**
     * 
     * BLOCKS
     * 
     */
    
    /**
     * Build the image block
     * 
     * @return string
     */
    private function buildImageBlock()
    {
        $image = $this->_alliance->getCurrentAlliance()->getAllianceImage();
        
        if (!empty($image)) {

            return '<tr><th colspan="2">' . FunctionsLib::setImage($image, $image) . '</td></tr>';
        }

        return '';   
    }
    
    /**
     * Build the tag block
     * 
     * @return string
     */
    private function buildTagBlock()
    {
        return $this->_alliance->getCurrentAlliance()->getAllianceTag();
    }
    
    /**
     * Build the name block
     * 
     * @return string
     */
    private function buildNameBlock()
    {
        return $this->_alliance->getCurrentAlliance()->getAllianceName();
    }
    
    /**
     * Build the members block
     * 
     * @return string
     */
    private function buildMembersBlock()
    {
        $list_of_members = '';
        
        if ($this->_alliance->hasAccess(AllianceRanks::view_member_list)) {
            
            $list_of_members = ' (' . FunctionsLib::setUrl('game.php?page=alliance&mode=memberslist', '', $this->getLang()['al_user_list']) . ')';
        }
        
        return $this->_alliance->getCurrentAlliance()->getAllianceMembers() . $list_of_members;
    }
    
    /**
     * Build the ranks block
     * 
     * @return string
     */
    private function buildRankBlock()
    {
        $rank = $this->getLang()['al_new_member_rank_text'];
        $admin_area = '';
            
        if ($this->_alliance->isOwner()) {

            $rank = ( $this->_ally['alliance_owner_range'] != '' ) ? $this->_ally['alliance_owner_range'] : $this->getLang()['al_founder_rank_text'];
        } elseif ($this->_user['user_ally_rank_id'] != 0 && isset($alliance_ranks[$this->_user['user_ally_rank_id'] - 1]['name'])) {

            $rank = $alliance_ranks[$this->_user['user_ally_rank_id'] - 1]['name'];
        }

        if ($this->_alliance->hasAccess(AllianceRanks::administration)) {
            
            $admin_area = ' (' . FunctionsLib::setUrl('game.php?page=alliance&mode=admin&edit=ally', '', $this->getLang()['al_manage_alliance']) . ')';
        }
        
        return $rank . $admin_area;
    }
    
    /**
     * Build the requests block
     * 
     * @return string
     */
    private function buildRequestsBlock()
    {
        return '';
    }
    
    /**
     * Build the circular message block
     * 
     * @return string
     */
    private function buildCircularBlock()
    {
        if ($this->_alliance->hasAccess(AllianceRanks::send_circular)) {
            
            return $this->getTemplate()->set('alliance/alliance_circular_row', $this->getLang());
        }
    }

    
    /**
     * 
     * OLD METHODS
     * OLD METHODS
     * OLD METHODS
     * OLD METHODS
     * OLD METHODS
     * 
     */
    
    
    
    
    
    
    
    
    
    
    /**
     * method ally_apply
     * param
     * return the apply page for someone without an alliance
     */
    private function ally_apply()
    {
        $parse = $this->getLang();

        if ($this->_user['user_ally_id'] == 0 && $this->_user['user_ally_request'] == 0) {

            if ($_GET['allyid'] != NULL) {

                $allyrow = $this->Alliance_Model->getAllianceDataById($_GET['allyid']);
            }

            if ($allyrow['alliance_request_notallow'] == 1) {

                FunctionsLib::message($this->getLang()['al_alliance_closed'], "game.php?page=alliance", 2);
            } else {

                if (!is_numeric($_GET['allyid']) or ! $_GET['allyid'] or $this->_user['user_ally_request'] != 0 or $this->_user['user_ally_id'] != 0) {

                    FunctionsLib::redirect('game.php?page=alliance');
                }

                extract($allyrow);

                if (isset($_POST['enviar']) && ( $_POST['enviar'] == $this->getLang()['al_applyform_send'] )) {
                    $this->Alliance_Model->createNewUserRequest(
                        $alliance_id, $_POST['text'], $this->_user['user_id']
                    );

                    FunctionsLib::message($this->getLang()['al_request_confirmation_message'], "game.php?page=alliance", 2);
                } else {

                    $text_apply = ( $alliance_request ) ? $alliance_request : $this->getLang()['al_default_request_text'];
                }

                $parse['allyid'] = (int) $_GET['allyid'];
                $parse['chars_count'] = strlen($text_apply);
                $parse['text_apply'] = $text_apply;
                $parse['Write_to_alliance'] = str_replace('%s', $alliance_tag, $this->getLang()['al_write_request']);

                return $this->getTemplate()->set('alliance/alliance_applyform', $parse);
            }
        }
    }

    /**
     * method ally_main
     * param
     * return the main page for someone without an alliance
     */
    private function ally_main()
    {
        $parse = $this->getLang();

        ##############################################################################################
        # DEFAULT PART WITHOUT ALLIANCE
        ##############################################################################################
        if ($this->_user['user_ally_id'] == 0 && $this->_user['user_ally_request'] != 0) {
            $allyquery = $this->Alliance_Model->getAllianceDataById($this->_user['user_ally_request']);

            extract($allyquery);

            if (isset($_POST['bcancel'])) {
                $this->Alliance_Model->cancelUserRequestById($this->_user['user_id']);

                $this->getLang()['request_text'] = str_replace('%s', $alliance_tag, $this->getLang()['al_request_deleted']);
                $this->getLang()['button_text'] = $this->getLang()['al_continue'];
            } else {
                $this->getLang()['request_text'] = str_replace('%s', $alliance_tag, $this->getLang()['al_request_wait_message']);
                $this->getLang()['button_text'] = $this->getLang()['al_delete_request'];
            }

            return $this->getTemplate()->set('alliance/alliance_apply_waitform', $this->getLang());
        }

        ##############################################################################################
        # DEFAULT PART WITH ALLIANCE
        ##############################################################################################
        if ($this->_user['user_ally_id'] != 0 && $this->_user['user_ally_request'] == 0) {

            $alliance_ranks = unserialize($this->_ally['alliance_ranks']);

            // IMAGE
            if ($this->_ally['alliance_ranks'] != '') {
                $parse['alliance_image'] = $this->image_block($this->_ally['alliance_image']);
                $this->_ally['alliance_ranks'] = $this->getTemplate()->set('alliance/alliance_image_row', $parse);
            }

            // RANKS
            if ($this->_ally['alliance_owner'] == $this->_user['user_id']) {
                $range = ( $this->_ally['alliance_owner_range'] != '' ) ? $this->_ally['alliance_owner_range'] : $this->getLang()['al_founder_rank_text'];
            } elseif ($this->_user['user_ally_rank_id'] != 0 && isset($alliance_ranks[$this->_user['user_ally_rank_id'] - 1]['name'])) {
                $range = $alliance_ranks[$this->_user['user_ally_rank_id'] - 1]['name'];
            } else {
                $range = $this->getLang()['al_new_member_rank_text'];
            }

            // MEMBER LIST
            if ($this->_ally['alliance_owner'] == $this->_user['user_id'] or $alliance_ranks[$this->_user['user_ally_rank_id'] - 1]['memberlist'] != 0) {
                $this->getLang()['members_list'] = " (<a href=\"game.php?page=alliance&mode=memberslist\">" . $this->getLang()['al_user_list'] . "</a>)";
            } else {
                $this->getLang()['members_list'] = '';
            }

            // ADMIN ALLIANCE
            if ($this->_ally['alliance_owner'] == $this->_user['user_id'] or $alliance_ranks[$this->_user['user_ally_rank_id'] - 1]['administrieren'] != 0) {
                $this->getLang()['alliance_admin'] = " (<a href=\"game.php?page=alliance&mode=admin&edit=ally\">" . $this->getLang()['al_manage_alliance'] . "</a>)";
            } else {
                $this->getLang()['alliance_admin'] = '';
            }

            // CIRCULAR MESSAGE
            if ($this->_ally['alliance_owner'] == $this->_user['user_id'] or $alliance_ranks[$this->_user['user_ally_rank_id'] - 1]['mails'] != 0) {
                $this->getLang()['send_circular_mail'] = $this->getTemplate()->set('alliance/alliance_circular_row', $parse);
            } else {
                $this->getLang()['send_circular_mail'] = '';
            }

            // REQUESTS
            $request_count = $this->Alliance_Model->getAllianceRequestsCount(
                    $this->_ally['alliance_id']
                )['total_requests'];

            $this->getLang()['requests'] = '';
            if ($request_count != 0) {
                if ($this->_ally['alliance_owner'] == $this->_user['user_id'] or $alliance_ranks[$this->_user['user_ally_rank_id'] - 1]['bewerbungen'] != 0) {
                    $parse['request_count'] = $request_count;
                    $this->getLang()['requests'] = $this->getTemplate()->set('alliance/alliance_requests_row', $parse);
                }
            }
            // EXIT ALLIANCE
            if ($this->_ally['alliance_owner'] != $this->_user['user_id']) {
                $this->getLang()['alliance_owner'] = $this->getTemplate()->set('alliance/alliance_abandon_alliance', $parse);
            } else {
                $this->getLang()['alliance_owner'] = '';
            }

            // GENERAL INFORMATION
            $this->getLang()['alliance_image'] = ( $this->_ally['alliance_image'] != '' ) ? "<tr><th colspan=2><img src=\"{$this->_ally['alliance_image']}\"></td></tr>" : '';
            $this->getLang()['range'] = $range;
            $this->getLang()['alliance_description'] = $this->description_block($this->_ally['alliance_description']);
            $this->getLang()['alliance_text'] = nl2br($this->bbcode->bbCode($this->_ally['alliance_text']));
            $this->getLang()['alliance_web'] = $this->web_block($this->_ally['alliance_web']);
            $this->getLang()['ally_tag'] = $this->_ally['alliance_tag'];
            $this->getLang()['ally_members'] = $this->_ally['ally_members'];
            $this->getLang()['alliance_name'] = $this->_ally['alliance_name'];

            return $this->getTemplate()->set('alliance/alliance_frontpage', $this->getLang());
        }
    }

    /**
     * method ally_exit
     * param
     * return the exit page for someone with an alliance
     */
    private function ally_exit()
    {
        $parse = $this->getLang();

        if ($this->_user['user_ally_id'] != 0 && $this->_user['user_ally_request'] == 0) {
            if ($this->_ally['alliance_owner'] == $this->_user['user_id']) {
                FunctionsLib::message($this->getLang()['al_founder_cant_leave_alliance'], "game.php?page=alliance", 2);
            }

            if (isset($_GET['yes']) && $_GET['yes'] == 1) {

                $this->Alliance_Model->exitAlliance($this->_user['user_id']);

                $this->getLang()['Go_out_welldone'] = str_replace("%s", $this->_ally['alliance_name'], $this->getLang()['al_leave_sucess']);

                $page = $this->messageBox(
                    $this->getLang()['Go_out_welldone'], "<br>", "game.php?page=alliance", $this->getLang()['al_continue']
                );
            } else {
                $this->getLang()['Want_go_out'] = str_replace("%s", $this->_ally['alliance_name'], $this->getLang()['al_do_you_really_want_to_go_out']);
                $page = $this->messageBox(
                    $this->getLang()['Want_go_out'], "<br>", "game.php?page=alliance&mode=exit&yes=1", $this->getLang()['al_go_out_yes']
                );
            }

            return $page;
        }
    }

    /**
     * method ally_members_list
     * param
     * return the members list page for someone with an alliance
     */
    private function ally_members_list()
    {
        $parse = $this->getLang();
        $alliance_ranks = unserialize($this->_ally['alliance_ranks']);

        if ($this->_user['user_ally_id'] != 0 && $this->_user['user_ally_request'] == 0 && $this->have_access($this->_ally['alliance_owner'], $this->permissions['see_users_list']) === true) {
            $sort1 = isset($_GET['sort1']) ? (int) $_GET['sort1'] : NULL; // ORDEN 1
            $sort2 = isset($_GET['sort2']) ? (int) $_GET['sort2'] : NULL; // ORDEN 2

            $listuser = $this->Alliance_Model->getAllianceMembers(
                $this->_user['user_ally_id'], $sort1, $sort2
            );

            $i = 0;
            $page_list = [];

            while ($u = $this->_db->fetchArray($listuser)) {
                $i++;
                $u['i'] = $i;

                if ($this->permissions['see_connected_users']) {
                    $u['user_onlinetime'] = $this->inactive_time($u['user_onlinetime']);
                } else {
                    $u['user_onlinetime'] = '"">-<';
                }

                if ($this->_ally['alliance_owner'] == $u['user_id']) {
                    $u['user_ally_range'] = ( $this->_ally['alliance_owner_range'] == '' ) ? $this->getLang()['al_founder_rank_text'] : $this->_ally['alliance_owner_range'];
                } elseif ($u['user_ally_rank_id'] == 0) {
                    $u['user_ally_range'] = $this->getLang()['al_new_member_rank_text'];
                } else {
                    $u['user_ally_range'] = $alliance_ranks[$u['user_ally_rank_id'] - 1]['name'];
                }

                $u['dpath'] = DPATH;
                $u['points'] = FormatLib::prettyNumber($u['user_statistic_total_points']);

                if ($u['user_ally_register_time'] > 0) {
                    $u['user_ally_register_time'] = date(FunctionsLib::readConfig('date_format_extended'), $u['user_ally_register_time']);
                } else {
                    $u['user_ally_register_time'] = "-";
                }

                $page_list[] = $u;
            }

            switch ($sort2) {
                case 1:
                    $s = 2;
                    break;
                case 2:
                    $s = 1;
                    break;
                default:
                    $s = 1;
                    break;
            }

            $parse['i'] = $i;
            $parse['s'] = $s;
            $parse['list_of_members'] = $page_list;

            return $this->getTemplate()->set('alliance/alliance_memberslist_view', $parse);
        }
    }

    /**
     * method ally_circular_message
     * param
     * return the circular message page for someone with an alliance
     */
    private function ally_circular_message()
    {
        $parse = $this->getLang();

        if ($this->_user['user_ally_id'] != 0 && $this->_user['user_ally_request'] == 0 && $this->have_access($this->_ally['alliance_owner'], $this->permissions['create_circular']) === true) {
            if (isset($_GET['sendmail']) && $_GET['sendmail'] == 1) {
                $list = '';
                $_POST['r'] = (int) $_POST['r'];
                $_POST['text'] = $_POST['text'];

                if ($_POST['r'] == 0) {

                    $sq = $this->Alliance_Model->getAllianceMembersById(
                        $this->_user['user_ally_id']
                    );
                } else {

                    $sq = $this->Alliance_Model->getAllianceMembersByIdAndRankId(
                        $this->_user['user_ally_id'], $_POST['r']
                    );
                }

                while ($u = $this->_db->fetchArray($sq)) {
                    FunctionsLib::sendMessage($u['user_id'], $this->_user['user_id'], '', 3, $this->_ally['alliance_tag'], $this->_user['user_name'], $_POST['text']);

                    $list .= "<br>{$u['user_name']} ";
                }

                $page = $this->messageBox($this->getLang()['al_circular_sended'], $list, "game.php?page=alliance", $this->getLang()['al_continue'], true);

                return $page;
            }

            $this->getLang()['r_list'] = "<option value=\"0\">" . $this->getLang()['al_all_players'] . "</option>";

            $alliance_ranks = unserialize($this->_ally['alliance_ranks']);

            if ($alliance_ranks) {
                foreach ($alliance_ranks as $id => $array) {
                    $this->getLang()['r_list'] .= "<option value=\"" . ( $id + 1 ) . "\">" . $array['name'] . "</option>";
                }
            }

            return $this->getTemplate()->set('alliance/alliance_circular_view', $this->getLang());
        }
    }

    /**
     * method ally_admin
     * param
     * return the admin page for someone with an alliance
     */
    private function ally_admin()
    {
        $parse = $this->getLang();

        if ($this->_user['user_ally_id'] != 0 && $this->_user['user_ally_request'] == 0) {
            $edit = ( isset($_GET['edit']) ? $_GET['edit'] : NULL );

            switch ($edit) {
                case ( $edit == 'rights' && $this->have_access($this->_ally['alliance_owner'], $this->permissions['right_hand']) === true ):

                    $d = ( isset($_GET['d']) && is_numeric($_GET['d']) && $_GET['d'] >= 0 ) ? $_GET['d'] : NULL;
                    $alliance_ranks = unserialize($this->_ally['alliance_ranks']);

                    if (!empty($_POST['newrangname'])) {

                        $this->Alliance_Model->createNewAllianceRank(
                            $this->_ally['alliance_id'], $alliance_ranks, $_POST['newrangname']
                        );

                        $goto = 'game.php?' . str_replace('&amp;', '&', $_SERVER['QUERY_STRING']);

                        FunctionsLib::redirect($goto);
                    } elseif (isset($_POST['id']) && $_POST['id'] != '' && is_array($_POST['id'])) {
                        $ally_ranks_new = array();

                        foreach ($_POST['id'] as $id) {
                            $name = $alliance_ranks[$id]['name'];
                            $ally_ranks_new[$id]['name'] = $name;
                            $ally_ranks_new[$id]['delete'] = isset($_POST['u' . $id . 'r0']) ? 1 : 0;
                            $ally_ranks_new[$id]['kick'] = ( isset($_POST['u' . $id . 'r1']) && $this->_ally['alliance_owner'] == $this->_user['user_id'] ) ? 1 : 0;
                            $ally_ranks_new[$id]['bewerbungen'] = isset($_POST['u' . $id . 'r2']) ? 1 : 0;
                            $ally_ranks_new[$id]['memberlist'] = isset($_POST['u' . $id . 'r3']) ? 1 : 0;
                            $ally_ranks_new[$id]['bewerbungenbearbeiten'] = isset($_POST['u' . $id . 'r4']) ? 1 : 0;
                            $ally_ranks_new[$id]['administrieren'] = isset($_POST['u' . $id . 'r5']) ? 1 : 0;
                            $ally_ranks_new[$id]['onlinestatus'] = isset($_POST['u' . $id . 'r6']) ? 1 : 0;
                            $ally_ranks_new[$id]['mails'] = isset($_POST['u' . $id . 'r7']) ? 1 : 0;
                            $ally_ranks_new[$id]['rechtehand'] = isset($_POST['u' . $id . 'r8']) ? 1 : 0;
                        }

                        $ranks = serialize($ally_ranks_new);

                        $this->Alliance_Model->updateAllianceRanks(
                            $this->_ally['alliance_id'], $ranks
                        );

                        $goto = 'game.php?' . str_replace('&amp;', '&', $_SERVER['QUERY_STRING']);

                        FunctionsLib::redirect($goto);
                    } elseif (isset($d) && isset($alliance_ranks[$d])) {
                        unset($alliance_ranks[$d]);

                        $this->_ally['ally_rank'] = serialize($alliance_ranks);

                        $this->Alliance_Model->updateAllianceRanks(
                            $this->_ally['alliance_id'], $this->_ally['ally_rank']
                        );
                    }

                    $i = 0;
                    $list = [];

                    if (count($alliance_ranks) != 0 && $alliance_ranks != '') {
                        foreach ($alliance_ranks as $a => $b) {
                            if ($this->_ally['alliance_owner'] == $this->_user['user_id']) {
                                $r1 = "<input type=checkbox name=\"u{$a}r0\"" . (($b['delete'] == 1) ? ' checked="checked"' : '') . ">";
                            } else {
                                $r1 = "<b>-</b>";
                            }

                            $row['id'] = $a;
                            $row['r0'] = $b['name'];
                            $row['delete'] = "<a href=\"game.php?page=alliance&mode=admin&edit=rights&d={$a}\"><img src=\"" . DPATH . "alliance/abort.gif\" border=0></a>";
                            $row['a'] = $a;
                            $row['r1'] = $r1;
                            $row['r2'] = "<input type=checkbox name=\"u{$a}r1\"" . (($b['kick'] == 1) ? ' checked="checked"' : '') . ">";
                            $row['r3'] = "<input type=checkbox name=\"u{$a}r2\"" . (($b['bewerbungen'] == 1) ? ' checked="checked"' : '') . ">";
                            $row['r4'] = "<input type=checkbox name=\"u{$a}r3\"" . (($b['memberlist'] == 1) ? ' checked="checked"' : '') . ">";
                            $row['r5'] = "<input type=checkbox name=\"u{$a}r4\"" . (($b['bewerbungenbearbeiten'] == 1) ? ' checked="checked"' : '') . ">";
                            $row['r6'] = "<input type=checkbox name=\"u{$a}r5\"" . (($b['administrieren'] == 1) ? ' checked="checked"' : '') . ">";
                            $row['r7'] = "<input type=checkbox name=\"u{$a}r6\"" . (($b['onlinestatus'] == 1) ? ' checked="checked"' : '') . ">";
                            $row['r8'] = "<input type=checkbox name=\"u{$a}r7\"" . (($b['mails'] == 1) ? ' checked="checked"' : '') . ">";
                            $row['r9'] = "<input type=checkbox name=\"u{$a}r8\"" . (($b['rechtehand'] == 1) ? ' checked="checked"' : '') . ">";

                            $list[] = $row;
                        }
                    }

                    $this->getLang()['list_of_ranks'] = $list;
                    $this->getLang()['dpath'] = DPATH;

                    return $this->getTemplate()->set('alliance/alliance_admin_laws', $this->getLang());
                    break;

                case '':
                case 'ally':
                default:
                    $t = isset($_GET['t']) ? (int) $_GET['t'] : NULL;

                    if ($t != 1 && $t != 2 && $t != 3) {
                        $t = 1;
                    }

                    if ($_POST) {
                        $_POST['owner_range'] = isset($_POST['owner_range']) ? stripslashes($_POST['owner_range']) : '';
                        $_POST['web'] = isset($_POST['web']) ? stripslashes($_POST['web']) : '';
                        $_POST['image'] = isset($_POST['image']) ? stripslashes($_POST['image']) : '';
                        $_POST['text'] = isset($_POST['text']) ? FunctionsLib::formatText($_POST['text']) : '';
                    }

                    if (isset($_POST['options'])) {
                        $this->_ally['alliance_owner_range'] = $this->_db->escapeValue(strip_tags($_POST['owner_range']));
                        $this->_ally['alliance_web'] = $this->_db->escapeValue(htmlspecialchars(strip_tags($_POST['web'])));
                        $this->_ally['alliance_image'] = $this->_db->escapeValue(htmlspecialchars(strip_tags($_POST['image'])));
                        $this->_ally['alliance_request_notallow'] = (int) $_POST['request_notallow'];

                        if ($this->_ally['alliance_request_notallow'] != 0 && $this->_ally['alliance_request_notallow'] != 1) {
                            FunctionsLib::redirect('game.php?page=alliance?mode=admin&edit=ally');
                        }

                        $this->Alliance_Model->updateAllianceSettings($this->_ally['alliance_id'], $this->_ally);
                    } elseif (isset($_POST['t'])) {
                        if ($t == 3) {
                            $this->_ally['alliance_request'] = $_POST['text'];


                            $this->Alliance_Model->updateAllianceRequestText($this->_ally['alliance_id'], $this->_ally['alliance_request']);

                            FunctionsLib::redirect('game.php?page=alliance&mode=admin&edit=ally&t=3');
                        } elseif ($t == 2) {
                            $this->_ally['alliance_text'] = $_POST['text'];

                            $this->Alliance_Model->updateAllianceText($this->_ally['alliance_id'], $this->_ally['alliance_text']);

                            FunctionsLib::redirect('game.php?page=alliance&mode=admin&edit=ally&t=2');
                        } else {
                            $this->_ally['alliance_description'] = $_POST['text'];

                            $this->Alliance_Model->updateAllianceDescription($this->_ally['alliance_id'], $this->_ally['alliance_description']);

                            FunctionsLib::redirect('game.php?page=alliance&mode=admin&edit=ally&t=1');
                        }
                    }

                    $this->getLang()['dpath'] = DPATH;

                    if ($t == 3) {
                        $this->getLang()['request_type'] = $this->getLang()['al_request_text'];
                    } elseif ($t == 2) {
                        $this->getLang()['request_type'] = $this->getLang()['al_inside_text'];
                    } else {
                        $this->getLang()['request_type'] = $this->getLang()['al_outside_text'];
                    }

                    if ($t == 2) {
                        $this->getLang()['text'] = $this->_ally['alliance_text'];
                    } else {
                        $this->getLang()['text'] = $this->_ally['alliance_description'];
                    }

                    if ($t == 3) {
                        $this->getLang()['text'] = $this->_ally['alliance_request'];
                    }

                    $this->getLang()['t'] = $t;
                    $this->getLang()['alliance_web'] = $this->_ally['alliance_web'];
                    $this->getLang()['alliance_image'] = $this->_ally['alliance_image'];
                    $this->getLang()['alliance_request_notallow_0'] = (($this->_ally['alliance_request_notallow'] == 1) ? ' SELECTED' : '');
                    $this->getLang()['alliance_request_notallow_1'] = (($this->_ally['alliance_request_notallow'] == 0) ? ' SELECTED' : '');
                    $this->getLang()['alliance_owner_range'] = $this->_ally['alliance_owner_range'];

                    return $this->getTemplate()->set('alliance/alliance_admin', $this->getLang());

                    break;

                case ( $edit == 'members' && $this->have_access($this->_ally['alliance_owner'], $this->permissions['admin_alliance']) === true ):

                    $rank = isset($_GET['rank']) ? (int) $_GET['rank'] : null;
                    $kick = isset($_GET['kick']) ? (int) $_GET['kick'] : null;
                    $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
                    $sort1 = isset($_GET['sort1']) ? (int) $_GET['sort1'] : null;
                    $sort2 = isset($_GET['sort2']) ? (int) $_GET['sort2'] : null;

                    $alliance_ranks = unserialize($this->_ally['alliance_ranks']);

                    if (isset($kick)) {
                        $this->have_access($this->_ally['alliance_owner'], $this->permissions['kick_users']);

                        $u = $this->Alliance_Model->getUserToBeKickedById($kick);

                        if ($u['user_ally_id'] == $this->_ally['alliance_id'] && $u['user_id'] != $this->_ally['alliance_owner']) {

                            $this->Alliance_Model->exitAlliance($u['user_id']);
                        }
                    } elseif (isset($_POST['newrang'])) {

                        $u = isset($id) ? $id : '';
                        $q = $this->Alliance_Model->getUserById($u);

                        if ((isset($alliance_ranks[$_POST['newrang'] - 1]) or $_POST['newrang'] == 0 ) && ( $q['user_id'] != $this->_ally['alliance_owner'] )) {

                            $this->Alliance_Model->updateUserRank($q['user_id'], $_POST['newrang']);
                        }
                    }

                    $listuser = $this->Alliance_Model->getAllianceMembers($this->_user['user_ally_id'], $sort1, $sort2);

                    $i = 0;
                    $r = $this->getLang();
                    $s = $this->getLang();
                    $this->getLang()['i'] = $this->_db->numRows($listuser);
                    $page_list = [];
                    $r['options'] = '';

                    while ($u = $this->_db->fetchArray($listuser)) {

                        $u['i'] = ++$i;
                        $u['points'] = FormatLib::prettyNumber($u['user_statistic_total_points']);
                        $days = floor(( time() - $u['user_onlinetime'] ) / ( 3600 * 24 ));

                        $u['user_onlinetime'] = str_replace("%s", $days, "%s d");

                        if ($this->_ally['alliance_owner'] == $u['user_id']) {

                            $ally_range = ($this->_ally['alliance_owner_range'] == '' ) ? $this->getLang()['al_founder_rank_text'] : $this->_ally['alliance_owner_range'];
                        } elseif ($u['user_ally_rank_id'] == 0 or ! $this->return_rank('name')) {

                            $ally_range = $this->getLang()['al_new_member_rank_text'];
                        } else {

                            $ally_range = $alliance_ranks[$u['user_ally_rank_id'] - 1]['name'];
                        }

                        if ($this->_ally['alliance_owner'] == $u['user_id'] or $rank == $u['user_id']) {

                            $u['actions'] = '-';
                        } elseif ($this->return_rank('kick') == 1 && $this->return_rank('administrieren') == 1 or $this->_ally['alliance_owner'] == $this->_user['user_id']) {

                            $u['actions'] = "<a href=\"game.php?page=alliance&mode=admin&edit=members&kick=" . $u['user_id'] . "\" onclick=\"javascript:return confirm('" . str_replace('%s', $u['user_name'], $this->getLang()['al_confirm_remove_member']) . "');\"><img src=\"" . DPATH . "alliance/abort.gif\" border=\"0\"></a> <a href=\"game.php?page=alliance&mode=admin&edit=members&rank=" . $u['user_id'] . "\"><img src=\"" . DPATH . "alliance/key.gif\" border=\"0\"></a>";
                        } elseif ($this->return_rank('administrieren') == 1) {

                            $u['actions'] = "<a href=\"game.php?page=alliance&mode=admin&edit=members&kick=" . $u['user_id'] . "\" onclick=\"javascript:return confirm('" . str_replace('%s', $u['user_name'], $this->getLang()['al_confirm_remove_member']) . "');\"><img src=\"" . DPATH . "alliance/abort.gif\" border=\"0\"></a> <a href=\"game.php?page=alliance&mode=admin&edit=members&rank=" . $u['user_id'] . "\"><img src=\"" . DPATH . "alliance/key.gif\" border=\"0\"></a>";
                        } else {

                            $u['actions'] = '-';
                        }

                        $u['dpath'] = DPATH;
                        $u['user_ally_register_time'] = date(FunctionsLib::readConfig('date_format_extended'), $u['user_ally_register_time']);

                        if ($rank == $u['user_id']) {
                            $r['options'] .= "<option onclick=\"document.editar_usu_rango.submit();\" value=\"0\">" . $this->getLang()['al_new_member_rank_text'] . "</option>";

                            if ($alliance_ranks != NULL) {
                                foreach ($alliance_ranks as $a => $b) {

                                    $r['options'] .= "<option onclick=\"document.editar_usu_rango.submit();\" value=\"" . ($a + 1) . "\"";

                                    if ($u['user_ally_rank_id'] - 1 == $a) {
                                        $r['options'] .= ' selected=selected';
                                    }

                                    $r['options'] .= ">{$b['name']}</option>";
                                }
                            }

                            $r['user_id'] = $u['user_id'];
                            $editar_miembros = $this->getTemplate()->set('alliance/alliance_admin_members_row_edit', $r);
                        }

                        if ($rank != $u['user_id']) {

                            $u['user_ally_range'] = $ally_range;
                        } else {

                            $u['user_ally_range'] = $editar_miembros;
                        }

                        $page_list[] = $u;
                    }

                    if ($sort2 == 1) {
                        $s = 2;
                    } elseif ($sort2 == 2) {
                        $s = 1;
                    } else {
                        $s = 1;
                    }

                    $this->getLang()['list_of_members'] = $page_list;
                    $this->getLang()['s'] = $s;

                    return $this->getTemplate()->set('alliance/alliance_admin_members_view', $this->getLang());

                    break;

                case ($edit == 'requests' && $this->have_access($this->_ally['alliance_owner'], $this->permissions['check_requests']) === true):

                    $show = isset($_GET['show']) ? (int) $_GET['show'] : null;

                    if (isset($_POST['action']) && ( $_POST['action'] == $this->getLang()['al_acept_request'] )) {

                        $this->Alliance_Model->addUserToAlliance($show, $this->_ally['alliance_id']);

                        FunctionsLib::sendMessage($show, $this->_user['user_id'], '', 3, $this->_ally['alliance_tag'], $this->getLang()['al_you_was_acceted'] . $this->_ally['alliance_name'], $this->getLang()['al_hi_the_alliance'] . $this->_ally['alliance_name'] . $this->getLang()['al_has_accepted'] . $_POST['text']);

                        FunctionsLib::redirect('game.php?page=alliance&mode=admin&edit=ally');
                    } elseif (isset($_POST['action']) && ( $_POST['action'] == $this->getLang()['al_decline_request'] ) && $_POST['action'] != '') {

                        $this->Alliance_Model->removeUserFromAlliance($show);

                        FunctionsLib::sendMessage($show, $this->_user['user_id'], '', 3, $this->_ally['alliance_tag'], $this->getLang()['al_you_was_declined'] . $this->_ally['alliance_name'], $this->getLang()['al_hi_the_alliance'] . $this->_ally['alliance_name'] . $this->getLang()['al_has_declined'] . $_POST['text']);

                        FunctionsLib::redirect('game.php?page=alliance&mode=admin&edit=ally');
                    }

                    $i = 0;
                    $query = $this->Alliance_Model->getAllianceRequests($this->_ally['alliance_id']);

                    $s = [];
                    $parse['list_of_requests'] = [];
                    $parse['request'] = '';
                    $parse['no_requests'] = '';

                    if ($query->num_rows > 0) {

                        while ($r = $this->_db->fetchArray($query)) {

                            if (isset($show) && $r['user_id'] == $show) {

                                $s[$show]['username'] = $r['user_name'];
                                $s[$show]['ally_request_text'] = nl2br($r['user_ally_request_text']);
                                $s[$show]['id'] = $r['user_id'];
                            }

                            $r['username'] = $r['user_name'];
                            $r['id'] = $r['user_id'];

                            $r['time'] = date(
                                FunctionsLib::readConfig('date_format_extended'), $r['user_ally_register_time']
                            );

                            $parse['list_of_requests'][] = $r;

                            $i++;
                        }
                    }

                    if (count($parse['list_of_requests']) == 0) {

                        $parse['no_requests'] = "<tr><th colspan=2>" . $this->getLang()['al_no_requests'] . "</th></tr>";
                    }

                    if (isset($show) && $show != 0 && $query->num_rows != 0) {

                        $s[$show]['Request_from'] = str_replace(
                            '%s', $s[$show]['username'], $this->getLang()['al_request_from']
                        );
                        $parse['request'] = $this->getTemplate()->set(
                            'alliance/alliance_admin_request_form', array_merge($s[$show], $this->getLang())
                        );
                    }

                    $parse['ally_tag'] = $this->_ally['alliance_tag'];
                    $parse['There_is_hanging_request'] = str_replace('%n', $i, $this->getLang()['al_no_request_pending']);

                    return $this->getTemplate()->set(
                            'alliance/alliance_admin_request_view', $parse
                    );
                    break;

                case ( $edit == 'name' && $this->have_access($this->_ally['alliance_owner'], $this->permissions['admin_alliance']) === true ):

                    $alliance_name = '';

                    if ($_POST) {
                        $alliance_name = $this->validateName($_POST['nametag']);

                        $this->Alliance_Model->updateAllianceName($this->_ally['alliance_id'], $alliance_name);
                    }

                    $parse['caso'] = ( $alliance_name == '' ) ? str_replace('%s', $this->_ally['alliance_name'], $this->getLang()['al_change_title']) : str_replace('%s', $alliance_name, $this->getLang()['al_change_title']);
                    $parse['caso_titulo'] = $this->getLang()['al_new_name'];

                    return $this->getTemplate()->set('alliance/alliance_admin_rename', $parse);

                    break;

                case ( $edit == 'tag' && $this->have_access($this->_ally['alliance_owner'], $this->permissions['admin_alliance']) === true ):

                    $alliance_tag = '';

                    if ($_POST) {
                        $alliance_tag = $this->validateTag($_POST['nametag']);

                        $this->Alliance_Model->updateAllianceTag($this->_ally['alliance_id'], $alliance_tag);
                    }

                    $parse['caso'] = ( $alliance_tag == '' ) ? str_replace('%s', $this->_ally['alliance_tag'], $this->getLang()['al_change_title']) : str_replace('%s', $alliance_tag, $this->getLang()['al_change_title']);
                    $parse['caso_titulo'] = $this->getLang()['al_new_tag'];

                    return $this->getTemplate()->set('alliance/alliance_admin_rename', $parse);

                    break;

                case ( $edit == 'exit' && $this->have_access($this->_ally['alliance_owner'], $this->permissions['disolve_alliance']) === true ):

                    $this->Alliance_Model->deleteAlliance($this->_ally['alliance_id']);

                    FunctionsLib::redirect('game.php?page=alliance');

                    break;

                case ( $edit == 'transfer' && $this->have_access($this->_ally['alliance_owner'], $this->permissions['admin_alliance']) === true ):

                    $alliance_ranks = unserialize($this->_ally['alliance_ranks']);

                    if (isset($_POST['newleader'])) {

                        $this->Alliance_Model->transferAlliance($this->_user['user_ally_id'], $this->_user['user_id'], $_POST['newleader']);

                        FunctionsLib::redirect('game.php?page=alliance');
                    }

                    $parse['list_of_members'] = [];

                    if ($this->_ally['alliance_owner'] != $this->_user['user_id']) {
                        FunctionsLib::redirect('game.php?page=alliance');
                    } else {
                        $listuser = $this->Alliance_Model->getAllianceMembersById($this->_user['user_ally_id']);
                        $righthand = $this->getLang();
                        $righthand['righthand'] = '';

                        while ($u = $this->_db->fetchArray($listuser)) {
                            if ($this->_ally['alliance_owner'] != $u['user_id']) {
                                if ($u['user_ally_rank_id'] != 0) {
                                    if ($alliance_ranks[$u['user_ally_rank_id'] - 1]['rechtehand'] == 1) {
                                        $righthand['righthand'] .= "\n<option value=\"" . $u['user_id'] . "\"";
                                        $righthand['righthand'] .= ">";
                                        $righthand['righthand'] .= "" . $u['user_name'];
                                        $righthand['righthand'] .= "&nbsp;[" . $alliance_ranks[$u['user_ally_rank_id'] - 1]['name'];
                                        $righthand['righthand'] .= "]&nbsp;&nbsp;</option>";
                                    }
                                }
                            }
                            $righthand['dpath'] = DPATH;
                        }

                        $parse['list_of_members'][] = $righthand;

                        return $this->getTemplate()->set('alliance/alliance_admin_transfer_view', $parse);
                    }

                    break;
            }
        }
    }

    /**
     * method image_block
     * param $alliance_description
     * return shows the description block, if any
     */
    private function description_block($alliance_description)
    {
        if ($alliance_description == '') {
            $alliance_description = $this->getLang()['al_description_message'];
        } else {
            $alliance_description = nl2br($this->bbcode->bbCode($alliance_description)) . '</th></tr>';
        }

        return '<tr><th colspan="2" height="100px">' . $alliance_description . '</th></tr>';
    }

    /**
     * method image_block
     * param $alliance_web
     * return shows the web block, if any
     */
    private function web_block($alliance_web)
    {
        if ($alliance_web != '') {
            $alliance_web = FunctionsLib::prepUrl($alliance_web);
            $alliance_web = FunctionsLib::setUrl($alliance_web, '', $alliance_web, 'target="_blank"');
        } else {
            $alliance_web = '-';
        }

        return '<tr><th>' . $this->getLang()['al_web_text'] . '</th><th>' . $alliance_web . '</th></tr>';
    }

    /**
     * method image_block
     * param $ally_id
     * param $alliance_request
     * return shows the request block, if any
     */
    private function request_block($ally_id, $alliance_request)
    {
        if (!$this->_user['user_ally_id'] && !$this->_user['user_ally_request'] && !$alliance_request) {

            $url = 'game.php?page=alliance&mode=apply&allyid=' . $ally_id;
            $url = FunctionsLib::setUrl($url, $this->getLang()['al_click_to_send_request'], $this->getLang()['al_click_to_send_request']);

            return '<tr><th>' . $this->getLang()['al_request'] . '</th><th>' . $url . '</th></tr>';
        }

        return '';
    }

    /**
     * method message_box
     * param $title
     * param $message
     * param $goto
     * param $button
     * param $two_lines
     * return shows a special message box with actions & buttons
     */
    private function messageBox($title, $message, $goto = '', $button = ' ok ', $two_lines = false)
    {
        $parse['goto'] = $goto;
        $parse['title'] = $title;
        $parse['message'] = $message;
        $parse['button'] = $button;
        $template = ( $two_lines ) ? 'alliance_message_box_row_two' : 'alliance_message_box_row_one';

        $parse['message_box_row'] = $this->getTemplate()->set('alliance/' . $template, $parse);

        return $this->getTemplate()->set('alliance/alliance_message_box', $parse);
    }

    /**
     * method return_rank
     * param $rank_type
     * return returns the rank permission
     */
    private function return_rank($rank_type)
    {
        $alliance_ranks = unserialize($this->_ally['alliance_ranks']);

        return ((isset($alliance_ranks[$this->_user['user_ally_rank_id'] - 1][$rank_type]) && $alliance_ranks[$this->_user['user_ally_rank_id'] - 1][$rank_type] == 1 ) or $this->_ally['alliance_owner'] == $this->_user['user_id']);
    }

    /**
     * method inactive_time
     * param $time
     * return the inactivity status
     */
    private function inactive_time($time)
    {
        if ($time + 60 * 10 >= time()) {
            return '"lime">' . $this->getLang()['online'] . '<';
        } elseif ($time + 60 * 20 >= time()) {
            return '"yellow">' . $this->getLang()['minutes'] . '<';
        } else {
            return '"red">' . $this->getLang()['offline'] . '<';
        }
    }

    /**
     * method have_access
     * param $alliance_owner
     * param $permission
     * return checks if the user is allowed to access a section
     */
    private function have_access($alliance_owner, $permission)
    {
        if ($alliance_owner != $this->_user['user_id'] && !$permission) {
            FunctionsLib::redirect('game.php?page=alliance');
        } else {
            return true;
        }
    }

    /**
     * method check_tag
     * param $alliance_tag
     * return the validated the ally tag
     */
    private function validateTag($alliance_tag)
    {
        $alliance_tag = trim($alliance_tag);
        $alliance_tag = htmlspecialchars_decode($alliance_tag, ENT_QUOTES);

        if ($alliance_tag == '' OR is_null($alliance_tag) OR ( strlen($alliance_tag) < 3 ) OR ( strlen($alliance_tag) > 8 )) {
            FunctionsLib::message($this->getLang()['al_tag_required'], "game.php?page=alliance&mode=make", 2);
            exit;
        }

        $check_tag = $this->Alliance_Model->checkAllianceTag($alliance_tag);

        if ($check_tag) {
            FunctionsLib::message(str_replace('%s', $alliance_tag, $this->getLang()['al_tag_already_exists']), "game.php?page=alliance&mode=make", 2);
            exit;
        }

        return $alliance_tag;
    }

    /**
     * method check_name
     * param $alliance_name
     * return the validated the ally name
     */
    private function validateName($alliance_name)
    {
        $alliance_name = trim($alliance_name);
        $alliance_name = htmlspecialchars_decode($alliance_name, ENT_QUOTES);

        if ($alliance_name == '' OR is_null($alliance_name) OR ( strlen($alliance_name) < 3 ) OR ( strlen($alliance_name) > 30 )) {
            FunctionsLib::message($this->getLang()['al_name_required'], "game.php?page=alliance&mode=make", 2);
            exit;
        }

        $check_name = $this->Alliance_Model->checkAllianceName($alliance_name);

        if ($check_name) {
            FunctionsLib::message(str_replace('%s', $alliance_name, $this->getLang()['al_name_already_exists']), "game.php?page=alliance&mode=make", 2);
            exit;
        }

        return $alliance_name;
    }

    /**
     * method get_permissions
     * param
     * return the current user permissions
     */
    private function setPermissions()
    {
        if ($this->_user['user_ally_id'] != 0 && $this->_user['user_ally_request'] == 0) {
            // GET ALLIANCE DATA

            if (!isset($this->_ally['alliance_id']) or $this->_ally['alliance_id'] <= 0) {
                $this->_ally = $this->Alliance_Model->getAllianceDataById($this->_user['user_ally_id']);
            }

            // CURRENT PERMISSIONS LIST
            $permissions_types = array(
                'see_connected_users' => 'onlinestatus',
                'see_users_list' => 'memberlist',
                'create_circular' => 'mails',
                'kick_users' => 'kick',
                'right_hand' => 'rechtehand',
                'disolve_alliance' => 'delete',
                'see_requests' => 'bewerbungen',
                'check_requests' => 'bewerbungenbearbeiten',
                'admin_alliance' => 'administrieren'
            );

            // BUILD THE PERMISSIONS ARRAY
            foreach ($permissions_types as $permission => $permission_id) {
                $this->permissions[$permission] = $this->return_rank($permission_id);
            }
        }
    }
}

/* end of alliance.php */
