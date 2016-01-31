<?php

/**
 * Modules Controller.
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
 * Modules Class.
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
class Modules extends XGPCore
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
        if (AdministrationLib::have_access($this->_current_user['user_authlevel']) && AdministrationLib::authorization($this->_current_user['user_authlevel'], 'edit_users') == 1) {
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
        $modules_array  = '';
        $modules_count  = count(explode(';', FunctionsLib::read_config('modules')));
        $row_template   = parent::$page->get_template('adm/modules_row_view');
        $module_rows    = '';
        $parse['alert'] = '';

        // SAVE PAGE
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['save']) {
            for ($i = 0; $i <= $modules_count - 2; ++$i) {
                $modules_array    .= ((isset($_POST["status{$i}"])) ? 1 : 0) . ';';
            }

            FunctionsLib::update_config('modules', $modules_array);

            $parse['alert'] = AdministrationLib::save_message('ok', $this->_lang['se_all_ok_message']);
        }

        // SHOW PAGE
        $modules_array = explode(';', FunctionsLib::read_config('modules'));

        foreach ($modules_array as $module => $status) {
            if ($status != null) {
                $parse['module']       = $module;
                $parse['module_name']  = $this->_lang['module'][$module];
                $parse['module_value'] = ($status == 1) ? 'checked' : '';
                $parse['color']        = ($status == 1) ? 'text-success' : 'text-error';

                $module_rows    .= parent::$page->parse_template($row_template, $parse);
            }
        }

        $parse['module_rows'] = $module_rows;

        parent::$page->display(parent::$page->parse_template(parent::$page->get_template('adm/modules_view'), $parse));
    }
}

/* end of modules.php */
