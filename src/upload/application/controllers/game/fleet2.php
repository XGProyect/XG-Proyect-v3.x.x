<?php
/**
 * Fleet2 Controller
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
use application\core\enumerators\PlanetTypesEnumerator as PlanetTypes;
use application\libraries\FleetsLib;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use application\libraries\OfficiersLib;
use application\libraries\premium\Premium;
use const JS_PATH;
use const MAX_GALAXY_IN_WORLD;
use const MAX_PLANET_IN_SYSTEM;
use const MAX_SYSTEM_IN_GALAXY;

/**
 * Fleet2 Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Fleet2 extends Controller
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
            'planet_types' => $this->buildPlanetTypesBlock(),
            'shortcuts' => $this->buildShortcutsBlock(),
            'colonies' => $this->buildColoniesBlock(),
            'acs' => $this->buildAcsBlock()
        ];

        // display the page
        parent::$page->display(
            $this->getTemplate()->set(
                'fleet/fleet2_view',
                array_merge(
                    $this->getLang(), $page, $this->setInputsData()
                )
            )
        );
    }
    
    /**
     * Build the fleet block
     * 
     * @return type
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

                    $this->_fleet_data['fleet_array'][$ship_id] = $amount_to_set;
                    $this->_fleet_data['fleet_list'] .= $ship_id . ',' . $amount_to_set . ';';
                    $this->_fleet_data['amount'] += $amount_to_set;
                    $this->_fleet_data['speed_all'][$ship_id] = FleetsLib::fleetMaxSpeed('', $ship_id, $this->_user);
                    
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
     * Build the planet type drop down
     * 
     * @return void
     */
    private function buildPlanetTypesBlock()
    {
        $planet_type = [
            'fl_planet' => PlanetTypes::planet,
            'fl_debris' => PlanetTypes::debris,
            'fl_moon' => PlanetTypes::moon
        ];
        
        $data = filter_input_array(INPUT_POST, [
            'planet_type' => [
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => 1, 'max_range' => 3]
            ]
        ]);
        
        $list_of_options = [];
        
        foreach ($planet_type as $label => $value) {
            
            $list_of_options[] = [
                'value' => $value,
                'selected' => ($value == $data['planet_type']) ? 'selected' : '',
                'title' => $this->getLang()[$label]
            ];
        }
        
        return $list_of_options;
    }
    
    /**
     * Build the shortcuts block
     * 
     * @return string
     */
    private function buildShortcutsBlock()
    {
        if (!OfficiersLib::isOfficierActive($this->_premium->getCurrentPremium()->getPremiumOfficierCommander())) {
        
            return '';
        }

        $shortcuts_string = $this->_user['user_fleet_shortcuts'];
        
        if ($shortcuts_string) {
            
            $shortcuts = explode(';', $shortcuts_string);
            $list_of_shortcuts = [];

            foreach ($shortcuts as $shortcut) {

                if ($shortcut != '') {

                    $item = explode(',', $shortcut);

                    $description = $item[0] . ' ' . FormatLib::prettyCoords($item[1], $item[2], $item[3]) . ' ';

                    switch ($item[4]) {
                        case 1:
                            $description .= $this->getLang()['fl_planet_shortcut'];
                            break;
                        case 2:
                            $description .= $this->getLang()['fl_debris_shortcut'];
                            break;
                        case 3:
                            $description .= $this->getLang()['fl_moon_shortcut'];
                            break;
                        default:
                            $description .= '';
                            break;
                    }

                    $list_of_shortcuts[] = [
                        'value' => $item[1] . ';' . $item[2] . ';' . $item[3] . ';' . $item[4],
                        'selected' => '',
                        'title' => $description
                    ];
                }
            }

            $shortcut_row = $this->getTemplate()->set(
                'fleet/fleet2_shortcuts_row',
                [
                    'select' => 'shortcuts',
                    'options' => $list_of_shortcuts
                ]
            );
        } else {

            $shortcut_row = $this->getTemplate()->set(
                'fleet/fleet2_shortcuts_noshortcuts_row',
                ['shorcut_message' => $this->getLang()['fl_no_shortcuts']]
            );
        }
        
        return $this->getTemplate()->set(
            'fleet/fleet2_shortcuts',
            array_merge($this->getLang(), ['shortcuts_rows' => $shortcut_row])
        );
    }
    
    /**
     * Build the colony shortcuts block
     * 
     * @return string
     */
    private function buildColoniesBlock()
    {
        $planets = $this->Fleet_Model->getAllPlanetsByUserId($this->_user['user_id']);
        $list_of_planets = [];
        
        if ($planets) {
            
            foreach($planets as $planet) {
                
                $list_of_planets[] = [
                    'value' => $planet['planet_galaxy'] . ';' . $planet['planet_system'] . ';' . $planet['planet_planet'] . ';' . $planet['planet_type'],
                    'selected' => '',
                    'title' => $planet['planet_name'] . ' ' . FormatLib::prettyCoords(
                        $planet['planet_galaxy'],
                        $planet['planet_system'],
                        $planet['planet_planet']
                    ) . ($planet['planet_type'] == PlanetTypes::moon ? ' (' . $this->getLang()['fcm_moon'] . ')' : '')
                ];
            }
            
            return $this->getTemplate()->set(
                'fleet/fleet2_shortcuts_row',
                [
                    'select' => 'colonies',
                    'options' => $list_of_planets
                ]
            );
        }

        return $this->getTemplate()->set(
            'fleet/fleet2_shortcuts_noshortcuts_row',
            ['shorcut_message' => $this->getLang()['fl_no_colony']]
        );
    }
    
    /**
     * Build the acs shortcuts block
     * 
     * @return string
     */
    private function buildAcsBlock()
    {
        $current_acs = $this->Fleet_Model->getOngoingAcs($this->_user['user_id']);
        $acs_fleets = [];
        
        if ($current_acs) {

            foreach ($current_acs as $acs) {

                $members = explode(',', $acs['acs_fleet_invited']);

                if (!in_array($this->_user['user_id'], $members)) {

                    continue;
                }

                $acs_fleets[] = [
                    'galaxy' => $acs['acs_fleet_galaxy'],
                    'system' => $acs['acs_fleet_system'],
                    'planet' => $acs['acs_fleet_planet'],
                    'planet_type' => $acs['acs_fleet_planet_type'],
                    'id' => $acs['acs_fleet_id'],
                    'name' => $acs['acs_fleet_name'],
                ];
            }
        }
        
        return $acs_fleets;
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
            'planet_type' => [
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => 1, 'max_range' => 3]
            ],
            'target_mission' => FILTER_VALIDATE_INT
        ]);

        if (is_null($data) or count($this->_fleet_data['speed_all']) <= 0) {
            
            FunctionsLib::redirect('game.php?page=fleet1');
        }
        
        // attach fleet data
        $_SESSION['fleet_data'] = [
            'fleet_speed' => min($this->_fleet_data['speed_all']),
            'fleetarray' => str_rot13(base64_encode(serialize($this->_fleet_data['fleet_array']))),
        ];

        return [
            'speedfactor' => FunctionsLib::fleetSpeedFactor(),
            'galaxy' => $this->_planet['planet_galaxy'],
            'system' => $this->_planet['planet_system'],
            'planet' => $this->_planet['planet_planet'],
            'planet_type' => $this->_planet['planet_type'],
            'galaxy_end' => $data['galaxy'] ?? $this->_planet['planet_galaxy'],
            'system_end' => $data['system'] ?? $this->_planet['planet_system'],
            'planet_end' => $data['planet'] ?? $this->_planet['planet_planet'],
            'target_mission' => $data['target_mission'] ?? 0
        ];
    }
}

/* end of fleet2.php */
