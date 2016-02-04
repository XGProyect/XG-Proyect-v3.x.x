<?php
/**
 * Registration Controller
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

namespace application\controllers\adm;

use application\core\XGPCore;
use application\libraries\adm\AdministrationLib;
use application\libraries\FunctionsLib;

/**
 * Registration Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Registration extends XGPCore
{
    private $_current_user;
    private $_game_config;
    private $_lang;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        $this->_lang = parent::$lang;
        $this->_current_user = parent::$users->getUserData();

        // Check if the user is allowed to access
        if (AdministrationLib::haveAccess($this->_current_user['user_authlevel']) && AdministrationLib::authorization($this->_current_user['user_authlevel'], 'config_game') == 1) {
            $this->_game_config = FunctionsLib::readConfig('', true);

            $this->build_page();
        } else {
            die(FunctionsLib::message($this->_lang['ge_no_permissions']));
        }
    }

    /**
     * method __destruct
     * param
     * return close db connection
     */
    public function __destruct()
    {
        parent::$db->closeConnection();
    }

    /**
     * method build_page
     * param
     * return main method, loads everything
     */
    private function build_page()
    {
        $parse = $this->_lang;
        $parse['alert'] = '';

        if (isset($_POST['opt_save']) && $_POST['opt_save'] == '1') {
            // CHECK BEFORE SAVE
            $this->run_validations();

            FunctionsLib::updateConfig('reg_enable', $this->_game_config['reg_enable']);
            FunctionsLib::updateConfig('reg_welcome_message', $this->_game_config['reg_welcome_message']);
            FunctionsLib::updateConfig('reg_welcome_email', $this->_game_config['reg_welcome_email']);

            $parse['alert'] = AdministrationLib::saveMessage('ok', $this->_lang['ur_all_ok_message']);
        }

        $parse['reg_closed'] = $this->_game_config['reg_enable'] == 1 ? " checked = 'checked' " : "";
        $parse['reg_welcome_message'] = $this->_game_config['reg_welcome_message'] == 1 ? " checked = 'checked' " : "";
        $parse['reg_welcome_email'] = $this->_game_config['reg_welcome_email'] == 1 ? " checked = 'checked' " : "";

        parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate('adm/registration_view'), $parse));
    }

    /**
     * method run_validations
     * param
     * return Run validations before insert data into the configuration file, if some data is not correctly validated it's not inserted.
     */
    private function run_validations()
    {
        // Activate registrations
        if (isset($_POST['reg_closed']) && $_POST['reg_closed'] == 'on') {
            $this->_game_config['reg_enable'] = 1;
        } else {
            $this->_game_config['reg_enable'] = 0;
        }

        // Enable welcome message
        if (isset($_POST['reg_welcome_message']) && $_POST['reg_welcome_message'] == 'on') {
            $this->_game_config['reg_welcome_message'] = 1;
        } else {
            $this->_game_config['reg_welcome_message'] = 0;
        }

        // Enable welcome email
        if (isset($_POST['reg_welcome_email']) && $_POST['reg_welcome_email'] == 'on') {
            $this->_game_config['reg_welcome_email'] = 1;
        } else {
            $this->_game_config['reg_welcome_email'] = 0;
        }
    }
}

/* end of registration.php */
