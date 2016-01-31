<?php

/**
 * Statistics Controller.
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
 * Statistics Class.
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
class Statistics extends XGPCore
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
        $game_stat             = FunctionsLib::read_config('stat');
        $game_stat_level       = FunctionsLib::read_config('stat_level');
        $game_stat_settings    = FunctionsLib::read_config('stat_settings');
        $game_stat_update_time = FunctionsLib::read_config('stat_update_time');
        $this->_lang['alert']  = '';

        if (isset($_POST['save']) && ($_POST['save'] == $this->_lang['cs_save_changes'])) {
            if (isset($_POST['stat']) && $_POST['stat'] != $game_stat) {
                FunctionsLib::update_config('stat', $_POST['stat']);

                $game_stat = $_POST['stat'];
                $ASD3      = $_POST['stat'];
            }

            if (isset($_POST['stat_level']) && is_numeric($_POST['stat_level']) && $_POST['stat_level'] != $game_stat_level) {
                FunctionsLib::update_config('stat_level',  $_POST['stat_level']);

                $game_stat_level = $_POST['stat_level'];
                $ASD1            = $_POST['stat_level'];
            }

            if (isset($_POST['stat_settings']) &&  is_numeric($_POST['stat_settings']) && $_POST['stat_settings'] != $game_stat_settings) {
                FunctionsLib::update_config('stat_settings',  $_POST['stat_settings']);

                $game_stat_settings = $_POST['stat_settings'];
            }

            if (isset($_POST['stat_update_time']) && is_numeric($_POST['stat_update_time']) && $_POST['stat_update_time'] != $game_stat_update_time) {
                FunctionsLib::update_config('stat_update_time',  $_POST['stat_update_time']);

                $game_stat_update_time = $_POST['stat_update_time'];
            }

            $this->_lang['alert'] = AdministrationLib::save_message('ok', $this->_lang['cs_all_ok_message']);
        }

        $selected                        = 'selected="selected"';
        $stat                            = (($game_stat == 1) ? 'sel_sta1' : 'sel_sta0');
        $this->_lang[$stat]              = $selected;
        $this->_lang['stat_level']       = $game_stat_level;
        $this->_lang['stat_settings']    = $game_stat_settings;
        $this->_lang['stat_update_time'] = $game_stat_update_time;
        $this->_lang['yes']              = $this->_lang['cs_yes'][1];
        $this->_lang['no']               = $this->_lang['cs_no'][0];

        parent::$page->display(parent::$page->parse_template(parent::$page->get_template('adm/statistics_view'), $this->_lang));
    }
}

/* end of statistics.php */
