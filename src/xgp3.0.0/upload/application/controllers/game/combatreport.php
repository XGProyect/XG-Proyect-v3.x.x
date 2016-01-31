<?php
/**
 * Combatreport Controller
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

namespace application\controllers\game;

use application\core\XGPCore;
use application\libraries\FunctionsLib;

/**
 * Combatreport Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Combatreport extends XGPCore
{
    const MODULE_ID = 23;

    private $langs;
    private $current_user;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->check_session();

        // Check module access
        FunctionsLib::module_message(FunctionsLib::is_module_accesible(self::MODULE_ID));

        $this->langs        = parent::$lang;
        $this->current_user = parent::$users->get_user_data();

        $this->buildPage();
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
    private function buildPage()
    {
        $report     = isset($_GET['report']) ? $_GET['report'] : die();
        $reportrow  = parent::$db->queryFetch(
            "SELECT *
            FROM " .  REPORTS . "
            WHERE `report_rid` = '" . (parent::$db->escapeValue($report)) . "';"
        );
        
        // Get owners
        $owners = explode(',', $reportrow['report_owners']);

        // Block other people
        if (!in_array($this->current_user['user_id'], $owners)) {
            die();
        }
        
        // When the fleet was destroyed in the first row
        if (($owners[0] == $this->current_user['user_id']) && ($reportrow['report_destroyed'] == 1)) {

            $page   = parent::$page->parse_template(
                parent::$page->get_template('combatreport/combatreport_no_fleet_view'),
                $this->langs
            );
        } else {
            
            // Any other case
            $report = stripslashes($reportrow['report_content']);

            foreach ($this->langs['tech_rc'] as $id => $s_name) {

                $search     = array($id);
                $replace    = array($s_name);
                $report     = str_replace($search, $replace, $report);
            }

            $no_fleet   = parent::$page->parse_template(
                parent::$page->get_template('combatreport/combatreport_no_fleet_view'),
                $this->langs
            );
            
            $destroyed  = parent::$page->parse_template(
                parent::$page->get_template('combatreport/combatreport_destroyed_view'),
                $this->langs
            );
            
            $search     = array($no_fleet);
            $replace    = array($destroyed);
            $report     = str_replace($search, $replace, $report);
            $page       = $report;
        }

        parent::$page->display($page, false, '', false);
    }
}

/* end of combatreport.php */
