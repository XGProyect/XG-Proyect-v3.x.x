<?php
/**
 * Maker Controller
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
use application\core\Database;
use application\libraries\adm\AdministrationLib;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use application\libraries\PlanetLib;

/**
 * Maker Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Maker extends Controller
{

    private $_current_user;
    private $_creator;
    private $_alert;
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
        $this->_creator = new PlanetLib();
        $this->_current_user = parent::$users->getUserData();

        // Check if the user is allowed to access
        if (AdministrationLib::haveAccess($this->_current_user['user_authlevel']) && AdministrationLib::authorization($this->_current_user['user_authlevel'], 'edit_users') == 1) {
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

    /**
     * method build_page
     * param
     * return main method, loads everything
     */
    private function build_page()
    {
        $parse = $this->_lang;

        switch (( isset($_GET['mode']) ? $_GET['mode'] : '')) {
            case 'alliance':

                $parse['content'] = $this->make_alliace();

                break;

            case 'moon':

                $parse['content'] = $this->make_moon();

                break;

            case 'planet':

                $parse['content'] = $this->make_planet();

                break;

            case 'user':

                $parse['content'] = $this->make_user();

                break;

            case '':
            default:

                $parse['content'] = '';

                break;
        }

        $parse['alert'] = $this->_alert;

        parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate('adm/maker_main_view'), $parse));
    }

    /**
     * method make_alliace
     * param
     * return a created alliance
     */
    private function make_alliace()
    {
        $parse = $this->_lang;
        $parse['founders_combo'] = $this->build_alliance_users_combo();

        if (isset($_POST['add_alliance']) && $_POST['add_alliance']) {
            $alliance_name = $this->_db->escapeValue((string) $_POST['name']);
            $alliance_tag = $this->_db->escapeValue((string) $_POST['tag']);
            $alliance_founder = (int) $_POST['founder'];

            $check_alliance = $this->_db->queryFetch("SELECT `alliance_id`
																FROM `" . ALLIANCE . "`
																WHERE `alliance_name` = '" . $alliance_name . "'
																	OR `alliance_tag` = '" . $alliance_tag . "';");

            if (!$check_alliance && !empty($alliance_founder) && $alliance_founder > 0) {
                $this->_db->query("INSERT INTO `" . ALLIANCE . "` SET
										`alliance_name`='" . $alliance_name . "',
										`alliance_tag`='" . $alliance_tag . "' ,
										`alliance_owner`='" . $alliance_founder . "',
										`alliance_owner_range` = '" . $this->_lang['mk_alliance_founder_rank'] . "',
										`alliance_register_time`='" . time() . "'");

                $new_alliance_id = $this->_db->insertId();

                $this->_db->query("INSERT INTO " . ALLIANCE_STATISTICS . " SET
										`alliance_statistic_alliance_id`='" . $new_alliance_id . "'");

                $this->_db->query("UPDATE `" . USERS . "` SET
										`user_ally_id`='" . $new_alliance_id . "',
										`user_ally_register_time`='" . time() . "'
										WHERE `user_id`='" . $alliance_founder . "'");

                $this->_alert = AdministrationLib::saveMessage('ok', $this->_lang['mk_alliance_added']);
            } else {
                $this->_alert = AdministrationLib::saveMessage('warning', $this->_lang['mk_alliance_all_fields']);
            }
        }

        return parent::$page->parseTemplate(parent::$page->getTemplate('adm/maker_alliance_view'), $parse);
    }

    /**
     * method make_moon
     * param
     * return a created moon
     */
    private function make_moon()
    {
        $parse = $this->_lang;
        $parse['planets_combo'] = $this->build_planet_combo();

        if (isset($_POST['add_moon']) && $_POST['add_moon']) {
            $planet_id = (int) $_POST['planet'];
            $moon_name = (string) $_POST['name'];
            $diameter = (int) $_POST['planet_diameter'];
            $temp_min = (int) $_POST['planet_temp_min'];
            $temp_max = (int) $_POST['planet_temp_max'];
            $max_fields = (int) $_POST['planet_field_max'];

            $moon_planet = $this->_db->queryFetch(
                "SELECT p.*, (SELECT `planet_id`
                FROM " . PLANETS . "
                WHERE `planet_galaxy` = (SELECT `planet_galaxy`
                                                                        FROM " . PLANETS . "
                                                                        WHERE `planet_id` = '" . $planet_id . "'
                                                                                AND `planet_type` = 1)
                                AND `planet_system` = (SELECT `planet_system`
                                                                                FROM " . PLANETS . "
                                                                                WHERE `planet_id` = '" . $planet_id . "'
                                                                                        AND `planet_type` = 1)
                                AND `planet_planet` = (SELECT `planet_planet`
                                                                                FROM " . PLANETS . "
                                                                                WHERE `planet_id` = '" . $planet_id . "'
                                                                                        AND `planet_type` = 1)
                                AND `planet_type` = 3) AS id_moon
                FROM " . PLANETS . " AS p
                WHERE p.`planet_id` = '" . $planet_id . "' AND
                p.`planet_type` = '1'"
            );


            if ($moon_planet && is_numeric($planet_id)) {
                if ($moon_planet['id_moon'] == '' && $moon_planet['planet_type'] == 1 && $moon_planet['planet_destroyed'] == 0) {

                    $galaxy = $moon_planet['planet_galaxy'];
                    $system = $moon_planet['planet_system'];
                    $planet = $moon_planet['planet_planet'];
                    $owner = $moon_planet['planet_user_id'];

                    $size = 0;
                    $errors = 0;
                    $mintemp = 0;
                    $maxtemp = 0;

                    if (!isset($_POST['diameter_check'])) {
                        if (is_numeric($diameter)) {
                            $size = $diameter;
                        } else {
                            $errors++;
                            $this->_alert = AdministrationLib::saveMessage('warning', $this->_lang['mk_moon_only_numbers']);
                        }
                    }

                    if (!isset($_POST['temp_check'])) {
                        if (is_numeric($temp_max) && is_numeric($temp_min)) {
                            $mintemp = $temp_min;
                            $maxtemp = $temp_max;
                        } else {
                            $errors++;
                            $this->_alert = AdministrationLib::saveMessage('warning', $this->_lang['mk_moon_only_numbers']);
                        }
                    }

                    if ($errors == 0) {
                        $this->_creator->setNewMoon(
                            $galaxy, $system, $planet, $owner, $moon_name, 0, $size, $max_fields, $mintemp, $maxtemp
                        );

                        $this->_alert = AdministrationLib::saveMessage('ok', $this->_lang['mk_moon_added']);
                    }
                } else {
                    $this->_alert = AdministrationLib::saveMessage('warning', $this->_lang['mk_moon_add_errors']);
                }
            } else {
                $this->_alert = AdministrationLib::saveMessage('error', $this->_lang['mk_moon_planet_doesnt_exist']);
            }
        }

        return parent::$page->parseTemplate(parent::$page->getTemplate('adm/maker_moon_view'), $parse);
    }

    /**
     * method make_planet
     * param
     * return a created planet
     */
    private function make_planet()
    {
        $parse = $this->_lang;
        $parse['users_combo'] = $this->build_users_combo();

        if (isset($_POST['add_planet']) && $_POST['add_planet']) {
            $user_id = (int) $_POST['user'];
            $galaxy = (int) $_POST['galaxy'];
            $system = (int) $_POST['system'];
            $planet = (int) $_POST['planet'];
            $name = (string) $_POST['name'];
            $field_max = (int) $_POST['planet_field_max'];
            $i = 0;

            $planet_query = $this->_db->queryFetch("SELECT *
																FROM " . PLANETS . "
																WHERE `planet_galaxy` = '" . $galaxy . "' AND
																		`planet_system` = '" . $system . "' AND
																		`planet_planet` = '" . $planet . "'");

            $user_query = $this->_db->queryFetch("SELECT *
															FROM " . USERS . "
															WHERE `user_id` = '" . $user_id . "'");

            if (is_numeric($user_id) && isset($user_id) && !$planet_query && $user_query) {
                if ($galaxy < 1 or $system < 1 or $planet < 1 or ! is_numeric($galaxy) or ! is_numeric($system) or ! is_numeric($planet)) {
                    $error = $this->_lang['mk_planet_unavailable_coords'];
                    $i++;
                }

                if ($galaxy > MAX_GALAXY_IN_WORLD or $system > MAX_SYSTEM_IN_GALAXY or $planet > MAX_PLANET_IN_SYSTEM) {
                    $error .= $this->_lang['mk_planet_wrong_coords'];
                    $i++;
                }

                if ($i == 0) {
                    if ($field_max <= 0 && !is_numeric($field_max)) {
                        $field_max = '163';
                    }

                    if (strlen($name) <= 0) {
                        $name = $this->_lang['mk_planet_default_name'];
                    }

                    $this->_creator->setNewPlanet($galaxy, $system, $planet, $user_id, '', '', false);

                    $this->_db->query("UPDATE " . PLANETS . " SET
											`planet_field_max` = '" . $field_max . "',
											`planet_name` = '" . $name . "'
											WHERE `planet_galaxy` = '" . $galaxy . "'
												AND `planet_system` = '" . $system . "'
												AND `planet_planet` = '" . $planet . "'
												AND `planet_type` = '1'");

                    $this->_alert = AdministrationLib::saveMessage('ok', $this->_lang['mk_planet_added']);
                } else {
                    $this->_alert = AdministrationLib::saveMessage('warning', $error);
                }
            } else {
                $this->_alert = AdministrationLib::saveMessage('warning', $this->_lang['mk_planet_unavailable_coords']);
            }
        }

        return parent::$page->parseTemplate(parent::$page->getTemplate('adm/maker_planet_view'), $parse);
    }

    /**
     * method make_user
     * param
     * return a created user
     */
    private function make_user()
    {
        $parse = $this->_lang;
        $parse['level_combo'] = $this->build_level_combo();

        if (isset($_POST['add_user']) && $_POST['add_user']) {
            $name = (string) $_POST['name'];
            $pass = (string) $_POST['password'];
            $email = (string) $_POST['email'];
            $galaxy = (int) $_POST['galaxy'];
            $system = (int) $_POST['system'];
            $planet = (int) $_POST['planet'];
            $auth = (int) $_POST['authlevel'];
            $time = time();
            $i = 0;
            $error = '';

            $check_user = $this->_db->queryFetch("SELECT `user_name`
														FROM " . USERS . "
														WHERE `user_name` = '" . $this->_db->escapeValue($_POST['name']) . "'
														LIMIT 1");

            $check_email = $this->_db->queryFetch("SELECT `user_email`
														FROM " . USERS . "
														WHERE `user_email` = '" . $this->_db->escapeValue($_POST['email']) . "'
														LIMIT 1");

            $check_planet = $this->_db->queryFetch("SELECT COUNT(planet_id) AS count
														FROM " . PLANETS . "
														WHERE `planet_galaxy` = '" . $galaxy . "' AND
																`planet_system` = '" . $system . "' AND
																`planet_planet` = '" . $planet . "' LIMIT 1");


            if (!is_numeric($galaxy) && !is_numeric($system) && !is_numeric($planet)) {
                $error = $this->_lang['mk_user_only_numbers'];
                $i++;
            } elseif ($galaxy > MAX_GALAXY_IN_WORLD or $system > MAX_SYSTEM_IN_GALAXY || $planet > MAX_PLANET_IN_SYSTEM || $galaxy < 1 || $system < 1 || $planet < 1) {
                $error = $this->_lang['mk_user_wrong_coords'];
                $i++;
            }

            if (!$name or ! $email or ! $galaxy or ! $system or ! $planet) {
                $error .= $this->_lang['mk_user_complete_all'];
                $i++;
            }

            if (!FunctionsLib::validEmail(strip_tags($email))) {
                $error .= $this->_lang['mk_user_invalid_email'];
                $i++;
            }

            if ($check_user) {
                $error .= $this->_lang['mk_user_existing_name'];
                $i++;
            }

            if ($check_email) {
                $error .= $this->_lang['mk_user_existing_email'];
                $i++;
            }

            if ($check_planet['count'] != 0) {
                $error .= $this->_lang['mk_user_existing_planet'];
                $i++;
            }

            if (isset($_POST['password_check']) && $_POST['password_check']) {
                $pass = $this->generate_password();
            } else {
                if (strlen($pass) < 4) {
                    $error .= $this->_lang['mk_user_invalid_password'];
                    $i++;
                }
            }

            if ($i == 0) {

                $this->_db->query("INSERT INTO " . USERS . " SET
										`user_name` = '" . $this->_db->escapeValue(strip_tags($name)) . "',
										`user_email` = '" . $this->_db->escapeValue($email) . "',
										`user_ip_at_reg` = '" . $_SERVER['REMOTE_ADDR'] . "',
										`user_home_planet_id` = '0',
										`user_register_time` = '" . $time . "',
										`user_onlinetime` = '" . $time . "',
										`user_authlevel` = '" . $auth . "',
										`user_password`='" . sha1($pass) . "';");

                $last_user_id = $this->_db->insertId();

                $this->_creator->setNewPlanet($galaxy, $system, $planet, $last_user_id, '', true);

                $last_planet_id = $this->_db->insertId();

                $this->_db->query("UPDATE " . USERS . " SET
										`user_home_planet_id` = '" . $last_planet_id . "',
										`user_current_planet` = '" . $last_planet_id . "',
										`user_galaxy` = '" . $galaxy . "',
										`user_system` = '" . $system . "',
										`user_planet` = '" . $planet . "'
										WHERE `user_id` = '" . $last_user_id . "'
										LIMIT 1;");

                $this->_db->query("INSERT INTO " . RESEARCH . " SET
										`research_user_id` = '" . $last_user_id . "';");

                $this->_db->query("INSERT INTO " . USERS_STATISTICS . " SET
										`user_statistic_user_id` = '" . $last_user_id . "';");

                $this->_db->query("INSERT INTO " . PREMIUM . " SET
										`premium_user_id` = '" . $last_user_id . "';");

                $this->_db->query("INSERT INTO " . PREFERENCES . " SET
										`preference_user_id` = '" . $last_user_id . "';");

                $this->_alert = AdministrationLib::saveMessage('ok', str_replace('%s', $pass, $this->_lang['mk_user_added']));
            } else {
                $this->_alert = AdministrationLib::saveMessage('warning', '<br/>' . $error);
            }
        }

        return parent::$page->parseTemplate(parent::$page->getTemplate('adm/maker_user_view'), $parse);
    }

    /**
     * method build_users_combo
     * param
     * return the list of users
     */
    private function build_users_combo()
    {
        $combo_rows = '';
        $users = $this->_db->query("SELECT `user_id`, `user_name`
												FROM " . USERS . ";");

        while ($users_row = $this->_db->fetchArray($users)) {
            if (isset($_GET['user']) && $_GET['user'] > 0) {
                $combo_rows .= '<option value="' . $users_row['user_id'] . '" ' . ( $_GET['user'] == $users_row['user_id'] ? ' selected' : '' ) . '>' . $users_row['user_name'] . '</option>';
            } else {
                $combo_rows .= '<option value="' . $users_row['user_id'] . '">' . $users_row['user_name'] . '</option>';
            }
        }

        return $combo_rows;
    }

    /**
     * method build_planet_combo
     * param
     * return the list of the user planets
     */
    private function build_planet_combo()
    {
        $combo_rows = '';
        $planets = $this->_db->query("SELECT `planet_id`, `planet_name`, `planet_galaxy`, `planet_system`, `planet_planet`
												FROM `" . PLANETS . "`
												WHERE `planet_destroyed` = '0'
													AND `planet_type` = '1';");

        while ($planets_row = $this->_db->fetchArray($planets)) {
            if (isset($_GET['planet']) && $_GET['planet'] > 0) {
                $combo_rows .= '<option value="' . $planets_row['planet_id'] . '" ' . ( $_GET['planet'] == $planets_row['planet_id'] ? 'selected' : '' ) . ' >' . $planets_row['planet_name'] . ' [' . $planets_row['planet_galaxy'] . ':' . $planets_row['planet_system'] . ':' . $planets_row['planet_planet'] . ']' . '</option>';
            } else {
                $combo_rows .= '<option value="' . $planets_row['planet_id'] . '">' . $planets_row['planet_name'] . ' ' . FormatLib::prettyCoords($planets_row['planet_galaxy'], $planets_row['planet_system'], $planets_row['planet_planet']) . '</option>';
            }
        }

        return $combo_rows;
    }

    /**
     * method build_level_combo
     * param
     * return the list of the user levels
     */
    private function build_level_combo()
    {
        $combo_rows = '';

        foreach ($this->_lang['user_level'] as $level_id => $level_text) {
            $combo_rows .= '<option value="' . $level_id . '">' . $level_text . '</option>';
        }

        return $combo_rows;
    }

    /**
     * method build_alliance_users_combo
     * param
     * return the list of users without alliance
     */
    private function build_alliance_users_combo()
    {
        $combo_rows = '';
        $users = $this->_db->query("SELECT `user_id`, `user_name`
												FROM `" . USERS . "`
												WHERE `user_ally_id` = '0'
													AND `user_ally_request` = '0';");

        while ($users_row = $this->_db->fetchArray($users)) {
            $combo_rows .= '<option value="' . $users_row['user_id'] . '">' . $users_row['user_name'] . '</option>';
        }

        return $combo_rows;
    }

    /**
     * generate_password()
     * param
     * return generates a password
     * */
    private function generate_password()
    {
        $characters = "aazertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN1234567890";
        $count = strlen($characters);
        $new_pass = "";
        $lenght = 6;
        srand((double) microtime() * 1000000);

        for ($i = 0; $i < $lenght; $i++) {
            $character_boucle = mt_rand(0, $count - 1);
            $new_pass = $new_pass . substr($characters, $character_boucle, 1);
        }

        return $new_pass;
    }
}

/* end of maker.php */
