<?php
/**
 * Buildstats Controller
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
use application\libraries\StatisticsLib;

/**
 * Buildstats Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Buildstats extends XGPCore
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
        if (AdministrationLib::haveAccess($this->_current_user['user_authlevel']) && AdministrationLib::authorization($this->_current_user['user_authlevel'], 'use_tools') == 1) {
            $this->build_page();
        } else {
            die(AdministrationLib::noAccessMessage($this->_lang['ge_no_permissions']));
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
        // RUN STATISTICS SCRIPT AND THE SET THE RESULT
        $result = StatisticsLib::makeStats();

        // PREPARE DATA TO PARSE
        $parse = $this->_lang;
        $parse['memory_p'] = str_replace(array("%p", "%m"), $result['memory_peak'], $this->_lang['sb_top_memory']);
        $parse['memory_e'] = str_replace(array("%e", "%m"), $result['end_memory'], $this->_lang['sb_final_memory']);
        $parse['memory_i'] = str_replace(array("%i", "%m"), $result['initial_memory'], $this->_lang['sb_start_memory']);
        $parse['alert'] = AdministrationLib::saveMessage('ok', str_replace("%t", $result['totaltime'], $this->_lang['sb_stats_update']));

        // UPDATE STATISTICS LAST UPDATE
        FunctionsLib::updateConfig('stat_last_update', $result['stats_time']);

        // SHOW TEMPLATE
        parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate('adm/buildstats_view'), $parse));
    }
}

/* end of buildstats.php */
