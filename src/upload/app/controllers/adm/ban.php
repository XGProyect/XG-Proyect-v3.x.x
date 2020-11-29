<?php
/**
 * Ban Controller
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\controllers\adm;

use App\core\BaseController;
use App\libraries\adm\AdministrationLib as Administration;
use App\libraries\Functions;

/**
 * Ban Class
 */
class Ban extends BaseController
{
    /**
     * @var int
     */
    private $_users_count = 0;
    /**
     * @var int
     */
    private $_banned_count = 0;

    /**
     * Constructor
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
    }

    /**
     * Users land here
     *
     * @return void
     */
    public function index(): void
    {
        // check if the user is allowed to access
        if (!Administration::authorization(__CLASS__, (int) $this->user['user_authlevel'])) {
            die(Administration::noAccessMessage($this->langs->line('no_permissions')));
        }

        // build the page
        $this->buildPage();
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage(): void
    {
        switch ((isset($_GET['mode']) ? $_GET['mode'] : '')) {
            case 'ban':
                $view = $this->showBan();

                break;

            case '':
            default:
                $view = $this->showDefault();

                break;
        }

        parent::$page->displayAdmin($view);
    }

    /**
     * method showDefault
     * param
     * return build the default page
     */
    private function showDefault()
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

        $parse['users_list'] = $this->getUsersList();
        $parse['banned_list'] = $this->getBannedList();
        $parse['users_amount'] = $this->_users_count;
        $parse['banned_amount'] = $this->_banned_count;

        return $this->getTemplate()->set('adm/ban_view', $parse);
    }

    /**
     * method showBan
     * param
     * return build the ban page
     */
    private function showBan()
    {
        $parse = $this->langs->language;
        $parse['js_path'] = JS_PATH;
        $parse['alert'] = '';
        $parse['bn_sub_title'] = '';
        $parse['reason'] = '';
        $ban_name = isset($_GET['ban_name']) ? $_GET['ban_name'] : null;

        if (isset($_GET['banuser']) && isset($ban_name)) {
            $parse['name'] = $ban_name;
            $parse['banned_until'] = '';
            $parse['changedate'] = $this->langs->line('bn_auto_lift_ban_message');
            $parse['vacation'] = '';

            $banned_user = $this->Ban_Model->getBannedUserData($ban_name);

            if ($banned_user) {
                $parse['banned_until'] = $this->langs->line('bn_banned_until') . ' (' . date(Functions::readConfig('date_format_extended'), $banned_user['banned_longer']) . ')';
                $parse['reason'] = $banned_user['banned_theme'];
                $parse['changedate'] = '<div style="float:left">' . $this->langs->line('bn_change_date') . '</div><div style="float:right">' . Administration::showPopUp($this->langs->line('bn_edit_ban_help')) . '</div>';
                $parse['vacation'] = $banned_user['preference_vacation_mode'] ? 'checked="checked"' : '';
            }

            if (isset($_POST['bannow']) && $_POST['bannow']) {
                if (!is_numeric($_POST['days']) or !is_numeric($_POST['hour'])) {
                    $parse['alert'] = Administration::saveMessage('warning', $this->langs->line('bn_all_fields_required'));
                } else {
                    $reas = (string) $_POST['text'];
                    $days = (int) $_POST['days'];
                    $hour = (int) $_POST['hour'];
                    $admin_name = $this->user['user_name'];
                    $admin_mail = $this->user['user_email'];
                    $current_time = time();
                    $ban_time = $days * 86400;
                    $ban_time += $hour * 3600;
                    $vacation_mode = isset($_POST['vacat']) ?? null;

                    if (isset($banned_user)) {
                        if ($banned_user['banned_longer'] > time()) {
                            $ban_time += ($banned_user['banned_longer'] - time());
                        }
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
            Functions::redirect('admin.php?page=ban');
        }

        return $this->getTemplate()->set("adm/ban_result_view", $parse);
    }

    /**
     * method getUsersList
     * param
     * return the users list (left select)
     */
    private function getUsersList()
    {
        $query_order = (isset($_GET['order']) && $_GET['order'] == 'id') ? 'user_id' : 'user_name';
        $where_authlevel = '';
        $where_banned = '';
        $users_list = '';

        if ($this->user['user_authlevel'] != 3) {
            $where_authlevel = "WHERE `user_authlevel` < '" . ($this->user['user_authlevel']) . "'";
        }

        if (isset($_GET['view']) && ($_GET['view'] == 'user_banned')) {
            if ($this->user['user_authlevel'] == 3) {
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
     * method getBannedList
     * param
     * return the banned users list (right select)
     */
    private function getBannedList()
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
