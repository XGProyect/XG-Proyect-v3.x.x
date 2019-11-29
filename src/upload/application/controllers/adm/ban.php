<?php
/**
 * Ban Controller
 *
 * PHP Version 7.1+
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\controllers\adm;

use application\core\Controller;
use application\core\Database;
use application\libraries\adm\AdministrationLib;
use application\libraries\FunctionsLib;

/**
 * Ban Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Ban extends Controller
{

    private $_lang;
    private $_current_user;
    private $_users_count = 0;
    private $_banned_count = 0;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        AdministrationLib::checkSession();

        $this->_db = new Database();
        $this->_lang = parent::$lang;
        $this->_current_user = parent::$users->getUserData();

        // Check if the user is allowed to access
        if (AdministrationLib::haveAccess($this->_current_user['user_authlevel']) && AdministrationLib::authorization($this->_current_user['user_authlevel'], 'edit_users') == 1) {
            $this->build_page();
        } else {
            die(AdministrationLib::noAccessMessage($this->_lang['ge_no_permissions']));
        }
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
    private function build_page()
    {
        switch ((isset($_GET['mode']) ? $_GET['mode'] : '')) {
            case 'ban':

                $view = $this->show_ban();

                break;

            case '':
            default:

                $view = $this->show_default();

                break;
        }

        parent::$page->displayAdmin($view);
    }

    /**
     * method show_default
     * param
     * return build the default page
     */
    private function show_default()
    {
        $parse = $this->_lang;
        $parse['js_path'] = JS_PATH;

        if (isset($_POST['unban_name']) && $_POST['unban_name']) {
            $username = $this->_db->escapeValue($_POST['unban_name']);

            $this->_db->query("DELETE FROM `" . BANNED . "`
									WHERE `banned_who` = '" . $username . "'");

            $this->_db->query("UPDATE `" . USERS . "` SET
									`user_banned` = '0'
									WHERE `user_name` = '" . $username . "'
									LIMIT 1");

            $parse['alert'] = AdministrationLib::saveMessage('ok', (str_replace('%s', $username, $this->_lang['bn_lift_ban_success'])));
        }

        $parse['users_list'] = $this->get_users_list();
        $parse['banned_list'] = $this->get_banned_list();
        $parse['users_amount'] = $this->_users_count;
        $parse['banned_amount'] = $this->_banned_count;

        return parent::$page->parseTemplate(parent::$page->getTemplate('adm/ban_view'), $parse);
    }

    /**
     * method show_ban
     * param
     * return build the ban page
     */
    private function show_ban()
    {
        $parse = $this->_lang;
        $parse['js_path'] = JS_PATH;
        $ban_name = isset($_GET['ban_name']) ? $this->_db->escapeValue($_GET['ban_name']) : null;

        if (isset($_GET['banuser']) && isset($_GET['ban_name'])) {
            $parse['name'] = $ban_name;
            $parse['banned_until'] = '';
            $parse['changedate'] = $this->_lang['bn_auto_lift_ban_message'];
            $parse['vacation'] = '';

            $banned_user = $this->_db->queryFetch(
                "SELECT
                                b.*,
                                p.`preference_user_id`,
                                p.`preference_vacation_mode`
                            FROM `" . BANNED . "` AS b
                            INNER JOIN `" . PREFERENCES . "` AS p
                                ON p.`preference_user_id` = (SELECT `user_id`
                                                            FROM `" . USERS . "`
                                                                WHERE `user_name` = '" . $ban_name . "'
                                                                LIMIT 1)
                            WHERE `banned_who` = '" . $ban_name . "'"
            );
            if ($banned_user) {
                $parse['banned_until'] = $this->_lang['bn_banned_until'] . ' (' . date(FunctionsLib::readConfig('date_format_extended'), $banned_user['banned_longer']) . ')';
                $parse['reason'] = $banned_user['banned_theme'];
                $parse['changedate'] = '<div style="float:left">' . $this->_lang['bn_change_date'] . '</div><div style="float:right">' . AdministrationLib::showPopUp($this->_lang['bn_edit_ban_help']) . '</div>';
            }

            $parse['vacation'] = $banned_user['preference_vacation_mode'] ? 'checked="checked"' : '';

            if (isset($_POST['bannow']) && $_POST['bannow']) {
                if (!is_numeric($_POST['days']) or !is_numeric($_POST['hour'])) {
                    $parse['alert'] = AdministrationLib::saveMessage('warning', $this->_lang['bn_all_fields_required']);
                } else {
                    $reas = (string) $_POST['text'];
                    $days = (int) $_POST['days'];
                    $hour = (int) $_POST['hour'];
                    $admin_name = $this->_current_user['user_name'];
                    $admin_mail = $this->_current_user['user_email'];
                    $current_time = time();
                    $ban_time = $days * 86400;
                    $ban_time += $hour * 3600;

                    if ($banned_user['banned_longer'] > time()) {
                        $ban_time += ($banned_user['banned_longer'] - time());
                    }

                    if (($ban_time + $current_time) < time()) {
                        $banned_until = $current_time;
                    } else {
                        $banned_until = $current_time + $ban_time;
                    }

                    if ($banned_user) {
                        $this->_db->query("UPDATE " . BANNED . "  SET
											`banned_who` = '" . $ban_name . "',
											`banned_theme` = '" . $reas . "',
											`banned_who2` = '" . $ban_name . "',
											`banned_time` = '" . $current_time . "',
											`banned_longer` = '" . $banned_until . "',
											`banned_author` = '" . $admin_name . "',
											`banned_email` = '" . $admin_mail . "'
											WHERE `banned_who2` = '" . $ban_name . "';");
                    } else {
                        $this->_db->query("INSERT INTO " . BANNED . " SET
											`banned_who` = '" . $ban_name . "',
											`banned_theme` = '" . $reas . "',
											`banned_who2` = '" . $ban_name . "',
											`banned_time` = '" . $current_time . "',
											`banned_longer` = '" . $banned_until . "',
											`banned_author` = '" . $admin_name . "',
											`banned_email` = '" . $admin_mail . "';");
                    }

                    $user_id = $this->_db->queryFetch("SELECT `user_id`
																FROM " . USERS . "
																WHERE `user_name` = '" . $ban_name . "' LIMIT 1");

                    $this->_db->query("UPDATE " . USERS . " AS u, " . PREFERENCES . " AS pr, " . PLANETS . " AS p SET
											u.`user_banned` = '" . $banned_until . "',
											pr.`preference_vacation_mode` = " . (isset($_POST['vacat']) ? "'" . time() . "'" : 'NULL') . ",
											p.`planet_building_metal_mine_percent` = '0',
											p.`planet_building_crystal_mine_percent` = '0',
											p.`planet_building_deuterium_sintetizer_percent` = '0'
											WHERE u.`user_id` = " . $user_id['user_id'] . "
													AND pr.`preference_user_id` = " . $user_id['user_id'] . "
													AND p.`planet_user_id` = " . $user_id['user_id'] . ";");

                    $parse['alert'] = AdministrationLib::saveMessage('ok', (str_replace('%s', $ban_name, $this->_lang['bn_ban_success'])));
                }
            }
        } else {
            FunctionsLib::redirect('admin.php?page=ban');
        }

        return parent::$page->parseTemplate(parent::$page->getTemplate("adm/ban_result_view"), $parse);
    }

    /**
     * method get_users_list
     * param
     * return the users list (left select)
     */
    private function get_users_list()
    {
        $query_order = (isset($_GET['order']) && $_GET['order'] == 'id') ? 'user_id' : 'user_name';
        $where_authlevel = '';
        $where_banned = '';
        $users_list = '';

        if ($this->_current_user['user_authlevel'] != 3) {
            $where_authlevel = "WHERE `user_authlevel` < '" . ($this->_current_user['user_authlevel']) . "'";
        }

        if (isset($_GET['view']) && ($_GET['view'] == 'user_banned')) {
            if ($this->_current_user['user_authlevel'] == 3) {
                $where_banned = "WHERE `user_banned` <> '0'";
            } else {
                $where_banned = "AND `user_banned` <> '1'";
            }
        }

        // get the users according to the filters
        $users_query = $this->_db->query("SELECT `user_id`, `user_name`, `user_banned`
																FROM `" . USERS . "`
																" . $where_authlevel . " " . $where_banned . "
																ORDER BY " . $query_order . " ASC");

        while ($user = $this->_db->fetchArray($users_query)) {
            $status = '';

            if ($user['user_banned'] == 1) {
                $status = $this->_lang['bn_status'];
            }

            $users_list .= '<option value="' . $user['user_name'] . '">' . $user['user_name'] . '&nbsp;&nbsp;(ID:&nbsp;' . $user['user_id'] . ')' . $status . '</option>';

            $this->_users_count++;
        }

        $this->_db->freeResult($users_query); // free resources

        return $users_list; // return builded list
    }

    /**
     * method get_banned_list
     * param
     * return the banned users list (right select)
     */
    private function get_banned_list()
    {
        $order = (isset($_GET['order2']) && $_GET['order2'] == 'id') ? 'user_id' : 'user_name';
        $banned_list = '';

        // get the banned users
        $banned_query = $this->_db->query("SELECT `user_id`, `user_name`
													FROM `" . USERS . "`
													WHERE `user_banned` <> '0'
													ORDER BY " . $order . " ASC");

        while ($user = $this->_db->fetchArray($banned_query)) {
            $banned_list .= '<option value="' . $user['user_name'] . '">' . $user['user_name'] . '&nbsp;&nbsp;(ID:&nbsp;' . $user['user_id'] . ')</option>';

            $this->_banned_count++;
        }

        $this->_db->freeResult($banned_query); // free resources

        return $banned_list; // return builded list
    }
}

/* end of ban.php */
