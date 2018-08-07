<?php
/**
 * Fleet3 Controller
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
namespace application\controllers\game;

use application\core\Controller;
use application\core\enumerators\MissionsEnumerator as Missions;
use application\core\enumerators\PlanetTypesEnumerator as PlanetTypes;
use application\libraries\FleetsLib;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use application\libraries\research\Researches;
use const JS_PATH;
use const MAX_GALAXY_IN_WORLD;
use const MAX_PLANET_IN_SYSTEM;
use const MAX_SYSTEM_IN_GALAXY;

/**
 * Fleet3 Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Fleet3 extends Controller
{

    const MODULE_ID = 8;

    /**
     *
     * @var array
     */
    private $_user;

    /**
     *
     * @var array
     */
    private $_planet;

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
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // load Model
        parent::loadModel('game/fleet');
        
        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        // set data
        $this->_user = $this->getUserData();
        
        // set planet data
        $this->_planet = $this->getPlanetData();

        // init a new fleets object
        $this->setUpFleets();

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
            [$this->_user],
            $this->_user['user_id']
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
            'stay_block' => $this->buildStayBlock()
        ];

        // display the page
        parent::$page->display(
            $this->getTemplate()->set(
                'fleet/fleet3_view',
                array_merge(
                    $this->getLang(), $page, $inputs_data
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

        $ships = $this->Fleet_Model->getShipsByPlanetId($this->_planet['planet_id']);

        $list_of_ships = [];
        $selected_fleet = $this->getSessionShips();
        
        if ($ships != null) {
            
            foreach($ships as $ship_name => $ship_amount) {

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
                        'consumption' => FleetsLib::shipConsumption($ship_id, $this->_user),
                        'speed' => FleetsLib::fleetMaxSpeed('', $ship_id, $this->_user),
                        'capacity' => $price[$ship_id]['capacity'] ?? 0,
                        'ship' => $amount_to_set
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
            $this->_planet['planet_galaxy'],
            $this->_planet['planet_system'],
            $this->_planet['planet_planet']
        ) . ' - ' . $this->getLang()['planet_type'][$this->_planet['planet_type']];
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
                    'mission' => $this->getLang()['type_mission'][$mission],
                    'expedition_message' => $mission == Missions::expedition ? $this->getLang()['fl_expedition_alert_message'] : '',
                    'id' => $mission == Missions::expedition ? ' ' : 'inpuT_' . $mission,
                    'checked' => $mission == $this->_current_mission ? ' checked="checked"' : ''
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
        
        if (in_array(Missions::expedition, $this->_allowed_missions)) {
            
            $stay_type = 'expeditiontime';
            
            for($i = 1; $i <= $max_exp_time; $i++) {
                
                $options[] = [
                    'value' => $i,
                    'selected' => $i == 1 ? ' selected' : ''
                ];
            }
        }
        
        if (in_array(Missions::stay, $this->_allowed_missions)) {
            
            $stay_type = 'holdingtime';
            
            foreach ($hours as $hour) {
                
                $options[] = [
                    'value' => $hour,
                    'selected' => $hour == 1 ? ' selected' : ''
                ];
            }
        }
        
        if (count($options) > 0) {
            
            return $this->getTemplate()->set(
                'fleet/fleet3_stay_row',
                array_merge(
                    $this->getLang(),
                    [
                        'stay_type' => $stay_type,
                        'options' => $options
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
            202 => [Missions::attack, Missions::acs, Missions::transport, Missions::deploy, Missions::stay, Missions::expedition],
            203 => [Missions::attack, Missions::acs, Missions::transport, Missions::deploy, Missions::stay, Missions::expedition],
            204 => [Missions::attack, Missions::acs, Missions::transport, Missions::deploy, Missions::stay, Missions::expedition],
            205 => [Missions::attack, Missions::acs, Missions::transport, Missions::deploy, Missions::stay, Missions::expedition],
            206 => [Missions::attack, Missions::acs, Missions::transport, Missions::deploy, Missions::stay, Missions::expedition],
            207 => [Missions::attack, Missions::acs, Missions::transport, Missions::deploy, Missions::stay, Missions::expedition],
            208 => [Missions::colonize, Missions::expedition],
            209 => [Missions::recycle, Missions::expedition],
            210 => [Missions::attack, Missions::acs, Missions::transport, Missions::deploy, Missions::stay, Missions::spy, Missions::expedition],
            211 => [Missions::attack, Missions::acs, Missions::transport, Missions::deploy, Missions::stay, Missions::expedition],
            212 => [],
            213 => [Missions::attack, Missions::acs, Missions::transport, Missions::deploy, Missions::stay, Missions::expedition],
            214 => [Missions::attack, Missions::acs, Missions::transport, Missions::deploy, Missions::stay, Missions::destroy, Missions::expedition],
            215 => [Missions::attack, Missions::acs, Missions::transport, Missions::deploy, Missions::stay, Missions::expedition],
        ];

        $mission_rules = [
            PlanetTypes::planet => [
                'own' => [
                    Missions::transport,
                    Missions::deploy,
                    Missions::stay
                ],
                'other' => [
                    Missions::attack,
                    Missions::acs,
                    Missions::transport,
                    Missions::stay,
                    Missions::spy,
                    Missions::colonize
                ]
            ],
            PlanetTypes::debris => [
                'own' => [
                    Missions::deploy,
                    Missions::recycle
                ],
                'other' => [
                    Missions::deploy,
                    Missions::recycle
                ]
            ],
            PlanetTypes::moon => [
                'own' => [
                    Missions::transport,
                    Missions::deploy,
                    Missions::stay
                ],
                'other' => [
                    Missions::attack,
                    Missions::acs,
                    Missions::transport,
                    Missions::stay,
                    Missions::spy,
                    Missions::destroy
                ]
            ]
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
            
            if ($selected_planet['planet_user_id'] == $this->_user['user_id']) {

                $action_type = 'own';
            } 
        }

        if ($_SESSION['fleet_data']['target']['planet'] == (MAX_PLANET_IN_SYSTEM + 1)) {
            
            $possible_missions = [Missions::expedition];
        } else {
            
            $possible_missions = $mission_rules[$_SESSION['fleet_data']['target']['type']][$action_type];

            if (!$acs && in_array(Missions::acs, $possible_missions)) {

                unset($possible_missions[array_search(Missions::acs, $possible_missions)]);
            }
        }

        if (count($ships) > 0) {
        
            foreach($ships as $ship_id => $amount) {
                
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
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => 1, 'max_range' => MAX_GALAXY_IN_WORLD]
            ],
            'system' => [
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => 1, 'max_range' => MAX_SYSTEM_IN_GALAXY]
            ],
            'planet' => [
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => 1, 'max_range' => (MAX_PLANET_IN_SYSTEM + 1)]
            ],
            'planettype' => [
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => 1, 'max_range' => 3]
            ],
            'speed' => [
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => 1, 'max_range' => 10]
            ],
            'target_mission' => FILTER_VALIDATE_INT,
            'fleet_group' => FILTER_VALIDATE_INT,
            'acs_target' => FILTER_SANITIZE_STRING
        ]);

        if (is_null($data)) {
            
            FunctionsLib::redirect('game.php?page=fleet1');
        }
        
        $this->_current_mission = $data['target_mission'];
        
        $distance = FleetsLib::targetDistance(
            $this->_planet['planet_galaxy'],
            $data['galaxy'],
            $this->_planet['planet_system'],
            $data['system'],
            $this->_planet['planet_planet'],
            $data['planet']
        );

        $fleet = $this->getSessionShips();
        $Speed_factor = FunctionsLib::fleetSpeedFactor();
        $fleet_speed = FleetsLib::fleetMaxSpeed($fleet, 0, $this->_user);

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
            $this->_user
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
                'acs_target' => $data['acs_target']
            ],
            'distance' => $distance,
            'consumption' => $consumption
        ];

        return [
            'metal' => floor($this->_planet['planet_metal']),
            'crystal' => floor($this->_planet['planet_crystal']),
            'deuterium' => floor($this->_planet['planet_deuterium']),
            'this_galaxy' => $this->_planet['planet_galaxy'],
            'this_system' => $this->_planet['planet_system'],
            'this_planet' => $this->_planet['planet_planet'],
            'this_planet_type' => $this->_planet['planet_type'],
            'galaxy_end' => $data['galaxy'] ?? $this->_planet['planet_galaxy'],
            'system_end' => $data['system'] ?? $this->_planet['planet_system'],
            'planet_end' => $data['planet'] ?? $this->_planet['planet_planet'],
            'planet_type_end' => $data['planettype'] ?? $this->_planet['planet_type'],
            'speed' => $data['speed'] ?? 10,
            'speedfactor' => FunctionsLib::fleetSpeedFactor()
        ];
    }
    
    /**
     * Get session set ships
     * 
     * @return array
     */
    private function getSessionShips()
    {
        return unserialize(base64_decode(str_rot13($_SESSION['fleet_data']['fleetarray'])));
    }
}

/* end of fleet3.php */
