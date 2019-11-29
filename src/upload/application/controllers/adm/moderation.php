<?php
/**
 * Moderation Controller
 *
 * PHP Version 7.1+
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
 * Moderation Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Moderation extends Controller
{

    private $_current_user;
    private $_lang;

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
        if (AdministrationLib::haveAccess($this->_current_user['user_authlevel']) && $this->_current_user['user_authlevel'] == 3) {
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
        $parse['alert'] = '';

        if (isset($_POST['mode']) && $_POST['mode']) {
            $view_m = isset($_POST['view_m']) ? $this->set_rank($_POST['view_m']) : '';
            $edit_m = isset($_POST['edit_m']) ? $this->set_rank($_POST['edit_m']) : '';
            $config_m = isset($_POST['config_m']) ? $this->set_rank($_POST['config_m']) : '';
            $tools_m = isset($_POST['tools_m']) ? $this->set_rank($_POST['tools_m']) : '';
            $log_m = isset($_POST['log_m']) ? $this->set_rank($_POST['log_m']) : '';
            $maintenance_m = isset($_POST['maintenance_m']) ? $this->set_rank($_POST['maintenance_m']) : '';
            $view_o = isset($_POST['view_o']) ? $this->set_rank($_POST['view_o']) : '';
            $edit_o = isset($_POST['edit_o']) ? $this->set_rank($_POST['edit_o']) : '';
            $config_o = isset($_POST['config_o']) ? $this->set_rank($_POST['config_o']) : '';
            $tools_o = isset($_POST['tools_o']) ? $this->set_rank($_POST['tools_o']) : '';
            $log_o = isset($_POST['log_o']) ? $this->set_rank($_POST['log_o']) : '';
            $maintenance_o = isset($_POST['maintenance_o']) ? $this->set_rank($_POST['maintenance_o']) : '';
            $log_a = isset($_POST['log_a']) ? $this->set_rank($_POST['log_a']) : '';

            $QueryEdit = $view_m . "," . $edit_m . "," . $config_m . "," . $tools_m . "," . $log_m . "," . $maintenance_m . ";" . $view_o . "," . $edit_o . "," . $config_o . "," . $tools_o . "," . $log_o . "," . $maintenance_o . ";" . $log_a . ";";

            FunctionsLib::updateConfig('moderation', $QueryEdit);

            $parse['alert'] = AdministrationLib::saveMessage('ok', $this->_lang['mod_all_ok_message']);
        }

        $QueryModeration = FunctionsLib::readConfig('moderation');
        $QueryModerationEx = explode(";", $QueryModeration);
        $Moderator = explode(",", $QueryModerationEx[0]);
        $Operator = explode(",", $QueryModerationEx[1]);
        $Administrator = explode(",", $QueryModerationEx[2]);

        // Operator (GO)
        if ($Moderator[0] == 1) {
            $parse['view_m'] = 'checked = "checked"';
        }
        if ($Moderator[1] == 1) {
            $parse['edit_m'] = 'checked = "checked"';
        }
        if ($Moderator[2] == 1) {
            $parse['config_m'] = 'checked = "checked"';
        }
        if ($Moderator[3] == 1) {
            $parse['tools_m'] = 'checked = "checked"';
        }
        if ($Moderator[4] == 1) {
            $parse['log_m'] = 'checked = "checked"';
        }
        if ($Moderator[5] == 1) {
            $parse['maintenance_m'] = 'checked = "checked"';
        }

        // Super Operator (SGo)
        if ($Operator[0] == 1) {
            $parse['view_o'] = 'checked = "checked"';
        }
        if ($Operator[1] == 1) {
            $parse['edit_o'] = 'checked = "checked"';
        }
        if ($Operator[2] == 1) {
            $parse['config_o'] = 'checked = "checked"';
        }
        if ($Operator[3] == 1) {
            $parse['tools_o'] = 'checked = "checked"';
        }
        if ($Operator[4] == 1) {
            $parse['log_o'] = 'checked = "checked"';
        }
        if ($Operator[5] == 1) {
            $parse['maintenance_o'] = 'checked = "checked"';
        }

        // Administrator (GA) (SOLO PARA EL HISTORIAL)
        if ($Administrator[0] == 1) {
            $parse['log_a'] = 'checked = "checked"';
        }

        $parse['mods'] = $this->_lang['user_level'][1];
        $parse['oper'] = $this->_lang['user_level'][2];
        $parse['adm'] = $this->_lang['user_level'][3];

        parent::$page->displayAdmin(
            $this->getTemplate()->set('adm/moderation_view', $parse)
        );
    }

    /**
     * method set_rank
     * param $rank
     * return return the rank value
     */
    private function set_rank($rank)
    {
        if ($rank == 'on') {
            return 1;
        } else {
            return 0;
        }
    }
}

/* end of moderation.php */
