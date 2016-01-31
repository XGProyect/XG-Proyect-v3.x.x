<?php

/**
 * Premium Controller.
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
 * Premium Class.
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
class Premium extends XGPCore
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
        if (AdministrationLib::have_access($this->_current_user['user_authlevel']) && AdministrationLib::authorization($this->_current_user['user_authlevel'], 'config_game') == 1) {
            $this->build_page();
        } else {
            die(FunctionsLib::message($this->_lang['ge_no_permissions']));
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
        $parse          = $this->_lang;
        $parse['alert'] = '';
        $error          = '';

        if (isset($_POST['save'])) {
            if (isset($_POST['premium_url']) && !empty($_POST['premium_url'])) {
                FunctionsLib::update_config('premium_url', FunctionsLib::prep_url($_POST['premium_url']));
            } else {
                $error               .= $this->_lang['pr_error_url'];
            }

            if (isset($_POST['trader_darkmatter']) && ($_POST['trader_darkmatter'] > 0)) {
                FunctionsLib::update_config('trader_darkmatter', $_POST['trader_darkmatter']);
            } else {
                $error               .= $this->_lang['pr_error_trader'];
            }

            if ($error    != '') {
                $parse['alert'] = AdministrationLib::save_message('warning', $error);
            } else {
                $parse['alert'] = AdministrationLib::save_message('ok', $this->_lang['pr_all_ok_message']);
            }
        }

        $parse['premium_url']       = FunctionsLib::read_config('premium_url');
        $parse['trader_darkmatter'] = FunctionsLib::read_config('trader_darkmatter');

        parent::$page->display(parent::$page->parse_template(parent::$page->get_template('adm/premium_view'), $parse));
    }
}

/* end of premium.php */
