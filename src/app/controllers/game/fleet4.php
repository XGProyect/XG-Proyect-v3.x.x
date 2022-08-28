<?php
/**
 * Fleet4 Controller
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
use App\core\enumerators\MissionsEnumerator as Missions;
use App\core\enumerators\PlanetTypesEnumerator as PlanetTypes;
use App\core\enumerators\ShipsEnumerator as Ships;
use App\libraries\FleetsLib;
use App\libraries\FormatLib;
use App\libraries\Functions;
use App\libraries\game\Fleets;
use App\libraries\premium\Premium;
use App\libraries\research\Researches;
use App\libraries\Users;

/**
 * Fleet4 Class
 */
class Fleet4 extends BaseController
{
    /**
     *
     * @var int
     */
    public const MODULE_ID = 8;

    /**
     *
     * @var string
     */
    public const REDIRECT_TARGET = 'game.php?page=movement';

    /**
     *
     * @var \Fleets
     */
    private $_fleets = null;

    /**
     *
     * @var \Research
     */
    private $_research = null;

    /**
     *
     * @var \Premium
     */
    private $_premium = null;

    /**
     * Already filtered POST data
     *
     * @var array
     */
    private $_clean_input_data = [];

    /**
     *
     * @var array
     */
    private $_fleet_data = [
        'fleet_owner' => 0,
        'fleet_mission' => 0,
        'fleet_amount' => 0,
        'fleet_array' => '',
        'fleet_start_time' => 0,
        'fleet_start_galaxy' => 0,
        'fleet_start_system' => 0,
        'fleet_start_planet' => 0,
        'fleet_start_type' => 0,
        'fleet_end_time' => 0,
        'fleet_end_stay' => 0,
        'fleet_end_galaxy' => 0,
        'fleet_end_system' => 0,
        'fleet_end_planet' => 0,
        'fleet_end_type' => 0,
        'fleet_resource_metal' => 0,
        'fleet_resource_crystal' => 0,
        'fleet_resource_deuterium' => 0,
        'fleet_fuel' => 0,
        'fleet_target_owner' => 0,
        'fleet_group' => 0,
    ];

    /**
     *
     * @var array
     */
    private $_target_data = [];

    /**
     *
     * @var boolean
     */
    private $_own_planet = false;

    /**
     *
     * @var boolean
     */
    private $_occupied_planet = false;

    /**
     *
     * @var int
     */
    private $_fleet_storage = 0;

    /**
     *
     * @var array
     */
    private $_fleet_ships = [];

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

        // load Language
        parent::loadLang(['game/fleet']);

        // init a new fleets object
        $this->setUpFleets();
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

        // build the page
        $this->buildPage();
    }

    /**
     * Creates a new ships object that will handle all the ships
     * creation methods and actions
     *
     * @return void
     */
    private function setUpFleets()
    {
        $this->_fleets = new Fleets(
            $this->Fleet_Model->getAllFleetsByUserId($this->user['user_id']),
            $this->user['user_id']
        );

        $this->_research = new Researches(
            [$this->user],
            $this->user['user_id']
        );

        $this->_premium = new Premium(
            [$this->user],
            $this->user['user_id']
        );
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage()
    {
        // filter stuff from fleet1, fleet2 and fleet3
        $this->setInputsData();

        // get the target
        $this->getTarget();

        // validate all the received data
        if ($this->runValidations()) {
            // final step, send and redirect
            $this->sendFleet();
        }

        Functions::redirect(self::REDIRECT_TARGET);
    }

    /**
     * Set inputs data
     *
     * @return array
     */
    private function setInputsData()
    {
        $exp_time = $this->_research->getCurrentResearch()->getResearchAstrophysics();

        $min_exp_time = $exp_time <= 0 ? 0 : 1;
        $max_exp_time = $exp_time;

        $data = filter_input_array(INPUT_POST, [
            'mission' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 1, 'max_range' => 15],
            ],
            'resource1' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 0, 'max_range' => $this->planet['planet_metal']],
            ],
            'resource2' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 0, 'max_range' => $this->planet['planet_crystal']],
            ],
            'resource3' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 0, 'max_range' => $this->planet['planet_deuterium']],
            ],
            'expeditiontime' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => $min_exp_time, 'max_range' => $max_exp_time],
            ],
            'holdingtime' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 0, 'max_range' => 32],
            ],
        ]);

        if (is_null($data)) {
            Functions::redirect('game.php?page=fleet1');
        }

        $this->_clean_input_data = $data;
    }

    /**
     * Get target user info and planet info
     *
     * @return void
     */
    private function getTarget()
    {
        $target_data = $this->getTargetData();

        $target = $this->Fleet_Model->getTargetDataByCoords(
            $target_data['galaxy'],
            $target_data['system'],
            $target_data['planet'],
            ($target_data['type'] != 2 ? $target_data['type'] : '1')
        );

        if ($target) {
            $this->_occupied_planet = true;

            // set target data
            $this->_target_data = $target;

            // validate owner
            if ($target['planet_user_id'] == $this->user['user_id']) {
                $this->_own_planet = true;
            }

            if ($target['planet_destroyed'] != 0) {
                Functions::redirect(self::REDIRECT_TARGET);
            }

            // set target owner
            $this->_fleet_data['fleet_target_owner'] = $target['planet_user_id'];
        }

        // set coords data
        $this->_fleet_data['fleet_start_galaxy'] = $this->planet['planet_galaxy'];
        $this->_fleet_data['fleet_start_system'] = $this->planet['planet_system'];
        $this->_fleet_data['fleet_start_planet'] = $this->planet['planet_planet'];
        $this->_fleet_data['fleet_start_type'] = $this->planet['planet_type'];
        $this->_fleet_data['fleet_end_galaxy'] = $target_data['galaxy'];
        $this->_fleet_data['fleet_end_system'] = $target_data['system'];
        $this->_fleet_data['fleet_end_planet'] = $target_data['planet'];
        $this->_fleet_data['fleet_end_type'] = $target_data['type'];
    }

    /**
     * Run multiple validations
     *
     * @return boolean
     */
    private function runValidations()
    {
        $validations = [
            'admin', 'ownVacations', 'targetVacations', 'acs', 'ships', 'mission', 'noobProtection', 'fleets', 'resources', 'time',
        ];

        foreach ($validations as $validation) {
            if (!$this->{'validate' . ucfirst($validation)}()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate both players level
     *
     * @return boolean
     */
    private function validateAdmin()
    {
        // skip if it's our own planet or it's an empty planet
        if ($this->_own_planet
            or !$this->_occupied_planet) {
            return true;
        }

        if (Functions::readConfig('adm_attack') != 0
            && $this->_target_data['user_authlevel'] >= 1
            && $this->user['user_authlevel'] == 0) {
            $this->showMessage(
                $this->langs->line('fl_admins_cannot_be_attacked')
            );
        }

        return true;
    }

    /**
     * Validate vacations for both players
     *
     * @return boolean
     */
    private function validateOwnVacations()
    {
        if ($this->userLibrary->isOnVacations($this->user)) {
            $this->showMessage($this->langs->line('fl_vacation_mode_active'));
        }

        // set owner
        $this->_fleet_data['fleet_owner'] = $this->user['user_id'];

        return true;
    }

    /**
     * Validate vacations for both players
     *
     * @return boolean
     */
    private function validateTargetVacations()
    {
        // skip if it's our own planet or it's an empty planet
        if ($this->_own_planet
            or !$this->_occupied_planet) {
            return true;
        }

        if (isset($this->_target_data)
            && $this->userLibrary->isOnVacations($this->_target_data)
            && $this->_clean_input_data['mission'] != Missions::RECYCLE) {
            $this->showMessage($this->langs->line('fl_in_vacation_player'));
        }

        return true;
    }

    /**
     * Validate any current ACS
     *
     * @return boolean
     */
    private function validateAcs()
    {
        $target_data = $this->getTargetData();

        if ($target_data['group'] > 0
            && $this->_clean_input_data['mission'] == Missions::ACS) {
            $target_string = 'g' . (int) $target_data['galaxy'] .
            's' . (int) $target_data['system'] .
            'p' . (int) $target_data['planet'] .
            't' . (int) $target_data['type'];

            if ($target_data['acs_target'] == $target_string
                && $this->Fleet_Model->getAcsCount($target_data['group']) > 0) {
                // set acs group
                $this->_fleet_data['fleet_group'] = $target_data['group'];

                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Validate if the received amount of ships is valid
     *
     * @return boolean
     */
    private function validateShips()
    {
        // post/session fleet
        $fleet = $this->getSessionShips();

        // planet ships
        $planet_ships = $this->Fleet_Model->getShipsByPlanetId($this->planet['planet_id']);

        // objects
        $objects = $this->objects->getObjects();
        $price = $this->objects->getPrice();

        if ($fleet) {
            $total_ships = 0;

            foreach ($fleet as $ship_id => $amount) {
                if (!isset($planet_ships[$objects[$ship_id]])
                    or ((int) $amount > $planet_ships[$objects[$ship_id]])) {
                    return false;
                }

                $total_ships += $amount;

                $this->_fleet_storage += FleetsLib::getMaxStorage(
                    $price[$ship_id]['capacity'],
                    $this->_research->getCurrentResearch()->getResearchHyperspaceTechnology()
                ) * $amount;
                $this->_fleet_ships[$objects[$ship_id]] = $amount;
            }

            $this->_fleet_data['fleet_amount'] = $total_ships;
            $this->_fleet_data['fleet_array'] = FleetsLib::setFleetShipsArray($fleet);

            return true;
        }

        return false;
    }

    /**
     * Validate the mission
     *
     * @return boolean
     */
    private function validateMission()
    {
        // post/session fleet
        $fleet = $this->getSessionShips();

        // clean data from post
        $data = $this->_clean_input_data;

        // target data
        $target = $this->_target_data;

        if (empty($data['mission'])) {
            Functions::redirect('game.php?page=fleet1');
        }

        if ($data['mission'] == Missions::ATTACK) {
            if ($this->_own_planet) {
                return false;
            }
        }

        if ($data['mission'] == Missions::SPY) {
            if (!isset($fleet[Ships::ship_espionage_probe])) {
                return false;
            }

            if ($this->_own_planet) {
                return false;
            }
        }

        if (
            $data['mission'] == Missions::DEPLOY
            && !$this->_own_planet
        ) {
            $this->showMessage(
                FormatLib::colorRed($this->langs->line('fl_deploy_only_your_planets'))
            );
        }

        if ($data['mission'] == Missions::STAY) {
            $is_buddy = $this->Fleet_Model->getBuddies(
                $this->planet['planet_user_id'],
                $this->_target_data['planet_user_id']
            ) >= 1;

            if ($this->_target_data['user_ally_id'] != $this->user['user_ally_id'] && !$is_buddy) {
                $this->showMessage(
                    FormatLib::colorRed($this->langs->line('fl_stay_not_on_enemy'))
                );
            }
        }

        if ($data['mission'] == Missions::COLONIZE) {
            if (!isset($fleet[Ships::ship_colony_ship])) {
                return false;
            }

            if ($this->_occupied_planet) {
                $this->showMessage(
                    FormatLib::colorRed($this->langs->line('fl_planet_populed'))
                );
            }
        }

        if ($data['mission'] == Missions::RECYCLE) {
            if ((count($target) <= 0)
                or ($target['planet_debris_metal'] == 0
                    && $target['planet_debris_crystal'] == 0
                    && time() > ($target['planet_invisible_start_time'] + DEBRIS_LIFE_TIME))) {
                return false;
            }
        }

        if ($data['mission'] == Missions::DESTROY) {
            if ($this->_own_planet
                or !$this->_occupied_planet
                or ($this->getTargetData()['type'] != PlanetTypes::MOON)
                or !isset($fleet[Ships::ship_deathstar])) {
                return false;
            }
        }

        if (
            $data['mission'] == Missions::EXPEDITION
            && !$this->_occupied_planet
        ) {
            $expeditions = $this->_fleets->getExpeditionsCount();
            $max_expeditions = FleetsLib::getMaxExpeditions(
                $this->_research->getCurrentResearch()->getResearchAstrophysics()
            );

            if ($max_expeditions <= 0) {
                $this->showMessage(
                    FormatLib::colorRed($this->langs->line('fl_expedition_tech_required'))
                );
            }

            if ($max_expeditions <= $expeditions) {
                $this->showMessage(
                    FormatLib::colorRed($this->langs->line('fl_expedition_fleets_limit'))
                );
            }
        } else {
            if (
                $data['mission'] != Missions::COLONIZE
                && !$this->_occupied_planet
            ) {
                return false;
            }
        }

        // add the fleet mission
        $this->_fleet_data['fleet_mission'] = $data['mission'];

        return true;
    }

    /**
     * Validate noob protection
     *
     * @return boolean
     */
    private function validateNoobProtection()
    {
        // skip if it's our own planet or it's an empty planet
        if (
            $this->_own_planet
            || !$this->_occupied_planet
        ) {
            return true;
        }

        if (!$this->userLibrary->isInactive($this->_target_data)) {
            $noob = Functions::loadLibrary('NoobsProtectionLib');

            $points = $noob->returnPoints(
                $this->user['user_id'],
                $this->_target_data['user_id']
            );

            $user_points = $points['user_points'];
            $target_points = $points['target_points'];

            $disallow_weak = [
                Missions::ATTACK, Missions::ACS, Missions::SPY, Missions::DESTROY,
            ];

            $disallow_strong = [
                Missions::ATTACK, Missions::ACS, Missions::STAY, Missions::SPY, Missions::DESTROY,
            ];

            if ($noob->isWeak(intval($user_points), intval($target_points))
                && in_array($this->_clean_input_data['mission'], $disallow_weak)) {
                $this->showMessage(
                    FormatLib::customColor($this->langs->line('fl_week_player'), 'lime')
                );
            }

            if ($noob->isStrong(intval($user_points), intval($target_points))
                && in_array($this->_clean_input_data['mission'], $disallow_strong)) {
                $this->showMessage(
                    FormatLib::colorRed($this->langs->line('fl_strong_player'))
                );
            }
        }

        return true;
    }

    /**
     * Validate the amount of fleets
     *
     * @return boolean
     */
    private function validateFleets()
    {
        $fleets = $this->_fleets->getFleetsCount();

        $max_fleets = FleetsLib::getMaxFleets(
            $this->_research->getCurrentResearch()->getResearchComputerTechnology(),
            $this->_premium->getCurrentPremium()->getPremiumOfficierAdmiral()
        );

        if ($max_fleets <= $fleets) {
            $this->showMessage(
                $this->langs->line('fl_no_slots')
            );
        }

        return true;
    }

    /**
     * Validate the resources
     *
     * @return boolean
     */
    private function validateResources()
    {
        $metal = $this->_clean_input_data['resource1'];
        $crystal = $this->_clean_input_data['resource2'];
        $deuterium = $this->_clean_input_data['resource3'];

        if ($metal + $crystal + $deuterium < 1
            && $this->_clean_input_data['mission'] == Missions::TRANSPORT) {
            $this->showMessage(
                FormatLib::customColor($this->langs->line('fl_empty_transport'), 'lime')
            );
        }

        $consumption = $this->getFleetData()['consumption'];
        $storage_needed = 0;

        // reduce cargo storage
        $this->_fleet_storage -= $consumption;

        $metal = max(0, $metal);
        $crystal = max(0, $crystal);
        $deuterium = max(0, $deuterium);

        if ($metal < 1) {
            $transport_metal = 0;
        } else {
            $transport_metal = $metal;
            $storage_needed += $transport_metal;
        }

        if ($crystal < 1) {
            $transport_crystal = 0;
        } else {
            $transport_crystal = $crystal;
            $storage_needed += $transport_crystal;
        }
        if ($deuterium < 1) {
            $transport_deuterium = 0;
        } else {
            $transport_deuterium = $deuterium;
            $storage_needed += $transport_deuterium;
        }

        $stock_metal = $this->planet['planet_metal'];
        $stock_crystal = $this->planet['planet_crystal'];
        $stock_deuterium = $this->planet['planet_deuterium'];
        $stock_deuterium -= $consumption;

        $stock_valid = false;

        if ($stock_metal >= $transport_metal) {
            if ($stock_crystal >= $transport_crystal) {
                if ($stock_deuterium >= $transport_deuterium) {
                    $stock_valid = true;
                }
            }
        }

        if (!$stock_valid) {
            $this->showMessage(
                FormatLib::colorRed($this->langs->line('fl_no_enought_deuterium') . FormatLib::prettyNumber($consumption))
            );
        }

        if ($storage_needed > $this->_fleet_storage) {
            $this->showMessage(
                FormatLib::colorRed($this->langs->line('fl_no_enought_cargo_capacity') . FormatLib::prettyNumber($storage_needed - $this->_fleet_storage))
            );
        }

        // add resources to fleet
        $this->_fleet_data['fleet_resource_metal'] = $transport_metal;
        $this->_fleet_data['fleet_resource_crystal'] = $transport_crystal;
        $this->_fleet_data['fleet_resource_deuterium'] = $transport_deuterium;
        $this->_fleet_data['fleet_fuel'] = $consumption;

        return true;
    }

    /**
     * Validate fleet times
     *
     * @return boolean
     */
    private function validateTime()
    {
        $fleet_data = $this->getFleetData();

        $duration = floor(FleetsLib::missionDuration(
            $fleet_data['speed'],
            $fleet_data['fleet_speed'],
            $fleet_data['distance'],
            Functions::fleetSpeedFactor()
        ));

        $base_time = time();
        $start_time = $duration + $base_time;
        $stay_duration = 0;
        $stay_time = 0;

        if ($this->_clean_input_data['mission'] == Missions::EXPEDITION) {
            $stay_duration = $this->_clean_input_data['expeditiontime'] * 3600;
            $stay_time = $start_time + $stay_duration;
        }

        if ($this->_clean_input_data['mission'] == Missions::STAY) {
            $stay_duration = $this->_clean_input_data['holdingtime'] * 3600;
            $stay_time = $start_time + $stay_duration;
        }

        $end_time = $stay_duration + (2 * $duration) + $base_time;

        if ($this->getTargetData()['group'] != 0) {
            $acs_start_time = $this->Fleet_Model->getAcsMaxTime(
                $this->getTargetData()['group']
            );

            if ($acs_start_time >= $start_time) {
                $end_time += $acs_start_time - $start_time;
                $start_time = $acs_start_time;
            } else {
                $this->Fleet_Model->updateAcsTimes(
                    $this->getTargetData()['group'],
                    $start_time,
                    ($start_time - $acs_start_time)
                );

                $end_time += $start_time - $acs_start_time;
            }
        }

        // add fleets times
        $this->_fleet_data['fleet_start_time'] = $start_time;
        $this->_fleet_data['fleet_end_time'] = $end_time;
        $this->_fleet_data['fleet_end_stay'] = $stay_time;

        return true;
    }

    /**
     * Get fleet data
     *
     * @return array
     */
    private function getFleetData()
    {
        return $_SESSION['fleet_data'];
    }

    /**
     * Get session set ships
     *
     * @return string
     */
    private function getSessionShips()
    {
        return unserialize(base64_decode(str_rot13($this->getFleetData()['fleetarray'])));
    }

    /**
     * Get the target data
     *
     * @return array
     */
    private function getTargetData()
    {
        return $_SESSION['fleet_data']['target'];
    }

    /**
     * Show message with some default fleet values
     *
     * @param type $message
     *
     * @return void
     */
    private function showMessage($message)
    {
        Functions::message(
            $message,
            self::REDIRECT_TARGET,
            3
        );
        exit();
    }

    /**
     * Send the fleet with the collected data
     *
     * @return void
     */
    private function sendFleet()
    {
        // create the new fleet and
        // remove from the planet the ships and resources
        $this->Fleet_Model->insertNewFleet(
            $this->_fleet_data,
            $this->planet,
            $this->_fleet_ships
        );
    }
}
