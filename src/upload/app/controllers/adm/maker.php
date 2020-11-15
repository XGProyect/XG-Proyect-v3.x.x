<?php declare (strict_types = 1);

/**
 * Maker Controller
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\controllers\adm;

use App\core\BaseController;
use App\core\enumerators\PlanetTypesEnumerator;
use App\core\enumerators\UserRanksEnumerator as UserRanks;
use App\libraries\adm\AdministrationLib as Administration;
use App\libraries\FormatLib as Format;
use App\libraries\Functions;

/**
 * Maker Class
 */
class Maker extends BaseController
{
    /**
     * Contains the alert string
     *
     * @var string
     */
    private $alert = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Model
        parent::loadModel('adm/maker');

        // load Language
        parent::loadLang(['adm/global', 'adm/maker']);
    }

    /**
     * Users land here
     *
     * @return void
     */
    public function index(): void
    {
        // check if the user is allowed to access
        if (!Administration::authorization(__CLASS__, (int) $this->user['user_authlevel'])) {
            die(Administration::noAccessMessage($this->langs->line('no_permissions')));
        }

        // build the page
        $this->buildPage();
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage(): void
    {
        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/maker_view',
                array_merge(
                    $this->langs->language,
                    $this->makeUser(),
                    $this->makeAlliace(),
                    $this->makePlanet(),
                    $this->makeMoon(),
                    [
                        'alert' => $this->alert ?? '',
                    ]
                )
            )
        );
    }

    /**
     * Create a new user
     *
     * @return array
     */
    private function makeUser(): array
    {
        $parse = $this->buildLevelCombo();

        if (isset($_POST['add_user']) && $_POST['add_user']) {
            $name = (string) $_POST['name'];
            $pass = (string) $_POST['password'];
            $email = (string) $_POST['email'];
            $galaxy = (int) $_POST['galaxy'];
            $system = (int) $_POST['system'];
            $planet = (int) $_POST['planet'];
            $auth = (int) $_POST['authlevel'];
            $i = 0;
            $error = '';

            $check_user = $this->Maker_Model->checkUserName($name);
            $check_email = $this->Maker_Model->checkUserEmail($email);
            $check_planet = $this->Maker_Model->checkPlanet($galaxy, $system, $planet);

            if (!is_numeric($galaxy) && !is_numeric($system) && !is_numeric($planet)) {
                $error = $this->langs->line('mk_user_only_numbers');
                $i++;
            } elseif ($galaxy > MAX_GALAXY_IN_WORLD or $system > MAX_SYSTEM_IN_GALAXY || $planet > MAX_PLANET_IN_SYSTEM || $galaxy < 1 || $system < 1 || $planet < 1) {
                $error = $this->langs->line('mk_user_wrong_coords');
                $i++;
            }

            if (!$name or !$email or !$galaxy or !$system or !$planet) {
                $error .= $this->langs->line('mk_user_complete_all');
                $i++;
            }

            if (!Functions::validEmail(strip_tags($email))) {
                $error .= $this->langs->line('mk_user_invalid_email');
                $i++;
            }

            if ($check_user) {
                $error .= $this->langs->line('mk_user_existing_name');
                $i++;
            }

            if ($check_email) {
                $error .= $this->langs->line('mk_user_existing_email');
                $i++;
            }

            if ($check_planet['count'] != 0) {
                $error .= $this->langs->line('mk_user_existing_planet');
                $i++;
            }

            if (isset($_POST['password_check']) && $_POST['password_check']) {
                $pass = Functions::generatePassword();
            } else {
                if (strlen($pass) < 4) {
                    $error .= $this->langs->line('mk_user_invalid_password');
                    $i++;
                }
            }

            if ($i == 0) {
                $this->Maker_Model->createNewUser($name, $email, $auth, $pass, $galaxy, $system, $planet);

                $this->alert = Administration::saveMessage('ok', strtr($this->langs->line('mk_user_added'), ['%s' => $pass]));
            } else {
                $this->alert = Administration::saveMessage('warning', '<br/>' . $error);
            }
        }

        return $parse;
    }

    /**
     * Create a new alliance
     *
     * @return array
     */
    private function makeAlliace(): array
    {
        $parse['founders_combo'] = $this->buildAllianceUsersCombo();

        if (isset($_POST['add_alliance']) && $_POST['add_alliance']) {
            $alliance_name = (string) $_POST['name'];
            $alliance_tag = (string) $_POST['tag'];
            $alliance_founder = (int) $_POST['founder'];

            $check_alliance = $this->Maker_Model->checkAlliance($alliance_name, $alliance_tag);

            if (!$check_alliance && !empty($alliance_founder) && $alliance_founder > 0) {
                $this->Maker_Model->createAlliance($alliance_name, $alliance_tag, $alliance_founder, $this->langs->line('mk_alliance_founder_rank'));

                $this->alert = Administration::saveMessage('ok', $this->langs->line('mk_alliance_added'));
            } else {
                $this->alert = Administration::saveMessage('warning', $this->langs->line('mk_alliance_all_fields'));
            }
        }

        return $parse;
    }

    /**
     * Create a new planet
     *
     * @return array
     */
    private function makePlanet(): array
    {
        $parse['users_combo'] = $this->buildUsersCombo();

        if (isset($_POST['add_planet']) && $_POST['add_planet']) {
            $user_id = (int) $_POST['user'];
            $galaxy = (int) $_POST['galaxy'];
            $system = (int) $_POST['system'];
            $planet = (int) $_POST['planet'];
            $name = (string) $_POST['name'];
            $field_max = (int) $_POST['planet_field_max'];
            $i = 0;

            $check_planet = $this->Maker_Model->checkPlanet($galaxy, $system, $planet);
            $user_query = $this->Maker_Model->checkUserById($user_id);

            if ($check_planet['count'] == 0 && $user_query) {
                if ($galaxy < 1 or $system < 1 or $planet < 1 or !is_numeric($galaxy) or !is_numeric($system) or !is_numeric($planet)) {
                    $error = $this->langs->line('mk_planet_unavailable_coords');
                    $i++;
                }

                if ($galaxy > MAX_GALAXY_IN_WORLD or $system > MAX_SYSTEM_IN_GALAXY or $planet > MAX_PLANET_IN_SYSTEM) {
                    $error .= $this->langs->line('mk_planet_wrong_coords');
                    $i++;
                }

                if ($i == 0) {
                    if ($field_max <= 0 && !is_numeric($field_max)) {
                        $field_max = '163';
                    }

                    if (strlen($name) <= 0) {
                        $name = $this->langs->line('mk_planet_default_name');
                    }

                    $this->Maker_Model->createNewPlanet($galaxy, $system, $planet, $user_id, $field_max, $name);

                    $this->alert = Administration::saveMessage('ok', $this->langs->line('mk_planet_added'));
                } else {
                    $this->alert = Administration::saveMessage('warning', $error);
                }
            } else {
                $this->alert = Administration::saveMessage('warning', $this->langs->line('mk_planet_unavailable_coords'));
            }
        }

        return $parse;
    }

    /**
     * Create a new moon
     *
     * @return array
     */
    private function makeMoon(): array
    {
        $parse['planets_combo'] = $this->buildPlanetCombo();

        if (isset($_POST['add_moon']) && $_POST['add_moon']) {
            $planet_id = (int) $_POST['planet'];
            $moon_name = (string) $_POST['name'];
            $diameter = (int) $_POST['planet_diameter'];
            $temp_min = (int) $_POST['planet_temp_min'];
            $temp_max = (int) $_POST['planet_temp_max'];
            $max_fields = (int) $_POST['planet_field_max'];

            $moon_planet = $this->Maker_Model->checkMoon($planet_id);

            if ($moon_planet && is_numeric($planet_id)) {
                if ($moon_planet['id_moon'] == '' && $moon_planet['planet_type'] == PlanetTypesEnumerator::PLANET && $moon_planet['planet_destroyed'] == 0) {
                    $galaxy = (int) $moon_planet['planet_galaxy'];
                    $system = (int) $moon_planet['planet_system'];
                    $planet = (int) $moon_planet['planet_planet'];
                    $owner = (int) $moon_planet['planet_user_id'];

                    $size = 0;
                    $errors = 0;
                    $mintemp = 0;
                    $maxtemp = 0;

                    if (!isset($_POST['diameter_check'])) {
                        if (is_numeric($diameter)) {
                            $size = $diameter;
                        } else {
                            $errors++;
                            $this->alert = Administration::saveMessage('warning', $this->langs->line('mk_moon_only_numbers'));
                        }
                    }

                    if (!isset($_POST['temp_check'])) {
                        if (is_numeric($temp_max) && is_numeric($temp_min)) {
                            $mintemp = $temp_min;
                            $maxtemp = $temp_max;
                        } else {
                            $errors++;
                            $this->alert = Administration::saveMessage('warning', $this->langs->line('mk_moon_only_numbers'));
                        }
                    }

                    if ($errors == 0) {
                        $this->Maker_Model->createNewMoon(
                            $galaxy,
                            $system,
                            $planet,
                            $owner,
                            $moon_name,
                            $size,
                            $max_fields,
                            $mintemp,
                            $maxtemp
                        );

                        $this->alert = Administration::saveMessage('ok', $this->langs->line('mk_moon_added'));
                    }
                } else {
                    $this->alert = Administration::saveMessage('warning', $this->langs->line('mk_moon_add_errors'));
                }
            } else {
                $this->alert = Administration::saveMessage('error', $this->langs->line('mk_moon_planet_doesnt_exist'));
            }
        }

        return $parse;
    }

    /**
     * Build the list of users combo
     *
     * @return string
     */
    private function buildUsersCombo(): string
    {
        $combo_rows = '';
        $users = $this->Maker_Model->getAllServerUsers();

        foreach ($users as $users_row) {
            if (isset($_GET['user']) && $_GET['user'] > 0) {
                $combo_rows .= '<option value="' . $users_row['user_id'] . '" ' . ($_GET['user'] == $users_row['user_id'] ? ' selected' : '') . '>' . $users_row['user_name'] . '</option>';
            } else {
                $combo_rows .= '<option value="' . $users_row['user_id'] . '">' . $users_row['user_name'] . '</option>';
            }
        }

        return $combo_rows;
    }

    /**
     * Build the list of planets combo
     *
     * @return string
     */
    private function buildPlanetCombo(): string
    {
        $combo_rows = '';
        $planets = $this->Maker_Model->getAllActivePlanets();

        foreach ($planets as $planets_row) {
            if (isset($_GET['planet']) && $_GET['planet'] > 0) {
                $combo_rows .= '<option value="' . $planets_row['planet_id'] . '" ' . ($_GET['planet'] == $planets_row['planet_id'] ? 'selected' : '') . ' >' . $planets_row['planet_name'] . ' [' . $planets_row['planet_galaxy'] . ':' . $planets_row['planet_system'] . ':' . $planets_row['planet_planet'] . ']' . '</option>';
            } else {
                $combo_rows .= '<option value="' . $planets_row['planet_id'] . '">' . $planets_row['planet_name'] . ' ' . Format::prettyCoords((int) $planets_row['planet_galaxy'], (int) $planets_row['planet_system'], (int) $planets_row['planet_planet']) . '</option>';
            }
        }

        return $combo_rows;
    }

    /**
     * Build the list of levels combo
     *
     * @return array
     */
    private function buildLevelCombo(): array
    {
        $user_levels = [];
        $ranks = [
            UserRanks::PLAYER,
            UserRanks::GO,
            UserRanks::SGO,
            UserRanks::ADMIN,
        ];

        foreach ($ranks as $rank_id) {
            $user_levels[] = [
                'id' => $rank_id,
                'name' => $this->langs->language['user_level'][$rank_id],
            ];
        }

        return [
            'user_levels' => $user_levels,
        ];
    }

    /**
     * Build the list of alliances combo
     *
     * @return string
     */
    private function buildAllianceUsersCombo(): string
    {
        $combo_rows = '';
        $users = $this->Maker_Model->getUsersWithoutAlliance();

        foreach ($users as $users_row) {
            $combo_rows .= '<option value="' . $users_row['user_id'] . '">' . $users_row['user_name'] . '</option>';
        }

        return $combo_rows;
    }

    /**
     * Generates a new random password
     *
     * @return string
     */
    private function generatePassword(): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = strlen($characters);
        $new_pass = '';
        $lenght = 16;
        srand((int) microtime() * 1000000);

        for ($i = 0; $i < $lenght; $i++) {
            $character_boucle = mt_rand(0, $count - 1);
            $new_pass = $new_pass . substr($characters, $character_boucle, 1);
        }

        return $new_pass;
    }
}
