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
use application\core\Database;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;

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

    private $_current_user;
    private $_lang;
    private $bbcode;
    private $_mode;
    private $_ally;
    private $_permissions;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        parent::loadModel('game/alliance');
        
        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        // DEFAULT VALUES
        $this->_db              = new Database();
        $this->_current_user    = $this->getUserData();
        $this->_lang            = $this->getLang();
        $this->bbcode           = FunctionsLib::loadLibrary('BBCodeLib');
        $this->_ally            = '';
        $this->_permissions     = array();

        // SOME REQUIRED PATHS
        $this->_lang['dpath']       = DPATH;
        $this->_lang['img_path']    = IMG_PATH;
        $this->_lang['js_path']     = JS_PATH;

        // SET THE PERMISSIONS
        $this->setPermissions();

        // BUILD THE ALLIANCE PAGE
        $this->buildPage();
    }

    /**
     * method __destruct
     * param
     * return close db connection
     */
    public function __destruct()
    {
        $this->_db->closeConnection();
    }

    /**
     * method build_page
     * param
     * return main method, loads everything
     */
    private function buildPage()
    {
        switch (( isset($_GET['mode']) ? $_GET['mode'] : null)) {
            ##############################################################################################
            # IN THIS CASE THE USER IS ONLY VISITING THE ALLIANCE
            ##############################################################################################
            case 'ainfo':

                $result_page = $this->ally_info();

                break;

            ##############################################################################################
            # IN THIS CASE THE USER IS NOT IN AN ALLIANCE
            ##############################################################################################
            case 'make':

                $result_page = $this->ally_make();

                break;

            case 'search':

                $result_page = $this->ally_search();

                break;

            case 'apply':

                $result_page = $this->ally_apply();

                break;

            ##############################################################################################
            # WHEN IS ALREADY IN AN ALLIANCE
            ##############################################################################################
            case 'exit':

                $result_page = $this->ally_exit();

                break;

            case 'memberslist':

                $result_page = $this->ally_members_list();

                break;

            case 'circular':

                $result_page = $this->ally_circular_message();

                break;

            case 'admin':

                $result_page = $this->ally_admin();

                break;

            ##############################################################################################
            # DEFAULT SECTION, WITH OR WITHOUT ALLIANCE
            ##############################################################################################
            default:

                $result_page = $this->ally_main();

                break;
        }

        ##############################################################################################
        # SHOW THE FINAL PAGE
        ##############################################################################################
        parent::$page->display($result_page);
    }

    /**
     * method ally_info
     * param
     * return the info page for a visitor
     */
    private function ally_info()
    {
        // GET ALLY ID
        $ally_id    = isset($_GET['allyid']) ? (int) $_GET['allyid'] : NULL;
        $id         = isset($_GET['id']) ? (int) $_GET['id'] : NULL;

        // VALIDATE AND GET ALLIANCE DATA
        if (is_numeric($ally_id) && $ally_id != 0) {
            $alliance_data = $this->_db->queryFetch(
                "SELECT a.`alliance_id`,
                        a.`alliance_image`,
                        a.`alliance_name`,
                        a.`alliance_tag`,
                        a.`alliance_description`,
                        a.`alliance_web`,
                        a.`alliance_request_notallow`,
                    (SELECT COUNT(user_id) AS `ally_members` 
                        FROM `" . USERS . "` 
                        WHERE `user_ally_id` = a.`alliance_id`) AS `ally_members`
                FROM `" . ALLIANCE . "` AS a
                WHERE a.`alliance_id` = '{$ally_id}'
                LIMIT 1;"
            );
        }

        // LET'S GET OUT OF HERE IF WE DIDN'T GET SOMETHING
        if (!$alliance_data) {
            FunctionsLib::redirect('game.php?page=alliance');
        }

        // PUT EACH QUERY FIELD INTO A VARIABLE WITH THE SAME NAME
        extract($alliance_data);

        // PARSE PAGE WITH THE PASSED VALUES
        return parent::$page->parseTemplate(
                        parent::$page->getTemplate('alliance/alliance_ainfo'), $this->_lang +
                        [
                            'alliance_image' => $this->image_block($alliance_image),
                            'alliance_tag' => $alliance_tag,
                            'alliance_name' => $alliance_name,
                            'ally_member_scount' => $ally_members,
                            'alliance_description' => $this->description_block($alliance_description),
                            'alliance_web' => $this->web_block($alliance_web),
                            'alliance_request' => $this->request_block($alliance_id, $alliance_request_notallow)
                        ]
        );
    }

    /**
     * method ally_make
     * param
     * return the create page for someone without an alliance
     */
    private function ally_make()
    {
        if ($this->_current_user['user_ally_id'] == 0 && $this->_current_user['user_ally_request'] == 0) {
            if (isset($_GET['yes']) && $_GET['yes'] == 1 && $_POST) {
                $alliance_tag = $this->check_tag($_POST['atag']);
                $alliance_name = $this->check_name($_POST['aname']);

                $this->_db->query("INSERT INTO " . ALLIANCE . " SET
                                                                            `alliance_name`='" . $alliance_name . "',
                                                                            `alliance_tag`='" . $alliance_tag . "' ,
                                                                            `alliance_owner`='" . (int) $this->_current_user['user_id'] . "',
                                                                            `alliance_owner_range` = '" . $this->_lang['al_alliance_founder_rank'] . "',
                                                                            `alliance_register_time`='" . time() . "'");

                $new_ally_id = $this->_db->insertId();

                $this->_db->query("INSERT INTO " . ALLIANCE_STATISTICS . " SET
                                                                            `alliance_statistic_alliance_id`='" . $new_ally_id . "'");

                $this->_db->query("UPDATE " . USERS . " SET
                                                                            `user_ally_id`='" . $new_ally_id . "',
                                                                            `user_ally_register_time`='" . time() . "'
                                                                            WHERE `user_id`='" . (int) $this->_current_user['user_id'] . "'");

                $message = str_replace(array('%s', '%d'), array($alliance_name, $alliance_tag), $this->_lang['al_created']);
                $page = $this->message_box($message, $message . "<br/><br/>", 'game.php?page=alliance', $this->_lang['al_continue']);
            } else {
                $page = parent::$page->parseTemplate(parent::$page->getTemplate('alliance/alliance_make'), $this->_lang);
            }

            return $page;
        }
    }

    /**
     * method ally_search
     * param
     * return the search page for someone without an alliance
     */
    private function ally_search()
    {
        if ($this->_current_user['user_ally_id'] == 0 && $this->_current_user['user_ally_request'] == 0) {

            $parse = $this->_lang;
            $page = parent::$page->parseTemplate(
                    parent::$page->getTemplate('alliance/alliance_searchform'), $parse
            );
            $parse['result'] = '';

            if ($_POST) {
                $search = $this->_db->query(
                        "SELECT a.*,
                    (SELECT COUNT(user_id) AS `ally_members` 
                        FROM `" . USERS . "` 
                        WHERE `user_ally_id` = a.`alliance_id`) AS `ally_members`
                FROM " . ALLIANCE . " AS a
                WHERE a.alliance_name LIKE '%" . $this->_db->escapeValue($_POST['searchtext']) . "%' OR
                        a.alliance_tag LIKE '%" . $this->_db->escapeValue($_POST['searchtext']) . "%' LIMIT 30"
                );

                if ($this->_db->numRows($search) != 0) {

                    while ($s = $this->_db->fetchArray($search)) {

                        $search_data = [];
                        $search_data['ally_tag'] = "<a href=\"game.php?page=alliance&mode=apply&allyid=" . $s['alliance_id'] . "\">" . $s['alliance_tag'] . "</a>";
                        $search_data['alliance_name'] = $s['alliance_name'];
                        $search_data['ally_members'] = $s['ally_members'];

                        $parse['result'] .= parent::$page->parseTemplate(
                                parent::$page->getTemplate('alliance/alliance_searchresult_row'), $search_data
                        );
                    }

                    $page .= parent::$page->parseTemplate(
                            parent::$page->getTemplate('alliance/alliance_searchresult_table'), $parse
                    );
                }
            }
            return $page;
        }
    }

    /**
     * method ally_apply
     * param
     * return the apply page for someone without an alliance
     */
    private function ally_apply()
    {
        $parse = $this->_lang;

        if ($this->_current_user['user_ally_id'] == 0 && $this->_current_user['user_ally_request'] == 0) {
            if ($_GET['allyid'] != NULL) {
                $alianza = $this->_db->queryFetch(
                        "SELECT *
                    FROM " . ALLIANCE . "
                    WHERE alliance_id = '" . (int) $_GET['allyid'] . "'"
                );
            }

            if ($alianza['alliance_request_notallow'] == 1) {

                FunctionsLib::message($this->_lang['al_alliance_closed'], "game.php?page=alliance", 2);
            } else {

                if (!is_numeric($_GET['allyid']) or ! $_GET['allyid'] or $this->_current_user['user_ally_request'] != 0 or $this->_current_user['user_ally_id'] != 0) {

                    FunctionsLib::redirect('game.php?page=alliance');
                }

                $allyrow = $this->_db->queryFetch(
                        "SELECT alliance_id, alliance_tag, alliance_request
                    FROM " . ALLIANCE . "
                    WHERE alliance_id = '" . (int) $_GET['allyid'] . "'"
                );

                if (!$allyrow) {
                    FunctionsLib::redirect('game.php?page=alliance');
                }

                extract($allyrow);

                if (isset($_POST['enviar']) && ( $_POST['enviar'] == $this->_lang['al_applyform_send'] )) {
                    $this->_db->query(
                            "UPDATE " . USERS . " SET
                        `user_ally_request` = '" . (int) $alliance_id . "' ,
                        `user_ally_request_text` = '" . $_POST['text'] . "',
                        `user_ally_register_time` = '" . time() . "'
                        WHERE `user_id`='" . $this->_current_user['user_id'] . "'"
                    );

                    FunctionsLib::message($this->_lang['al_request_confirmation_message'], "game.php?page=alliance", 2);
                } else {
                    $text_apply = ( $alliance_request ) ? $alliance_request : $this->_lang['al_default_request_text'];
                }

                $parse['allyid'] = (int) $_GET['allyid'];
                $parse['chars_count'] = strlen($text_apply);
                $parse['text_apply'] = $text_apply;
                $parse['Write_to_alliance'] = str_replace('%s', $alliance_tag, $this->_lang['al_write_request']);

                return parent::$page->parseTemplate(parent::$page->getTemplate('alliance/alliance_applyform'), $parse);
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
        $parse          = $this->_lang;

        ##############################################################################################
        # DEFAULT PART WITHOUT ALLIANCE
        ##############################################################################################
        if ($this->_current_user['user_ally_id'] == 0 && $this->_current_user['user_ally_request'] != 0) {
            $allyquery = $this->_db->queryFetch("SELECT `alliance_tag`
                                                                                                        FROM " . ALLIANCE . "
                                                                                                        WHERE alliance_id = '" . (int) $this->_current_user['user_ally_request'] . "' ORDER BY `alliance_id`");

            extract($allyquery);

            if (isset($_POST['bcancel'])) {
                $this->_db->query("UPDATE " . USERS . "
                                                                        SET `user_ally_request` = '0'
                                                                        WHERE `user_id`= " . (int) $this->_current_user['user_id']);

                $this->_lang['request_text'] = str_replace('%s', $alliance_tag, $this->_lang['al_request_deleted']);
                $this->_lang['button_text'] = $this->_lang['al_continue'];
            } else {
                $this->_lang['request_text'] = str_replace('%s', $alliance_tag, $this->_lang['al_request_wait_message']);
                $this->_lang['button_text'] = $this->_lang['al_delete_request'];
            }

            return parent::$page->parseTemplate(parent::$page->getTemplate('alliance/alliance_apply_waitform'), $this->_lang);
        } elseif ($this->_current_user['user_ally_id'] == 0 && $this->_current_user['user_ally_request'] == 0) {
            return parent::$page->parseTemplate(parent::$page->getTemplate('alliance/alliance_defaultmenu'), $this->_lang);
        }

        ##############################################################################################
        # DEFAULT PART WITH ALLIANCE
        ##############################################################################################
        if ($this->_current_user['user_ally_id'] != 0 && $this->_current_user['user_ally_request'] == 0) {

            $alliance_ranks = unserialize($this->_ally['alliance_ranks']);
            
            // IMAGE
            if ($this->_ally['alliance_ranks'] != '') {
                $parse['alliance_image'] = $this->image_block($this->_ally['alliance_image']);
                $this->_ally['alliance_ranks'] = parent::$page->parseTemplate(parent::$page->getTemplate('alliance/alliance_image_row'), $parse);
            }

            // RANKS
            if ($this->_ally['alliance_owner'] == $this->_current_user['user_id']) {
                $range = ( $this->_ally['alliance_owner_range'] != '' ) ? $this->_ally['alliance_owner_range'] : $this->_lang['al_founder_rank_text'];
            } elseif ($this->_current_user['user_ally_rank_id'] != 0 && isset($alliance_ranks[$this->_current_user['user_ally_rank_id'] - 1]['name'])) {
                $range = $alliance_ranks[$this->_current_user['user_ally_rank_id'] - 1]['name'];
            } else {
                $range = $this->_lang['al_new_member_rank_text'];
            }

            // MEMBER LIST
            if ($this->_ally['alliance_owner'] == $this->_current_user['user_id'] or $alliance_ranks[$this->_current_user['user_ally_rank_id'] - 1]['memberlist'] != 0) {
                $this->_lang['members_list'] = " (<a href=\"game.php?page=alliance&mode=memberslist\">" . $this->_lang['al_user_list'] . "</a>)";
            } else {
                $this->_lang['members_list'] = '';
            }

            // ADMIN ALLIANCE
            if ($this->_ally['alliance_owner'] == $this->_current_user['user_id'] or $alliance_ranks[$this->_current_user['user_ally_rank_id'] - 1]['administrieren'] != 0) {
                $this->_lang['alliance_admin'] = " (<a href=\"game.php?page=alliance&mode=admin&edit=ally\">" . $this->_lang['al_manage_alliance'] . "</a>)";
            } else {
                $this->_lang['alliance_admin'] = '';
            }

            // CIRCULAR MESSAGE
            if ($this->_ally['alliance_owner'] == $this->_current_user['user_id'] or $alliance_ranks[$this->_current_user['user_ally_rank_id'] - 1]['mails'] != 0) {
                $this->_lang['send_circular_mail'] = parent::$page->parseTemplate(parent::$page->getTemplate('alliance/alliance_circular_row'), $parse);
            } else {
                $this->_lang['send_circular_mail'] = '';
            }

            // REQUESTS
            $request_count = $this->_db->numRows($this->_db->query("SELECT `user_id`
                                                                                                                                                    FROM `" . USERS . "`
                                                                                                                                                    WHERE `user_ally_request` = '" . (int) $this->_ally['alliance_id'] . "'"));

            if ($request_count != 0) {
                if ($this->_ally['alliance_owner'] == $this->_current_user['user_id'] or $alliance_ranks[$this->_current_user['user_ally_rank_id'] - 1]['bewerbungen'] != 0) {
                    $parse['request_count'] = $request_count;
                    $this->_lang['requests'] = parent::$page->parseTemplate(parent::$page->getTemplate('alliance/alliance_requests_row'), $parse);
                }
            }
            // EXIT ALLIANCE
            if ($this->_ally['alliance_owner'] != $this->_current_user['user_id']) {
                $this->_lang['alliance_owner'] = parent::$page->parseTemplate(parent::$page->getTemplate('alliance/alliance_abandon_alliance'), $parse);
            } else {
                $this->_lang['alliance_owner'] = '';
            }

            // GENERAL INFORMATION
            $this->_lang['alliance_image'] = ( $this->_ally['alliance_image'] != '' ) ? "<tr><th colspan=2><img src=\"{$this->_ally['alliance_image']}\"></td></tr>" : '';
            $this->_lang['range'] = $range;
            $this->_lang['alliance_description'] = $this->description_block($this->_ally['alliance_description']);
            $this->_lang['alliance_text'] = nl2br($this->bbcode->bbCode($this->_ally['alliance_text']));
            $this->_lang['alliance_web'] = $this->web_block($this->_ally['alliance_web']);
            $this->_lang['ally_tag'] = $this->_ally['alliance_tag'];
            $this->_lang['ally_members'] = $this->_ally['ally_members'];
            $this->_lang['alliance_name'] = $this->_ally['alliance_name'];

            return parent::$page->parseTemplate(parent::$page->getTemplate('alliance/alliance_frontpage'), $this->_lang);
        }
    }

    /**
     * method ally_exit
     * param
     * return the exit page for someone with an alliance
     */
    private function ally_exit()
    {
        $parse = $this->_lang;

        if ($this->_current_user['user_ally_id'] != 0 && $this->_current_user['user_ally_request'] == 0) {
            if ($this->_ally['alliance_owner'] == $this->_current_user['user_id']) {
                FunctionsLib::message($this->_lang['al_founder_cant_leave_alliance'], "game.php?page=alliance", 2);
            }

            if (isset($_GET['yes']) && $_GET['yes'] == 1) {
                $this->_db->query("UPDATE `" . USERS . "` SET
                                                                            `user_ally_id` = 0,
                                                                            `user_ally_rank_id` = 0
                                                                            WHERE `user_id`='" . $this->_current_user['user_id'] . "'");

                $this->_lang['Go_out_welldone'] = str_replace("%s", $this->_ally['alliance_name'], $this->_lang['al_leave_sucess']);
                $page = $this->message_box(
                    $this->_lang['Go_out_welldone'],
                    "<br>",
                    "game.php?page=alliance",
                    $this->_lang['al_continue']
                );
            } else {
                $this->_lang['Want_go_out'] = str_replace("%s", $this->_ally['alliance_name'], $this->_lang['al_do_you_really_want_to_go_out']);
                $page = $this->message_box(
                    $this->_lang['Want_go_out'],
                    "<br>",
                    "game.php?page=alliance&mode=exit&yes=1",
                    $this->_lang['al_go_out_yes']
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
        $parse = $this->_lang;
        $alliance_ranks = unserialize($this->_ally['alliance_ranks']);

        if ($this->_current_user['user_ally_id'] != 0 && $this->_current_user['user_ally_request'] == 0 && $this->have_access($this->_ally['alliance_owner'], $this->permissions['see_users_list']) === true) {
            $sort1 = isset($_GET['sort1']) ? (int) $_GET['sort1'] : NULL; // ORDEN 1
            $sort2 = isset($_GET['sort2']) ? (int) $_GET['sort2'] : NULL; // ORDEN 2

            if ($sort2) {
                $sort = $this->return_sort($sort1, $sort2);
            } else {
                $sort = '';
            }

            $listuser = $this->_db->query(
                    "SELECT u.user_id, 
                                u.user_onlinetime, 
                                u.user_name, 
                                u.user_galaxy, 
                                u.user_system, 
                                u.user_planet, 
                                u.user_ally_register_time, 
                                u.user_ally_rank_id,
                                s.user_statistic_total_points
                        FROM `" . USERS . "` AS u
                        INNER JOIN `" . USERS_STATISTICS . "`AS s ON u.user_id = s.user_statistic_user_id
                        WHERE u.user_ally_id='" . $this->_current_user['user_ally_id'] . "'" . $sort
            );

            $i = 0;
            $page_list = '';

            while ($u = $this->_db->fetchArray($listuser)) {
                $i++;
                $u['i'] = $i;

                if ($this->permissions['see_connected_users']) {
                    $u['user_onlinetime'] = $this->inactive_time($u['user_onlinetime']);
                } else {
                    $u['user_onlinetime'] = '"">-<';
                }

                if ($this->_ally['alliance_owner'] == $u['user_id']) {
                    $u['user_ally_range'] = ( $this->_ally['alliance_owner_range'] == '' ) ? $this->_lang['al_founder_rank_text'] : $this->_ally['alliance_owner_range'];
                } elseif ($u['user_ally_rank_id'] == 0) {
                    $u['user_ally_range'] = $this->_lang['al_new_member_rank_text'];
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

                $page_list .= parent::$page->parseTemplate(parent::$page->getTemplate('alliance/alliance_memberslist_row'), $u);
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
            $parse['list'] = $page_list;

            return parent::$page->parseTemplate(parent::$page->getTemplate('alliance/alliance_memberslist_table'), $parse);
        }
    }

    /**
     * method ally_circular_message
     * param
     * return the circular message page for someone with an alliance
     */
    private function ally_circular_message()
    {
        $parse = $this->_lang;

        if ($this->_current_user['user_ally_id'] != 0 && $this->_current_user['user_ally_request'] == 0 && $this->have_access($this->_ally['alliance_owner'], $this->permissions['create_circular']) === true) {
            if (isset($_GET['sendmail']) && $_GET['sendmail'] == 1) {
                $list = '';
                $_POST['r'] = (int) $_POST['r'];
                $_POST['text'] = $_POST['text'];

                if ($_POST['r'] == 0) {
                    $sq = $this->_db->query("SELECT `user_id`, `user_name`
                                                                                            FROM `" . USERS . "`
                                                                                            WHERE `user_ally_id` = '" . $this->_current_user['user_ally_id'] . "'");
                } else {
                    $sq = $this->_db->query("SELECT `user_id`, `user_name`
                                                                                                    FROM `" . USERS . "`
                                                                                                    WHERE `user_ally_id` = '" . $this->_current_user['user_ally_id'] . "' AND
                                                                                                                    `user_ally_rank_id` = '" . (int) $_POST['r'] . "'");
                }

                while ($u = $this->_db->fetchArray($sq)) {
                    FunctionsLib::sendMessage($u['user_id'], $this->_current_user['user_id'], '', 3, $this->_ally['alliance_tag'], $this->_current_user['user_name'], $_POST['text']);

                    $list .= "<br>{$u['user_name']} ";
                }

                $page = $this->message_box($this->_lang['al_circular_sended'], $list, "game.php?page=alliance", $this->_lang['al_continue'], true);

                return $page;
            }

            $this->_lang['r_list'] = "<option value=\"0\">" . $this->_lang['al_all_players'] . "</option>";

            $alliance_ranks = unserialize($this->_ally['alliance_ranks']);

            if ($alliance_ranks) {
                foreach ($alliance_ranks as $id => $array) {
                    $this->_lang['r_list'] .= "<option value=\"" . ( $id + 1 ) . "\">" . $array['name'] . "</option>";
                }
            }

            return parent::$page->parseTemplate(parent::$page->getTemplate('alliance/alliance_circular'), $this->_lang);
        }
    }

    /**
     * method ally_admin
     * param
     * return the admin page for someone with an alliance
     */
    private function ally_admin()
    {
        $parse = $this->_lang;

        if ($this->_current_user['user_ally_id'] != 0 && $this->_current_user['user_ally_request'] == 0) {
            $edit = ( isset($_GET['edit']) ? $_GET['edit'] : NULL );

            switch ($edit) {
                case ( $edit == 'rights' && $this->have_access($this->_ally['alliance_owner'], $this->permissions['right_hand']) === true ):

                    $d = ( isset($_GET['d']) && is_numeric($_GET['d']) && $_GET['d'] >= 0 ) ? $_GET['d'] : NULL;
                    $alliance_ranks = unserialize($this->_ally['alliance_ranks']);

                    if (!empty($_POST['newrangname'])) {
                        $name = $this->_db->escapeValue(strip_tags($_POST['newrangname']));

                        $alliance_ranks[] = array(
                            'name' => $name,
                            'mails' => 0,
                            'delete' => 0,
                            'kick' => 0,
                            'bewerbungen' => 0,
                            'administrieren' => 0,
                            'bewerbungenbearbeiten' => 0,
                            'memberlist' => 0,
                            'onlinestatus' => 0,
                            'rechtehand' => 0
                        );

                        $ranks = serialize($alliance_ranks);

                        $this->_db->query("UPDATE " . ALLIANCE . " SET
                                                                                            `alliance_ranks`='" . $ranks . "'
                                                                                            WHERE `alliance_id` = " . (int) $this->_ally['alliance_id']);

                        $goto = $_SERVER['PHP_SELF'] . "?" . str_replace('&amp;', '&', $_SERVER['QUERY_STRING']);

                        FunctionsLib::redirect($goto);
                    } elseif (isset($_POST['id']) && $_POST['id'] != '' && is_array($_POST['id'])) {
                        $ally_ranks_new = array();

                        foreach ($_POST['id'] as $id) {
                            $name = $alliance_ranks[$id]['name'];
                            $ally_ranks_new[$id]['name'] = $name;
                            $ally_ranks_new[$id]['delete'] = isset($_POST['u' . $id . 'r0']) ? 1 : 0;
                            $ally_ranks_new[$id]['kick'] = ( isset($_POST['u' . $id . 'r1']) && $this->_ally['alliance_owner'] == $this->_current_user['user_id'] ) ? 1 : 0;
                            $ally_ranks_new[$id]['bewerbungen'] = isset($_POST['u' . $id . 'r2']) ? 1 : 0;
                            $ally_ranks_new[$id]['memberlist'] = isset($_POST['u' . $id . 'r3']) ? 1 : 0;
                            $ally_ranks_new[$id]['bewerbungenbearbeiten'] = isset($_POST['u' . $id . 'r4']) ? 1 : 0;
                            $ally_ranks_new[$id]['administrieren'] = isset($_POST['u' . $id . 'r5']) ? 1 : 0;
                            $ally_ranks_new[$id]['onlinestatus'] = isset($_POST['u' . $id . 'r6']) ? 1 : 0;
                            $ally_ranks_new[$id]['mails'] = isset($_POST['u' . $id . 'r7']) ? 1 : 0;
                            $ally_ranks_new[$id]['rechtehand'] = isset($_POST['u' . $id . 'r8']) ? 1 : 0;
                        }

                        $ranks = serialize($ally_ranks_new);

                        $this->_db->query("UPDATE " . ALLIANCE . " SET
                                                                                            `alliance_ranks`='" . $ranks . "'
                                                                                            WHERE `alliance_id`= " . $this->_ally['alliance_id']);

                        $goto = $_SERVER['PHP_SELF'] . "?" . str_replace('&amp;', '&', $_SERVER['QUERY_STRING']);

                        FunctionsLib::redirect($goto);
                    } elseif (isset($d) && isset($alliance_ranks[$d])) {
                        unset($alliance_ranks[$d]);

                        $this->_ally['ally_rank'] = serialize($alliance_ranks);

                        $this->_db->query("UPDATE " . ALLIANCE . " SET
                                                                                            `alliance_ranks`='" . $this->_ally['ally_rank'] . "'
                                                                                            WHERE `alliance_id` = " . $this->_ally['alliance_id'] . "");
                    }

                    $i = 0;
                    $list = '';

                    if (count($alliance_ranks) != 0 && $alliance_ranks != '') {
                        foreach ($alliance_ranks as $a => $b) {
                            if ($this->_ally['alliance_owner'] == $this->_current_user['user_id']) {
                                $r1 = "<input type=checkbox name=\"u{$a}r0\"" . (($b['delete'] == 1) ? ' checked="checked"' : '') . ">";
                            } else {
                                $r1 = "<b>-</b>";
                            }

                            $this->_lang['id'] = $a;
                            $this->_lang['r0'] = $b['name'];
                            $this->_lang['delete'] = "<a href=\"game.php?page=alliance&mode=admin&edit=rights&d={$a}\"><img src=\"" . DPATH . "alliance/abort.gif\" border=0></a>";
                            $this->_lang['a'] = $a;
                            $this->_lang['r1'] = $r1;
                            $this->_lang['r2'] = "<input type=checkbox name=\"u{$a}r1\"" . (($b['kick'] == 1) ? ' checked="checked"' : '') . ">";
                            $this->_lang['r3'] = "<input type=checkbox name=\"u{$a}r2\"" . (($b['bewerbungen'] == 1) ? ' checked="checked"' : '') . ">";
                            $this->_lang['r4'] = "<input type=checkbox name=\"u{$a}r3\"" . (($b['memberlist'] == 1) ? ' checked="checked"' : '') . ">";
                            $this->_lang['r5'] = "<input type=checkbox name=\"u{$a}r4\"" . (($b['bewerbungenbearbeiten'] == 1) ? ' checked="checked"' : '') . ">";
                            $this->_lang['r6'] = "<input type=checkbox name=\"u{$a}r5\"" . (($b['administrieren'] == 1) ? ' checked="checked"' : '') . ">";
                            $this->_lang['r7'] = "<input type=checkbox name=\"u{$a}r6\"" . (($b['onlinestatus'] == 1) ? ' checked="checked"' : '') . ">";
                            $this->_lang['r8'] = "<input type=checkbox name=\"u{$a}r7\"" . (($b['mails'] == 1) ? ' checked="checked"' : '') . ">";
                            $this->_lang['r9'] = "<input type=checkbox name=\"u{$a}r8\"" . (($b['rechtehand'] == 1) ? ' checked="checked"' : '') . ">";

                            $list .= parent::$page->parseTemplate(parent::$page->getTemplate('alliance/alliance_admin_laws_row'), $this->_lang);
                        }
                    }

                    $this->_lang['list'] = $list;
                    $this->_lang['dpath'] = DPATH;

                    return parent::$page->parseTemplate(parent::$page->getTemplate('alliance/alliance_admin_laws'), $this->_lang);

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
                        $this->_ally['alliance_owner_range']    = $this->_db->escapeValue(strip_tags($_POST['owner_range']));
                        $this->_ally['alliance_web']            = $this->_db->escapeValue(htmlspecialchars(strip_tags($_POST['web'])));
                        $this->_ally['alliance_image']          = $this->_db->escapeValue(htmlspecialchars(strip_tags($_POST['image'])));
                        $this->_ally['alliance_request_notallow'] = (int) $_POST['request_notallow'];

                        if ($this->_ally['alliance_request_notallow'] != 0 && $this->_ally['alliance_request_notallow'] != 1) {
                            FunctionsLib::redirect('game.php?page=alliance?mode=admin&edit=ally');
                        }

                        $this->_db->query("UPDATE " . ALLIANCE . " SET
                                                                                            `alliance_owner_range`='" . $this->_ally['alliance_owner_range'] . "',
                                                                                            `alliance_image`='" . $this->_ally['alliance_image'] . "',
                                                                                            `alliance_web`='" . $this->_ally['alliance_web'] . "',
                                                                                            `alliance_request_notallow`='" . $this->_ally['alliance_request_notallow'] . "'
                                                                                            WHERE `alliance_id` = '" . $this->_ally['alliance_id'] . "'");
                    } elseif (isset($_POST['t'])) {
                        if ($t == 3) {
                            $this->_ally['alliance_request'] = $_POST['text'];

                            $this->_db->query("UPDATE " . ALLIANCE . " SET
                                                                                                    `alliance_request`='" . $this->_ally['alliance_request'] . "'
                                                                                                    WHERE `alliance_id` = '" . $this->_ally['alliance_id'] . "'");

                            FunctionsLib::redirect('game.php?page=alliance&mode=admin&edit=ally&t=3');
                        } elseif ($t == 2) {
                            $this->_ally['alliance_text'] = $_POST['text'];

                            $this->_db->query("UPDATE " . ALLIANCE . " SET
                                                                                                    `alliance_text`='" . $this->_ally['alliance_text'] . "'
                                                                                                    WHERE `alliance_id` = '" . $this->_ally['alliance_id'] . "'");

                            FunctionsLib::redirect('game.php?page=alliance&mode=admin&edit=ally&t=2');
                        } else {
                            $this->_ally['alliance_description'] = $_POST['text'];

                            $this->_db->query("UPDATE " . ALLIANCE . " SET
                                                                                                    `alliance_description`='" . $this->_ally['alliance_description'] . "'
                                                                                                    WHERE `alliance_id` = '" . $this->_ally['alliance_id'] . "'");

                            FunctionsLib::redirect('game.php?page=alliance&mode=admin&edit=ally&t=1');
                        }
                    }

                    $this->_lang['dpath'] = DPATH;

                    if ($t == 3) {
                        $this->_lang['request_type'] = $this->_lang['al_request_text'];
                    } elseif ($t == 2) {
                        $this->_lang['request_type'] = $this->_lang['al_inside_text'];
                    } else {
                        $this->_lang['request_type'] = $this->_lang['al_outside_text'];
                    }

                    if ($t == 2) {
                        $this->_lang['text'] = $this->_ally['alliance_text'];
                    } else {
                        $this->_lang['text'] = $this->_ally['alliance_description'];
                    }

                    if ($t == 3) {
                        $this->_lang['text'] = $this->_ally['alliance_request'];
                    }

                    $this->_lang['t']                           = $t;
                    $this->_lang['alliance_web']                = $this->_ally['alliance_web'];
                    $this->_lang['alliance_image']              = $this->_ally['alliance_image'];
                    $this->_lang['alliance_request_notallow_0'] = (($this->_ally['alliance_request_notallow'] == 1) ? ' SELECTED' : '');
                    $this->_lang['alliance_request_notallow_1'] = (($this->_ally['alliance_request_notallow'] == 0) ? ' SELECTED' : '');
                    $this->_lang['alliance_owner_range']        = $this->_ally['alliance_owner_range'];

                    return parent::$page->parseTemplate(parent::$page->getTemplate('alliance/alliance_admin'), $this->_lang);

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

                        $u = $this->_db->queryFetch(
                                "SELECT `user_ally_id`, `user_id`
                            FROM `" . USERS . "`
                            WHERE `user_id` = '" . (int) $kick . "'
                            LIMIT 1"
                        );

                        if ($u['user_ally_id'] == $this->_ally['alliance_id'] && $u['user_id'] != $this->_ally['alliance_owner']) {

                            $this->_db->query(
                                    "UPDATE " . USERS . " SET
                                `user_ally_id`='0',
                                `user_ally_rank_id` = 0
                                WHERE `user_id`='" . (int) $u['user_id'] . "' LIMIT 1;"
                            );
                        }
                    } elseif (isset($_POST['newrang'])) {

                        $u = isset($id) ? $id : '';
                        $q = $this->_db->queryFetch(
                                "SELECT `user_id`
                            FROM " . USERS . "
                            WHERE `user_id` = '" . (int) $u . "'
                            LIMIT 1"
                        );

                        if ((isset($alliance_ranks[$_POST['newrang'] - 1]) or $_POST['newrang'] == 0 ) && ( $q['user_id'] != $this->_ally['alliance_owner'] )) {
                            $this->_db->query(
                                    "UPDATE " . USERS . " SET
                                `user_ally_rank_id` = '" . $this->_db->escapeValue($_POST['newrang']) . "'
                                WHERE `user_id`='" . $q['user_id'] . "'"
                            );
                        }
                    }

                    if ($sort2) {

                        $sort = $this->return_sort($sort1, $sort2);
                    } else {

                        $sort = '';
                    }

                    $listuser = $this->_db->query(
                            "SELECT u.user_id, 
                                u.user_onlinetime, 
                                u.user_name, 
                                u.user_galaxy, 
                                u.user_system, 
                                u.user_planet, 
                                u.user_ally_register_time, 
                                u.user_ally_rank_id, 
                                s.user_statistic_total_points
                        FROM `" . USERS . "` AS u
                        INNER JOIN `" . USERS_STATISTICS . "`AS s ON u.user_id = s.user_statistic_user_id
                        WHERE u.user_ally_id='" . $this->_current_user['user_ally_id'] . "'" . $sort
                    );

                    $i = 0;
                    $r = $this->_lang;
                    $s = $this->_lang;
                    $this->_lang['i'] = $this->_db->numRows($listuser);
                    $page_list = '';
                    $r['options'] = '';

                    while ($u = $this->_db->fetchArray($listuser)) {

                        $u['i'] = ++$i;
                        $u['points'] = FormatLib::prettyNumber($u['user_statistic_total_points']);
                        $days = floor(( time() - $u['user_onlinetime'] ) / ( 3600 * 24 ));

                        $u['user_onlinetime'] = str_replace("%s", $days, "%s d");

                        if ($this->_ally['alliance_owner'] == $u['user_id']) {

                            $ally_range = ($this->_ally['alliance_owner_range'] == '' ) ? $this->_lang['al_founder_rank_text'] : $this->_ally['alliance_owner_range'];
                        } elseif ($u['user_ally_rank_id'] == 0 or ! $this->return_rank('name')) {

                            $ally_range = $this->_lang['al_new_member_rank_text'];
                        } else {

                            $ally_range = $alliance_ranks[$u['user_ally_rank_id'] - 1]['name'];
                        }

                        if ($this->_ally['alliance_owner'] == $u['user_id'] or $rank == $u['user_id']) {

                            $u['acciones'] = '-';
                        } elseif ($this->return_rank('kick') == 1 && $this->return_rank('administrieren') == 1 or $this->_ally['alliance_owner'] == $this->_current_user['user_id']) {

                            $u['acciones'] = "<a href=\"game.php?page=alliance&mode=admin&edit=members&kick=" . $u['user_id'] . "\" onclick=\"javascript:return confirm('" . str_replace('%s', $u['user_name'], $this->_lang['al_confirm_remove_member']) . "');\"><img src=\"" . DPATH . "alliance/abort.gif\" border=\"0\"></a> <a href=\"game.php?page=alliance&mode=admin&edit=members&rank=" . $u['user_id'] . "\"><img src=\"" . DPATH . "alliance/key.gif\" border=\"0\"></a>";
                        } elseif ($this->return_rank('administrieren') == 1) {

                            $u['acciones'] = "<a href=\"game.php?page=alliance&mode=admin&edit=members&kick=" . $u['user_id'] . "\" onclick=\"javascript:return confirm('" . str_replace('%s', $u['user_name'], $this->_lang['al_confirm_remove_member']) . "');\"><img src=\"" . DPATH . "alliance/abort.gif\" border=\"0\"></a> <a href=\"game.php?page=alliance&mode=admin&edit=members&rank=" . $u['user_id'] . "\"><img src=\"" . DPATH . "alliance/key.gif\" border=\"0\"></a>";
                        } else {

                            $u['acciones'] = '-';
                        }

                        $u['dpath'] = DPATH;
                        $u['user_ally_register_time'] = date(FunctionsLib::readConfig('date_format_extended'), $u['user_ally_register_time']);

                        if ($rank == $u['user_id']) {
                            $r['options'] .= "<option onclick=\"document.editar_usu_rango.submit();\" value=\"0\">" . $this->_lang['al_new_member_rank_text'] . "</option>";

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
                            $editar_miembros = parent::$page->parseTemplate(
                                    parent::$page->getTemplate('alliance/alliance_admin_members_row_edit'), $r
                            );
                        }

                        if ($rank != $u['user_id']) {

                            $u['user_ally_range'] = $ally_range;
                        } else {

                            $u['user_ally_range'] = $editar_miembros;
                        }

                        $page_list .= parent::$page->parseTemplate(
                                parent::$page->getTemplate('alliance/alliance_admin_members_row'), $u
                        );
                    }

                    if ($sort2 == 1) {
                        $s = 2;
                    } elseif ($sort2 == 2) {
                        $s = 1;
                    } else {
                        $s = 1;
                    }

                    $this->_lang['memberslist'] = $page_list;
                    $this->_lang['s'] = $s;

                    return parent::$page->parseTemplate(parent::$page->getTemplate('alliance/alliance_admin_members_table'), $this->_lang);

                    break;

                case ($edit == 'requests' && $this->have_access($this->_ally['alliance_owner'], $this->permissions['check_requests']) === true):

                    $show = isset($_GET['show']) ? (int) $_GET['show'] : null;

                    if (isset($_POST['action']) && ( $_POST['action'] == $this->_lang['al_acept_request'] )) {

                        $this->_db->query(
                            "UPDATE " . USERS . " SET
                            user_ally_request_text = '',
                            user_ally_request = '0',
                            user_ally_id = '" . $this->_ally['alliance_id'] . "'
                            WHERE user_id = '" . $show . "'"
                        );

                        FunctionsLib::sendMessage($show, $this->_current_user['user_id'], '', 3, $this->_ally['alliance_tag'], $this->_lang['al_you_was_acceted'] . $this->_ally['alliance_name'], $this->_lang['al_hi_the_alliance'] . $this->_ally['alliance_name'] . $this->_lang['al_has_accepted'] . $_POST['text']);

                        FunctionsLib::redirect('game.php?page=alliance&mode=admin&edit=ally');
                    } elseif (isset($_POST['action']) && ( $_POST['action'] == $this->_lang['al_decline_request'] ) && $_POST['action'] != '') {

                        $this->_db->query("UPDATE " . USERS . " SET
                                                                                            user_ally_request_text='',
                                                                                            user_ally_request='0',
                                                                                            user_ally_id='0'
                                                                                            WHERE user_id = '" . (int) $show . "'");

                        FunctionsLib::sendMessage($show, $this->_current_user['user_id'], '', 3, $this->_ally['alliance_tag'], $this->_lang['al_you_was_declined'] . $this->_ally['alliance_name'], $this->_lang['al_hi_the_alliance'] . $this->_ally['alliance_name'] . $this->_lang['al_has_declined'] . $_POST['text']);

                        FunctionsLib::redirect('game.php?page=alliance&mode=admin&edit=ally');
                    }

                    $i = 0;
                    $query = $this->_db->query(
                            "SELECT user_id, user_name, user_ally_request_text, user_ally_register_time
                        FROM " . USERS . "
                        WHERE user_ally_request = '" . $this->_ally['alliance_id'] . "'"
                    );

                    $s = [];
                    $parse['list'] = '';

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

                        $parse['list'] .= parent::$page->parseTemplate(
                                parent::$page->getTemplate('alliance/alliance_admin_request_row'), $r
                        );

                        $i++;
                    }

                    if ($parse['list'] == '') {

                        $parse['list'] = "<tr><th colspan=2>" . $this->_lang['al_no_requests'] . "</th></tr>";
                    }

                    if (isset($show) && $show != 0 && $parse['list'] != '') {

                        $s[$show]['Request_from'] = str_replace(
                                '%s', $s[$show]['username'], $this->_lang['al_request_from']
                        );
                        $parse['request'] = parent::$page->parseTemplate(
                                parent::$page->getTemplate('alliance/alliance_admin_request_form'), array_merge($s[$show], $this->_lang)
                        );
                    } else {

                        $parse['request'] = '';
                    }

                    $parse['ally_tag'] = $this->_ally['alliance_tag'];
                    $parse['There_is_hanging_request'] = str_replace('%n', $i, $this->_lang['al_no_request_pending']);

                    return parent::$page->parseTemplate(
                                    parent::$page->getTemplate('alliance/alliance_admin_request_table'), $parse
                    );

                    break;

                case ( $edit == 'name' && $this->have_access($this->_ally['alliance_owner'], $this->permissions['admin_alliance']) === true ):

                    $alliance_name = '';

                    if ($_POST) {
                        $alliance_name = $this->check_name($_POST['nametag']);

                        $this->_db->query("UPDATE " . ALLIANCE . " AS a SET
                                                                                            a.`alliance_name` = '" . $alliance_name . "',
                                                                                            WHERE a.`alliance_id` = '" . $this->_ally['alliance_id'] . "';");
                    }

                    $parse['caso'] = ( $alliance_name == '' ) ? str_replace('%s', $this->_ally['alliance_name'], $this->_lang['al_change_title']) : str_replace('%s', $alliance_name, $this->_lang['al_change_title']);
                    $parse['caso_titulo'] = $this->_lang['al_new_name'];

                    return parent::$page->parseTemplate(parent::$page->getTemplate('alliance/alliance_admin_rename'), $parse);

                    break;

                case ( $edit == 'tag' && $this->have_access($this->_ally['alliance_owner'], $this->permissions['admin_alliance']) === true ):

                    $alliance_tag = '';

                    if ($_POST) {
                        $alliance_tag = $this->check_tag($_POST['nametag']);

                        $this->_db->query("UPDATE " . ALLIANCE . " SET
                                                                                            `alliance_tag` = '" . $alliance_tag . "'
                                                                                            WHERE `alliance_id` = '" . $this->_current_user['user_ally_id'] . "';");
                    }

                    $parse['caso'] = ( $alliance_tag == '' ) ? str_replace('%s', $this->_ally['alliance_tag'], $this->_lang['al_change_title']) : str_replace('%s', $alliance_tag, $this->_lang['al_change_title']);
                    $parse['caso_titulo'] = $this->_lang['al_new_tag'];

                    return parent::$page->parseTemplate(parent::$page->getTemplate('alliance/alliance_admin_rename'), $parse);

                    break;

                case ( $edit == 'exit' && $this->have_access($this->_ally['alliance_owner'], $this->permissions['disolve_alliance']) === true ):

                    $this->_db->query("UPDATE `" . USERS . "` SET
                                                                                    `user_ally_id` = '0'
                                                                                    WHERE `user_ally_id` = '" . $this->_ally['alliance_id'] . "'");

                    $this->_db->query("DELETE FROM " . ALLIANCE . "
                                                                                    WHERE `alliance_id` = '" . $this->_ally['alliance_id'] . "'
                                                                                    LIMIT 1");

                    FunctionsLib::redirect('game.php?page=alliance');

                    break;

                case ( $edit == 'transfer' && $this->have_access($this->_ally['alliance_owner'], $this->permissions['admin_alliance']) === true ):

                    if (isset($_POST['newleader'])) {
                        $this->_db->query("UPDATE " . USERS . " AS u1, " . ALLIANCE . " AS a, " . USERS . " AS u2 SET
                                                                                            u1.`user_ally_rank_id` = '0',
                                                                                            a.`alliance_owner` = '" . $this->_db->escapeValue(strip_tags($_POST['newleader'])) . "',
                                                                                            u2.`user_ally_rank_id` = '0'
                                                                                            WHERE u1.`user_id`=" . $this->_current_user['user_id'] . " AND
                                                                                                            a.`alliance_id`=" . $this->_current_user['user_ally_id'] . " AND
                                                                                                            u2.user_id`='" . $this->_db->escapeValue(strip_tags($_POST['newleader'])) . "'");

                        FunctionsLib::redirect('game.php?page=alliance');
                    }

                    $page_list = '';

                    if ($this->_ally['alliance_owner'] != $this->_current_user['user_id']) {
                        FunctionsLib::redirect('game.php?page=alliance');
                    } else {
                        $listuser = $this->_db->query("SELECT *
                                                                                                                                    FROM " . USERS . "
                                                                                                                                    WHERE user_ally_id = '" . $this->_current_user['user_ally_id'] . "'");
                        $righthand = $this->_lang;

                        while ($u = $this->_db->fetchArray($listuser)) {
                            if ($this->_ally['alliance_owner'] != $u['user_id']) {
                                if ($u['ally_rank_id'] != 0) {
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

                        $page_list .= parent::$page->parseTemplate(parent::$page->getTemplate('alliance/alliance_admin_transfer_row'), $righthand);
                        $parse['list'] = $page_list;

                        return parent::$page->parseTemplate(parent::$page->getTemplate('alliance/alliance_admin_transfer'), $parse);
                    }

                    break;
            }
        }
    }

    /**
     * method image_block
     * param $alliance_image
     * return shows the image block, if any
     */
    private function image_block($alliance_image)
    {
        if ($alliance_image != '') {
            return '<tr><th colspan="2">' . FunctionsLib::setImage($alliance_image, $alliance_image) . '</td></tr>';
        }

        return '';
    }

    /**
     * method image_block
     * param $alliance_description
     * return shows the description block, if any
     */
    private function description_block($alliance_description)
    {
        if ($alliance_description == '') {
            $alliance_description = $this->_lang['al_description_message'];
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

        return '<tr><th>' . $this->_lang['al_web_text'] . '</th><th>' . $alliance_web . '</th></tr>';
    }

    /**
     * method image_block
     * param $ally_id
     * param $alliance_request
     * return shows the request block, if any
     */
    private function request_block($ally_id, $alliance_request)
    {
        if (!$this->_current_user['user_ally_id'] && !$this->_current_user['user_ally_request'] && !$alliance_request) {

            $url = 'game.php?page=alliance&mode=apply&allyid=' . $ally_id;
            $url = FunctionsLib::setUrl($url, $this->_lang['al_click_to_send_request'], $this->_lang['al_click_to_send_request']);

            return '<tr><th>' . $this->_lang['al_request'] . '</th><th>' . $url . '</th></tr>';
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
    private function message_box($title, $message, $goto = '', $button = ' ok ', $two_lines = false)
    {
        $parse['goto'] = $goto;
        $parse['title'] = $title;
        $parse['message'] = $message;
        $parse['button'] = $button;
        $template = ( $two_lines ) ? 'alliance_message_box_row_two' : 'alliance_message_box_row_one';

        $parse['message_box_row'] = parent::$page->parseTemplate(parent::$page->getTemplate('alliance/' . $template), $parse);

        return parent::$page->parseTemplate(parent::$page->getTemplate('alliance/alliance_message_box'), $parse);
    }

    /**
     * method return_rank
     * param $rank_type
     * return returns the rank permission
     */
    private function return_rank($rank_type)
    {
        $alliance_ranks = unserialize($this->_ally['alliance_ranks']);

        return ((isset($alliance_ranks[$this->_current_user['user_ally_rank_id'] - 1][$rank_type]) && $alliance_ranks[$this->_current_user['user_ally_rank_id'] - 1][$rank_type] == 1 ) or $this->_ally['alliance_owner'] == $this->_current_user['user_id']);
    }

    /**
     * method inactive_time
     * param $time
     * return the inactivity status
     */
    private function inactive_time($time)
    {
        if ($time + 60 * 10 >= time()) {
            return '"lime">' . $this->_lang['online'] . '<';
        } elseif ($time + 60 * 20 >= time()) {
            return '"yellow">' . $this->_lang['minutes'] . '<';
        } else {
            return '"red">' . $this->_lang['offline'] . '<';
        }
    }

    /**
     * method return_sort
     * param $sort1
     * param $sort2
     * return the requested order
     */
    private function return_sort($sort1, $sort2)
    {
        // FIRST ORDER
        switch ($sort1) {
            case 1:
                $sort = " ORDER BY `user_name`";
                break;
            case 2:
                $sort = " ORDER BY `user_ally_rank_id`";
                break;
            case 3:
                $sort = " ORDER BY `user_statistic_total_points`";
                break;
            case 4:
                $sort = " ORDER BY `user_ally_register_time`";
                break;
            case 5:
                $sort = " ORDER BY `user_onlinetime`";
                break;
            default:
                $sort = " ORDER BY `user_id`";
                break;
        }

        // SECOND ORDER
        if ($sort2 == 1) {

            $sort .= " DESC;";
        } elseif ($sort2 == 2) {

            $sort .= " ASC;";
        }

        return $sort; // RETORNA LA FORMA DE ORDEN
    }

    /**
     * method have_access
     * param $alliance_owner
     * param $permission
     * return checks if the user is allowed to access a section
     */
    private function have_access($alliance_owner, $permission)
    {
        if ($alliance_owner != $this->_current_user['user_id'] && !$permission) {
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
    private function check_tag($alliance_tag)
    {
        $alliance_tag = trim($alliance_tag);
        $alliance_tag = htmlspecialchars_decode($alliance_tag, ENT_QUOTES);

        if ($alliance_tag == '' OR is_null($alliance_tag) OR ( strlen($alliance_tag) < 3 ) OR ( strlen($alliance_tag) > 8 )) {
            FunctionsLib::message($this->_lang['al_tag_required'], "game.php?page=alliance&mode=make", 2);
            exit;
        }

        $alliance_tag = $this->_db->escapeValue($alliance_tag);

        $check_tag = $this->_db->queryFetch("SELECT `alliance_tag`
                                                                                                                    FROM `" . ALLIANCE . "`
                                                                                                                    WHERE `alliance_tag` = '" . $alliance_tag . "'");
        if ($check_tag) {
            FunctionsLib::message(str_replace('%s', $alliance_tag, $this->_lang['al_tag_already_exists']), "game.php?page=alliance&mode=make", 2);
            exit;
        }

        return $alliance_tag;
    }

    /**
     * method check_name
     * param $alliance_name
     * return the validated the ally name
     */
    private function check_name($alliance_name)
    {
        $alliance_name = trim($alliance_name);
        $alliance_name = htmlspecialchars_decode($alliance_name, ENT_QUOTES);

        if ($alliance_name == '' OR is_null($alliance_name) OR ( strlen($alliance_name) < 3 ) OR ( strlen($alliance_name) > 30 )) {
            FunctionsLib::message($this->_lang['al_name_required'], "game.php?page=alliance&mode=make", 2);
            exit;
        }

        $alliance_name = $this->_db->escapeValue($alliance_name);

        $check_name = $this->_db->queryFetch("SELECT `alliance_name`
                                                                                                            FROM `" . ALLIANCE . "`
                                                                                                            WHERE `alliance_name` = '" . $alliance_name . "'");

        if ($check_name) {
            FunctionsLib::message(str_replace('%s', $alliance_name, $this->_lang['al_name_already_exists']), "game.php?page=alliance&mode=make", 2);
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
        if ($this->_current_user['user_ally_id'] != 0 && $this->_current_user['user_ally_request'] == 0) {
            // GET ALLIANCE DATA

            if (!isset($this->_ally['alliance_id']) or $this->_ally['alliance_id'] <= 0) {
                $this->_ally = $this->_db->queryFetch(
                        "SELECT a.*,
                            (SELECT COUNT(user_id) AS `ally_members` 
                                FROM `" . USERS . "` 
                                WHERE `user_ally_id` = a.`alliance_id`) AS `ally_members`
                    FROM " . ALLIANCE . " AS a
                    WHERE a.`alliance_id` = '" . (int) $this->_current_user['user_ally_id'] . "'"
                );
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
