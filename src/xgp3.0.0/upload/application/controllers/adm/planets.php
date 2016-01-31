<?php

/**
 * Planets Controller.
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
 * Planets Class.
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
class Planets extends XGPCore
{
    private $_current_user;
    private $_game_config;
    private $_lang;

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
            $this->_game_config = FunctionsLib::read_config('', true);

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

        if (isset($_POST['opt_save']) && $_POST['opt_save'] == '1') {
            // CHECK BEFORE SAVE
            $this->run_validations();

            FunctionsLib::update_config('initial_fields', $this->_game_config['initial_fields']);
            FunctionsLib::update_config('metal_basic_income', $this->_game_config['metal_basic_income']);
            FunctionsLib::update_config('crystal_basic_income', $this->_game_config['crystal_basic_income']);
            FunctionsLib::update_config('deuterium_basic_income', $this->_game_config['deuterium_basic_income']);
            FunctionsLib::update_config('energy_basic_income', $this->_game_config['energy_basic_income']);

            $parse['alert'] = AdministrationLib::save_message('ok', $this->_lang['np_all_ok_message']);
        }

        $parse['initial_fields']         = $this->_game_config['initial_fields'];
        $parse['metal_basic_income']     = $this->_game_config['metal_basic_income'];
        $parse['crystal_basic_income']   = $this->_game_config['crystal_basic_income'];
        $parse['deuterium_basic_income'] = $this->_game_config['deuterium_basic_income'];
        $parse['energy_basic_income']    = $this->_game_config['energy_basic_income'];

        parent::$page->display(parent::$page->parse_template(parent::$page->get_template('adm/planets_view'),  $parse));
    }

    /**
     * method run_validations
     * param
     * return Run validations before insert data into the configuration file, if some data is not correctly validated it's not inserted.
     */
    private function run_validations()
    {
        // Initial fields
        if (isset($_POST['initial_fields']) && is_numeric($_POST['initial_fields'])) {
            $this->_game_config['initial_fields'] = $_POST['initial_fields'];
        }

        // Metal production
        if (isset($_POST['metal_basic_income']) && is_numeric($_POST['metal_basic_income'])) {
            $this->_game_config['metal_basic_income'] = $_POST['metal_basic_income'];
        }

        // Crystal production
        if (isset($_POST['crystal_basic_income']) && is_numeric($_POST['crystal_basic_income'])) {
            $this->_game_config['crystal_basic_income'] = $_POST['crystal_basic_income'];
        }

        // Deuterium production
        if (isset($_POST['deuterium_basic_income']) && is_numeric($_POST['deuterium_basic_income'])) {
            $this->_game_config['deuterium_basic_income'] = $_POST['deuterium_basic_income'];
        }

        // Energy production
        if (isset($_POST['energy_basic_income']) && is_numeric($_POST['energy_basic_income'])) {
            $this->_game_config['energy_basic_income'] = $_POST['energy_basic_income'];
        }
    }
}

/* end of planets.php */
