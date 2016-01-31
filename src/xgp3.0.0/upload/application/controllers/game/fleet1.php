<?php

/**
 * Fleet1 Controller.
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

namespace application\controllers\game;

use application\core\XGPCore;
use application\libraries\FleetsLib;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;

/**
 * Fleet1 Class.
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
class Fleet1 extends XGPCore
{
    const MODULE_ID = 8;

    private $_lang;
    private $_current_user;
    private $_current_planet;

    /**
     * __construct().
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->check_session();

        // Check module access
        FunctionsLib::module_message(FunctionsLib::is_module_accesible(self::MODULE_ID));

        $this->_lang           = parent::$lang;
        $this->_current_user   = parent::$users->get_user_data();
        $this->_current_planet = parent::$users->get_planet_data();

        $this->build_page();
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
        $resource = parent::$objects->getObjects();
        $reslist  = parent::$objects->getObjectsList();

        #####################################################################################################
        // SOME DEFAULT VALUES
        #####################################################################################################
        // QUERYS
        $count = parent::$db->queryFetch('SELECT
															(SELECT COUNT(fleet_owner) AS `actcnt`
																FROM ' . FLEETS . "
																WHERE `fleet_owner` = '" . (int) $this->_current_user['user_id'] . "') AS max_fleet,
															(SELECT COUNT(fleet_owner) AS `expedi`
																FROM " . FLEETS . "
																WHERE `fleet_owner` = '" . (int) $this->_current_user['user_id'] . "'
																	AND `fleet_mission` = '15') AS max_expeditions");

        // LOAD TEMPLATES REQUIRED
        $inputs_template    = parent::$page->get_template('fleet/fleet1_inputs');
        $ships_row_template = parent::$page->get_template('fleet/fleet1_row_ships');

        // LANGUAGE
        $this->_lang['js_path'] = XGP_ROOT . JS_PATH;
        $parse                  = $this->_lang;

        $MaxFlyingFleets = $count['max_fleet'];
        $MaxExpedition   = $this->_current_user[$resource[124]];

        if ($MaxExpedition >= 1) {
            $ExpeditionEnCours  = $count['max_expeditions'];
            $EnvoiMaxExpedition = FleetsLib::get_max_expeditions($MaxExpedition);
        } else {
            $ExpeditionEnCours  = 0;
            $EnvoiMaxExpedition = 0;
        }

        $MaxFlottes                  = FleetsLib::get_max_fleets($this->_current_user[$resource[108]], $this->_current_user['premium_officier_admiral']);
        $missiontype                 = FleetsLib::get_missions();
        $galaxy                      = isset($_GET['galaxy']) ? (int) $_GET['galaxy'] : $this->_current_planet['planet_galaxy'];
        $system                      = isset($_GET['system']) ? (int) $_GET['system'] : $this->_current_planet['planet_system'];
        $planet                      = isset($_GET['planet']) ? (int) $_GET['planet'] : $this->_current_planet['planet_planet'];
        $planettype                  = isset($_GET['planettype']) ? (int) $_GET['planettype'] : $this->_current_planet['planet_type'];
        $target_mission              = isset($_GET['target_mission']) ? (int) $_GET['target_mission'] : false;
        $parse['flyingfleets']       = $MaxFlyingFleets;
        $parse['maxfleets']          = $MaxFlottes;
        $parse['currentexpeditions'] = $ExpeditionEnCours;
        $parse['maxexpeditions']     = $EnvoiMaxExpedition;
        $parse['message_nofreeslot'] = ($MaxFlottes == $MaxFlyingFleets) ? parent::$page->parse_template(parent::$page->get_template('fleet/fleet1_noslots_row'), $parse) : '';
        $ships                       = $this->_lang;
        $ShipData                    = '';
        $ship_inputs                 = '';
        $ships_row                   = '';

        foreach ($reslist['fleet'] as $n => $i) {
            if ($this->_current_planet[$resource[$i]] > 0) {
                if ($i == 212) {
                    $ships['fleet_max_speed'] = '-';
                } else {
                    $ships['fleet_max_speed'] = FleetsLib::fleet_max_speed('', $i, $this->_current_user);
                }

                $ships['ship']         = $this->_lang['tech'][$i];
                $ships['amount']       = FormatLib::pretty_number($this->_current_planet[$resource[$i]]);
                $inputs['i']           = $i;
                $inputs['maxship']     = $this->_current_planet[$resource[$i]];
                $inputs['consumption'] = FleetsLib::ship_consumption($i, $this->_current_user);
                $inputs['speed']       = FleetsLib::fleet_max_speed('', $i, $this->_current_user);
                $inputs['capacity']    = isset($pricelist[$i]['capacity']) ? $pricelist[$i]['capacity'] : 0;

                if ($i == 212) {
                    $ships['max_ships'] = '';
                    $ships['set_ships'] = '';
                } else {
                    $ships['max_ships'] = "<a href=\"javascript:maxShip('ship" . $i . "'); shortInfo();\">" . $this->_lang['fl_max'] . '</a>';
                    $ships['set_ships'] = '<input name="ship' . $i . "\" size=\"10\" value=\"0\" onfocus=\"javascript:if(this.value == '0') this.value='';\" onblur=\"javascript:if(this.value == '') this.value='0';\" alt=\"" . $this->_lang['tech'][$i] . $this->_current_planet[$resource[$i]] . '" onChange="shortInfo()" onKeyUp="shortInfo()" />';
                }

                $ship_inputs    .=    parent::$page->parse_template($inputs_template, $inputs);
                $ships_row        .=    parent::$page->parse_template($ships_row_template, $ships);
            }
            $have_ships = true;
        }

        if (!$have_ships) {
            $parse['noships_row'] = parent::$page->parse_template(parent::$page->get_template('fleet/fleet1_noships_row'), $this->_lang);
        } else {
            if ($MaxFlottes > $MaxFlyingFleets) {
                $parse['none_max_selector'] = parent::$page->parse_template(parent::$page->get_template('fleet/fleet_selectors'), $this->_lang);
                $parse['continue_button']   = parent::$page->parse_template(parent::$page->get_template('fleet/fleet1_button'), $this->_lang);
            }
        }

        $parse['body']               = $ships_row;
        $parse['shipdata']           = $ship_inputs;
        $parse['galaxy']             = $galaxy;
        $parse['system']             = $system;
        $parse['planet']             = $planet;
        $parse['planettype']         = $planettype;
        $parse['target_mission']     = $target_mission;
        $parse['envoimaxexpedition'] = $EnvoiMaxExpedition;
        $parse['expeditionencours']  = $ExpeditionEnCours;
        $parse['target_mission']     = $target_mission;

        parent::$page->display(parent::$page->parse_template(parent::$page->get_template('fleet/fleet1_table'), $parse));
    }
}

/* end of fleet1.php */
