<?php
/**
 * Users Controller
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
use application\core\Database;
use application\libraries\adm\AdministrationLib;
use application\libraries\FunctionsLib;
use application\libraries\Statistics_library;

/**
 * Users Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Users extends Controller
{

    private $_lang;
    private $_edit;
    private $_planet;
    private $_moon;
    private $_id;
    private $_authlevel;
    private $_alert_info;
    private $_alert_type;
    private $_user_query;
    private $_current_user;
    private $_stats;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        AdministrationLib::checkSession();

        $this->_db = new Database();
        $this->_lang = parent::$lang;
        $this->_current_user = parent::$users->getUserData();
        $this->_stats = new Statistics_library();

        // Check if the user is allowed to access
        if (AdministrationLib::haveAccess($this->_current_user['user_authlevel']) && AdministrationLib::authorization($this->_current_user['user_authlevel'], 'edit_users') == 1) {
            $this->build_page();
        } else {
            die(AdministrationLib::noAccessMessage($this->_lang['ge_no_permissions']));
        }
    }
    ######################################
    #
    # main methods
    #
    ######################################

    /**
     * method build_page
     * param
     * return main method, loads everything
     */
    private function build_page()
    {
        $parse = $this->_lang;
        $user = isset($_GET['user']) ? trim($_GET['user']) : NULL;
        $type = isset($_GET['type']) ? trim($_GET['type']) : NULL;
        $this->_edit = isset($_GET['edit']) ? trim($_GET['edit']) : NULL;
        $this->_planet = isset($_GET['planet']) ? trim($_GET['planet']) : NULL;
        $this->_moon = isset($_GET['moon']) ? trim($_GET['moon']) : NULL;

        $parse['alert'] = '';

        if ($user != '') {

            if (!$this->check_user($user)) {

                $parse['alert'] = AdministrationLib::saveMessage('error', $this->_lang['us_nothing_found']);
                $user = '';
            } else {

                $this->_user_query = $this->_db->queryFetch(
                    "SELECT u.*,
                            p.*,
                            pr.*,
                            r.*
                    FROM " . USERS . " AS u
                        INNER JOIN " . PREFERENCES . " AS pr ON pr.preference_user_id = u.user_id
                        INNER JOIN " . PREMIUM . " AS p ON p.premium_user_id = u.user_id
                        INNER JOIN " . RESEARCH . " AS r ON r.research_user_id = u.user_id
                    WHERE (u.user_id = '{$this->_id}') LIMIT 1;"
                );

                // save the data
                if (isset($_POST['send_data']) && $_POST['send_data']) {

                    $this->save_data($type);
                }

                $this->_user_query = $this->_db->queryFetch(
                    "SELECT u.*,
                            p.*,
                            pr.*,
                            r.*
                    FROM " . USERS . " AS u
                        INNER JOIN " . PREFERENCES . " AS pr ON pr.preference_user_id = u.user_id
                        INNER JOIN " . PREMIUM . " AS p ON p.premium_user_id = u.user_id
                        INNER JOIN " . RESEARCH . " AS r ON r.research_user_id = u.user_id
                    WHERE (u.user_id = '{$this->_id}') LIMIT 1;"
                );
            }
        }

        // physical delete
        if (isset($_GET['mode']) && $_GET['mode'] == 'delete' && $this->_user_query['user_authlevel'] != 3) {

            parent::$users->deleteUser($this->_user_query['user_id']);

            $parse['alert'] = AdministrationLib::saveMessage('ok', $this->_lang['us_user_deleted']);
        }

        $parse['type'] = ($type != '') ? $type : 'info';
        $parse['user'] = ($user != '') ? $user : '';
        $parse['status'] = ($user != '') ? '' : ' disabled';
        $parse['status_box'] = ($user != '' && $this->_id != $this->_current_user['user_id']) ? '' : ' disabled';
        $parse['tag'] = ($user != '') ? 'a' : 'button';
        $parse['user_rank'] = AdministrationLib::returnRank($this->_authlevel);
        $parse['content'] = ($user != '' && $type != '') ? $this->get_data($type) : '';

        parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate("adm/users_view"), $parse));
    }

    /**
     * method get_data
     * param $type
     * return the page for the current type
     */
    private function get_data($type)
    {
        switch ($type) {
            case 'info':
            case '':
            default:

                return $this->get_data_info();

                break;

            case 'settings':

                return $this->get_data_settings();

                break;

            case 'research':

                return $this->get_data_research();

                break;

            case 'premium':

                return $this->get_data_premium();

                break;

            case 'planets':

                return $this->get_data_planets();

                break;

            case 'moons':

                return $this->get_data_moons();

                break;
        }
    }

    /**
     * method save_data
     * param $type
     * return save data for the current type
     */
    private function save_data($type)
    {
        switch ($type) {
            case 'info':
            case '':
            default:

                $this->save_info();

                break;

            case 'settings':

                $this->save_settings();

                break;

            case 'research':


                $this->save_research();

                break;

            case 'premium':

                $this->save_premium();

                break;

            case 'planets':

                switch ($this->_edit) {
                    case '':
                    case 'planet':
                    default:

                        $this->save_planet(1);

                        break;

                    case 'buildings':

                        $this->saveBuildings(1);

                        break;

                    case 'ships':

                        $this->save_ships(1);

                        break;

                    case 'defenses':

                        $this->save_defenses(1);

                        break;
                }

                break;

            case 'moons':

                switch ($this->_edit) {
                    case '':
                    case 'moon':
                    default:

                        $this->save_planet(3);

                        break;

                    case 'buildings':

                        $this->saveBuildings(3);

                        break;

                    case 'ships':

                        $this->save_ships(3);

                        break;

                    case 'defenses':

                        $this->save_defenses(3);

                        break;
                }

                break;
        }
    }

    /**
     * delete_data
     * 
     * @param type $type Type
     * 
     * @return void
     */
    private function delete_data($type)
    {
        switch ($type) {
            case 'planet':
                //$this->delete_planet();

                break;

            case 'moon':
                //$this->delete_moon();

                break;
        }
    }

    /**
     * method refresh_page
     * param
     * return refresh the page
     */
    private function refresh_page()
    {
        // SET PARAMS
        $page = ( isset($_GET['page']) ? '?page=' . $_GET['page'] : '' );
        $type = ( isset($_GET['type']) ? '&type=' . $_GET['type'] : '' );
        $user = ( isset($_GET['user']) ? '&user=' . $_GET['user'] : '' );

        // REDIRECTION
        FunctionsLib::redirect("admin.php{$page}{$type}{$user}");
    }
    ######################################
    #
    # get_data methods
    #
    ######################################

    /**
     * method get_data_info
     * param
     * return the information page for the current user
     */
    private function get_data_info()
    {
        $parse = $this->_lang;
        $parse += (array) $this->_user_query;
        $parse['information'] = str_replace('%s', $this->_user_query['user_name'], $this->_lang['us_user_information']);
        $parse['main_planet'] = $this->build_planet_combo($this->_user_query, 'user_home_planet_id');
        $parse['current_planet'] = $this->build_planet_combo($this->_user_query, 'user_current_planet');
        $parse['alliances'] = $this->build_alliance_combo($this->_user_query);
        $parse['user_register_time'] = ( $this->_user_query['user_register_time'] == 0 ) ? '-' : date(FunctionsLib::readConfig('date_format_extended'), $this->_user_query['user_register_time']);
        $parse['user_onlinetime'] = $this->last_activity($this->_user_query['user_onlinetime']);
        $parse['sel' . $this->_user_query['user_authlevel']] = 'selected';
        $parse['user_banned'] = ( $this->_user_query['user_banned'] <= 0 ) ? '<p class="text-error">' . $this->_lang['ge_no'] : '<p class="text-success">' . $this->_lang['ge_yes'];
        $parse['user_banned'] .= ( $this->_user_query['user_banned'] > 0 ) ? $this->_lang['us_user_information_banned_until'] . date(FunctionsLib::readConfig('date_format'), $this->_user_query['user_banned']) . '</p>' : '</p>';
        $parse['user_fleet_shortcuts'] = $this->build_shortcuts_combo($this->_user_query['user_fleet_shortcuts']);
        $parse['alert_info'] = ( $this->_alert_type != '' ) ? AdministrationLib::saveMessage($this->_alert_type, $this->_alert_info) : '';

        return parent::$page->parseTemplate(parent::$page->getTemplate("adm/users_information_view"), $parse);
    }

    /**
     * method get_settings_info
     * param
     * return the settings page for the current user
     */
    private function get_data_settings()
    {
        $parse = $this->_lang;
        $parse['settings'] = str_replace('%s', $this->_user_query['user_name'], $this->_lang['us_user_settings']);
        $parse['preference_planet_sort'] = $this->planet_sort_combo();
        $parse['preference_planet_sort_sequence'] = $this->planet_order_combo();
        $parse['preference_spy_probes'] = $this->_user_query['preference_spy_probes'];
        $parse['preference_vacations_status'] = ( $this->_user_query['preference_vacation_mode'] > 0 ) ? ' checked="checked" ' : '';
        $parse['preference_vacation_mode'] = ( $this->_user_query['preference_vacation_mode'] > 0 ) ? $this->vacation_set() : '';
        $parse['preference_delete_mode'] = ( $this->_user_query['preference_delete_mode'] ) ? ' checked="checked" ' : '';
        $parse['alert_info'] = ( $this->_alert_type != '' ) ? AdministrationLib::saveMessage($this->_alert_type, $this->_alert_info) : '';

        return parent::$page->parseTemplate(parent::$page->getTemplate("adm/users_settings_view"), $parse);
    }

    /**
     * method get_research_info
     * param
     * return the research page for the current user
     */
    private function get_data_research()
    {
        $parse = $this->_lang;
        $parse += (array) $this->_user_query;
        $parse['research'] = str_replace(array('%s', '%d'), array($this->_user_query['user_name'], $this->_id), $this->_lang['us_user_research']);
        $parse['technologies_table'] = $this->research_table();
        $parse['alert_info'] = ( $this->_alert_type != '' ) ? AdministrationLib::saveMessage($this->_alert_type, $this->_alert_info) : '';

        return parent::$page->parseTemplate(parent::$page->getTemplate("adm/users_research_view"), $parse);
    }

    /**
     * method get_data_premium
     * param
     * return the premium page for the current user
     */
    private function get_data_premium()
    {
        $parse = $this->_lang;
        $parse['premium'] = str_replace('%s', $this->_user_query['user_name'], $this->_lang['us_user_premium']);
        $parse['premium_dark_matter'] = $this->_user_query['premium_dark_matter'];
        $parse['premium_table'] = $this->premium_table();
        $parse['alert_info'] = ( $this->_alert_type != '' ) ? AdministrationLib::saveMessage($this->_alert_type, $this->_alert_info) : '';

        return parent::$page->parseTemplate(parent::$page->getTemplate("adm/users_premium_view"), $parse);
    }

    /**
     * method get_data_planets
     * param
     * return the planets page for the current user
     */
    private function get_data_planets()
    {
        $sub_query = '';

        // CHOOSE THE ACTION
        switch ($this->_edit) {
            case 'planet':

                $get_query = 'p.* ';

                break;

            case 'buildings':

                $get_query = 'b.* ';

                break;

            case 'ships':

                $get_query = 's.* ';

                break;

            case 'defenses':

                $get_query = 'd.* ';

                break;


            case '':
            default:

                $get_query = 'p.*, b.*, d.*, s.*,
                    m.planet_id AS moon_id,
                    m.planet_name AS moon_name,
                    m.planet_image AS moon_image,
                    m.planet_destroyed AS moon_destroyed ';

                break;
        } // SWITCH

        if ($this->_planet > 0) {
            $sub_query = ' AND p.`planet_id` = ' . $this->_planet;
        }

        $planets_query = $this->_db->query("SELECT {$get_query}
            FROM " . PLANETS . " AS p
            INNER JOIN " . BUILDINGS . " AS b ON b.building_planet_id = p.`planet_id`
            INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id`
            INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id`
            LEFT JOIN " . PLANETS . " AS m ON m.planet_id = (SELECT mp.`planet_id`
            FROM " . PLANETS . " AS mp
            WHERE (mp.planet_galaxy=p.planet_galaxy AND
                mp.planet_system=p.planet_system AND
                mp.planet_planet=p.planet_planet AND
                mp.planet_type=3))
            WHERE p.`planet_user_id` = '" . $this->_id . "'
                AND p.`planet_type` = 1{$sub_query};");

        $parse = $this->_lang;
        $parse['planets'] = str_replace('%s', $this->_user_query['user_name'], $this->_lang['us_user_planets']);

        // CHOOSE THE ACTION
        switch ($this->_edit) {
            case 'planet':

                $parse += $this->edit_main($planets_query);
                $view = parent::$page->parseTemplate(parent::$page->getTemplate("adm/users_planets_main_view"), $parse);

                break;

            case 'buildings':

                $parse['buildings_table'] = $this->edit_buildings($planets_query, 1);
                $view = parent::$page->parseTemplate(parent::$page->getTemplate("adm/users_planets_buildings_view"), $parse);

                break;

            case 'ships':

                $parse['ships_table'] = $this->edit_ships($planets_query);
                $view = parent::$page->parseTemplate(parent::$page->getTemplate("adm/users_planets_ships_view"), $parse);

                break;

            case 'defenses':

                $parse['defenses_table'] = $this->edit_defenses($planets_query, 1);
                $view = parent::$page->parseTemplate(parent::$page->getTemplate("adm/users_planets_defenses_view"), $parse);

                break;

            case 'delete':

                $this->_db->query(
                    "UPDATE " . PLANETS . " AS p, " . PLANETS . " AS m, " . USERS . " AS u SET
                    p.`planet_destroyed` = '" . (time() + (PLANETS_LIFE_TIME * 3600)) . "',
                    m.`planet_destroyed` = '" . (time() + (PLANETS_LIFE_TIME * 3600)) . "',
                    u.`user_current_planet` = u.`user_home_planet_id`
                    WHERE p.`planet_id` = '" . (int) $this->_planet . "' AND
                                    m.`planet_galaxy` = p.`planet_galaxy` AND
                                    m.`planet_system` = p.`planet_system` AND
                                    m.`planet_planet` = p.`planet_planet` AND
                                    m.`planet_type` = '3';"
                );

                $this->refresh_page();

                break;

            case '':
            default:

                $parse['planets_table'] = $this->planets_table($planets_query);
                $view = parent::$page->parseTemplate(parent::$page->getTemplate("adm/users_planets_view"), $parse);

                break;
        } // SWITCH

        $parse['alert_info'] = ( $this->_alert_type != '' ) ? AdministrationLib::saveMessage($this->_alert_type, $this->_alert_info) : '';

        return $view;
    }

    /**
     * method get_data_moons
     * param
     * return the moons page for the current user
     */
    private function get_data_moons()
    {
        $sub_query = '';

        // CHOOSE THE ACTION
        switch ($this->_edit) {
            case 'moon':

                $get_query = 'm.* ';

                break;

            case 'buildings':

                $get_query = 'b.* ';

                break;

            case 'ships':

                $get_query = 's.* ';

                break;

            case 'defenses':

                $get_query = 'd.* ';

                break;

            case '':
            default:

                $get_query = 'm.*, b.*, d.*, s.*';

                break;
        } // SWITCH

        if ($this->_moon > 0) {
            $sub_query = ' AND m.`planet_id` = ' . $this->_moon;
        }

        $moons_query = $this->_db->query(
            "SELECT {$get_query}
            FROM " . PLANETS . " AS m
            INNER JOIN " . BUILDINGS . " AS b ON b.building_planet_id = m.planet_id
            INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = m.planet_id
            INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = m.planet_id
            WHERE m.`planet_user_id` = '" . $this->_id . "'
                            AND m.`planet_type` = 3{$sub_query};"
        );

        $parse = $this->_lang;
        $parse['moons'] = str_replace('%s', $this->_user_query['user_name'], $this->_lang['us_user_moons']);

        // CHOOSE THE ACTION
        switch ($this->_edit) {
            case 'moon':

                $parse += $this->edit_main($moons_query);
                $view = parent::$page->parseTemplate(parent::$page->getTemplate("adm/users_moons_main_view"), $parse);

                break;

            case 'buildings':

                $parse['buildings_table'] = $this->edit_buildings($moons_query, 3);
                $view = parent::$page->parseTemplate(parent::$page->getTemplate("adm/users_planets_buildings_view"), $parse);

                break;

            case 'ships':

                $parse['ships_table'] = $this->edit_ships($moons_query);
                $view = parent::$page->parseTemplate(parent::$page->getTemplate("adm/users_planets_ships_view"), $parse);

                break;

            case 'defenses':

                $parse['defenses_table'] = $this->edit_defenses($moons_query, 3);
                $view = parent::$page->parseTemplate(parent::$page->getTemplate("adm/users_planets_defenses_view"), $parse);

                break;

            case 'delete':

                $this->_db->query("UPDATE " . PLANETS . " AS m, " . USERS . " AS u SET
                    m.`planet_destroyed` = '" . (time() + (PLANETS_LIFE_TIME * 3600)) . "',
                    u.`user_current_planet` = u.`user_home_planet_id`
                    WHERE m.`planet_id` = '" . (int) $this->_moon . "' AND
                                    m.`planet_type` = '3';");
                $this->refresh_page();

                break;

            case '':
            default:

                $parse['moons_table'] = $this->moons_table($moons_query);
                $view = parent::$page->parseTemplate(parent::$page->getTemplate("adm/users_moons_view"), $parse);

                break;
        } // SWITCH

        $parse['alert_info'] = ( $this->_alert_type != '' ) ? AdministrationLib::saveMessage($this->_alert_type, $this->_alert_info) : '';

        return $view;
    }
    ######################################
    #
    # save / update methods
    #
    ######################################

    /**
     * method save_info
     * param
     * return save information for the current user
     */
    private function save_info()
    {
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $perm_email = isset($_POST['user_email_permanent']) ? $_POST['user_email_permanent'] : '';
        $authlevel = isset($_POST['authlevel']) ? $_POST['authlevel'] : -1;
        $id_planet = isset($_POST['id_planet']) ? $_POST['id_planet'] : 0;
        $cur_planet = isset($_POST['current_planet']) ? $_POST['current_planet'] : 0;
        $ally_id = isset($_POST['ally_id']) ? $_POST['ally_id'] : 0;

        $authlevel = (int) $authlevel;
        $id_planet = (int) $id_planet;
        $cur_planet = (int) $cur_planet;
        $ally_id = (int) $ally_id;

        $errors = '';

        if ($username == '' or $this->check_username($username)) {
            $errors .= $this->_lang['us_error_username'] . '<br />';
        }

        if ($password != '') {
            $password = "'" . sha1($password) . "'";
        } else {
            $password = "`user_password`";
        }

        if ($email == '' or $this->check_email($email, true)) {
            $errors .= $this->_lang['us_error_email'] . '<br />';
        }

        if ($perm_email == '' or $this->check_email($perm_email, false)) {
            $errors .= $this->_lang['us_error_perm_email'] . '<br />';
        }

        if ($authlevel < 0 or $authlevel > 3) {
            $errors .= $this->_lang['us_error_authlevel'] . '<br />';
        }

        if ($id_planet <= 0) {
            $errors .= $this->_lang['us_error_idplanet'] . '<br />';
        }

        if ($cur_planet <= 0) {
            $errors .= $this->_lang['us_error_current_planet'] . '<br />';
        }

        if ($ally_id < 0) {
            $errors .= $this->_lang['us_error_ally_id'] . '<br />';
        }

        if ($errors != '') {
            $this->_alert_info = $errors;
            $this->_alert_type = 'error';
        } else {
            $this->_db->query("UPDATE " . USERS . " SET
                `user_name` = '" . $username . "',
                `user_password` = " . $password . ",
                `user_email` = '" . $email . "',
                `user_email_permanent` = '" . $perm_email . "',
                `user_authlevel` = '" . $authlevel . "',
                `user_home_planet_id` = '" . $id_planet . "',
                `user_current_planet` = '" . $cur_planet . "',
                `user_ally_id` = '" . $ally_id . "'
                WHERE `user_id` = '" . $this->_id . "';");

            if ($this->_current_user['user_id'] == $this->_id) {

                $_SESSION['user_name'] = $username;
            } else {

                // clean up
                $this->_db->query(
                    "DELETE FROM `" . SESSIONS . "` WHERE session_data LIKE '%user_id|s:1:\"" . $this->_id . "\"%'"
                );
            }

            $this->_alert_info = $this->_lang['us_all_ok_message'];
            $this->_alert_type = 'ok';
        }
    }

    /**
     * method save_settings
     * param
     * return save settings for the current user
     */
    private function save_settings()
    {
        $vacation_time = FunctionsLib::getDefaultVacationTime(); // DEFAULT VACATION TIME BEFORE A USER CAN REMOVE IT
        $preference_planet_sort = ( ( isset($_POST['preference_planet_sort']) ) ? (int) $_POST['preference_planet_sort'] : 0 );
        $preference_planet_sort_sequence = ( ( isset($_POST['preference_planet_sort_sequence']) ) ? (int) $_POST['preference_planet_sort_sequence'] : 0 );
        $preference_spy_probes = ( ( isset($_POST['preference_spy_probes']) ) ? (int) $_POST['preference_spy_probes'] : 0 );
        $preference_vacations_status = ( ( isset($_POST['preference_vacations_status']) && $_POST['preference_vacations_status'] == 'on' ) ? 1 : 0 );
        $preference_vacation_mode = ( ( isset($_POST['preference_vacations_status']) && $_POST['preference_vacations_status'] == 'on' ) ? "'" . $vacation_time . "'" : 'NULL' );
        $preference_delete_mode = ( ( isset($_POST['preference_delete_mode']) && $_POST['preference_delete_mode'] == 'on' ) ? "'" . time() . "'" : 'NULL' );

        // BUILD THE SPECIFIC QUERY
        if (($this->_user_query['preference_vacation_mode'] > 0) && $preference_vacations_status == 0) {

            // WE HAVE TO REMOVE HIM FROM VACATION AND SET PLANET PRODUCTION
            $vacation_head = " , " . PLANETS . " AS p";
            $vacation_condition = " AND p.`planet_user_id` = '" . (int) $this->_id . "'";
            $vacation_query = "
			pr.`preference_vacation_mode` = {$preference_vacation_mode},
			p.`planet_building_metal_mine_percent` = '10',
			p.`planet_building_crystal_mine_percent` = '10',
			p.`planet_building_deuterium_sintetizer_percent` = '10',
			p.`planet_building_solar_plant_percent` = '10',
			p.`planet_building_fusion_reactor_percent` = '10',
			p.`planet_ship_solar_satellite_percent` = '10',";
        } elseif ($this->_user_query['preference_vacation_mode'] == 0 
            or is_null($this->_user_query['preference_vacation_mode']) 
            && $preference_vacations_status == 1) {

            // WE HAVE TO ADD HIM TO VACATION AND REMOVE PLANET PRODUCTION
            $vacation_head = " , " . PLANETS . " AS p";
            $vacation_condition = " AND p.`planet_user_id` = '" . (int) $this->_id . "'";
            $vacation_query = "
			pr.`preference_vacation_mode` = {$preference_vacation_mode},
			p.`planet_metal_perhour` = '" . FunctionsLib::readConfig('metal_basic_income') . "',
			p.`planet_crystal_perhour` = '" . FunctionsLib::readConfig('crystal_basic_income') . "',
			p.`planet_deuterium_perhour` = '" . FunctionsLib::readConfig('deuterium_basic_income') . "',
			p.`planet_energy_used` = '0',
			p.`planet_energy_max` = '0',
			p.`planet_building_metal_mine_percent` = '0',
			p.`planet_building_crystal_mine_percent` = '0',
			p.`planet_building_deuterium_sintetizer_percent` = '0',
			p.`planet_building_solar_plant_percent` = '0',
			p.`planet_building_fusion_reactor_percent` = '0',
			p.`planet_ship_solar_satellite_percent` = '0',";
        } else {
            $vacation_head = '';
            $vacation_condition = '';
            $vacation_query = '';
        }

        $this->_db->query("UPDATE " . PREFERENCES . " AS pr{$vacation_head} SET
                                    {$vacation_query}
                                    pr.`preference_spy_probes` = '{$preference_spy_probes}',
									pr.`preference_planet_sort` = '{$preference_planet_sort}',
									pr.`preference_planet_sort_sequence` = '{$preference_planet_sort_sequence}',									
									pr.`preference_delete_mode` = {$preference_delete_mode}
									WHERE pr.`preference_user_id` = '{$this->_id}'{$vacation_condition}");


        $this->_alert_info = $this->_lang['us_all_ok_message'];
        $this->_alert_type = 'ok';
    }

    /**
     * method save_research
     * param
     * return save research for the current user
     */
    private function save_research()
    {
        // QUERY START
        $query_string = "UPDATE " . RESEARCH . " SET ";

        // LOOP THRU ALL THE TECHNOLOGIES
        foreach ($_POST as $tech => $level) {
            if (strpos($tech, 'research_') !== false) {
                $level = ( isset($level) ? $level : 0 );
                $query_string .= "`{$tech}` = '" . $this->_db->escapeValue($level) . "',";
            }
        }

        // REMOVE LAST COMMA
        $query_string = substr_replace($query_string, '', -1);

        // QUERY END
        $query_string .= " WHERE `research_user_id` = '" . $this->_db->escapeValue($this->_id) . "';";

        // RUN THE QUERY
        $this->_db->query($query_string);

        // Points rebuild
        $this->_stats->rebuildPoints($this->_id, 0, 'research');

        // RETURN THE ALERT
        $this->_alert_info = $this->_lang['us_all_ok_message'];
        $this->_alert_type = 'ok';
    }

    /**
     * method save_premium
     * param
     * return save research for the current user
     */
    private function save_premium()
    {
        // QUERY START
        $query_string = "UPDATE " . PREMIUM . " SET ";

        // LOOP THRU ALL THE TECHNOLOGIES
        foreach ($_POST as $premium => $data) {
            // IS A VALUE FROM PREMIUM TABLE
            if (strpos($premium, 'premium_') !== false) {
                // DARK MATTER HAS A DIFFERENT BEHAVIOUR
                if ($premium == 'premium_dark_matter') {
                    // IF IS NOT A NUMERIC VALUE, SET IT TO 0
                    if (!is_numeric($data) or empty($data) or ! isset($data)) {
                        $data = 0;
                    }
                } else {
                    // IF THE TIME = 0, IT'S BECAUSE THE OFFICIER IS GOING TO BE INACTIVE
                    switch ($data) {
                        default:
                        case 0:
                            $data = $this->_user_query[$premium];
                            break;

                        case 1:
                            $data = 0;
                            break;

                        case 2:
                        case 3:
                            // SET THE TIME (3 = 3 MONTHS, 2 = ONE WEEK, 1 = NOT ACTIVE / DEACTIVATE)
                            $data = time() + ( $data == 3 ? ( 3600 * 24 * 30 * 3 ) : ( 3600 * 24 * 7 ) );
                            break;
                    } // switch
                }

                // BUILD THE QUERY STRING WITH THE DATA
                $query_string .= "`{$premium}` = '" . $this->_db->escapeValue($data) . "',";
            }
        }

        // REMOVE LAST COMMA
        $query_string = substr_replace($query_string, '', -1);

        // QUERY END
        $query_string .= " WHERE `premium_user_id` = '" . $this->_db->escapeValue($this->_id) . "';";

        // RUN THE QUERY
        $this->_db->query($query_string);

        // RETURN THE ALERT
        $this->_alert_info = $this->_lang['us_all_ok_message'];
        $this->_alert_type = 'ok';
    }

    /**
     * method save_planet
     * param $type
     * return save planet for the current user
     */
    private function save_planet($type = 1)
    {
        $id_get = $this->_planet;

        if ($type == 3) {
            $id_get = $this->_moon;
        }

        if ((int) $id_get <= 0) {
            return;
        }

        // QUERY START
        $query_string = "UPDATE " . PLANETS . " SET ";

        // LOOP THRU ALL THE PLANET DATA
        foreach ($_POST as $field => $value) {
            switch ($field) {
                case 'send_data':

                    continue;

                    break;

                case 'planet_destroyed':

                    if ($value == 1) {
                        $query_string .= "`planet_destroyed` = '" . (time() + (PLANETS_LIFE_TIME * 3600)) . "',";
                    } else {
                        $query_string .= "`planet_destroyed` = '0',";
                    }

                    break;

                case 'planet_last_jump_time':

                    $query_string .= "`planet_last_jump_time` = '0',";

                    break;

                case '':
                default:

                    $query_string .= "`{$field}` = '" . $this->_db->escapeValue($value) . "',";

                    break;
            }
        }

        // REMOVE LAST COMMA
        $query_string = substr_replace($query_string, '', -1);

        // QUERY END
        $query_string .= " WHERE `planet_id` = '" . $this->_db->escapeValue($id_get) . "';";

        // RUN THE QUERY
        $this->_db->query($query_string);

        // RETURN THE ALERT
        $this->_alert_info = $this->_lang['us_all_ok_message'];
        $this->_alert_type = 'ok';
    }

    /**
     * saveBuildings
     * 
     * @param int $type Type
     * 
     * @return void
     */
    private function saveBuildings($type = 1)
    {
        $id_get = $this->_planet;

        if ($type == 3) {

            $id_get = $this->_moon;
        }

        // QUERY START
        $query_string = "UPDATE " . BUILDINGS . ", " . PLANETS . " SET ";
        $total_fields = 0;

        // LOOP THRU ALL THE BUILDINGS
        foreach ($_POST as $building => $level) {
            if (strpos($building, 'building_') !== false) {
                $level = ( isset($level) ? $level : 0 );
                $query_string .= "`{$building}` = '" . $this->_db->escapeValue($level) . "',";
                $total_fields += $level;
            }
        }

        // REMOVE LAST COMMA
        //$query_string = substr_replace($query_string, '', -1);
        // QUERY END
        $query_string .= " `planet_field_current` = '" . $total_fields . "', ";
        $query_string .= " `planet_field_max` = IF(`planet_type` = 3, 1 + `building_mondbasis` * " . FIELDS_BY_MOONBASIS_LEVEL . ", `planet_field_max`) ";
        $query_string .= " WHERE `building_planet_id` = '" . $this->_db->escapeValue($id_get) . "' 
                            AND `planet_id` = '" . $this->_db->escapeValue($id_get) . "';";

        // RUN THE QUERY
        $this->_db->query($query_string);

        // Points rebuild
        $this->_stats->rebuildPoints($this->_id, $id_get, 'buildings');

        // RETURN THE ALERT
        $this->_alert_info = $this->_lang['us_all_ok_message'];
        $this->_alert_type = 'ok';
    }

    /**
     * method save_ships
     * param $type
     * return save ships for the current planet
     */
    private function save_ships($type = 1)
    {
        $id_get = $this->_planet;

        if ($type == 3) {
            $id_get = $this->_moon;
        }

        // QUERY START
        $query_string = "UPDATE " . SHIPS . " SET ";

        // LOOP THRU ALL THE SHIPS
        foreach ($_POST as $ship => $amount) {
            if (strpos($ship, 'ship_') !== false) {
                $level = ( isset($amount) ? $amount : 0 );
                $query_string .= "`{$ship}` = '" . $this->_db->escapeValue($amount) . "',";
            }
        }

        // REMOVE LAST COMMA
        $query_string = substr_replace($query_string, '', -1);

        // QUERY END
        $query_string .= " WHERE `ship_planet_id` = '" . $this->_db->escapeValue($id_get) . "';";

        // RUN THE QUERY
        $this->_db->query($query_string);

        // Points rebuild
        $this->_stats->rebuildPoints($this->_id, $id_get, 'ships');

        // RETURN THE ALERT
        $this->_alert_info = $this->_lang['us_all_ok_message'];
        $this->_alert_type = 'ok';
    }

    /**
     * method save_defenses
     * param $type
     * return save defenses for the current planet
     */
    private function save_defenses($type = 1)
    {
        $id_get = $this->_planet;

        if ($type == 3) {
            $id_get = $this->_moon;
        }

        // QUERY START
        $query_string = "UPDATE " . DEFENSES . " SET ";

        // LOOP THRU ALL THE DEFENSES
        foreach ($_POST as $defense => $amount) {
            if (strpos($defense, 'defense_') !== false) {
                $level = ( isset($amount) ? $amount : 0 );
                $query_string .= "`{$defense}` = '" . $this->_db->escapeValue($amount) . "',";
            }
        }

        // REMOVE LAST COMMA
        $query_string = substr_replace($query_string, '', -1);

        // QUERY END
        $query_string .= " WHERE `defense_planet_id` = '" . $this->_db->escapeValue($id_get) . "';";

        // RUN THE QUERY
        $this->_db->query($query_string);

        // Points rebuild
        $this->_stats->rebuildPoints($this->_id, $id_get, 'defenses');

        // RETURN THE ALERT
        $this->_alert_info = $this->_lang['us_all_ok_message'];
        $this->_alert_type = 'ok';
    }
    ######################################
    #
    # build combo methods
    #
    ######################################

    /**
     * method build_users_combo
     * param $user_id
     * return the list of users
     */
    private function build_users_combo($user_id)
    {
        $combo_rows = '';
        $users = $this->_db->query("SELECT `user_id`, `user_name`
												FROM " . USERS . ";");

        while ($users_row = $this->_db->fetchArray($users)) {
            $combo_rows .= '<option value="' . $users_row['user_id'] . '" ' . ( $users_row['user_id'] == $user_id ? ' selected' : '' ) . '>' . $users_row['user_name'] . '</option>';
        }

        return $combo_rows;
    }

    /**
     * method build_planet_combo
     * param $user_data
     * param $id_field
     * return the list of the user planets
     */
    private function build_planet_combo($user_data, $id_field)
    {
        $combo_rows = '';
        $planets = $this->_db->query("SELECT `planet_id`, `planet_name`, `planet_galaxy`, `planet_system`, `planet_planet`
												FROM " . PLANETS . "
												WHERE planet_user_id = '" . $this->_id . "';");

        while ($planets_row = $this->_db->fetchArray($planets)) {
            if ($user_data[$id_field] == $planets_row['planet_id']) {
                $combo_rows .= '<option value="' . $planets_row['planet_id'] . '" selected>' . $planets_row['planet_name'] . ' [' . $planets_row['planet_galaxy'] . ':' . $planets_row['planet_system'] . ':' . $planets_row['planet_planet'] . ']' . '</option>';
            } else {
                $combo_rows .= '<option value="' . $planets_row['planet_id'] . '">' . $planets_row['planet_name'] . ' [' . $planets_row['planet_galaxy'] . ':' . $planets_row['planet_system'] . ':' . $planets_row['planet_planet'] . ']' . '</option>';
            }
        }

        return $combo_rows;
    }

    /**
     * method build_alliance_combo
     * param $user_data
     * return the list of alliances
     */
    private function build_alliance_combo($user_data)
    {
        $combo_rows = '';
        $alliances = $this->_db->query("SELECT `alliance_id`, `alliance_name`, `alliance_tag`
												FROM " . ALLIANCE . ";");

        while ($alliance_row = $this->_db->fetchArray($alliances)) {
            if ($user_data['user_ally_id'] == $alliance_row['alliance_id']) {
                $combo_rows .= '<option value="' . $alliance_row['alliance_id'] . '" selected>' . $alliance_row['alliance_name'] . ' [' . $alliance_row['alliance_tag'] . ']' . '</option>';
            } else {
                $combo_rows .= '<option value="' . $alliance_row['alliance_id'] . '">' . $alliance_row['alliance_name'] . ' [' . $alliance_row['alliance_tag'] . ']' . '</option>';
            }
        }

        return $combo_rows;
    }

    /**
     * method build_shortcuts_combo
     * param $shortcuts
     * return the list of shortcuts
     */
    private function build_shortcuts_combo($shortcuts)
    {
        if ($shortcuts) {
            $scarray = explode(";", $shortcuts);

            foreach ($scarray as $a => $b) {
                if ($b != "") {
                    $c = explode(',', $b);

                    $shortcut['description'] = $c[0] . " " . $c[1] . ":" . $c[2] . ":" . $c[3] . " ";

                    switch ($c[4]) {
                        case 1:
                            $shortcut['description'] .= $this->_lang['us_planet_shortcut'];
                            break;
                        case 2:
                            $shortcut['description'] .= $this->_lang['us_debris_shortcut'];
                            break;
                        case 3:
                            $shortcut['description'] .= $this->_lang['us_moon_shortcut'];
                            break;
                        default:
                            $shortcut['description'] .= '';
                            break;
                    }

                    $shortcut['select'] = 'shortcuts';
                    $shortcut['selected'] = '';
                    $shortcut['value'] = $c['1'] . ';' . $c['2'] . ';' . $c['3'] . ';' . $c['4'];
                    $shortcut['title'] = $shortcut['description'];
                    $shortcuts .= '<option value="' . $shortcut['value'] . '"' . $shortcut['selected'] . '>' . $shortcut['title'] . '</option>';
                }
            }
            return $shortcuts;
        } else {
            return '<option value="">-</option>';
        }
    }

    /**
     * method planet_sort_combo
     * param
     * return planet sort combo
     */
    private function planet_sort_combo()
    {
        $sort = '';
        $sort_types = array(
            0 => $this->_lang['us_user_preference_planet_sort_op1'],
            1 => $this->_lang['us_user_preference_planet_sort_op2'],
            2 => $this->_lang['us_user_preference_planet_sort_op3']
        );

        foreach ($sort_types as $id => $name) {
            $sort .= "<option value =\"{$id}\"" . ( ( $this->_user_query['preference_planet_sort'] == $id ) ? " selected" : "" ) . ">{$name}</option>";
        }

        return $sort;
    }

    /**
     * method planet_order_combo
     * param
     * return planet order combo
     */
    private function planet_order_combo()
    {
        $order = '';
        $order_types = array(
            0 => $this->_lang['us_user_preference_planet_sort_sequence_op1'],
            1 => $this->_lang['us_user_preference_planet_sort_sequence_op2'],
        );

        foreach ($order_types as $id => $name) {
            $order .= "<option value =\"{$id}\"" . ( ( $this->_user_query['preference_planet_sort_sequence'] == $id ) ? " selected" : "" ) . ">{$name}</option>";
        }

        return $order;
    }

    /**
     * method premium_combo
     * param $expire_date
     * return premium combo
     */
    private function premium_combo($expire_date)
    {
        $premium = '';
        $premium_types = array(
            0 => '-',
            1 => $this->_lang['us_user_premium_deactivate'],
            2 => $this->_lang['us_user_premium_activate_one_week'],
            3 => $this->_lang['us_user_premium_activate_three_month']
        );

        foreach ($premium_types as $id => $name) {
            $premium .= "<option value=\"{$id}\">{$name}</option>";
        }

        return $premium;
    }

    /**
     * method build_percent_combo
     * param $current_value
     * return percent combo
     */
    private function build_percent_combo($current_value)
    {
        $percent = '';
        $percent_values = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10);

        foreach ($percent_values as $id => $number) {
            $percent .= "<option value=\"{$id}\"  " . ( $current_value == $number ? ' selected' : '') . ">" . ( $number * 10 ) . "</option>";
        }

        return $percent;
    }

    /**
     * method build_process_queue
     * param $current_queue
     * return process queue combo
     */
    private function build_process_queue($current_queue)
    {
        if (!empty($current_queue)) {
            $queue_list = '';
            $current_queue = explode(';', $current_queue);

            foreach ($current_queue as $key => $queues) {
                $queue = explode(',', $queues);

                $queue_list .= "<option value=\"{$queue[0]}\">" . $this->_lang['tech'][$queue[0]] . " (" . $queue[1] . "^) (" . date("i:s", $queue[2]) . ") (" . date('i:s', $queue[3] - time()) . ") [" . $queue[4] . "] </option>";
            }

            return $queue_list;
        }
    }

    /**
     * method build_image_combo
     * param $current_image
     * return image combo
     */
    private function build_image_combo($current_image)
    {
        $images_dir = opendir(XGP_ROOT . DEFAULT_SKINPATH . 'planets');
        $exceptions = array('.', '..', '.htaccess', 'index.html', '.DS_Store', 'small',);
        $images_options = '';

        while (( $image_dir = readdir($images_dir) ) !== false) {
            if (strpos($image_dir, '.jpg')) {
                $images_options .= "<option ";

                if ($current_image . '.jpg' == $image_dir) {
                    $images_options .= "selected = selected";
                }

                $images_options .= " value=\"" . preg_replace("/\\.[^.\\s]{3,4}$/", "", $image_dir) . "\">" . $image_dir . "</option>";
            }
        }

        return $images_options;
    }
    ######################################
    #
	# sub tables methods
    #
	######################################

    /**
     * method research_table
     * param
     * return the builded technologies table with respective levels
     */
    private function research_table()
    {
        $template = parent::$page->getTemplate('adm/users_research_table_view');
        $prepare_table = '';
        $flag = 1;

        foreach ($this->_user_query as $tech => $level) {
            if (strpos($tech, 'research_') !== false) {
                if ($flag <= 3) { // SKIP NOT REQUIRED FIELDS
                    $flag++;
                } else {
                    $parse['technology'] = $this->_lang['us_user_' . $tech];
                    $parse['field'] = $tech;
                    $parse['level'] = $level;

                    $prepare_table .= parent::$page->parseTemplate($template, $parse);
                }
            }
        }

        return $prepare_table;
    }

    /**
     * method premium_table
     * param
     * return the builded premium table with respective officiers combo and expiration
     */
    private function premium_table()
    {
        $template = parent::$page->getTemplate("adm/users_premium_table_view");
        $prepare_table = '';
        $flag = 1;

        foreach ($this->_user_query as $officier => $expire) {
            if (strpos($officier, 'premium_') !== false) {
                if ($flag <= 2) { // SKIP NOT REQUIRED FIELDS
                    $flag++;
                } else {
                    if (!isset($this->_lang['us_user_' . $officier])) {
                        continue;
                    }

                    $parse['premium'] = $this->_lang['us_user_' . $officier];
                    $parse['status'] = ( $expire == 0 ) ? $this->_lang['us_user_premium_inactive'] : ( $this->_lang['us_user_premium_active_until'] . date(FunctionsLib::readConfig('date_format'), $expire) );
                    $parse['status_style'] = ( $expire == 0 ) ? 'text-error' : 'text-success';
                    $parse['field'] = $officier;
                    $parse['combo'] = $this->premium_combo($expire);

                    $prepare_table .= parent::$page->parseTemplate($template, $parse);
                }
            }
        }

        return $prepare_table;
    }

    /**
     * method planets_table
     * param $planets_data
     * return the builded planets table
     */
    private function planets_table($planets_data)
    {
        $parse = $this->_lang;
        $parse['image_path'] = DEFAULT_SKINPATH . "planets/small/s_";
        $parse['user'] = $this->_user_query['user_name'];
        $template = parent::$page->getTemplate("adm/users_planets_table_view");
        $prepare_table = '';

        while ($planets = $this->_db->fetchAssoc($planets_data)) {

            $parse['planet_id'] = $planets['planet_id'];
            $parse['planet_name'] = $planets['planet_name'];
            $parse['planet_image'] = $planets['planet_image'];
            $style = '';

            if ($planets['planet_destroyed'] != 0) {

                $parse['planet_status'] = '<strong><a title="' . $this->_lang['us_user_planets_destroyed'] . '">
                (' . $this->_lang['us_user_planets_destroyed_short'] . ')</a></strong>';
                $parse['planet_image_style'] = 'class="greyout"';
            }

            $parse['moon_id'] = '';
            $parse['moon_name'] = '';
            $parse['moon_image'] = '';

            if (isset($planets['moon_id'])) {

                $parse['moon_id'] = $planets['moon_id'];
                $parse['moon_name'] = str_replace('%s', $planets['moon_name'], $this->_lang['us_user_moon_title']);

                if ($planets['moon_destroyed'] != 0) {
                    $parse['moon_status'] = '<strong><a title="' . $this->_lang['us_user_planets_destroyed'] . '">
                    (' . $this->_lang['us_user_planets_destroyed_short'] . ')</a></strong>';
                    $style = 'class="greyout"';
                }

                $parse['moon_image'] = "<img src=\"{$parse['image_path']}{$planets['moon_image']}.jpg\" alt=\"{$planets['moon_image']}.jpg\" title=\"{$planets['moon_image']}.jpg\" border=\"0\" " . $style . ">";
            }

            $prepare_table .= parent::$page->parseTemplate($template, $parse);
        }

        return $prepare_table;
    }

    /**
     * method moons_table
     * param $moons_data
     * return the builded moons table
     */
    private function moons_table($moons_data)
    {
        $parse = $this->_lang;
        $parse['image_path'] = DEFAULT_SKINPATH . 'planets/small/s_';
        $parse['user'] = $this->_user_query['user_name'];
        $template = parent::$page->getTemplate('adm/users_moons_table_view');
        $prepare_table = '';

        while ($moons = $this->_db->fetchAssoc($moons_data)) {
            $parse['moon_id'] = $moons['planet_id'];
            $parse['moon_name'] = str_replace('%s', $moons['planet_name'], $this->_lang['us_user_moon_title']);
            $parse['moon_image'] = $moons['planet_image'];

            if ($moons['planet_destroyed'] != 0) {

                $parse['moon_status'] = '<strong><a title="' . $this->_lang['us_user_planets_destroyed'] . '">
                (' . $this->_lang['us_user_planets_destroyed_short'] . ')</a></strong>';
                $parse['moon_image_style'] = 'class="greyout"';
            }

            $prepare_table .= parent::$page->parseTemplate($template, $parse);
        }

        return $prepare_table;
    }
    ######################################
    #
    # edition methods (pages)
    #
    ######################################

    /**
     * method edit_main
     * param $planets_data
     * return the edit main table
     */
    private function edit_main($planets_data)
    {
        $parse = $this->_lang;
        $parse += $this->_db->fetchArray($planets_data);
        $parse['planet_user_id'] = $this->build_users_combo($parse['planet_user_id']);
        $parse['planet_last_update'] = date(FunctionsLib::readConfig('date_format_extended'), $parse['planet_last_update']);
        $parse['type1'] = $parse['planet_type'] == 1 ? ' selected' : '';
        $parse['type2'] = $parse['planet_type'] == 3 ? ' selected' : '';
        $parse['dest1'] = $parse['planet_destroyed'] > 0 ? ' selected' : '';
        $parse['dest2'] = $parse['planet_destroyed'] <= 0 ? ' selected' : '';
        $parse['planet_destroyed'] = $parse['planet_destroyed'] > 0 ? date(FunctionsLib::readConfig('date_format_extended'), $parse['planet_destroyed']) : '-';
        $parse['planet_b_building'] = $parse['planet_b_building'] > 0 ? date(FunctionsLib::readConfig('date_format_extended'), $parse['planet_b_building']) : '-';
        $parse['planet_b_building_id'] = $this->build_process_queue($parse['planet_b_building_id']);
        $parse['planet_b_tech'] = $parse['planet_b_tech'] > 0 ? date(FunctionsLib::readConfig('date_format_extended'), $parse['planet_b_tech']) : '-';
        $parse['planet_b_hangar'] = $parse['planet_b_hangar'] > 0 ? date(FunctionsLib::readConfig('date_format_extended'), $parse['planet_b_hangar']) : '-';
        $parse['planet_image'] = $this->build_image_combo($parse['planet_image']);
        $parse['planet_building_metal_mine_percent'] = $this->build_percent_combo($parse['planet_building_metal_mine_percent']);
        $parse['planet_building_crystal_mine_percent'] = $this->build_percent_combo($parse['planet_building_crystal_mine_percent']);
        $parse['planet_building_deuterium_sintetizer_percent'] = $this->build_percent_combo($parse['planet_building_deuterium_sintetizer_percent']);
        $parse['planet_building_solar_plant_percent'] = $this->build_percent_combo($parse['planet_building_solar_plant_percent']);
        $parse['planet_building_fusion_reactor_percent'] = $this->build_percent_combo($parse['planet_building_fusion_reactor_percent']);
        $parse['planet_ship_solar_satellite_percent'] = $this->build_percent_combo($parse['planet_ship_solar_satellite_percent']);
        $parse['planet_last_jump_time'] = $parse['planet_last_jump_time'] > 0 ? date(FunctionsLib::readConfig('date_format_extended'), $parse['planet_last_jump_time']) : '-';
        $parse['planet_invisible_start_time'] = $parse['planet_invisible_start_time'] > 0 ? date(FunctionsLib::readConfig('date_format_extended'), $parse['planet_invisible_start_time']) : '-';

        return $parse;
    }

    /**
     * method edit_buildings
     * param $planets_data
     * param $type
     * return the edit main table
     */
    private function edit_buildings($planets_data, $type = 1)
    {
        $exclude_buildings = array('building_mondbasis', 'building_phalanx', 'building_jump_gate');

        if ($type == 3) {
            $exclude_buildings = array('building_metal_mine', 'building_crystal_mine', 'building_deuterium_sintetizer', 'building_solar_plant', 'building_fusion_reactor', 'building_nano_factory', 'building_laboratory', 'building_terraformer', 'building_ally_deposit', 'building_missile_silo');
        }

        $template = parent::$page->getTemplate("adm/users_planets_buildings_table_view");
        $prepare_table = '';
        $flag = 1;


        foreach ($this->_db->fetchAssoc($planets_data) as $building => $level) {
            if (strpos($building, 'building_') !== false && !in_array($building, $exclude_buildings)) {
                if ($flag <= 2) { // SKIP NOT REQUIRED FIELDS
                    $flag++;
                } else {
                    $parse['building'] = $this->_lang['us_user_' . $building];
                    $parse['field'] = $building;
                    $parse['level'] = $level;

                    $prepare_table .= parent::$page->parseTemplate($template, $parse);
                }
            }
        }

        return $prepare_table;
    }

    /**
     * method edit_ships
     * param $planets_data
     * return the edit main table
     */
    private function edit_ships($planets_data)
    {
        $template = parent::$page->getTemplate("adm/users_planets_ships_table_view");
        $prepare_table = '';
        $flag = 1;

        foreach ($this->_db->fetchAssoc($planets_data) as $ship => $amount) {
            if (strpos($ship, 'ship_') !== false) {
                if ($flag <= 2) { // SKIP NOT REQUIRED FIELDS
                    $flag++;
                } else {
                    $parse['ship'] = $this->_lang['us_user_' . $ship];
                    $parse['field'] = $ship;
                    $parse['amount'] = $amount;

                    $prepare_table .= parent::$page->parseTemplate($template, $parse);
                }
            }
        }

        return $prepare_table;
    }

    /**
     * method edit_defenses
     * param $planets_data
     * param $type
     * return the edit main table
     */
    private function edit_defenses($planets_data, $type = 1)
    {
        $exclude_buildings = array('');

        if ($type == 3) {
            $exclude_buildings = array('defense_anti-ballistic_missile', 'defense_interplanetary_missile');
        }

        $template = parent::$page->getTemplate("adm/users_planets_defenses_table_view");
        $prepare_table = '';
        $flag = 1;

        foreach ($this->_db->fetchAssoc($planets_data) as $defense => $amount) {
            if (strpos($defense, 'defense_') !== false && !in_array($defense, $exclude_buildings)) {
                if ($flag <= 2) { // SKIP NOT REQUIRED FIELDS
                    $flag++;
                } else {
                    $parse['defense'] = $this->_lang['us_user_' . $defense];
                    $parse['field'] = $defense;
                    $parse['amount'] = $amount;

                    $prepare_table .= parent::$page->parseTemplate($template, $parse);
                }
            }
        }

        return $prepare_table;
    }
    ######################################
    #
    # edition methods (pages)
    #
    ######################################

    /**
     * delete_planet
     * 
     * @param int $id_planet Planet ID
     * 
     * @return void
     */
    private function delete_planet($id_planet = 0)
    {
        if ($id_planet == 0) {
            $id_planet = $this->_planet;
        }

        $this->delete_moon();

        $this->_db->query(
            "DELETE p,b,d,s FROM " . PLANETS . " AS p
            INNER JOIN " . BUILDINGS . " AS b ON b.building_planet_id = p.`planet_id`
            INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id`
            INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id`
            WHERE `planet_id` = '" . $id_planet . "' 
                AND `planet_type`= 1;"
        );
    }

    /**
     * delete_moon
     * 
     * @param int $id_moon Moon ID
     * 
     * @return void
     */
    private function delete_moon($id_moon = 0)
    {
        if ($id_moon == 0) {
            $id_moon = $this->_moon;
        }

        $this->_db->query(
            "DELETE m,b,d,s FROM " . PLANETS . " AS m
            INNER JOIN " . BUILDINGS . " AS b ON b.building_planet_id = m.`planet_id`
            INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = m.`planet_id`
            INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = m.`planet_id`
            WHERE `planet_id` = '" . $id_moon . "' 
                AND `planet_type` = 3;"
        );
    }
    ######################################
    #
    # other required methods
    #
    ######################################

    /**
     * method check_username
     * param $user
     * return true if user exists, false if user doesn't exist
     */
    private function check_user($user)
    {
        $user_query = $this->_db->queryFetch("SELECT `user_id`, `user_authlevel`
													FROM " . USERS . "
													WHERE `user_name` = '" . $user . "' OR
															`user_email` = '" . $user . "' OR
															`user_email_permanent` = '" . $user . "';");

        $this->_id = $user_query['user_id'];
        $this->_authlevel = $user_query['user_authlevel'];

        return ( $user_query['user_id'] != '' && $user_query != NULL );
    }

    /**
     * method last_activity
     * param $online_time
     * return the last activity time
     */
    private function last_activity($time)
    {
        if ($time + 60 * 10 >= time()) {
            return '<p class="text-success">' . $this->_lang['us_online'] . '</p>';
        } elseif ($time + 60 * 20 >= time()) {
            return '<p class="text-warning">' . $this->_lang['us_minutes'] . '</p>';
        } else {
            return '<p class="text-error">' . $this->_lang['us_offline'] . '</p>';
        }
    }

    /**
     * method check_username
     * param $username
     * return true if the username exists
     */
    private function check_username($username)
    {
        return $this->_db->queryFetch("SELECT `user_id`
											FROM `" . USERS . "`
											WHERE `user_name` = '" . $username . "' AND
													`user_id` <> '" . $this->_id . "';");
    }

    /**
     * method check_email
     * param $email
     * param $type
     * return true if the email exists
     */
    private function check_email($email, $type)
    {
        if ($type) {
            $email_type = 'user_email_permanent';
        } else {
            $email_type = 'user_email';
        }

        return $this->_db->queryFetch("SELECT `user_id`
												FROM `" . USERS . "`
												WHERE `{$email_type}` = '{$email}' AND
													`user_id` <> '{$this->_id}';");
    }

    /**
     * method vacation_set
     * param
     * return format vacation end date
     */
    private function vacation_set()
    {
        return $this->_lang['us_user_preference_vacations_until'] . date(FunctionsLib::readConfig('date_format_extended'), $this->_user_query['preference_vacation_mode']);
    }
}

/* end of users.php */
