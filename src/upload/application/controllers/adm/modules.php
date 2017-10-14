<?php
/**
 * Modules Controller
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

use application\core\Controller;
use application\libraries\adm\AdministrationLib;
use application\libraries\FunctionsLib;

/**
 * Modules Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Modules extends Controller
{

    private $_lang;
    private $_current_user;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        AdministrationLib::checkSession();

        $this->_lang = parent::$lang;
        $this->_current_user = parent::$users->getUserData();

        // Check if the user is allowed to access
        if (AdministrationLib::haveAccess($this->_current_user['user_authlevel']) && AdministrationLib::authorization($this->_current_user['user_authlevel'], 'edit_users') == 1) {
            $this->build_page();
        } else {
            die(AdministrationLib::noAccessMessage($this->_lang['ge_no_permissions']));
        }
    }

    /**
     * method build_page
     * param
     * return main method, loads everything
     */
    private function build_page()
    {
        $parse = $this->_lang;
        $modules_array = '';
        $modules_count = count(explode(';', FunctionsLib::readConfig('modules')));
        $row_template = parent::$page->getTemplate('adm/modules_row_view');
        $module_rows = '';
        $parse['alert'] = '';

        // SAVE PAGE
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['save']) {
            for ($i = 0; $i <= $modules_count - 2; $i++) {
                $modules_array .= ( ( isset($_POST["status{$i}"]) ) ? 1 : 0 ) . ';';
            }

            FunctionsLib::updateConfig('modules', $modules_array);

            $parse['alert'] = AdministrationLib::saveMessage('ok', $this->_lang['se_all_ok_message']);
        }

        // SHOW PAGE
        $modules_array = explode(';', FunctionsLib::readConfig('modules'));

        foreach ($modules_array as $module => $status) {
            if ($status != NULL) {
                $parse['module'] = $module;
                $parse['module_name'] = $this->_lang['module'][$module];
                $parse['module_value'] = ( $status == 1 ) ? 'checked' : '';
                $parse['color'] = ( $status == 1 ) ? 'text-success' : 'text-error';

                $module_rows .= parent::$page->parseTemplate($row_template, $parse);
            }
        }

        $parse['module_rows'] = $module_rows;

        parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate("adm/modules_view"), $parse));
    }
}

/* end of modules.php */
