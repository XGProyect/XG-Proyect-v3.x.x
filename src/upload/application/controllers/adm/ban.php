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
use application\libraries\adm\AdministrationLib as Administration;
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
        Administration::checkSession();

        // load Model
        parent::loadModel('adm/ban');

        // load Language
        parent::loadLang(['adm/global', 'adm/ban']);

        $this->_current_user = parent::$users->getUserData();

        // check if the user is allowed to access
        if (!Administration::authorization(__CLASS__, (int) $this->_current_user['user_authlevel'])) {
            die(Administration::noAccessMessage($this->langs->line('no_permissions')));
        }

        $this->build_page();
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
        $parse = $this->langs->language;
        $parse['js_path'] = JS_PATH;
        $parse['alert'] = '';
        $parse['bn_sub_title'] = '';
        $parse['np_general'] = '';

        if (isset($_POST['unban_name']) && $_POST['unban_name']) {
            $username = $_POST['unban_name'];

            $this->Ban_Model->unbanUser($username);

            $parse['alert'] = Administration::saveMessage('ok', (str_replace('%s', $username, $this->langs->line('bn_lift_ban_success'))));
        }

        $parse['users_list'] = $this->get_users_list();
        $parse['banned_list'] = $this->get_banned_list();
        $parse['users_amount'] = $this->_users_count;
        $parse['banned_amount'] = $this->_banned_count;

        return $this->getTemplate()->set('adm/ban_view', $parse);
    }

    /**
     * method show_ban
     * param
     * return build the ban page
     */
    private function show_ban()
    {
        $parse = $this->langs->language;
        $parse['js_path'] = JS_PATH;
        $parse['alert'] = '';
        $parse['bn_sub_title'] = '';
        $parse['reason'] = '';
        $ban_name = isset($_GET['ban_name']) ?? null;

        if (isset($_GET['banuser']) && isset($_GET['ban_name'])) {
            $parse['name'] = $ban_name;
            $parse['banned_until'] = '';
            $parse['changedate'] = $this->langs->line('bn_auto_lift_ban_message');
            $parse['vacation'] = '';

            $banned_user = $this->Ban_Model->getBannedUserData($ban_name);

            if ($banned_user) {
                $parse['banned_until'] = $this->langs->line('bn_banned_until') . ' (' . date(FunctionsLib::readConfig('date_format_extended'), $banned_user['banned_longer']) . ')';
                $parse['reason'] = $banned_user['banned_theme'];
                $parse['changedate'] = '<div style="float:left">' . $this->langs->line('bn_change_date') . '</div><div style="float:right">' . Administration::showPopUp($this->langs->line('bn_edit_ban_help')) . '</div>';
            }

            $parse['vacation'] = $banned_user['preference_vacation_mode'] ? 'checked="checked"' : '';

            if (isset($_POST['bannow']) && $_POST['bannow']) {
                if (!is_numeric($_POST['days']) or !is_numeric($_POST['hour'])) {
                    $parse['alert'] = Administration::saveMessage('warning', $this->langs->line('bn_all_fields_required'));
                } else {
                    $reas = (string) $_POST['text'];
                    $days = (int) $_POST['days'];
                    $hour = (int) $_POST['hour'];
                    $admin_name = $this->_current_user['user_name'];
                    $admin_mail = $this->_current_user['user_email'];
                    $current_time = time();
                    $ban_time = $days * 86400;
                    $ban_time += $hour * 3600;
                    $vacation_mode = isset($_POST['vacat']) ?? null;

                    if ($banned_user['banned_longer'] > time()) {
                        $ban_time += ($banned_user['banned_longer'] - time());
                    }

                    if (($ban_time + $current_time) < time()) {
                        $banned_until = $current_time;
                    } else {
                        $banned_until = $current_time + $ban_time;
                    }

                    $this->Ban_Model->setOrUpdateBan(
                        $banned_user,
                        [
                            'ban_name' => $ban_name,
                            'ban_reason' => $reas,
                            'ban_time' => $current_time,
                            'ban_until' => $banned_until,
                            'ban_author' => $admin_name,
                            'ban_author_email' => $admin_mail,
                        ],
                        $vacation_mode
                    );

                    $parse['alert'] = Administration::saveMessage('ok', (str_replace('%s', $ban_name, $this->langs->line('bn_ban_success'))));
                }
            }
        } else {
            FunctionsLib::redirect('admin.php?page=ban');
        }

        return $this->getTemplate()->set("adm/ban_result_view", $parse);
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
        $users_query = $this->Ban_Model->getListOfUsers($where_authlevel, $where_banned, $query_order);

        foreach ($users_query as $user) {
            $status = '';

            if ($user['user_banned'] == 1) {
                $status = $this->langs->line('bn_status');
            }

            $users_list .= '<option value="' . $user['user_name'] . '">' . $user['user_name'] . '&nbsp;&nbsp;(ID:&nbsp;' . $user['user_id'] . ')' . $status . '</option>';

            $this->_users_count++;
        }

        unset($users_query); // free resources

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
        $banned_query = $this->Ban_Model->getBannedUsers($order);

        foreach ($banned_query as $user) {
            $banned_list .= '<option value="' . $user['user_name'] . '">' . $user['user_name'] . '&nbsp;&nbsp;(ID:&nbsp;' . $user['user_id'] . ')</option>';

            $this->_banned_count++;
        }

        unset($banned_query); // free resources

        return $banned_list; // return builded list
    }
}

/* end of ban.php */
