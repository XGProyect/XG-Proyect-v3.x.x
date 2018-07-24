<?php
/**
 * Fleet1 Controller
 *
 * PHP Version 5.5+
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
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

/**
 * Fleet1 Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Fleet1 extends Controller
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
     * @var int
     */
    private $_ship_count = 0;
    
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
            'fleets' => $this->_fleets->getFleetsCount(),
            'max_fleets' => FleetsLib::getMaxFleets(
                $this->_research->getCurrentResearch()->getResearchComputerTechnology(),
                $this->_premium->getCurrentPremium()->getPremiumOfficierAdmiral()
            ),
            'expeditions' => $this->_fleets->getExpeditionsCount(),
            'max_expeditions' => FleetsLib::getMaxExpeditions(
                $this->_research->getCurrentResearch()->getResearchAstrophysics()
            ),
            'no_slot' => $this->buildNoSlotBlock(),
            'list_of_ships' => $this->buildListOfShips(),
            'none_max_selector' => $this->buildActionsBlock(),
            'no_ships' => $this->buildNoShipsBlock(),
            'continue_button' => $this->buildContinueBlock()
        ];

        // display the page
        parent::$page->display(
            $this->getTemplate()->set(
                'fleet/fleet1_view',
                array_merge(
                    $this->getLang(), $page, $this->setInputsData()
                )
            )
        );
    }
    
    /**
     * Build the not slot block
     * 
     * @return type
     */
    private function buildNoSlotBlock()
    {
        if (!$this->checkAvailableSlot()) {
            
            return $this->getTemplate()->set('fleet/fleet1_noslots_row', $this->getLang());
        }
        
        return null;
    }
    
    /**
     * Build the list of ships
     * 
     * @return array
     */
    private function buildListOfShips()
    {
        $objects = parent::$objects->getObjects();
        $price = parent::$objects->getPrice();

        $ships = $this->Fleet_Model->getShipsByPlanetId($this->_planet['planet_id']);

        $list_of_ships = [];
        
        if ($ships != null) {
            
            foreach($ships as $ship_name => $ship_amount) {

                if ($ship_amount != 0) {

                    $this->_ship_count += $ship_amount;
                    
                    $ship_id = array_search($ship_name, $objects);

                    $list_of_ships[] = [
                        'ship_name' => $this->buildShipName($ship_name, $ship_id),
                        'ship_amount' => $this->buildShipAmount($ship_amount),
                        'max_ships_link' => $this->buildMaxShipsLink($ship_id) ?? '-',
                        'ships_input' => $this->buildShipsInput($ship_id) ?? '-',
                        'ship_id' => $ship_id,
                        'max_ships' => $ship_amount,
                        'consumption' => FleetsLib::shipConsumption($ship_id, $this->_user),
                        'speed' => FleetsLib::fleetMaxSpeed('', $ship_id, $this->_user),
                        'capacity' => $price[$ship_id]['capacity'] ?? 0
                    ];
                }
            }
        }
        
        return $list_of_ships;
    }
    
    /**
     * Build the ship name block
     * 
     * @param string $ship_name Ship Name
     * @param int    $ship_id   Ship ID
     * 
     * @return type
     */
    private function buildShipName($ship_name, $ship_id)
    {
        $title = $this->getLang()['fl_speed_title'] . FleetsLib::fleetMaxSpeed('', $ship_id, $this->_user);
        
        return FunctionsLib::setUrl('', $title, $this->getLang()[$ship_name]);
    }
    
    /**
     * Build the ship amount block
     * 
     * @param int $ship_amount Ship Amount
     * 
     * @return string
     */
    private function buildShipAmount($ship_amount)
    {
        return FormatLib::prettyNumber($ship_amount);
    }
    
    /**
     * Build the ship max link
     * 
     * @param int $ship_id Ship ID
     * 
     * @return string
     */
    private function buildMaxShipsLink($ship_id)
    {   
        if ($ship_id == 212) {
            
            return null;
        }
        
        return FunctionsLib::setUrl('#', '', $this->getLang()['fl_max'], 'onclick="javascript:maxShip(\'ship' . $ship_id . '\');"');
    }
    
    /**
     * Build the ship input field
     * 
     * @param int $ship_id Ship ID
     * 
     * @return string
     */
    private function buildShipsInput($ship_id)
    {
        if ($ship_id == 212) {
            
            return null;
        }
        
        return '<input name="ship' . $ship_id . '" size="10" value="0" onfocus="javascript:if(this.value == \'0\') this.value=\'\';" onblur="javascript:if(this.value == \'0\') this.value=\'\';"/>';
    }
    
    /**
     * Build the actions block
     * 
     * @return string
     */
    private function buildActionsBlock()
    {
        if ($this->_ship_count > 0
            && $this->checkAvailableSlot()) {
            
            return $this->getTemplate()->set('fleet/fleet_selectors', $this->getLang());
        }
        
        return '';
    }
    
    /**
     * Build the no ships block
     * 
     * @return string
     */
    private function buildNoShipsBlock()
    {
        if ($this->_ship_count <= 0) {
            
            return $this->getTemplate()->set('fleet/fleet1_noships_row', $this->getLang());
        }
        
        return '';
    }
    
    /**
     * Build the continue button block
     * 
     * @return string
     */
    private function buildContinueBlock()
    {
        if ($this->_ship_count > 0
            && $this->checkAvailableSlot()) {
            
            return $this->getTemplate()->set('fleet/fleet1_button', $this->getLang());
        }
        
        return '';
    }
    
    /**
     * Check if we can send the fleet
     * 
     * @return boolean
     */
    private function checkAvailableSlot()
    {
        return (FleetsLib::getMaxFleets(
            $this->_research->getCurrentResearch()->getResearchComputerTechnology(),
            $this->_premium->getCurrentPremium()->getPremiumOfficierAdmiral()
        ) > $this->_fleets->getFleetsCount());
    }
    
    /**
     * Set inputs data
     * 
     * @return array
     */
    private function setInputsData()
    {
        $data = filter_input_array(INPUT_GET, [
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
            'target_mission' => FILTER_VALIDATE_INT
        ]);
        
        // always reset, and define as array
        $_SESSION['fleet_data'] = [];
        
        return [
            'galaxy' => $data['galaxy'] ?? $this->_planet['planet_galaxy'],
            'system' => $data['system'] ?? $this->_planet['planet_system'],
            'planet' => $data['planet'] ?? $this->_planet['planet_planet'],
            'planettype' => $data['planettype'] ?? $this->_planet['planet_type'],
            'target_mission' => $data['target_mission'] ?? 0
        ];
    }
}

/* end of fleet1.php */
