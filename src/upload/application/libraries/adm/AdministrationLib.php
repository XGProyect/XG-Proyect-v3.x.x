<?php
/**
 * Administration Library
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
namespace application\libraries\adm;

use application\core\Database;
use application\core\XGPCore;
use application\libraries\FunctionsLib;

/**
 * AdministrationLib Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class AdministrationLib extends XGPCore
{

    /**
     * haveAccess
     *
     * @param int $user_level User level
     *
     * @return void
     */
    public static function haveAccess($user_level)
    {
        if ($user_level >= 1) {

            return true;
        } else {

            return false;
        }
    }

    /**
     * noAccessMessage
     *
     * @param string $mes Message
     *
     * @return void
     */
    public static function noAccessMessage($mes = '')
    {
        FunctionsLib::message(
            self::saveMessage('error', $mes, false), '', '', true
        );
    }

    /**
     * installDirExists
     *
     * @return boolean
     */
    public static function installDirExists()
    {
        if (file_exists(XGP_ROOT . PUBLIC_PATH . 'install/')) {

            return true;
        } else {

            return false;
        }
    }

    /**
     * authorization
     *
     * @param int    $user_level User level
     * @param string $permission Permission
     *
     * @return array
     */
    public static function authorization($user_level, $permission)
    {
        $QueryModeration = FunctionsLib::readConfig('moderation');
        $QueryModerationEx = explode(";", $QueryModeration);
        $Moderator = explode(",", $QueryModerationEx[0]);
        $Operator = explode(",", $QueryModerationEx[1]);
        $Administrator = explode(",", $QueryModerationEx[2]);

        if ($user_level == 1) {
            $permissions['observation'] = $Moderator[0];
            $permissions['edit_users'] = $Moderator[1];
            $permissions['config_game'] = $Moderator[2];
            $permissions['use_tools'] = $Moderator[3];
            $permissions['track_activity'] = $Moderator[4];
        }

        if ($user_level == 2) {
            $permissions['observation'] = $Operator[0];
            $permissions['edit_users'] = $Operator[1];
            $permissions['config_game'] = $Operator[2];
            $permissions['use_tools'] = $Operator[3];
            $permissions['track_activity'] = $Operator[4];
        }

        if ($user_level == 3) {
            $permissions['observation'] = 1;
            $permissions['edit_users'] = 1;
            $permissions['config_game'] = 1;
            $permissions['use_tools'] = 1;
            $permissions['track_activity'] = $Administrator[0];
        }

        return $permissions[$permission];
    }

    /**
     * saveMessage
     *
     * @param string $result  Result
     * @param string $message Message
     *
     * @return string
     */
    public static function saveMessage($result, $message, $dismissible = true)
    {
        switch ($result) {
            case 'ok':
                $parse['color'] = 'alert-success';
                $parse['status'] = parent::$lang['gn_ok_title'];
                break;

            case 'error':
                $parse['color'] = 'alert-error';
                $parse['status'] = parent::$lang['gn_error_title'];
                break;

            case 'warning':
                $parse['color'] = 'alert-block';
                $parse['status'] = parent::$lang['gn_warning_title'];
                break;
        }

        $parse['message'] = $message;

        if (!$dismissible) {

            $parse['dismissible'] = 'hide';
        }

        return parent::$page->parseTemplate(parent::$page->getTemplate('adm/save_message_view'), $parse);
    }

    /**
     * returnRank
     *
     * @param int $authlevel Auth level
     *
     * @return string
     */
    public static function returnRank($authlevel)
    {
        switch ($authlevel) {
            default:
            case 0:
                return parent::$lang['ge_user'];

                break;

            case 1:
                return parent::$lang['ge_go'];

                break;

            case 2:
                return parent::$lang['ge_sgo'];

                break;

            case 3:
                return parent::$lang['ge_ga'];

                break;
        }
    }

    /**
     * showPopUp
     *
     * @param string $message Message
     *
     * @return string
     */
    public static function showPopUp($message)
    {
        $parse['message'] = $message;

        return parent::$page->parseTemplate(parent::$page->getTemplate('adm/popup_view'), $parse);
    }

    /**
     * secureConnection
     *
     * @return void
     */
    public static function secureConnection()
    {
        if ((FunctionsLib::readConfig('ssl_enabled') == 1) && ($_SERVER['SERVER_PORT'] !== 443) && (empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] === 'off')) {

            FunctionsLib::redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        }
    }

    /**
     * adminLogin
     *
     * @param int    $admin_id   Admin ID
     * @param string $admin_name Admin name
     * @param string $password   Password
     *
     * @return void
     */
    public static function adminLogin($admin_id = 0, $admin_name = '', $password = '')
    {
        if ($admin_id != 0 && !empty($admin_name) && !empty($password)) {

            parent::$users->userLogin($admin_id, $admin_name, $password);

            $_SESSION['admin_id'] = $admin_id;
            $_SESSION['admin_name'] = $admin_name;
            $_SESSION['admin_password'] = sha1($password . '-' . SECRETWORD);

            return true;
        } else {

            return false;
        }
    }

    /**
     * checkSession
     *
     * @return void
     */
    public static function checkSession()
    {
        if (!self::isSessionSet()) {

            $db = new Database();
            $parse = parent::$lang;

            if ($_POST) {

                $login = $db->queryFetch(
                    "SELECT `user_id`, `user_name`, `user_password`
                    FROM " . USERS . "
                    WHERE `user_email` = '" . $db->escapeValue($_POST['inputEmail']) . "'
                        AND `user_password` = '" . sha1($_POST['inputPassword']) . "'
                        AND `user_authlevel` >= '1'
                        LIMIT 1"
                );

                if ($login) {

                    // User login
                    if (self::adminLogin($login['user_id'], $login['user_name'], $login['user_password'])) {

                        // Redirect to game
                        FunctionsLib::redirect('admin.php');
                    }
                }

                // If login fails
                FunctionsLib::redirect(SYSTEM_ROOT . 'admin.php?error=1');
            } else {

                $parse['alert'] = '';

                if (isset($_GET['error']) && $_GET['error'] == 1) {

                    $parse['alert'] = self::saveMessage('error', parent::$lang['lg_error_wrong_data'], false);
                }

                parent::$page->display(
                    parent::$page->parseTemplate(parent::$page->getTemplate('adm/login_view'), $parse), false, '', false
                );
            }
        }
    }

    /**
     * closeSession
     *
     * @return boolean
     */
    public static function closeSession()
    {
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_password']);
    }

    /**
     * isSessionSet
     *
     * @return boolean
     */
    private static function isSessionSet()
    {
        if (!isset($_SESSION['admin_id']) or ! isset($_SESSION['admin_name']) or ! isset($_SESSION['admin_password'])) {

            return false;
        } else {

            return true;
        }
    }

    /**
     * Check if an update is required
     * 
     * @return void
     */
    public static function updateRequired()
    {
        if (SYSTEM_VERSION != FunctionsLib::readConfig('version')) {

            $exclude_pages = ['', 'home', 'update', 'logout'];

            if (isset($_GET['page']) && !in_array($_GET['page'], $exclude_pages)) {

                FunctionsLib::redirect(XGP_ROOT . 'admin.php?page=update');
            }
        }
    }
}

/* end of AdministrationLib.php */
