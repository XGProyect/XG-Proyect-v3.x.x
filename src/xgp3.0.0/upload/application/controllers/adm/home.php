<?php

/**
 * Home Controller.
 *
 * PHP Version 5.5+
 *
 * @category Controller
 *
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 *
 * @link     http://www.xgproyect.org
 *
 * @version  3.0.0
 */

namespace application\controllers\adm;

use application\core\XGPCore;
use application\libraries\adm\AdministrationLib;
use application\libraries\FunctionsLib;

/**
 * Home Class.
 *
 * @category Classes
 *
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 *
 * @link     http://www.xgproyect.org
 *
 * @version  3.0.0
 */
class Home extends XGPCore
{
    private $_lang;
    private $_current_user;

    /**
     * __construct().
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->check_session();

        $this->_lang         = parent::$lang;
        $this->_current_user = parent::$users->get_user_data();

        // Check if the user is allowed to access
        if (!AdministrationLib::have_access($this->_current_user['user_authlevel'])) {
            die(FunctionsLib::message($this->_lang['ge_no_permissions']));
        } else {
            $this->build_page();
        }
    }

    /**
     * method __destruct
     * param
     * return close db connection.
     */
    public function __destruct()
    {
        parent::$db->closeConnection();
    }

    /**
     * method build_page
     * param
     * return main method, loads everything.
     */
    private function build_page()
    {
        $parse       = $this->_lang;
        $error       = 0;
        $old_version = false;
        $message[1]  = '';
        $message[2]  = '';
        $message[3]  = '';
        $message[4]  = '';

        // VERIFICATIONS
        if ($this->_current_user['user_authlevel'] >= 3) {
            if (is_writable(XGP_ROOT . CONFIGS_PATH . 'config.php')) {
                $message[1] = $this->_lang['hm_config_file_writable'] . '<br />';
                ++$error;
            }

            if ((@filesize(XGP_ROOT . LOGS_PATH . 'ErrorLog.php')) != 0) {
                $message[2] = $this->_lang['hm_database_errors'] . '<br />';
                ++$error;
            }

            if ($this->check_updates()) {
                $message[3]  = $this->_lang['hm_old_version'] . '<br />';
                $old_version = true;
                ++$error;
            }

            if (AdministrationLib::install_dir_exists()) {
                $message[4] = $this->_lang['hm_install_file_detected'] . '<br />';
                ++$error;
            }
        }

        if ($error > 1) {
            $parse['error_message'] = '<br />' . $message[1] . $message[2] . $message[3] . $message[4];
            $parse['second_style']  = 'alert-error';
            $parse['error_type']    = $this->_lang['hm_errors'];
        } elseif ($error == 1) {
            $parse['error_message'] = '<br />' . $message[1] . $message[2] . $message[3] . $message[4];
            $parse['second_style']  = 'alert-block';
            $parse['error_type']    = $this->_lang['hm_warning'];
        } else {
            $parse['error_message'] = $this->_lang['hm_all_ok'];
            $parse['second_style']  = 'alert-success';
            $parse['error_type']    = $this->_lang['hm_ok'];
        }

        $parse['game_version']      = FunctionsLib::read_config('version');
        $parse['old_version_alert'] = ($old_version) ? '<a href="http://www.xgproyect.org/downloads/">' . $this->_lang['hm_update'] . '</a> <i class="icon-download"></i>' : '';

        parent::$page->display(parent::$page->parse_template(parent::$page->get_template('adm/home_view'), $parse));
    }

    /**
     * method check_updates
     * param
     * return check for updates and returns true or false.
     */
    private function check_updates()
    {
        if (function_exists('file_get_contents')) {
            $last_v   = @file_get_contents('http://xgproyect.xgproyect.org/current.php');
            $system_v = FunctionsLib::read_config('version');

            return version_compare($system_v, $last_v, '<');
        }
    }
}

/* end of home.php */
