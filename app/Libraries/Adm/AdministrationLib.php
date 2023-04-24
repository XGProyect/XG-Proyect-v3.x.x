<?php

namespace App\Libraries\Adm;

use App\Core\Language;
use App\Core\Template;
use App\Libraries\Functions;
use App\Libraries\Page;
use App\Libraries\Users;

class AdministrationLib
{
    /**
     * Return a new instance of Template
     *
     * @return Template
     */
    public static function getTemplate(): Template
    {
        return new Template();
    }

    /**
     * haveAccess
     *
     * @param int $user_level User level
     *
     * @return void
     */
    public static function haveAccess($user_level)
    {
        return ($user_level >= 1);
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
        (new Page(new Users()))->displayAdmin(
            self::saveMessage('error', $mes, false)
        );
    }

    /**
     * installDirExists
     *
     * @return boolean
     */
    public static function installDirExists()
    {
        return (file_exists(XGP_ROOT . PUBLIC_PATH . 'install/'));
    }

    /**
     * authorization
     *
     * @param int    $user_level User level
     * @param string $permission Permission
     *
     * @return array
     */
    public static function authorization(string $module, int $user_level)
    {
        $cleaned_module_name = strtolower(substr(strrchr($module, '\\'), 1));
        $permissions = new Permissions(Functions::readConfig('admin_permissions'));

        return $permissions->isAccessAllowed($cleaned_module_name, $user_level);
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
        $lang = new Language();
        $lang = $lang->loadLang('adm/global', true);

        switch ($result) {
            case 'ok':
                $parse['color'] = 'alert-success';
                $parse['status'] = $lang->line('gn_ok_title');
                break;
            case 'error':
                $parse['color'] = 'alert-danger';
                $parse['status'] = $lang->line('gn_error_title');
                break;
            case 'warning':
                $parse['color'] = 'alert-warning';
                $parse['status'] = $lang->line('gn_warning_title');
                break;
            case 'info':
                $parse['color'] = 'alert-info';
                $parse['status'] = '';
                break;
        }

        $parse['message'] = $message;

        if (!$dismissible) {
            $parse['dismissible'] = 'd-none';
        }

        return self::getTemplate()->set(
            'adm/save_message_view',
            $parse
        );
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

        return self::getTemplate()->set(
            'adm/popup_view',
            $parse
        );
    }

    /**
     * adminLogin
     *
     * @param int    $admin_id   Admin ID
     * @param string $password   Password
     *
     * @return void
     */
    public static function adminLogin($admin_id = 0, $password = '')
    {
        if ($admin_id != 0 && !empty($password)) {
            // login as a user
            (new Users())->userLogin($admin_id, $password);

            // admin login
            $_SESSION['admin_id'] = $admin_id;
            $_SESSION['admin_password'] = Functions::hash($password . '-' . SECRETWORD);

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
            $page = filter_input(INPUT_GET, 'page', FILTER_UNSAFE_RAW);

            if ($page != 'login') {
                Functions::redirect(SYSTEM_ROOT . 'admin.php?page=login&redirect=' . $page);
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
        unset($_SESSION['admin_id'], $_SESSION['admin_password']);
    }

    /**
     * isSessionSet
     *
     * @return boolean
     */
    private static function isSessionSet()
    {
        return !(!isset($_SESSION['admin_id']) or !isset($_SESSION['admin_password']));
    }

    /**
     * Check if an update is required
     *
     * @return void
     */
    public static function updateRequired()
    {
        if (SYSTEM_VERSION != Functions::readConfig('version')) {
            $exclude_pages = ['', 'home', 'update', 'logout'];

            if (isset($_GET['page']) && !in_array($_GET['page'], $exclude_pages)) {
                Functions::redirect(XGP_ROOT . 'admin.php?page=update');
            }
        }
    }
}
