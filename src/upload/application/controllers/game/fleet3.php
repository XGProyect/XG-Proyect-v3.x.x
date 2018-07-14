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
use application\libraries\fleets\Fleets;
use application\libraries\FleetsLib;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use application\libraries\premium\Premium;
use application\libraries\research\Researches;
use const JS_PATH;
use const MAX_GALAXY_IN_WORLD;
use const MAX_PLANET_IN_SYSTEM;
use const MAX_SYSTEM_IN_GALAXY;
use const PLANETS;

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
    private $_fleets = null;
    
    /**
     *
     * @var \Fleets
     */
    private $_research = null;
    
    /**
     *
     * @var \Premium
     */
    private $_premium = null;
    
    /**
     *
     * @var array
     */
    private $_fleet_data = [
        'fleet_array' => [],
        'fleet_list' => '',
        'amount' => 0,
        'speed_all' => []
    ];
    
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
        $this->_fleets = new Fleets(
            $this->Fleet_Model->getAllFleetsByUserId($this->_user['user_id']),
            $this->_user['user_id']
        );
        
        $this->_research = new Researches(
            [$this->_user],
            $this->_user['user_id']
        );
        
        $this->_premium = new Premium(
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
        /**
         * Parse the items
         */
        $page = [
            'js_path' => JS_PATH,
            'fleet_block' => $this->buildFleetBlock(),
            'title' => $this->buildTitleBlock(),
        ];

        // display the page
        parent::$page->display(
            $this->getTemplate()->set(
                'fleet/fleet3_view',
                array_merge(
                    $this->getLang(), $page, $this->setInputsData()
                )
            )
        );

        #####################################################################################################
        // SOME DEFAULT VALUES
        #####################################################################################################
        // ARRAYS
        $exp_values = [1, 2, 3, 4, 5];
        $hold_values = [0, 1, 2, 4, 8, 16, 32];

        // OTHER VALUES
        $fleet_acs = (int) $_POST['fleet_group'];
        $YourPlanet = false;
        $UsedPlanet = false;
        $MissionSelector = '';
        $available_ships = $this->getAvailableShips($_POST);
        $missiontype = [];

        // QUERYS
        $select = $this->_db->queryFetch(
            "SELECT `planet_user_id`
            FROM `" . PLANETS . "`
            WHERE `planet_galaxy` = '" . $galaxy . "'
            AND `planet_system` = '" . $system . "'
            AND `planet_planet` = '" . $planet . "'
            AND `planet_type` = '" . $planettype . "';"
        );

        if ($select) {

            if ($select['planet_user_id'] == $this->current_user['user_id']) {

                $YourPlanet = true;
                $UsedPlanet = true;
            } else {

                $UsedPlanet = true;
            }
        }

        if ($planettype == 2) {

            if ($available_ships['ship209'] >= 1) {

                $missiontype[8] = $this->getLang()['type_mission'][8];
            } else {

                $missiontype = [];
            }
        } elseif ($planettype == 1 or $planettype == 3) {

            if ($available_ships['ship208'] >= 1 && !$UsedPlanet) {

                $missiontype[7] = $this->getLang()['type_mission'][7];
            } elseif ($available_ships['ship210'] >= 1 && !$YourPlanet) {

                $missiontype[6] = $this->getLang()['type_mission'][6];
            }

            if ($available_ships['ship202'] >= 1 or
                $available_ships['ship203'] >= 1 or
                $available_ships['ship204'] >= 1 or
                $available_ships['ship205'] >= 1 or
                $available_ships['ship206'] >= 1 or
                $available_ships['ship207'] >= 1 or
                $available_ships['ship210'] >= 1 or
                $available_ships['ship211'] >= 1 or
                $available_ships['ship213'] >= 1 or
                $available_ships['ship214'] >= 1 or
                $available_ships['ship215'] >= 1) {

                if (!$YourPlanet) {

                    $missiontype[1] = $this->getLang()['type_mission'][1];
                }

                $missiontype[3] = $this->getLang()['type_mission'][3];
                $missiontype[5] = $this->getLang()['type_mission'][5];
            }
        } elseif ($available_ships['ship209'] >= 1 or $available_ships['ship208']) {
            $missiontype[3] = $this->getLang()['type_mission'][3];
        }
        
        if ($YourPlanet) {

            $missiontype[4] = $this->getLang()['type_mission'][4];
        }

        if ($planettype == 3 || $planettype == 1 && ($fleet_acs > 0) && $UsedPlanet) {

            if ($this->Fleet_Model->acsExists($fleet_acs, $galaxy, $system, $planet, $planettype)) {

                $missiontype[2] = $this->getLang()['type_mission'][2];
            }
        }

        if ($planettype == 3 && $available_ships['ship214'] >= 1 && !$YourPlanet && $UsedPlanet) {

            $missiontype[9] = $this->getLang()['type_mission'][9];
        }

        $fleetarray = unserialize(base64_decode(str_rot13($_POST['usedfleet'])));
        $mission = $_POST['target_mission'];
        $SpeedFactor = FunctionsLib::fleetSpeedFactor();
        $AllFleetSpeed = FleetsLib::fleetMaxSpeed($fleetarray, 0, $this->current_user);
        $GenFleetSpeed = $_POST['speed'];
        $MaxFleetSpeed = min($AllFleetSpeed);
        $distance = FleetsLib::targetDistance(
                $_POST['thisgalaxy'], $galaxy, $_POST['thissystem'], $system, $_POST['thisplanet'], $planet
        );

        $duration = FleetsLib::missionDuration($GenFleetSpeed, $MaxFleetSpeed, $distance, $SpeedFactor);

        $consumption = FleetsLib::fleetConsumption(
                $fleetarray, $SpeedFactor, $duration, $distance, $this->current_user
        );

        #####################################################################################################
        // INPUTS DATA
        #####################################################################################################
        $parse['consumption'] = $consumption;
        $parse['distance'] = $distance;
        $parse['fleet_group'] = $_POST['fleet_group'];
        $parse['acs_target_mr'] = $_POST['acs_target_mr'];

        #####################################################################################################
        // MISSION TYPES
        #####################################################################################################
        if (count($missiontype) > 0) {

            if ($planet == 16) {

                $parse_mission['value'] = 15;
                $parse_mission['mission'] = $this->getLang()['type_mission'][15];
                $parse_mission['expedition_message'] = $this->getLang()['fl_expedition_alert_message'];
                $parse_mission['id'] = ' ';
                $parse_mission['checked'] = ' checked="checked"';

                $MissionSelector .= parent::$page->parseTemplate(fleet/fleet3_mission_row, $parse_mission);
            } else {

                $i = 0;

                foreach ($missiontype as $a => $b) {

                    $parse_mission['value'] = $a;
                    $parse_mission['mission'] = $b;
                    $parse_mission['expedition_message'] = '';
                    $parse_mission['id'] = ' id="inpuT_' . $i . '" ';
                    $parse_mission['checked'] = (($mission == $a) ? ' checked="checked"' : '');

                    $i++;

                    $MissionSelector .= parent::$page->parseTemplate(fleet/fleet3_mission_row, $parse_mission);
                }
            }
        } else {
            FunctionsLib::redirect('game.php?page=fleet1');
        }

        #####################################################################################################
        // STAY / EXPEDITION BLOCKS
        #####################################################################################################
        $stay_row['options'] = '';
        $StayBlock = '';

        if ($planet == 16) {

            $stay_row['stay_type'] = 'expeditiontime';

            foreach ($exp_values as $value) {
                $stay['value'] = $value;
                $stay['selected'] = '';
                $stay['title'] = $value;

                $stay_row['options'] .= parent::$page->parseTemplate(fleet/fleet_options, $stay);
            }

            $StayBlock = parent::$page->parseTemplate(fleet/fleet3_stay_row, array_merge($stay_row, $this->getLang()));
        } elseif (isset($missiontype[5])) {

            $stay_row['stay_type'] = 'holdingtime';

            foreach ($hold_values as $value) {

                $stay['value'] = $value;
                $stay['selected'] = (($value == 1) ? ' selected' : '');
                $stay['title'] = $value;

                $stay_row['options'] .= parent::$page->parseTemplate(fleet/fleet_options, $stay);
            }

            $StayBlock = parent::$page->parseTemplate(fleet/fleet3_stay_row, array_merge($stay_row, $this->getLang()));
        }

        $parse['missionselector'] = $MissionSelector;
        $parse['stayblock'] = $StayBlock;
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
        $selected_fleet = filter_input_array(INPUT_POST);
        
        if ($ships != null) {
            
            foreach($ships as $ship_name => $ship_amount) {

                if ($ship_amount != 0) {
                    
                    $ship_id = array_search($ship_name, $objects);
                    
                    if (!isset($selected_fleet['ship' . $ship_id])
                        or $selected_fleet['ship' . $ship_id] == 0) {
                        
                        continue;
                    }
                    
                    $amount_to_set = $selected_fleet['ship' . $ship_id];
                    
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
     * getAvailableShips
     *
     * @param array $post_data Post Data
     *
     * @return array
     */
    private function getAvailableShips($post_data)
    {
        if (is_array($post_data)) {

            $ships = array();
            $resource = parent::$objects->getObjects();

            foreach ($resource as $ship => $amount) {

                if (strpos($amount, 'ship') !== false) {

                    if (isset($post_data['ship' . $ship])) {
                        $ships['ship' . $ship] = $post_data['ship' . $ship];
                    } else {
                        $ships['ship' . $ship] = 0;
                    }
                }
            }

            return $ships;
        }

        return array();
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
                'options'   => ['min_range' => 1, 'max_range' => MAX_PLANET_IN_SYSTEM]
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
        ]);

        if (is_null($data)) {
            
            FunctionsLib::redirect('game.php?page=fleet1');
        }
        
        // attach speed and target data
        $_SESSION['fleet_data'] += [
            'speed' => $data['speed'],
            'target' => [
                'galaxy' => $data['galaxy'],
                'system' => $data['system'],
                'planet' => $data['planet'],
                'type' => $data['planettype']
            ]
        ];

        return [
            'metal' => floor($this->_planet['planet_metal']),
            'crystal' => floor($this->_planet['planet_crystal']),
            'deuterium' => floor($this->_planet['planet_deuterium']),
            'consumption' => '',
            'distance' => '',
            'this_galaxy' => $this->_planet['planet_galaxy'],
            'this_system' => $this->_planet['planet_system'],
            'this_planet' => $this->_planet['planet_planet'],
            'this_planet_type' => $this->_planet['planet_type'],
            'galaxy_end' => $data['galaxy'] ?? $this->_planet['planet_galaxy'],
            'system_end' => $data['system'] ?? $this->_planet['planet_system'],
            'planet_end' => $data['planet'] ?? $this->_planet['planet_planet'],
            'planet_type_end' => $data['planettype'] ?? $this->_planet['planet_type'],
            'speed' => $data['speed'] ?? 10,
            'speedfactor' => FunctionsLib::fleetSpeedFactor(),
            'fleet_group' => '',
            'acs_target_mr' => ''
        ];
    }
}

/* end of fleet3.php */
