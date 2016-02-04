<?php
/**
 * Information Controller
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
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;

/**
 * Information Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Information extends XGPCore
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
        parent::$users->checkSession();

        $this->_lang = parent::$lang;
        $this->_current_user = parent::$users->getUserData();

        // Check if the user is allowed to access
        if (AdministrationLib::haveAccess($this->_current_user['user_authlevel']) && AdministrationLib::authorization($this->_current_user['user_authlevel'], 'observation') == 1) {
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
        $update = FunctionsLib::readConfig('stat_last_update');
        $backup = FunctionsLib::readConfig('last_backup');
        $cleanup = FunctionsLib::readConfig('last_cleanup');
        $modules = explode(';', FunctionsLib::readConfig('modules'));
        $count_modules = 0;

        // COUNT MODULES
        foreach ($modules as $module) {
            if ($module == 1) {
                $count_modules++;
            }
        }

        // LOAD STATISTICS
        $inactive_time = ( time() - 60 * 60 * 24 * 7 );
        $users_count = parent::$db->queryFetch("SELECT (
																SELECT COUNT(user_id)
																	FROM " . USERS . "
															 ) AS users_count,

															 ( SELECT COUNT(user_id)
															 		FROM " . USERS . "
															 		WHERE user_onlinetime < {$inactive_time} AND user_onlinetime <> 0
															 ) AS inactive_count,

															 ( SELECT COUNT(setting_user_id)
															 		FROM " . SETTINGS . "
															 		WHERE setting_vacations_status <> 0
															 ) AS on_vacation,

															 ( SELECT COUNT(setting_user_id)
															 		FROM " . SETTINGS . "
															 		WHERE setting_delete_account <> 0
															 ) AS to_delete,

															 ( SELECT COUNT(user_id)
															 		FROM " . USERS . "
															 		WHERE user_banned <> 0
															 ) AS banned_users,

															 ( SELECT COUNT(fleet_id)
															 		FROM " . FLEETS . "
															 ) AS fleets_count");

        // LOAD STATISTICS
        $db_tables = parent::$db->query("SHOW TABLE STATUS");
        $db_size = 0;

        while ($row = parent::$db->fetchArray($db_tables)) {
            $db_size += $row['Data_length'] + $row['Index_length'];
        }

        // PARSE STATISTICS
        $parse['info_points'] = date(FunctionsLib::readConfig('date_format_extended'), $update) . ' | ' . FormatLib::prettyTime(( time() - $update));
        $parse['info_backup'] = date(FunctionsLib::readConfig('date_format_extended'), $backup) . ' | ' . FormatLib::prettyTime(( time() - $backup));
        $parse['info_cleanup'] = date(FunctionsLib::readConfig('date_format_extended'), $cleanup) . ' | ' . FormatLib::prettyTime(( time() - $cleanup));
        $parse['info_modules'] = $count_modules . '/' . ( count($modules) - 1 );
        $parse['info_total_users'] = $users_count['users_count'];
        $parse['info_inactive_users'] = $users_count['inactive_count'];
        $parse['info_vacation_users'] = $users_count['on_vacation'];
        $parse['info_delete_mode_users'] = $users_count['to_delete'];
        $parse['info_banned_users'] = $users_count['banned_users'];
        $parse['info_flying_fleets'] = $users_count['fleets_count'];
        $parse['info_database_size'] = round($db_size / 1024, 1) . ' kb';
        $parse['info_database_server'] = 'MySQL ' . parent::$db->serverInfo();

        parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate("adm/information_view"), $parse));
    }
}

/* end of information.php */
