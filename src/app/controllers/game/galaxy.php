<?php
/**
 * Galaxy Controller
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */

namespace App\controllers\game;

use App\core\BaseController;
use App\libraries\FleetsLib;
use App\libraries\FormatLib;
use App\libraries\Formulas;
use App\libraries\Functions;
use App\libraries\Users;

/**
 * Galaxy Class
 */
class Galaxy extends BaseController
{
    /**
     * The module ID
     *
     * @var int
     */
    public const MODULE_ID = 11;

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

    /**
     * @var mixed
     */
    private $_galaxy;
    /**
     * @var mixed
     */
    private $_system;
    /**
     * @var mixed
     */
    private $_noob;
    /**
     * @var mixed
     */
    private $_galaxyLib;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Users::checkSession();

        // load Model
        parent::loadModel('game/fleet');
        parent::loadModel('game/galaxy');

        // load Language
        parent::loadLang(['game/global', 'game/defenses', 'game/missions', 'game/galaxy']);

        $this->_resource = $this->objects->getObjects();
        $this->_pricelist = $this->objects->getPrice();
        $this->_reslist = $this->objects->getObjectsList();
        $this->_noob = Functions::loadLibrary('NoobsProtectionLib');
        $this->_galaxyLib = Functions::loadLibrary('GalaxyLib');

        if ($this->user['preference_vacation_mode'] > 0) {
            Functions::message($this->langs->line('gl_no_access_vm_on'), '', '');
        }

        // init a new galaxy object
        //$this->setUpPreferences();
    }

    /**
     * Users land here
     *
     * @return void
     */
    public function index(): void
    {
        // Check module access
        Functions::moduleMessage(Functions::isModuleAccesible(self::MODULE_ID));

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
        // fleets
        $max_fleets = FleetsLib::getMaxFleets($this->user['research_computer_technology'], $this->user['premium_officier_admiral']);
        $current_fleets = $this->Galaxy_Model->countAmountFleetsByUserId($this->user['user_id']);

        // missiles and espionage probes
        $CurrentPlID = $this->planet['planet_id'];
        $CurrentSP = $this->planet['ship_espionage_probe'];

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

        if ($mode == 2 && $this->planet['defense_interplanetary_missile'] < 1) {
            die(Functions::message($this->langs->line('gl_no_missiles'), "game.php?page=galaxy&mode=0", 2));
        }

        $this->galaxy = $this->Galaxy_Model->getGalaxyDataByGalaxyAndSystem($this->_galaxy, $this->_system, $this->user['user_id']);

        $parse['selected_galaxy'] = $this->_galaxy;
        $parse['selected_system'] = $this->_system;
        $parse['selected_planet'] = $planet;
        $parse['currentmip'] = $this->planet['defense_interplanetary_missile'];
        $parse['maxfleetcount'] = $current_fleets;
        $parse['fleetmax'] = $max_fleets;
        $parse['recyclers'] = FormatLib::prettyNumber($this->planet['ship_recycler']);
        $parse['spyprobes'] = FormatLib::prettyNumber($CurrentSP);
        $parse['missile_count'] = sprintf($this->langs->line('gl_missil_to_launch'), $this->planet['defense_interplanetary_missile']);
        $parse['current'] = isset($_GET['current']) ? $_GET['current'] : null;
        $parse['current_galaxy'] = $this->planet['planet_galaxy'];
        $parse['current_system'] = $this->planet['planet_system'];
        $parse['current_planet'] = $this->planet['planet_planet'];
        $parse['coords'] = FormatLib::prettyCoords((int) $this->_galaxy, (int) $this->_system, (int) $planet);
        $parse['planet_type'] = $this->planet['planet_type'];
        $parse['mip'] = ($mode == 2) ? $this->template->set(
            'galaxy/galaxy_missile_selector',
            array_merge($parse, $this->langs->language)
        ) : ' ';

        $this->page->display(
            $this->template->set(
                'game/galaxy_view',
                array_merge(
                    $this->langs->language,
                    [
                        'js_path' => JS_PATH,
                        'list_of_positions' => $this->buildPositionsList(),
                        'planet_count' => $this->planet_count,
                        'max_galaxy' => MAX_GALAXY_IN_WORLD,
                        'max_system' => MAX_SYSTEM_IN_GALAXY,
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
            $this->langs
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

        switch ($mode) {
            case 0:
                $galaxy = $this->planet['planet_galaxy'];
                $system = $this->planet['planet_system'];
                $planet = $this->planet['planet_planet'];
                break;
            case 1:
                // validate, we want only numbers
                $galaxy = (isset($_POST['galaxy']) && intval($_POST['galaxy'])) ? preg_replace("[^0-9]", "", $_POST['galaxy']) : 1;
                $system = (isset($_POST['system']) && intval($_POST['system'])) ? preg_replace("[^0-9]", "", $_POST['system']) : 1;

                /**
                 * Change galaxy
                 */
                if (isset($_POST['galaxyRight'])) {
                    if ($galaxy >= MAX_GALAXY_IN_WORLD) {
                        $galaxy = 1;
                    } else {
                        $galaxy++;
                    }
                }

                if (isset($_POST['galaxyLeft'])) {
                    if ($galaxy <= 1) {
                        $galaxy = MAX_GALAXY_IN_WORLD;
                    } else {
                        $galaxy--;
                    }
                }

                /**
                 * Change system
                 */
                if (isset($_POST['systemRight'])) {
                    if ($system >= MAX_SYSTEM_IN_GALAXY) {
                        $system = 1;
                    } else {
                        $system++;
                    }
                }

                if (isset($_POST['systemLeft'])) {
                    if ($system <= 1) {
                        $system = MAX_SYSTEM_IN_GALAXY;
                    } else {
                        $system--;
                    }
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

        return $return;
    }

    /**
     * method send_missiles
     * param
     * return send missiles routine
     */
    private function sendMissiles()
    {
        $galaxy = intval($_GET['galaxy']);
        $system = intval($_GET['system']);
        $planet = intval($_GET['planet']);
        $missiles_amount = ($_POST['SendMI'] < 0) ? 0 : intval($_POST['SendMI']);
        $target = $_POST['Target'];

        $current_missiles = $this->planet['defense_interplanetary_missile'];
        $tempvar1 = abs($system - $this->planet['planet_system']);
        $tempvar2 = Formulas::missileRange($this->user['research_impulse_drive']);

        $target_user = $this->Galaxy_Model->getTargetUserDataByCoords($galaxy, $system, $planet);

        $user_points = $this->_noob->returnPoints($this->user['user_id'], $target_user['user_id']);
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

        if ($tempvar1 >= $tempvar2 || $galaxy != $this->planet['planet_galaxy']) {
            $error .= $this->langs->line('gl_not_send_other_galaxy') . '<br>';
            $errors++;
        }

        if (!$target_user) {
            $error .= $this->langs->line('gl_planet_doesnt_exists') . '<br>';
            $errors++;
        }

        if ($missiles_amount > $current_missiles) {
            $error .= $this->langs->line('gl_cant_send') . $missiles_amount . $this->langs->line('gl_missile') . $current_missiles . '<br>';
            $errors++;
        }

        if (((!is_numeric($target) && $target != "all") or ($target < 0 or $target > 8))) {
            $error .= $this->langs->line('gl_wrong_target') . '<br>';
            $errors++;
        }

        if ($current_missiles == 0) {
            $error .= $this->langs->line('gl_no_missiles') . '<br>';
            $errors++;
        }

        if ($missiles_amount == 0) {
            $error .= $this->langs->line('gl_add_missile_number') . '<br>';
            $errors++;
        }

        if ($target_user['user_onlinetime'] >= (time() - 60 * 60 * 24 * 7)) {
            if ($this->_noob->isWeak(intval($MyGameLevel), intval($HeGameLevel))) {
                $error .= $this->langs->line('fl_week_player') . '<br>';
                $errors++;
            } elseif ($this->_noob->isStrong(intval($MyGameLevel), intval($HeGameLevel))) {
                $error .= $this->langs->line('fl_strong_player') . '<br>';
                $errors++;
            }
        }
        if ($target_user['preference_vacation_mode'] > 0) {
            $error .= $this->langs->line('fl_in_vacation_player') . '<br>';
            $errors++;
        }

        if ($errors != 0) {
            Functions::message($error, "game.php?page=galaxy&mode=0&galaxy=" . $galaxy . "&system=" . $system, 3);
        }

        $flight_time = round(((30 + (60 * $tempvar1)) * 2500) / Functions::readConfig('fleet_speed'));

        $DefenseLabel = [
            0 => $this->langs->line('gl_all_defenses'),
            1 => $this->langs->line('defense_rocket_launcher'),
            2 => $this->langs->line('defense_light_laser'),
            3 => $this->langs->line('defense_heavy_laser'),
            4 => $this->langs->line('defense_gauss_cannon'),
            5 => $this->langs->line('defense_ion_cannon'),
            6 => $this->langs->line('defense_plasma_turret'),
            7 => $this->langs->line('defense_small_shield_dome'),
            8 => $this->langs->line('defense_large_shield_dome'),
        ];

        $this->Fleet_Model->insertNewMissilesMission([
            'fleet_owner' => $this->user['user_id'],
            'fleet_amount' => $missiles_amount,
            'fleet_array' => FleetsLib::setFleetShipsArray([503 => $missiles_amount]),
            'fleet_start_time' => (time() + $flight_time),
            'fleet_start_galaxy' => $this->planet['planet_galaxy'],
            'fleet_start_system' => $this->planet['planet_system'],
            'fleet_start_planet' => $this->planet['planet_planet'],
            'fleet_end_time' => (time() + $flight_time + 1),
            'fleet_end_galaxy' => $galaxy,
            'fleet_end_system' => $system,
            'fleet_end_planet' => $planet,
            'fleet_target_obj' => $target,
            'fleet_target_owner' => $target_user['user_id'],
            'user_current_planet' => $this->user['user_current_planet'],
        ]);

        Functions::message("<b>" . $missiles_amount . "</b>" . $this->langs->line('gl_missiles_sended') . $DefenseLabel[$target], "game.php?page=overview", 3);
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

        $errors_types = [
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
        ];

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

        $current_fleets = $this->Galaxy_Model->countAmountFleetsByUserId($this->user['user_id']);

        $target_user = $this->Galaxy_Model->getTargetUserDataByCoords($galaxy, $system, $planet, $_POST['planettype']);

        if ($target_user == null) {
            $target_user = $this->user;
        }

        // invisible debris by jstar
        if ($order == 8) {
            $TargetGPlanet = $this->Galaxy_Model->getPlanetDebrisByCoords($galaxy, $system, $planet);

            if ($TargetGPlanet['planet_debris_metal'] == 0 && $TargetGPlanet['planet_debris_crystal'] == 0 && time() > ($TargetGPlanet['planet_invisible_start_time'] + DEBRIS_LIFE_TIME)) {
                die();
            }
        }

        $user_points = $this->_noob->returnPoints($this->user['user_id'], $target_user['user_id']);
        $CurrentPoints = $user_points['user_points'];
        $TargetPoints = $user_points['target_points'];
        $TargetVacat = $target_user['preference_vacation_mode'];

        if ((FleetsLib::getMaxFleets($this->user[$this->_resource[108]], $this->user['premium_officier_admiral'])) <= $current_fleets) {
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

        if ($target_user['user_onlinetime'] >= (time() - 60 * 60 * 24 * 7)) {
            if ($this->_noob->isWeak(intval($CurrentPoints), intval($TargetPoints)) && $target_user['user_id'] != '' && $order == 6) {
                die("603 ");
            }

            if ($this->_noob->isStrong(intval($CurrentPoints), intval($TargetPoints)) && $target_user['user_id'] != '' && $order == 6) {
                die("604 ");
            }
        }

        if ($target_user['user_id'] == '' && $order != 8) {
            die("601 ");
        }

        if (($target_user['user_id'] == $this->planet['planet_user_id']) && ($order == 6)) {
            die("601 ");
        }

        $Distance = FleetsLib::targetDistance($this->planet['planet_galaxy'], $_POST['galaxy'], $this->planet['planet_system'], $_POST['system'], $this->planet['planet_planet'], $_POST['planet']);
        $speedall = FleetsLib::fleetMaxSpeed($FleetArray, 0, $this->user);
        $SpeedAllMin = min($speedall);
        $Duration = FleetsLib::missionDuration(10, $SpeedAllMin, $Distance, Functions::fleetSpeedFactor());

        $fleet['fly_time'] = $Duration;
        $fleet['start_time'] = $Duration + time();
        $fleet['end_time'] = ($Duration * 2) + time();

        $FleetShipCount = 0;
        $FleetDBArray = [];
        $fleet_sub_query = [];
        $consumption = 0;
        $SpeedFactor = Functions::fleetSpeedFactor();

        foreach ($FleetArray as $Ship => $Count) {
            if ($Ship != '') {
                $ShipSpeed = $this->_pricelist[$Ship]['speed'];
                $spd = 35000 / ($Duration * $SpeedFactor - 10) * sqrt($Distance * 10 / $ShipSpeed);
                $basicConsumption = $this->_pricelist[$Ship]['consumption'] * $Count;
                $consumption += $basicConsumption * $Distance / 35000 * (($spd / 10) + 1) * (($spd / 10) + 1);
                $FleetShipCount += $Count;
                $FleetDBArray[$Ship] = $Count;
                $fleet_sub_query[$this->_resource[$Ship]] = $Count;
            }
        }

        $consumption = round($consumption) + 1;

        if ($UserDeuterium < $consumption) {
            die("613 ");
        }

        if (Functions::readConfig('adm_attack') == 1 && $target_user['user_authlevel'] > 0) {
            die("601 ");
        }

        $this->Fleet_Model->insertNewFleet(
            [
                'fleet_owner' => $this->user['user_id'],
                'fleet_mission' => intval($order),
                'fleet_amount' => $FleetShipCount,
                'fleet_array' => FleetsLib::setFleetShipsArray($FleetDBArray),
                'fleet_start_time' => $fleet['start_time'],
                'fleet_start_galaxy' => $this->planet['planet_galaxy'],
                'fleet_start_system' => $this->planet['planet_system'],
                'fleet_start_planet' => $this->planet['planet_planet'],
                'fleet_start_type' => $this->planet['planet_type'],
                'fleet_end_time' => $fleet['end_time'],
                'fleet_end_galaxy' => intval($_POST['galaxy']),
                'fleet_end_system' => intval($_POST['system']),
                'fleet_end_planet' => intval($_POST['planet']),
                'fleet_end_type' => intval($_POST['planettype']),
                'fleet_resource_metal' => 0,
                'fleet_resource_crystal' => 0,
                'fleet_resource_deuterium' => 0,
                'fleet_fuel' => $consumption,
                'fleet_target_owner' => $target_user['user_id'],
            ],
            $this->planet,
            $fleet_sub_query
        );

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
