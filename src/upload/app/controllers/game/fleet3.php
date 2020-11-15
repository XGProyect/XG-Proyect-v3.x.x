<?php
/**
 * Fleet3 Controller
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
use App\libraries\research\Researches;

/**
 * Fleet3 Class
 */
class Fleet3 extends BaseController
{
    /**
     *
     * @var int
     */
    const MODULE_ID = 8;

    /**
     *
     * @var string
     */
    const REDIRECT_TARGET = 'game.php?page=fleet1';

    /**
     *
     * @var \Fleets
     */
    private $_research = null;

    /**
     *
     * @var int
     */
    private $_current_mission = 0;

    /**
     *
     * @var array
     */
    private $_allowed_missions = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // load Model
        parent::loadModel('game/fleet');

        // load Language
        parent::loadLang(['game/global', 'game/missions', 'game/fleet']);

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
        $this->_research = new Researches(
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
        $inputs_data = $this->setInputsData();

        /**
         * Parse the items
         */
        $page = [
            'js_path' => JS_PATH,
            'fleet_block' => $this->buildFleetBlock(),
            'title' => $this->buildTitleBlock(),
            'mission_selector' => $this->buildMissionBlock(),
            'stay_block' => $this->buildStayBlock(),
        ];

        // display the page
        parent::$page->display(
            $this->getTemplate()->set(
                'fleet/fleet3_view',
                array_merge(
                    $this->langs->language,
                    $page,
                    $inputs_data
                )
            )
        );
    }

    /**
     * Build the fleet inputs block
     *
     * @return array
     */
    private function buildFleetBlock()
    {
        $objects = parent::$objects->getObjects();
        $price = parent::$objects->getPrice();

        $ships = $this->Fleet_Model->getShipsByPlanetId($this->planet['planet_id']);

        $list_of_ships = [];
        $selected_fleet = $this->getSessionShips();

        if ($ships != null) {
            foreach ($ships as $ship_name => $ship_amount) {
                if ($ship_amount != 0) {
                    $ship_id = array_search($ship_name, $objects);

                    if (!isset($selected_fleet[$ship_id])
                        or $selected_fleet[$ship_id] == 0) {
                        continue;
                    }

                    $amount_to_set = $selected_fleet[$ship_id];

                    if ($amount_to_set > $ship_amount) {
                        $amount_to_set = $ship_amount;
                    }

                    $list_of_ships[] = [
                        'ship_id' => $ship_id,
                        'consumption' => FleetsLib::shipConsumption($ship_id, $this->user),
                        'speed' => FleetsLib::fleetMaxSpeed('', $ship_id, $this->user),
                        'capacity' => FleetsLib::getMaxStorage(
                            $price[$ship_id]['capacity'],
                            $this->_research->getCurrentResearch()->getResearchHyperspaceTechnology()
                        ),
                        'ship' => $amount_to_set,
                    ];
                }
            }
        }

        return $list_of_ships;
    }

    /**
     * Build the title block
     *
     * @return string
     */
    private function buildTitleBlock()
    {
        return FormatLib::prettyCoords(
            $this->planet['planet_galaxy'],
            $this->planet['planet_system'],
            $this->planet['planet_planet']
        ) . ' - ' . $this->langs->language['planet_type'][$this->planet['planet_type']];
    }

    /**
     * Build the missions block
     *
     * @return string
     */
    private function buildMissionBlock()
    {
        $list_of_missions = $this->getAllowedMissions();
        $mission_selector = [];

        if (count($list_of_missions)) {
            foreach ($list_of_missions as $mission) {
                $mission_selector[] = [
                    'value' => $mission,
                    'mission' => $this->langs->language['type_mission'][$mission],
                    'expedition_message' => $mission == Missions::EXPEDITION ? $this->langs->line('fl_expedition_alert_message') : '',
                    'id' => $mission == Missions::EXPEDITION ? ' ' : 'inpuT_' . $mission,
                    'checked' => $mission == $this->_current_mission ? ' checked="checked"' : '',
                ];
            }
        }

        return $mission_selector;
    }

    /**
     * Build the stay time block
     *
     * @return string
     */
    private function buildStayBlock()
    {
        // by rule, expedition time is based on the astrophysics level, relation 1:1 level:hour
        $max_exp_time = $this->_research->getCurrentResearch()->getResearchAstrophysics();
        $hours = [0, 1, 2, 4, 8, 16, 32];
        $options = [];
        $stay_type = '';

        if (in_array(Missions::EXPEDITION, $this->_allowed_missions)) {
            $stay_type = 'expeditiontime';

            for ($i = 1; $i <= $max_exp_time; $i++) {
                $options[] = [
                    'value' => $i,
                    'selected' => $i == 1 ? ' selected' : '',
                ];
            }
        }

        if (in_array(Missions::STAY, $this->_allowed_missions)) {
            $stay_type = 'holdingtime';

            foreach ($hours as $hour) {
                $options[] = [
                    'value' => $hour,
                    'selected' => $hour == 1 ? ' selected' : '',
                ];
            }
        }

        if (count($options) > 0) {
            return $this->getTemplate()->set(
                'fleet/fleet3_stay_row',
                array_merge(
                    $this->langs->language,
                    [
                        'stay_type' => $stay_type,
                        'options' => $options,
                    ]
                )
            );
        }

        return '';
    }

    /**
     * Get allowed missions per ship and option
     *
     * @return array
     */
    private function getAllowedMissions()
    {
        /**
         * rules
         */
        $ships_rules = [
            Ships::ship_small_cargo_ship => [
                Missions::ATTACK, Missions::ACS, Missions::TRANSPORT, Missions::DEPLOY, Missions::STAY, Missions::EXPEDITION,
            ],
            Ships::ship_big_cargo_ship => [
                Missions::ATTACK, Missions::ACS, Missions::TRANSPORT, Missions::DEPLOY, Missions::STAY, Missions::EXPEDITION,
            ],
            Ships::ship_light_fighter => [
                Missions::ATTACK, Missions::ACS, Missions::TRANSPORT, Missions::DEPLOY, Missions::STAY, Missions::EXPEDITION,
            ],
            Ships::ship_heavy_fighter => [
                Missions::ATTACK, Missions::ACS, Missions::TRANSPORT, Missions::DEPLOY, Missions::STAY, Missions::EXPEDITION,
            ],
            Ships::ship_cruiser => [
                Missions::ATTACK, Missions::ACS, Missions::TRANSPORT, Missions::DEPLOY, Missions::STAY, Missions::EXPEDITION,
            ],
            Ships::ship_battleship => [
                Missions::ATTACK, Missions::ACS, Missions::TRANSPORT, Missions::DEPLOY, Missions::STAY, Missions::EXPEDITION,
            ],
            Ships::ship_colony_ship => [
                Missions::DEPLOY, Missions::COLONIZE, Missions::EXPEDITION,
            ],
            Ships::ship_recycler => [
                Missions::DEPLOY, Missions::RECYCLE, Missions::EXPEDITION,
            ],
            Ships::ship_espionage_probe => [
                Missions::ATTACK, Missions::ACS, Missions::DEPLOY, Missions::STAY, Missions::SPY, Missions::EXPEDITION,
            ],
            Ships::ship_bomber => [
                Missions::ATTACK, Missions::ACS, Missions::TRANSPORT, Missions::DEPLOY, Missions::STAY, Missions::EXPEDITION,
            ],
            Ships::ship_solar_satellite => [],
            Ships::ship_destroyer => [
                Missions::ATTACK, Missions::ACS, Missions::TRANSPORT, Missions::DEPLOY, Missions::STAY, Missions::EXPEDITION,
            ],
            Ships::ship_deathstar => [
                Missions::ATTACK, Missions::ACS, Missions::TRANSPORT, Missions::DEPLOY, Missions::STAY, Missions::DESTROY, Missions::EXPEDITION,
            ],
            Ships::ship_battlecruiser => [
                Missions::ATTACK, Missions::ACS, Missions::TRANSPORT, Missions::DEPLOY, Missions::STAY, Missions::EXPEDITION,
            ],
        ];

        $mission_rules = [
            PlanetTypes::PLANET => [
                'own' => [
                    Missions::TRANSPORT,
                    Missions::DEPLOY,
                ],
                'other' => [
                    Missions::ATTACK,
                    Missions::ACS,
                    Missions::TRANSPORT,
                    Missions::STAY,
                    Missions::SPY,
                    Missions::COLONIZE,
                ],
            ],
            PlanetTypes::DEBRIS => [
                'own' => [
                    Missions::RECYCLE,
                ],
                'other' => [
                    Missions::RECYCLE,
                ],
            ],
            PlanetTypes::MOON => [
                'own' => [
                    Missions::TRANSPORT,
                    Missions::DEPLOY,
                ],
                'other' => [
                    Missions::ATTACK,
                    Missions::ACS,
                    Missions::TRANSPORT,
                    Missions::STAY,
                    Missions::SPY,
                    Missions::DESTROY,
                ],
            ],
        ];

        /**
         * data
         */
        $ships = $this->getSessionShips();
        $acs = $this->Fleet_Model->getAcsCount(
            $_SESSION['fleet_data']['target']['group']
        );

        $missions = [];
        $action_type = 'other';
        $ocuppied = false;

        $selected_planet = $this->Fleet_Model->getPlanetOwnerByCoords(
            $_SESSION['fleet_data']['target']['galaxy'],
            $_SESSION['fleet_data']['target']['system'],
            $_SESSION['fleet_data']['target']['planet'],
            $_SESSION['fleet_data']['target']['type']
        );

        if ($selected_planet) {
            $ocuppied = true;

            if ($selected_planet['planet_user_id'] == $this->user['user_id']) {
                $action_type = 'own';
            }
        }

        if ($_SESSION['fleet_data']['target']['planet'] == (MAX_PLANET_IN_SYSTEM + 1)) {
            $possible_missions = [Missions::EXPEDITION];
        } else {
            $possible_missions = $mission_rules[$_SESSION['fleet_data']['target']['type']][$action_type];

            if (!$acs && in_array(Missions::ACS, $possible_missions)) {
                unset($possible_missions[array_search(Missions::ACS, $possible_missions)]);
            }

            if ($selected_planet && !$this->isFriendly($selected_planet) && in_array(Missions::STAY, $possible_missions)) {
                unset($possible_missions[array_search(Missions::STAY, $possible_missions)]);
            }

            if ($ocuppied && in_array(Missions::COLONIZE, $possible_missions)) {
                unset($possible_missions[array_search(Missions::COLONIZE, $possible_missions)]);
            }
        }

        if (count($ships) > 0) {
            foreach ($ships as $ship_id => $amount) {
                if ($amount > 0) {
                    $missions[] = array_intersect(
                        $ships_rules[$ship_id],
                        $possible_missions
                    );
                }
            }
        }

        // merge for each ship, but made them unique
        $missions_set = array_unique(array_merge(...$missions));

        // sort by value from lower to higher
        sort($missions_set);

        if (count($missions_set) <= 0) {
            Functions::redirect(self::REDIRECT_TARGET);
        }

        $this->_allowed_missions = $missions_set;

        return $missions_set;
    }

    /**
     * Set inputs data
     *
     * @return array
     */
    private function setInputsData()
    {
        $data = filter_input_array(INPUT_POST, [
            'galaxy' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 1, 'max_range' => MAX_GALAXY_IN_WORLD],
            ],
            'system' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 1, 'max_range' => MAX_SYSTEM_IN_GALAXY],
            ],
            'planet' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 1, 'max_range' => (MAX_PLANET_IN_SYSTEM + 1)],
            ],
            'planettype' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 1, 'max_range' => 3],
            ],
            'speed' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 1, 'max_range' => 10],
            ],
            'target_mission' => FILTER_VALIDATE_INT,
            'fleet_group' => FILTER_VALIDATE_INT,
            'acs_target' => FILTER_SANITIZE_STRING,
        ]);

        // remove values that din't pass the validation
        $data = array_diff($data, [null, false]);

        if (is_null($data) or count($data) != 8 or $this->isCurrentPlanet($data)) {
            Functions::redirect(self::REDIRECT_TARGET);
        }

        $this->_current_mission = $data['target_mission'];

        $distance = FleetsLib::targetDistance(
            $this->planet['planet_galaxy'],
            $data['galaxy'],
            $this->planet['planet_system'],
            $data['system'],
            $this->planet['planet_planet'],
            $data['planet']
        );

        $fleet = $this->getSessionShips();
        $Speed_factor = Functions::fleetSpeedFactor();
        $fleet_speed = FleetsLib::fleetMaxSpeed($fleet, 0, $this->user);

        $consumption = FleetsLib::fleetConsumption(
            $fleet,
            $Speed_factor,
            FleetsLib::missionDuration(
                $data['speed'],
                min($fleet_speed),
                $distance,
                $Speed_factor
            ),
            $distance,
            $this->user
        );

        // attach speed and target data
        $_SESSION['fleet_data'] += [
            'speed' => $data['speed'],
            'target' => [
                'galaxy' => $data['galaxy'],
                'system' => $data['system'],
                'planet' => $data['planet'],
                'type' => $data['planettype'],
                'group' => $data['fleet_group'],
                'acs_target' => $data['acs_target'],
            ],
            'distance' => $distance,
            'consumption' => $consumption,
        ];

        return [
            'this_metal' => floor($this->planet['planet_metal']),
            'this_crystal' => floor($this->planet['planet_crystal']),
            'this_deuterium' => floor($this->planet['planet_deuterium']),
            'this_galaxy' => $this->planet['planet_galaxy'],
            'this_system' => $this->planet['planet_system'],
            'this_planet' => $this->planet['planet_planet'],
            'this_planet_type' => $this->planet['planet_type'],
            'galaxy_end' => $data['galaxy'] ?? $this->planet['planet_galaxy'],
            'system_end' => $data['system'] ?? $this->planet['planet_system'],
            'planet_end' => $data['planet'] ?? $this->planet['planet_planet'],
            'planet_type_end' => $data['planettype'] ?? $this->planet['planet_type'],
            'speed' => $data['speed'] ?? 10,
            'speedfactor' => Functions::fleetSpeedFactor(),
        ];
    }

    /**
     * Get the ships that were set in the session
     *
     * @return array
     */
    private function getSessionShips(): array
    {
        if (isset($_SESSION['fleet_data']['fleetarray'])) {
            return unserialize(base64_decode(str_rot13($_SESSION['fleet_data']['fleetarray'])));
        }

        Functions::redirect(self::REDIRECT_TARGET);
    }

    /**
     * Check if it is a friendly target
     *
     * @param array $target_planet
     * @return boolean
     */
    private function isFriendly(array $target_planet): bool
    {
        $is_buddy = $this->Fleet_Model->getBuddies(
            $this->user['user_id'],
            $target_planet['planet_user_id']
        ) >= 1;

        if (!$is_buddy
            && (
                ($target_planet['user_ally_id'] == 0 && $this->user['user_ally_id'] == 0)
                or ($target_planet['user_ally_id'] != $this->user['user_ally_id'])
            )
        ) {
            return false;
        }

        return true;
    }

    /**
     * Check if it is the current planet
     *
     * @param array $target
     * @return boolean
     */
    private function isCurrentPlanet(array $target): bool
    {
        return Functions::isCurrentPlanet(
            $this->planet,
            [
                'planet_galaxy' => $target['galaxy'],
                'planet_system' => $target['system'],
                'planet_planet' => $target['planet'],
                'planet_type' => $target['planettype'],
            ]
        );
    }
}

/* end of fleet3.php */
