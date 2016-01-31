<?php

/**
 * Fleetmovements Controller.
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
use application\libraries\FleetsLib;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;

/**
 * Fleetmovements Class.
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
class Fleetmovements extends XGPCore
{
    private $_lang;
    private $_current_user;
    private $_flying_fleets;

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
        if (AdministrationLib::have_access($this->_current_user['user_authlevel']) && AdministrationLib::authorization($this->_current_user['user_authlevel'], 'observation') == 1) {
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
        $this->_flying_fleets = parent::$db->query('SELECT f.*,
															(SELECT `user_name`
																FROM `' . USERS . '`
																WHERE `user_id` = f.fleet_owner) AS fleet_username,
															(SELECT `user_name`
																FROM `' . USERS . '`
																WHERE `user_id` = f.fleet_target_owner) AS target_username
														FROM `' . FLEETS . '` AS f
														ORDER BY f.`fleet_end_time` ASC;');

        $parse              = $this->_lang;
        $parse['flt_table'] = $this->flying_fleets_table();

        parent::$page->display(parent::$page->parse_template(parent::$page->get_template('adm/fleet_view'), $parse));
    }

    /**
     * method flying_fleets_table
     * param
     * return the fleets table.
     */
    private function flying_fleets_table()
    {
        $table = '';
        $i     = 0;

        while ($fleet = parent::$db->fetchArray($this->_flying_fleets)) {
            $block['num']       = ++$i;
            $block['mission']   = $this->resources_pop_up($this->_lang['ff_type_mission'][$fleet['fleet_mission']] . ' ' . (FleetsLib::is_fleet_returning($fleet) ? $this->_lang['ff_r'] : $this->_lang['ff_a']), $fleet);
            $block['amount']    = $this->ships_pop_up($this->_lang['ff_ships'], $fleet);
            $block['beginning'] = FormatLib::pretty_coords($fleet['fleet_start_galaxy'], $fleet['fleet_start_system'], $fleet['fleet_start_planet']);
            $block['departure'] = date(FunctionsLib::read_config('date_format_extended'), $fleet['fleet_creation']);
            $block['objective'] = FormatLib::pretty_coords($fleet['fleet_end_galaxy'], $fleet['fleet_end_system'], $fleet['fleet_end_planet']);
            $block['arrival']   = date(FunctionsLib::read_config('date_format_extended'), $fleet['fleet_start_time']);
            $block['return']    = date(FunctionsLib::read_config('date_format_extended'), $fleet['fleet_end_time']);

            $table .= parent::$page->parse_template(parent::$page->get_template('adm/fleet_rows_view'), $block);
        }

        return $table;
    }

    /**
     * method resources_pop_up
     * param
     * return the resources fleet popup.
     */
    private function resources_pop_up($title, $content)
    {
        $total_resources = $content['fleet_resource_metal'] + $content['fleet_resource_crystal'] + $content['fleet_resource_deuterium'];

        if ($total_resources != 0) {
            $resources_popup = $this->_lang['ff_metal'] . ': ' . FormatLib::pretty_number($content['fleet_resource_metal']) . '<br />';
            $resources_popup   .= $this->_lang['ff_crystal'] . ': ' . FormatLib::pretty_number($content['fleet_resource_crystal']) . '<br />';
            $resources_popup   .= $this->_lang['ff_deuterium'] . ': ' . FormatLib::pretty_number($content['fleet_resource_deuterium']);
        } else {
            $resources_popup = $this->_lang['ff_no_resources'];
        }

        $parse['popup_title']   = $title;
        $parse['popup_content'] = $resources_popup;

        return parent::$page->parse_template(parent::$page->get_template('adm/fleet_popup_view'), $parse);
    }

    /**
     * method ships_pop_up
     * param
     * return the ships fleet popup.
     */
    private function ships_pop_up($title, $content)
    {
        $ships  = explode(';', $content['fleet_array']);
        $pop_up = '';

        foreach ($ships as $item => $group) {
            if ($group != '') {
                $ship = explode(',', $group);
                $pop_up       .= $this->_lang['tech'][$ship[0]] . ': ' . FormatLib::pretty_number($ship[1]) . '<br />';
            }
        }

        $parse['popup_title']   = $title;
        $parse['popup_content'] = $pop_up;

        return parent::$page->parse_template(parent::$page->get_template('adm/fleet_popup_view'), $parse);
    }
}

/* end of fleetmovements.php */
