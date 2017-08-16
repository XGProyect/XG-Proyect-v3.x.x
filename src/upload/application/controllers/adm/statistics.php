<?php
/**
 * Statistics Controller
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

/**
 * Statistics Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Statistics extends XGPCore
{
    private $langs;
    private $current_user;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        AdministrationLib::checkSession();

        $this->langs        = parent::$lang;
        $this->current_user = parent::$users->getUserData();

        // Check if the user is allowed to access
        if (AdministrationLib::haveAccess($this->current_user['user_authlevel'])
            && AdministrationLib::authorization($this->current_user['user_authlevel'], 'config_game') == 1) {

            $this->buildPage();
        } else {

            die(AdministrationLib::noAccessMessage($this->langs['ge_no_permissions']));
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
    private function buildPage()
    {
        $game_stat_level        = FunctionsLib::readConfig('stat_admin_level');
        $game_stat_settings     = FunctionsLib::readConfig('stat_points');
        $game_stat_update_time  = FunctionsLib::readConfig('stat_update_time');
        $this->langs['alert']   = '';

        if (isset($_POST['save']) && ($_POST['save'] == $this->langs['cs_save_changes'])) {

            if (isset($_POST['stat_admin_level']) && is_numeric($_POST['stat_admin_level']) && $_POST['stat_admin_level'] != $game_stat_level) {

                FunctionsLib::updateConfig('stat_admin_level', $_POST['stat_admin_level']);

                $game_stat_level    = $_POST['stat_admin_level'];
                $ASD1               = $_POST['stat_admin_level'];
            }

            if (isset($_POST['stat_points']) && is_numeric($_POST['stat_points']) && $_POST['stat_points'] != $game_stat_settings) {
                FunctionsLib::updateConfig('stat_points', $_POST['stat_points']);

                $game_stat_settings = $_POST['stat_points'];
            }

            if (isset($_POST['stat_update_time']) && is_numeric($_POST['stat_update_time']) && $_POST['stat_update_time'] != $game_stat_update_time) {

                FunctionsLib::updateConfig('stat_update_time', $_POST['stat_update_time']);

                $game_stat_update_time  = $_POST['stat_update_time'];
            }

            $this->langs['alert']   = AdministrationLib::saveMessage('ok', $this->langs['cs_all_ok_message']);
        }

        $this->langs['stat_admin_level']    = $game_stat_level;
        $this->langs['stat_points']         = $game_stat_settings;
        $this->langs['stat_update_time']    = $game_stat_update_time;
        $this->langs['yes']                 = $this->langs['cs_yes'][1];
        $this->langs['no']                  = $this->langs['cs_no'][0];
        $this->langs['admin_levels']        = $this->adminLevels($game_stat_level);
        
        parent::$page->display(
            parent::$page->parseTemplate(parent::$page->getTemplate('adm/statistics_view'),
            $this->langs)
        );
    }
    
    /**
     * adminLevels
     * 
     * @param string $selected Selected level
     * 
     * @return string
     */
    private function adminLevels($selected)
    {
        $options    = '';

        foreach ($this->langs['user_level'] as $id => $name) {

            if ($selected == $id) {
                $sel    = 'selected="selected"';
            } else {
                $sel    = '';
            }
            
            $options    .= '<option value="' . $id . '" ' . $sel . '>' . $name . '</option>\n';
        }
        
        return $options;
    }
}

/* end of statistics.php */
