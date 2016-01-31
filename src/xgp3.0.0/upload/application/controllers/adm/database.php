<?php

/**
 * Database Controller.
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
 * Database Class.
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
class Database extends XGPCore
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
            $this->build_page($this->_current_user);
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
        $template       = parent::$page->get_template('adm/data_row_view');
        $parse['alert'] = '';

        if (!$_POST) {
            $Tablas         = parent::$db->query('SHOW TABLES', 'todas');
            $parse['tabla'] = '';

            while ($row = parent::$db->fetchArray($Tablas)) {
                $row['row']          = $row[0];
                $row['status_style'] = 'text-info';
                $row['status']       = $this->_lang['db_select_action'];
                $parse['tabla']       .= parent::$page->parse_template($template, $row);
            }
        } else {
            $Tablas         = parent::$db->query('SHOW TABLES', 'todas');
            $parse['tabla'] = '';

            while ($row = parent::$db->fetchArray($Tablas)) {
                if (isset($_POST['Optimize'])) {
                    parent::$db->query("OPTIMIZE TABLE {$row[0]}", "$row[0]");
                    $Message = $this->_lang['db_opt'];
                }

                if (isset($_POST['Repair'])) {
                    parent::$db->query("REPAIR TABLE {$row[0]}", "$row[0]");
                    $Message = $this->_lang['db_rep'];
                }

                if (isset($_POST['Check'])) {
                    parent::$db->query("CHECK TABLE {$row[0]} ", "$row[0]");
                    $Message = $this->_lang['db_check_ok'];
                }

                if (mysql_errno()) {
                    $row['row']          = $row[0];
                    $row['status_style'] = 'text-error';
                    $row['status']       = $this->_lang['db_not_opt'];
                    $parse['tabla']       .= parent::$page->parse_template($template, $row);
                } else {
                    $row['row']          = $row[0];
                    $row['status_style'] = 'text-success';
                    $row['status']       = $Message;
                    $parse['tabla']       .= parent::$page->parse_template($template, $row);
                }
            }
        }

        parent::$page->display(parent::$page->parse_template(parent::$page->get_template('adm/data_view'), $parse));
    }
}

/* end of database.php */
