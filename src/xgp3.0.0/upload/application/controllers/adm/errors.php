<?php

/**
 * Errors Controller.
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
 * Errors Class.
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
class Errors extends XGPCore
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
        $parse                = $this->_lang;
        $load_template        = parent::$page->get_template('adm/errors_row_view');
        $deleteall            = isset($_GET['deleteall']) ? $_GET['deleteall'] : '';
        $file                 = XGP_ROOT . LOGS_PATH . 'ErrorLog.php';
        $errors_all           = @file_get_contents($file);
        $i                    = 0;
        $parse['errors_list'] = '';

        if ($errors_all != '') {
            $errors_all = explode('||', $errors_all);

            foreach ($errors_all as $error) {
                $errors_row = explode('|', $error);

                if (isset($errors_row[3])) {
                    ++$i;

                    $parse['errors_list']    .= parent::$page->parse_template($load_template, $errors_row);
                }
            }
        }

        $parse['errors_list_resume'] = $i . $this->_lang['er_errors'];

        if ($deleteall == 'yes') {
            $fh = fopen($file, 'w');
            fclose($fh);
            FunctionsLib::redirect('admin.php?page=errors');
        }

        parent::$page->display(parent::$page->parse_template(parent::$page->get_template('adm/errors_view'), $parse));
    }
}

/* end of errors.php */
