<?php
/**
 * Reset Controller
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
use application\libraries\PlanetLib;

/**
 * Reset Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Reset extends Controller
{

    private $_current_user;
    private $_creator;
    private $_lang;

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

        // Check if the user is allowed to access
        if (AdministrationLib::haveAccess($this->_current_user['user_authlevel']) && $this->_current_user['user_authlevel'] == 3) {

            $this->_creator = new PlanetLib();

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
        $this->_db->closeConnection();
    }

    private function reset_universe()
    {
        $this->_db->query("RENAME TABLE " . PLANETS . " TO " . PLANETS . "_s");
        $this->_db->query("RENAME TABLE " . USERS . " TO " . USERS . "_s");

        $this->_db->query("CREATE  TABLE IF NOT EXISTS " . PLANETS . " ( LIKE " . PLANETS . "_s );");
        $this->_db->query("CREATE  TABLE IF NOT EXISTS " . USERS . " ( LIKE " . USERS . "_s );");

        $this->_db->query("TRUNCATE TABLE " . ACS . "");
        $this->_db->query("TRUNCATE TABLE " . ACS_MEMBERS . "");
        $this->_db->query("TRUNCATE TABLE " . ALLIANCE . "");
        $this->_db->query("TRUNCATE TABLE " . ALLIANCE_STATISTICS . "");
        $this->_db->query("TRUNCATE TABLE " . BANNED . "");
        $this->_db->query("TRUNCATE TABLE " . BUDDY . "");
        $this->_db->query("TRUNCATE TABLE " . BUILDINGS . "");
        $this->_db->query("TRUNCATE TABLE " . DEFENSES . "");
        $this->_db->query("TRUNCATE TABLE " . FLEETS . "");
        $this->_db->query("TRUNCATE TABLE " . MESSAGES . "");
        $this->_db->query("TRUNCATE TABLE " . NOTES . "");
        $this->_db->query("TRUNCATE TABLE " . PREMIUM . "");
        $this->_db->query("TRUNCATE TABLE " . RESEARCH . "");
        $this->_db->query("TRUNCATE TABLE " . REPORTS . "");
        $this->_db->query("TRUNCATE TABLE " . SETTINGS . "");
        $this->_db->query("TRUNCATE TABLE " . USERS_STATISTICS . "");
        $this->_db->query("TRUNCATE TABLE " . SHIPS . "");

        $AllUsers = $this->_db->query("SELECT `user_name`, `user_password`, `user_email`, `user_email_permanent`,`user_authlevel`,`user_galaxy`,`user_system`,`user_planet`, `user_onlinetime`, `user_register_time`, `user_home_planet_id`
											FROM " . USERS . "_s
											WHERE 1;");

        $LimitTime = time() - (15 * (24 * (60 * 60)));
        $TransUser = 0;

        while ($TheUser = $this->_db->fetchAssoc($AllUsers)) {
            if ($TheUser['user_onlinetime'] > $LimitTime) {
                $UserPlanet = $this->_db->queryFetch("SELECT `planet_name`
																FROM " . PLANETS . "_s
																WHERE `planet_id` = '" . $TheUser['user_home_planet_id'] . "';");
                if ($UserPlanet['planet_name'] != "") {
                    $Time = time();

                    $this->_db->query(
                        "INSERT INTO " . USERS . " SET
                            `user_name` = '" . $TheUser['user_name'] . "',
                            `user_email` = '" . $TheUser['user_email'] . "',
                            `user_email_permanent` = '" . $TheUser['user_email_permanent'] . "',
                            `user_home_planet_id` = '0',
                            `user_authlevel` = '" . $TheUser['user_authlevel'] . "',
                            `user_galaxy` = '" . $TheUser['user_galaxy'] . "',
                            `user_system` = '" . $TheUser['user_system'] . "',
                            `user_planet` = '" . $TheUser['user_planet'] . "',
                            `user_register_time` = '" . $TheUser['user_register_time'] . "',
                            `user_onlinetime` = '" . $Time . "',
                            `user_password` = '" . $TheUser['user_password'] . "';"
                    );

                    $last_id = $this->_db->insertId();
                    $NewUser = $last_id;

                    $this->_db->query("INSERT INTO " . RESEARCH . " SET
											`research_user_id` = '" . $last_id . "';");

                    $this->_db->query("INSERT INTO " . USERS_STATISTICS . " SET
											`user_statistic_user_id` = '" . $last_id . "';");

                    $this->_db->query("INSERT INTO " . PREMIUM . " SET
											`premium_user_id` = '" . $last_id . "';");

                    $this->_db->query("INSERT INTO " . SETTINGS . " SET
											`setting_user_id` = '" . $last_id . "';");

                    $this->_db->query("UPDATE " . USERS . " SET
											`user_banned` = '0'
											WHERE `user_id` > '1'");

                    $this->_creator->setNewPlanet($TheUser['user_galaxy'], $TheUser['user_system'], $TheUser['user_planet'], $NewUser, $UserPlanet['planet_name'], true);

                    $PlanetID = $this->_db->queryFetch(
                        "SELECT `planet_id`
                        FROM " . PLANETS . "
                        WHERE `planet_user_id` = '" . $NewUser . "'
                        LIMIT 1;"
                    );

                    $this->_db->query(
                        "UPDATE " . USERS . " SET
                        `user_home_planet_id` = '" . $PlanetID['planet_id'] . "',
                        `user_current_planet` = '" . $PlanetID['planet_id'] . "'
                        WHERE `user_id` = '" . $NewUser . "';"
                    );
                    $TransUser++;
                }
            }
        }

        $this->_db->query("DROP TABLE " . PLANETS . "_s");
        $this->_db->query("DROP TABLE " . USERS . "_s");
    }

    /**
     * method build_page
     * param
     * return main method, loads everything
     */
    private function build_page()
    {
        $parse = $this->_lang;

        if ($_POST) {
            if (isset($_POST['resetall']) && $_POST['resetall'] != 'on') {
                // HANGARES Y DEFENSAS
                if ($_POST['defenses'] == 'on') {
                    $this->_db->query("UPDATE " . DEFENSES . " SET
											`defense_rocket_launcher` = '0',
											`defense_light_laser` = '0',
											`defense_heavy_laser` = '0',
											`defense_gauss_cannon` = '0',
											`defense_ion_cannon` = '0',
											`defense_plasma_turret` = '0',
											`defense_small_shield_dome` = '0',
											`defense_large_shield_dome` = '0',
											`defense_anti-ballistic_missile` = '0',
											`defense_interplanetary_missile` = '0'");
                }

                if ($_POST['ships'] == 'on') {
                    $this->_db->query("UPDATE " . SHIPS . " SET
											`ship_small_cargo_ship` = '0',
											`ship_big_cargo_ship` = '0',
											`ship_light_fighter` = '0',
											`ship_heavy_fighter` = '0',
											`ship_cruiser` = '0',
											`ship_battleship` = '0',
											`ship_colony_ship` = '0',
											`ship_recycler` = '0',
											`ship_espionage_probe` = '0',
											`ship_bomber` = '0',
											`ship_solar_satellite` = '0',
											`ship_destroyer` = '0',
											`ship_deathstar` = '0',
											`ship_battlecruiser` = '0'");
                }

                if ($_POST['h_d'] == 'on') {
                    $this->_db->query("UPDATE " . PLANETS . " SET
											`planet_b_hangar` = '0',
											`planet_b_hangar_id` = ''");
                }

                // EDIFICIOS
                if ($_POST['edif_p'] == 'on') {
                    $this->_db->query("UPDATE " . BUILDINGS . " AS b
											INNER JOIN " . PLANETS . " AS p ON b.building_planet_id = p.`planet_id` SET
											`building_metal_mine` = '0',
											`building_crystal_mine` = '0',
											`building_deuterium_sintetizer` = '0',
											`building_solar_plant` = '0',
											`building_fusion_reactor` = '0',
											`building_robot_factory` = '0',
											`building_nano_factory` = '0',
											`building_hangar` = '0',
											`building_metal_store` = '0',
											`building_crystal_store` = '0',
											`building_deuterium_tank` = '0',
											`building_laboratory` = '0',
											`building_terraformer` = '0',
											`building_ally_deposit` = '0',
											`building_missile_silo` = '0'
											WHERE p.`planet_type` = '1'");
                }

                if ($_POST['edif_l'] == 'on') {
                    $this->_db->query("UPDATE " . BUILDINGS . " AS b
											INNER JOIN " . PLANETS . " AS p ON b.building_planet_id = p.`planet_id` SET
											`building_mondbasis` = '0',
											`building_phalanx` = '0',
											`building_jump_gate` = '0',
											`planet_last_jump_time` = '0',
											`fusion_plant` = '0',
											`building_robot_factory` = '0',
											`building_hangar` = '0',
											`building_metal_store` = '0',
											`building_crystal_store` = '0',
											`building_deuterium_tank` = '0',
											`building_ally_deposit` = '0'
											WHERE p.`planet_type` = '3'");
                }

                if ($_POST['edif'] == 'on') {
                    $this->_db->query("UPDATE " . PLANETS . " SET
											`planet_b_building` = '0',
											`planet_b_building_id` = ''");
                }

                // INVESTIGACIONES Y OFICIALES
                if ($_POST['inves'] == 'on') {
                    $this->_db->query("UPDATE " . RESEARCH . " SET
											`research_espionage_technology` = '0',
											`research_computer_technology` = '0',
											`research_weapons_technology` = '0',
											`research_shielding_technology` = '0',
											`research_armour_technology` = '0',
											`research_energy_technology` = '0',
											`research_hyperspace_technology` = '0',
											`research_combustion_drive` = '0',
											`research_impulse_drive` = '0',
											`research_hyperspace_drive` = '0',
											`research_laser_technology` = '0',
											`research_ionic_technology` = '0',
											`research_plasma_technology` = '0',
											`research_intergalactic_research_network` = '0',
											`research_astrophysics` = '0',
											`research_graviton_technology` = '0'");
                }

                if ($_POST['ofis'] == 'on') {
                    $this->_db->query("UPDATE " . PREMIUM . " SET
											`premium_officier_commander` = '0',
											`premium_officier_admiral` = '0',
											`premium_officier_engineer` = '0',
											`premium_officier_geologist` = '0',
											`premium_officier_technocrat` = '0'");
                }

                if ($_POST['inves_c'] == 'on') {
                    $this->_db->query("UPDATE " . PLANETS . " SET
											`planet_b_tech` = '0',
											`planet_b_tech_id` = '0'");

                    $this->_db->query("UPDATE " . RESEARCH . " SET
											`research_current_research` = '0'");
                }

                // RECURSOS
                if ($_POST['dark'] == 'on') {
                    $this->_db->query("UPDATE " . PREMIUM . " SET
											`premium_dark_matter` = '0'");
                }

                if ($_POST['resources'] == 'on') {
                    $this->_db->query("UPDATE " . PLANETS . " SET
											`planet_metal` = '0',
											`planet_crystal` = '0',
											`planet_deuterium` = '0'");
                }

                // GENERAL
                if ($_POST['notes'] == 'on') {
                    $this->_db->query("TRUNCATE TABLE " . NOTES . "");
                }

                if ($_POST['rw'] == 'on') {
                    $this->_db->query("TRUNCATE TABLE " . REPORTS . "");
                }

                if ($_POST['friends'] == 'on') {
                    $this->_db->query("TRUNCATE TABLE " . BUDDY . "");
                }

                if ($_POST['alliances'] == 'on') {
                    $this->_db->query("TRUNCATE TABLE " . ALLIANCE . "");
                    $this->_db->query("TRUNCATE TABLE " . ALLIANCE_STATISTICS . "");
                    $this->_db->query("UPDATE " . USERS . " SET
											`user_ally_id` = '0',
											`user_ally_request` = '0',
											`user_ally_request_text` = 'NULL',
											`user_ally_register_time` = '0',
											`user_ally_rank_id` = '0'");
                }


                if ($_POST['fleets'] == 'on') {
                    $this->_db->query("TRUNCATE TABLE " . FLEETS . "");
                }

                if ($_POST['banneds'] == 'on') {
                    $this->_db->query("TRUNCATE TABLE " . BANNED . "");
                    $this->_db->query("UPDATE " . USERS . " SET
											`user_banned` = '0'
											WHERE `user_id` > '1'");
                }

                if ($_POST['messages'] == 'on') {
                    $this->_db->query("TRUNCATE TABLE " . MESSAGES . "");
                }

                if ($_POST['statpoints'] == 'on') {
                    $this->_db->query("TRUNCATE TABLE " . USERS_STATISTICS . "");
                    $this->_db->query("TRUNCATE TABLE " . ALLIANCE_STATISTICS . "");
                }

                if ($_POST['moons'] == 'on') {
                    $this->_db->query("DELETE FROM " . PLANETS . " WHERE `planet_type` = '3'");
                }
            } else { // REINICIAR TODO
                $this->reset_universe();
            }

            $parse['alert'] = AdministrationLib::saveMessage('ok', $this->_lang['re_reset_excess']);
        }

        parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate('adm/reset_view'), $parse));
    }
}

/* end of reset.php */
