<?php
/**
 * Galaxy Controller
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
namespace application\controllers\game;

use application\core\Controller;
use application\core\Database;
use application\libraries\FleetsLib;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;

/**
 * Galaxy Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Galaxy extends Controller
{
    /**
     * The module ID
     *
     * @var int
     */
    const MODULE_ID = 11;

    /**
     *
     * @var array
     */
    private $user;

    /**
     *
     * @var array
     */
    private $planet;

    /**
     * Contains the galaxy data
     *
     * @var array
     */
    private $galaxy = [];

    /**
     * Contains the current amount of planets
     *
     * @var integer
     */
    private $planet_count = 0;

    private $_galaxy;
    private $_system;
    private $_formula;
    private $_noob;
    private $_galaxyLib;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // load Model
        parent::loadModel('game/galaxy');

        // load Language
        parent::loadLang(['global', 'defenses', 'missions', 'galaxy']);

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        // set data
        $this->user = $this->getUserData();
        $this->planet = $this->getPlanetData();

        $this->_db = new Database();
        $this->_resource = parent::$objects->getObjects();
        $this->_pricelist = parent::$objects->getPrice();
        $this->_reslist = parent::$objects->getObjectsList();
        $this->_formula = FunctionsLib::loadLibrary('FormulaLib');
        $this->_noob = FunctionsLib::loadLibrary('NoobsProtectionLib');
        $this->_galaxyLib = FunctionsLib::loadLibrary('GalaxyLib');

        if ($this->user['preference_vacation_mode'] > 0) {
            FunctionsLib::message($this->langs->line('gl_no_access_vm_on'), '', '');
        }

        // init a new galaxy object
        //$this->setUpPreferences();

        // time to do something
        $this->runAction();

        // build the page
        $this->buildPage();
    }

    /**
     * Run an action
     *
     * @return void
     */
    private function runAction()
    {
        if (isset($_GET['fleet']) && $_GET['fleet'] == 'true') {
            $this->sendFleet();
        }

        if (isset($_GET['missiles']) && $_GET['missiles'] == 'true') {
            $this->sendMissiles();
        }
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage(): void
    {
        $fleetmax = FleetsLib::getMaxFleets($this->user['research_computer_technology'], $this->user['premium_officier_admiral']);
        $CurrentPlID = $this->planet['planet_id'];
        $CurrentSP = $this->planet['ship_espionage_probe'];

        $maxfleet = $this->_db->query(
            "SELECT `fleet_id`
            FROM " . FLEETS . "
            WHERE `fleet_owner` = '" . intval($this->user['user_id']) . "';"
        );

        $maxfleet_count = $this->_db->numRows($maxfleet);

        if (!isset($mode)) {
            if (isset($_GET['mode'])) {
                $mode = intval($_GET['mode']);
            } else {
                $mode = 0;
            }
        }

        $setted_position = $this->validatePosition($mode);
        $this->_galaxy = $setted_position['galaxy'];
        $this->_system = $setted_position['system'];
        $planet = $setted_position['planet'];
        $psystem = $setted_position['psystem'];

        if ($mode == 2 && $this->planet['defense_interplanetary_missile'] < 1) {
            die(FunctionsLib::message($this->langs->line('gl_no_missiles'), "game.php?page=galaxy&mode=0", 2));
        }

        $this->galaxy = $this->Galaxy_Model->getGalaxyDataByGalaxyAndSystem($this->_galaxy, $this->_system);

        $parse['galaxy'] = $this->_galaxy;
        $parse['system'] = $this->_system;
        $parse['planet'] = $planet;
        $parse['currentmip'] = $this->planet['defense_interplanetary_missile'];
        $parse['maxfleetcount'] = $maxfleet_count;
        $parse['fleetmax'] = $fleetmax;
        $parse['recyclers'] = FormatLib::prettyNumber($this->planet['ship_recycler']);
        $parse['spyprobes'] = FormatLib::prettyNumber($CurrentSP);
        $parse['missile_count'] = sprintf($this->langs->line('gl_missil_to_launch'), $this->planet['defense_interplanetary_missile']);
        $parse['current'] = isset($_GET['current']) ? $_GET['current'] : null;
        $parse['current_galaxy'] = $this->planet['planet_galaxy'];
        $parse['current_system'] = $this->planet['planet_system'];
        $parse['current_planet'] = $this->planet['planet_planet'];
        $parse['planet_type'] = $this->planet['planet_type'];
        $parse['mip'] = ($mode == 2) ? $this->getTemplate()->set(
            'galaxy/galaxy_missile_selector',
            $parse
        ) : ' ';

        parent::$page->display(
            $this->getTemplate()->set(
                'game/galaxy_view',
                array_merge(
                    $this->langs->language,
                    [
                        'js_path' => JS_PATH,
                        'list_of_positions' => $this->buildPositionsList(),
                        'planet_count' => $this->planet_count,
                    ],
                    $parse
                )
            )
        );
    }

    /**
     * Build the list of positions for the galaxy
     *
     * @return array
     */
    private function buildPositionsList(): array
    {
        $list_of_positions = [];
        $galaxy_row = new $this->_galaxyLib(
            $this->user,
            $this->planet,
            $this->_galaxy,
            $this->_system,
            $this->langs->language
        );

        // set the current planets
        foreach ($this->galaxy as $planet) {
            $this->planet_count++;

            $list_of_positions[$planet['planet_planet']] = $galaxy_row->buildRow($planet, $planet['planet_planet']);
        }

        // fill the empty positions
        for ($i = 1; $i <= MAX_PLANET_IN_SYSTEM; $i++) {
            if (!isset($list_of_positions[$i])) {
                $list_of_positions[$i] = [
                    'pos' => $i,
                    'planet' => '',
                    'planetname' => '',
                    'moon' => '',
                    'debris' => '',
                    'username' => '',
                    'alliance' => '',
                    'actions' => '',
                ];
            }
        }

        ksort($list_of_positions);

        return $list_of_positions;
    }

    /**
     * method validate_position
     * param $mode
     * return validates the position setted by the user
     */
    private function validatePosition($mode)
    {
        $return['galaxy'] = '';
        $return['system'] = '';
        $return['planet'] = '';
        $return['psystem'] = '';

        switch ($mode) {
            case 0:
                $galaxy = $this->planet['planet_galaxy'];
                $system = $this->planet['planet_system'];
                $planet = $this->planet['planet_planet'];
                break;
            case 1:
                // ONLY NUMBERS
                $_POST['galaxy'] = (isset($_POST['galaxy']) && intval($_POST['galaxy'])) ? preg_replace("[^0-9]", "", $_POST['galaxy']) : 1;
                $_POST['system'] = (isset($_POST['system']) && intval($_POST['system'])) ? preg_replace("[^0-9]", "", $_POST['system']) : 1;

                // DO NOT GO FAR FAR AWAY.. xD
                $_POST['galaxy'] = $_POST['galaxy'] > MAX_GALAXY_IN_WORLD ? MAX_GALAXY_IN_WORLD : $_POST['galaxy'];
                $_POST['system'] = $_POST['system'] > MAX_SYSTEM_IN_GALAXY ? MAX_SYSTEM_IN_GALAXY : $_POST['system'];

                if (isset($_POST['galaxyLeft'])) {
                    if ($_POST['galaxy'] < 1) {
                        $_POST['galaxy'] = 1;
                        $galaxy = 1;
                    } elseif ($_POST['galaxy'] == 1) {
                        $_POST['galaxy'] = 1;
                        $galaxy = 1;
                    } else {
                        $galaxy = $_POST['galaxy'] - 1;
                    }
                } elseif (isset($_POST['galaxyRight'])) {
                    if ($_POST['galaxy'] > MAX_GALAXY_IN_WORLD or $_POST['galaxyRight'] > MAX_GALAXY_IN_WORLD) {
                        $_POST['galaxy'] = MAX_GALAXY_IN_WORLD;
                        $_POST['galaxyRight'] = MAX_GALAXY_IN_WORLD;
                        $galaxy = MAX_GALAXY_IN_WORLD;
                    } elseif ($_POST['galaxy'] == MAX_GALAXY_IN_WORLD) {
                        $_POST['galaxy'] = MAX_GALAXY_IN_WORLD;
                        $galaxy = MAX_GALAXY_IN_WORLD;
                    } else {
                        $galaxy = $_POST['galaxy'] + 1;
                    }
                } else {
                    $galaxy = $_POST['galaxy'];
                }

                if (isset($_POST['systemLeft'])) {
                    if ($_POST['system'] < 1) {
                        $_POST['system'] = 1;
                        $system = 1;
                    } elseif ($_POST['system'] == 1) {
                        $_POST['system'] = 1;
                        $system = 1;
                    } else {
                        $system = $_POST['system'] - 1;
                    }
                } elseif (isset($_POST['systemRight'])) {
                    if ($_POST['system'] > MAX_SYSTEM_IN_GALAXY or $_POST['systemRight'] > MAX_SYSTEM_IN_GALAXY) {
                        $_POST['system'] = MAX_SYSTEM_IN_GALAXY;
                        $system = MAX_SYSTEM_IN_GALAXY;
                    } elseif ($_POST['system'] == MAX_SYSTEM_IN_GALAXY) {
                        $_POST['system'] = MAX_SYSTEM_IN_GALAXY;
                        $system = MAX_SYSTEM_IN_GALAXY;
                    } else {
                        $system = $_POST['system'] + 1;
                    }
                } else {
                    $system = $_POST['system'];
                }
                break;
            case 2:
                $galaxy = intval($_GET['galaxy']);
                $system = intval($_GET['system']);
                $planet = intval($_GET['planet']);
                break;
            case 3:
                $galaxy = intval($_GET['galaxy']);
                $system = intval($_GET['system']);
                break;
            default:
                $galaxy = 1;
                $system = 1;
                break;
        }

        $return['galaxy'] = $galaxy;
        $return['system'] = $system;
        $return['planet'] = isset($planet) ? $planet : null;
        $return['psystem'] = isset($_POST['system']) ? $_POST['system'] : null;

        return $return;
    }

    /**
     * method send_missiles
     * param
     * return send missiles routine
     */
    private function sendMissiles()
    {
        $g = intval($_GET['galaxy']);
        $s = intval($_GET['system']);
        $i = intval($_GET['planet']);
        $anz = ($_POST['SendMI'] < 0) ? 0 : intval($_POST['SendMI']);
        $target = $_POST['Target'];

        $missiles = $this->planet['defense_interplanetary_missile'];
        $tempvar1 = abs($s - $this->planet['planet_system']);
        $tempvar2 = $this->_formula->missileRange($this->user['research_impulse_drive']);

        $tempvar3 = $this->_db->queryFetch(
            "SELECT u.`user_id`,u.`user_onlinetime`,pr.`preference_vacation_mode`
            FROM " . USERS . " AS u
            INNER JOIN " . PREFERENCES . " AS pr ON pr.preference_user_id = u.user_id
            WHERE u.user_id = (SELECT `planet_user_id`
            FROM " . PLANETS . "
            WHERE planet_galaxy = " . $g . "  AND
                planet_system = " . $s . " AND
                planet_planet = " . $i . " AND
                planet_type = 1 LIMIT 1) LIMIT 1"
        );

        $user_points = $this->_noob->returnPoints($this->user['user_id'], $tempvar3['user_id']);
        $MyGameLevel = $user_points['user_points'];
        $HeGameLevel = $user_points['target_points'];

        $error = '';
        $errors = 0;

        if ($this->planet['building_missile_silo'] < 4) {
            $error .= $this->langs->line('gl_silo_level') . '<br>';
            $errors++;
        }

        if ($this->user['research_impulse_drive'] == 0) {
            $error .= $this->langs->line('gl_impulse_drive_required') . '<br>';
            $errors++;
        }

        if ($tempvar1 >= $tempvar2 || $g != $this->planet['planet_galaxy']) {
            $error .= $this->langs->line('gl_not_send_other_galaxy') . '<br>';
            $errors++;
        }

        if (!$tempvar3) {
            $error .= $this->langs->line('gl_planet_doesnt_exists') . '<br>';
            $errors++;
        }

        if ($anz > $missiles) {
            $error .= $this->langs->line('gl_cant_send') . $anz . $this->langs->line('gl_missile') . $missiles . '<br>';
            $errors++;
        }

        if (((!is_numeric($target) && $target != "all") or ($target < 0 or $target > 8))) {
            $error .= $this->langs->line('gl_wrong_target') . '<br>';
            $errors++;
        }

        if ($missiles == 0) {
            $error .= $this->langs->line('gl_no_missiles') . '<br>';
            $errors++;
        }

        if ($anz == 0) {
            $error .= $this->langs->line('gl_add_missile_number') . '<br>';
            $errors++;
        }

        if ($tempvar3['user_onlinetime'] >= (time() - 60 * 60 * 24 * 7)) {
            if ($this->_noob->isWeak($MyGameLevel, $HeGameLevel)) {
                $error .= $this->langs->line('fl_week_player') . '<br>';
                $errors++;
            } elseif ($this->_noob->isStrong($MyGameLevel, $HeGameLevel)) {
                $error .= $this->langs->line('fl_strong_player') . '<br>';
                $errors++;
            }
        }
        if ($tempvar3['preference_vacation_mode'] > 0) {
            $error .= $this->langs->line('fl_in_vacation_player') . '<br>';
            $errors++;
        }

        if ($errors != 0) {
            FunctionsLib::message($error, "game.php?page=galaxy&mode=0&galaxy=" . $g . "&system=" . $s, 3);
        }

        $ziel_id = $tempvar3['user_id'];

        $flugzeit = round(((30 + (60 * $tempvar1)) * 2500) / FunctionsLib::readConfig('fleet_speed'));

        $DefenseLabel = array(
            0 => $this->langs->line('gl_all_defenses'),
            1 => $this->langs->line('defense_rocket_launcher'),
            2 => $this->langs->line('defense_light_laser'),
            3 => $this->langs->line('defense_heavy_laser'),
            4 => $this->langs->line('defense_gauss_cannon'),
            5 => $this->langs->line('defense_ion_cannon'),
            6 => $this->langs->line('defense_plasma_turret'),
            7 => $this->langs->line('defense_small_shield_dome'),
            8 => $this->langs->line('defense_large_shield_dome'),
        );

        $this->_db->query(
            "INSERT INTO `" . FLEETS . "` SET
            `fleet_owner` = '" . $this->user['user_id'] . "',
            `fleet_mission` = '10',
            `fleet_amount` = " . $anz . ",
            `fleet_array` = '" . FleetsLib::setFleetShipsArray([503 => $anz]) . "',
            `fleet_start_time` = '" . (time() + $flugzeit) . "',
            `fleet_start_galaxy` = '" . $this->planet['planet_galaxy'] . "',
            `fleet_start_system` = '" . $this->planet['planet_system'] . "',
            `fleet_start_planet` ='" . $this->planet['planet_planet'] . "',
            `fleet_start_type` = '1',
            `fleet_end_time` = '" . (time() + $flugzeit + 1) . "',
            `fleet_end_stay` = '0',
            `fleet_end_galaxy` = '" . $g . "',
            `fleet_end_system` = '" . $s . "',
            `fleet_end_planet` = '" . $i . "',
            `fleet_end_type` = '1',
            `fleet_target_obj` = '" . $target . "',
            `fleet_resource_metal` = '0',
            `fleet_resource_crystal` = '0',
            `fleet_resource_deuterium` = '0',
            `fleet_target_owner` = '" . $ziel_id . "',
            `fleet_group` = '0',
            `fleet_mess` = '0',
            `fleet_creation` = '" . time() . "';"
        );

        $this->_db->query(
            "UPDATE `" . DEFENSES . "` SET
            `defense_interplanetary_missile` = `defense_interplanetary_missile` - " . $anz . "
            WHERE `defense_planet_id` =  '" . $this->user['user_current_planet'] . "'"
        );

        FunctionsLib::message("<b>" . $anz . "</b>" . $this->langs->line('gl_missiles_sended') . $DefenseLabel[$target], "game.php?page=overview", 3);
    }

    /**
     * method send_fleet
     * param
     * return send fleet routine
     */
    private function sendFleet()
    {
        $max_spy_probes = $this->user['preference_spy_probes'];
        $UserSpyProbes = $this->planet['ship_espionage_probe'];
        $UserRecycles = $this->planet['ship_recycler'];
        $UserDeuterium = $this->planet['planet_deuterium'];
        $UserMissiles = $this->planet['defense_interplanetary_missile'];
        $fleet = [];
        $speedalls = [];
        $PartialFleet = false;
        $PartialCount = 0;
        $order = isset($_POST['order']) ? $_POST['order'] : null;
        $ResultMessage = '';
        $fleet['fleetlist'] = '';
        $fleet['amount'] = '';

        switch ($order) {
            case 6:
                $_POST['ship210'] = $_POST['shipcount'];
                break;
            case 7:
                $_POST['ship208'] = $_POST['shipcount'];
                break;
            case 8:
                $_POST['ship209'] = $_POST['shipcount'];
                break;
        }

        $fleet['amount'] = 0;

        foreach ($this->_reslist['fleet'] as $ship_id) {
            $TName = "ship" . $ship_id;
            $ship_amount = isset($_POST[$TName]) ? (int) $_POST[$TName] : 0;

            if ($ship_id > 200 && $ship_id < 300 && $ship_amount > 0) {
                if ($ship_amount > $this->planet[$this->_resource[$ship_id]]) {
                    $fleet['fleetarray'][$ship_id] = (int) $this->planet[$this->_resource[$ship_id]];
                    $fleet['fleetlist'] .= $ship_id . "," . $this->planet[$this->_resource[$ship_id]] . ";";
                    $fleet['amount'] += (int) $this->planet[$this->_resource[$ship_id]];
                    $PartialCount += (int) $this->planet[$this->_resource[$ship_id]];

                    // we sent less that the amount requested
                    $PartialFleet = true;
                } else {
                    $fleet['fleetarray'][$ship_id] = $ship_amount;
                    $fleet['fleetlist'] .= $ship_id . "," . $ship_amount . ";";
                    $fleet['amount'] += $ship_amount;
                    $speedalls[$ship_id] = $ship_amount;
                }
            }
        }

        $errors_types = array(
            600 => $this->langs->line('gl_success'),
            601 => $this->langs->line('gl_error'),
            602 => $this->langs->line('gl_no_moon'),
            603 => $this->langs->line('gl_noob_protection'),
            604 => $this->langs->line('gl_too_strong'),
            605 => $this->langs->line('gl_vacation_mode'),
            610 => $this->langs->line('gl_only_amount_ships'),
            611 => $this->langs->line('gl_no_ships'),
            612 => $this->langs->line('gl_no_slots'),
            613 => $this->langs->line('gl_no_deuterium'),
            614 => $this->langs->line('gl_no_planet'),
            615 => $this->langs->line('gl_not_enough_storage'),
            616 => $this->langs->line('gl_multi_alarm'),
        );

        if ($PartialFleet == true) {
            if ($PartialCount < 1) {
                die("611 ");
            }
        }

        $galaxy = isset($_POST['galaxy']) ? (int) $_POST['galaxy'] : 0;
        $system = isset($_POST['system']) ? (int) $_POST['system'] : 0;
        $planet = isset($_POST['planet']) ? (int) $_POST['planet'] : 0;
        $FleetArray = isset($fleet['fleetarray']) ? $fleet['fleetarray'] : null;

        if (($galaxy > MAX_GALAXY_IN_WORLD or $galaxy < 1) or ($system > MAX_SYSTEM_IN_GALAXY or $system < 1) or ($planet > MAX_PLANET_IN_SYSTEM or $planet < 1) or (is_null($FleetArray))) {
            die("614 ");
        }

        $CurrentFlyingFleets = $this->_db->queryFetch(
            "SELECT COUNT(fleet_id) AS `Nbre`
            FROM " . FLEETS . "
            WHERE `fleet_owner` = '" . $this->user['user_id'] . "';"
        );

        $CurrentFlyingFleets = $CurrentFlyingFleets['Nbre'];

        $TargetRow = $this->_db->queryFetch(
            "SELECT *
            FROM " . PLANETS . "
            WHERE `planet_galaxy` = '" . $this->_db->escapeValue($_POST['galaxy']) . "' AND
                `planet_system` = '" . $this->_db->escapeValue($_POST['system']) . "' AND
                `planet_planet` = '" . $this->_db->escapeValue($_POST['planet']) . "' AND
                `planet_type` = '" . $this->_db->escapeValue($_POST['planettype']) . "';"
        );

        if ($TargetRow == null) {
            $TargetUser = $this->user;
        } elseif ($TargetRow['planet_user_id'] != '') {
            $TargetUser = $this->_db->queryFetch(
                "SELECT u.`user_id`, u.`user_onlinetime`, u.`user_authlevel`, pr.`preference_vacation_mode`
                FROM " . USERS . " AS u
                INNER JOIN " . PREFERENCES . " AS pr ON pr.preference_user_id = u.user_id
                WHERE `user_id` = '" . $TargetRow['planet_user_id'] . "';"
            );
        }

        // invisible debris by jstar
        if ($order == 8) {
            $TargetGPlanet = $this->_db->queryFetch(
                "SELECT planet_invisible_start_time, planet_debris_metal, planet_debris_crystal
                FROM " . PLANETS . "
                WHERE planet_galaxy = '" . $this->_db->escapeValue($_POST['galaxy']) . "' AND
                                planet_system = '" . $this->_db->escapeValue($_POST['system']) . "' AND
                                planet_planet = '" . $this->_db->escapeValue($_POST['planet']) . "' AND
                                planet_type = 1;"
            );

            if ($TargetGPlanet['planet_debris_metal'] == 0 && $TargetGPlanet['planet_debris_crystal'] == 0 && time() > ($TargetGPlanet['planet_invisible_start_time'] + DEBRIS_LIFE_TIME)) {
                die();
            }
        }

        $user_points = $this->_noob->returnPoints($this->user['user_id'], $TargetUser['user_id']);
        $CurrentPoints = $user_points['user_points'];
        $TargetPoints = $user_points['target_points'];
        $TargetVacat = $TargetUser['preference_vacation_mode'];

        if ((FleetsLib::getMaxFleets($this->user[$this->_resource[108]], $this->user['premium_officier_admiral'])) <= $CurrentFlyingFleets) {
            die("612 ");
        }

        if (!is_array($FleetArray)) {
            die("611 ");
        }

        if (!(($order == 6) or ($order == 8))) {
            die("601 ");
        }

        if (($TargetVacat && $order != 8) or ($this->user['preference_vacation_mode'] > 0)) {
            die("605 ");
        }

        if ($TargetUser['user_onlinetime'] >= (time() - 60 * 60 * 24 * 7)) {
            if ($this->_noob->isWeak($CurrentPoints, $TargetPoints) && $TargetRow['planet_user_id'] != '' && $order == 6) {
                die("603 ");
            }

            if ($this->_noob->isStrong($CurrentPoints, $TargetPoints) && $TargetRow['planet_user_id'] != '' && $order == 6) {
                die("604 ");
            }
        }

        if ($TargetRow['planet_user_id'] == '' && $order != 8) {
            die("601 ");
        }

        if (($TargetRow['planet_user_id'] == $this->planet['planet_user_id']) && ($order == 6)) {
            die("601 ");
        }

        $Distance = FleetsLib::targetDistance($this->planet['planet_galaxy'], $_POST['galaxy'], $this->planet['planet_system'], $_POST['system'], $this->planet['planet_planet'], $_POST['planet']);
        $speedall = FleetsLib::fleetMaxSpeed($FleetArray, 0, $this->user);
        $SpeedAllMin = min($speedall);
        $Duration = FleetsLib::missionDuration(10, $SpeedAllMin, $Distance, FunctionsLib::fleetSpeedFactor());

        $fleet['fly_time'] = $Duration;
        $fleet['start_time'] = $Duration + time();
        $fleet['end_time'] = ($Duration * 2) + time();

        $FleetShipCount = 0;
        $FleetDBArray = [];
        $FleetSubQRY = '';
        $consumption = 0;
        $SpeedFactor = FunctionsLib::fleetSpeedFactor();

        foreach ($FleetArray as $Ship => $Count) {
            if ($Ship != '') {
                $ShipSpeed = $this->_pricelist[$Ship]['speed'];
                $spd = 35000 / ($Duration * $SpeedFactor - 10) * sqrt($Distance * 10 / $ShipSpeed);
                $basicConsumption = $this->_pricelist[$Ship]['consumption'] * $Count;
                $consumption += $basicConsumption * $Distance / 35000 * (($spd / 10) + 1) * (($spd / 10) + 1);
                $FleetShipCount += $Count;
                $FleetDBArray[$Ship] = $Count;
                $FleetSubQRY .= "`" . $this->_resource[$Ship] . "` = `" . $this->_resource[$Ship] . "` - " . $Count . ", ";
            }
        }

        $consumption = round($consumption) + 1;

        if ($UserDeuterium < $consumption) {
            die("613 ");
        }

        if (FunctionsLib::readConfig('adm_attack') == 1 && $TargetUser['user_authlevel'] > 0) {
            die("601 ");
        }

        $this->_db->query(
            "INSERT INTO " . FLEETS . " SET
            `fleet_owner` = '" . $this->user['user_id'] . "',
            `fleet_mission` = '" . intval($order) . "',
            `fleet_amount` = '" . $FleetShipCount . "',
            `fleet_array` = '" . FleetsLib::setFleetShipsArray($FleetDBArray) . "',
            `fleet_start_time` = '" . $fleet['start_time'] . "',
            `fleet_start_galaxy` = '" . $this->planet['planet_galaxy'] . "',
            `fleet_start_system` = '" . $this->planet['planet_system'] . "',
            `fleet_start_planet` = '" . $this->planet['planet_planet'] . "',
            `fleet_start_type` = '" . $this->planet['planet_type'] . "',
            `fleet_end_time` = '" . $fleet['end_time'] . "',
            `fleet_end_galaxy` = '" . intval($_POST['galaxy']) . "',
            `fleet_end_system` = '" . intval($_POST['system']) . "',
            `fleet_end_planet` = '" . intval($_POST['planet']) . "',
            `fleet_end_type` = '" . intval($_POST['planettype']) . "',
            `fleet_target_owner` = '" . $TargetRow['planet_user_id'] . "',
            `fleet_creation` = '" . time() . "';"
        );

        $UserDeuterium -= $consumption;

        $this->_db->query(
            "UPDATE " . PLANETS . " AS p
            INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id` SET
            $FleetSubQRY
            p.`planet_deuterium` = '" . (($UserDeuterium < 1) ? 0 : $UserDeuterium) . "'
            WHERE p.`planet_id` = '" . $this->planet['planet_id'] . "';"
        );

        $CurrentFlyingFleets++;

        foreach ($FleetArray as $Ships => $Count) {
            if ($max_spy_probes > $this->planet[$this->_resource[$Ships]]) {
                $ResultMessage = "610 " . $FleetShipCount;
            }
        }

        if ($ResultMessage == '') {
            $ResultMessage = "600 " . $Ships;
        }

        die($ResultMessage);
    }
}

/* end of galaxy.php */
