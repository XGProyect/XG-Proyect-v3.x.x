<?php
/**
 * Shipyard Controller
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
use application\libraries\DevelopmentsLib;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use Exception;

/**
 * Shipyard Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Shipyard extends Controller
{
    const MODULE_ID = 7;
    
    /**
     * List of currently available buildings
     * 
     * @var array
     */
    private $_allowed_items = [];

    /**
     * Store if we are currently building or not
     * 
     * @var boolean
     */
    private $_building_in_progress = false;
    
    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // load Model
        parent::loadModel('game/shipyard');
        
        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        $this->_user    = $this->getUserData();
        $this->_planet  = $this->getPlanetData();
        
        // init a new building object with the current building queue
        $this->setUpShipyard();
        
        // time to do something
        $this->runAction();
        
        // build the page
        $this->buildPage();
    }
    
    /**
     * Creates a new building object that will handle all the building
     * creation methods and actions
     * 
     * @return void
     */
    private function setUpShipyard()
    {/*
        $this->_building = new Building(
            $this->_planet,
            $this->_user,
            $this->getObjects()
        );*/
        
        // validate and display
        $this->showShipyardRequiredMessage();
        
        // set a list of allowed items
        $this->setAllowedItems();
        
        // check if any facility is working
        $this->isAnyFacilityWorking();
    }
    
    /**
     * Run an action
     * 
     * @return void
     */
    private function runAction()
    {
        $items  = filter_input(INPUT_POST, 'fmenge', FILTER_DEFAULT , FILTER_REQUIRE_ARRAY);

        if(!is_null($items) && $items !== false) {

            $total_items_to_build   = 0;
            $shipyard_queue         = '';
            $resources_consumed     = [
                'metal' => 0,
                'crystal' => 0,
                'deuterium' => 0,
            ];
            
            foreach ($items as $item => $amount) {
                
                // avoid elements that not match the criteria
                if (!in_array($item, $this->_allowed_items) 
                    or $amount <= 0) {

                    continue;
                }

                $item   = (int)$item;
                $amount = (int)$amount;
                
                // calculate the max amount of elements that can be build
                $amount = $this->getMaxBuildableItems($item, $amount);

                // If after every validation, the amount of items to build, is more than 0
                if ($amount > 0) {
                    $resources_needed                    = $this->getItemNeededResourcesByAmount($item, $amount);
                    $resources_consumed['metal']        -= $resources_needed['metal'];
                    $resources_consumed['crystal']      -= $resources_needed['crystal'];
                    $resources_consumed['deuterium']    -= $resources_needed['deuterium'];
                    $shipyard_queue                     .= $item . ',' . $amount . ';';

                    $total_items_to_build += $amount;   
                }
            }
            
            if ($total_items_to_build > 0) {
                
                $this->Shipyard_Model->insertItemsToBuild(
                    $resources_consumed,
                    $shipyard_queue,
                    $this->_planet['planet_id']
                );
            }
            
            FunctionsLib::redirect('game.php?page=' .  $this->getCurrentPage());
        }
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
        $page                   = [];
        $page['message']        = $this->showShipyardUpgradeMessage();
        $page['items_rows']     = $this->buildListOfItems();
        $page['build_button']   = $this->getBuildItemsButton();
        $page['building_list']  = $this->buildItemsQueue();
        
        // display the page
        parent::$page->display(
            parent::$page->get('shipyard/shipyard_table')->parse($page)
        );
    }
    
    /**
     * Show a message that indicates that the shipyard is being upgraded
     * 
     * @return string
     */
    private function showShipyardUpgradeMessage()
    {
        if ($this->_building_in_progress) {
            
            return FormatLib::colorRed($this->getLang()['bd_building_shipyard']);
        }
        
        return '';
    }
    
    /**
     * Build the list of ships and defenses
     * 
     * @return string
     */
    private function buildListOfItems()
    {
        $buildings_list = '';
        
        if (!is_null($this->_allowed_items)) {
            
            foreach ($this->_allowed_items as $item_id) {
                
                $buildings_list .= parent::$page->get('shipyard/shipyard_table_row')->parse(
                    $this->setListOfShipyardItem($item_id)
                );
            }
        }
        
        return $buildings_list;
    }
    
    /**
     * Build each building block
     * 
     * @param int $item_id Building ID
     * 
     * @return array
     */
    private function setListOfShipyardItem($item_id)
    {
        $item_to_parse  = [];
        
        $item_to_parse['dpath']                 = DPATH;
        $item_to_parse['element']               = $item_id;
        $item_to_parse['element_name']          = $this->getLang()['tech'][$item_id];
        $item_to_parse['element_description']   = $this->getLang()['res']['descriptions'][$item_id];
        $item_to_parse['element_price']         = $this->getItemPriceWithFormat($item_id);
        $item_to_parse['building_time']         = $this->getItemTimeWithFormat($item_id);
        $item_to_parse['element_nbre']          = $this->getItemAmountWithFormat($item_id);
        $item_to_parse['add_element']           = $this->getItemInsertBlock($item_id);
                
        return $item_to_parse;
    }
    
    /**
     * Expects a item ID (ship or defense) to calculate and format the price
     * 
     * @param int $item_id Building ID
     * 
     * @return string
     */
    private function getItemPriceWithFormat($item_id)
    {        
        return DevelopmentsLib::formatedDevelopmentPrice(
            $this->_user,
            $this->_planet,
            $item_id,
            false
        );
    }
    
    /**
     * Expects a item ID (ship or defense) to calculate and format the time
     * 
     * @param int $item_id Item ID
     * 
     * @return string
     */
    private function getItemTimeWithFormat($item_id)
    {
        return DevelopmentsLib::formatedDevelopmentTime(
            $this->getItemTime($item_id)
        );
    }
    
    
    /**
     * Expects a item ID (ship or defense) to calculate the construction time
     * 
     * @param int $item_id Item ID
     * 
     * @return int
     */
    private function getItemTime($item_id)
    {
        return DevelopmentsLib::developmentTime(
            $this->_user,
            $this->_planet,
            $item_id
        );
    }
    
    /**
     * Expects an item ID to calculate and format the item current amount
     * 
     * @param int $item_id Item ID
     * 
     * @return string
     */
    private function getItemAmountWithFormat($item_id)
    {
        $amount = $this->getItemAmount($item_id);
        
        if ($amount == 0) {
            
            return '';
        }
        
        return ' (' . $this->getLang()['bd_available'] . FormatLib::prettyNumber($amount) . ')';
    }
    
    /**
     * Insert the item box that allows new item inserts
     * 
     * @param int $item_id Item ID
     * 
     * @return string
     */
    private function getItemInsertBlock($item_id)
    {
        if (!$this->_building_in_progress && !parent::$users->isOnVacations($this->_user)) {
            
            $box_data               = [];
            $box_data['item_id']    = $item_id;
            $box_data['tab_index']  = $item_id;

            return parent::$page->parseTemplate(
                parent::$page->getTemplate('shipyard/shipyard_build_box'),
                $box_data
            );
        }
        
        return '';
    }
    
    /**
     * Expects an item ID to calculate the item current amount
     * 
     * @param int $item_id Item ID
     * 
     * @return int
     */
    private function getItemAmount($item_id)
    {
        return $this->_planet[$this->getObjects()->getObjects()[$item_id]];
    }
    
    /**
     * Get build items button, if the shipyard is not being improved
     * 
     * @return string
     */
    private function getBuildItemsButton()
    {
        if (!$this->_building_in_progress && !parent::$users->isOnVacations($this->_user)) {
            return parent::$page->get('shipyard/shipyard_build_button')->parse(
                $this->getLang()
            );   
        }
        
        return '';
    }
    
    /**
     * 
     * @return type
     */
    private function buildItemsQueue()
    {
        $queue                  = explode(';', $this->_planet['planet_b_hangar_id']);
        $queue_time             = 0;
        $item_time_per_type     = '';
        $item_name_per_type     = '';
        $item_amount_per_type   = '';
        
        if (!is_null($queue)) {
            
            foreach ($queue as $item_data) {
                
                if(!empty($item_data)) {
                    
                    $item_values    = explode(',', $item_data);
                    
                    // $item_values[0] = item ID
                    $item_time      = $this->getItemTime($item_values[0]);
                    
                    $item_time_per_type     .= $item_time . ',';
                    $item_name_per_type     .= '\'' . html_entity_decode($this->getLang()['tech'][$item_values[0]], ENT_COMPAT, "utf-8") . '\',';
                    $item_amount_per_type   .= $item_values[1] . ',';
                    
                    // $item_values[1] = amount
                    $queue_time    += $item_time * $item_values[1];
                }
            }
        }

        $block                          = $this->getLang();
        $block['a']                     = $item_amount_per_type;
        $block['b']                     = $item_name_per_type;
        $block['c']                     = $item_time_per_type;
        $block['b_hangar_id_plus']      = $this->_planet['planet_b_hangar'];
        $block['current_page']          = $this->getCurrentPage();
        $block['pretty_time_b_hangar']  = FormatLib::prettyTime($queue_time - $this->_planet['planet_b_hangar']);

        return parent::$page->get('shipyard/shipyard_script')->parse($block);
    }
    
    /**
     * Determine the current page and validate it
     * 
     * @return array
     * 
     * @throws Exception
     */
    private function getCurrentPage()
    {
        try {
            $get_value      = filter_input(INPUT_GET, 'page');
            $allowed_pages  = ['shipyard', 'defense'];

            if (in_array($get_value, $allowed_pages)) {

                return $get_value;
            }
            
            throw new Exception('"shipyard" and "defense" are the valid options');

        } catch (Exception $e) {
            
            die('Caught exception: ' . $e->getMessage() . "\n");
        }
    }
    
    /**
     * Get an array with an allowed set of items for the current page,
     * filtering by page and available technologies
     * 
     * @return array
     */
    private function setAllowedItems()
    {
        $allowed_buildings = [
            'shipyard'  => [202, 203, 204, 205, 206, 207, 208, 209, 210, 211, 212, 213, 214, 215],
            'defense'   => [401, 402, 403, 404, 405, 406, 407, 408, 502, 503]
        ];

        $this->_allowed_items = array_filter($allowed_buildings[$this->getCurrentPage()], function($value) {
            return DevelopmentsLib::isDevelopmentAllowed(
                $this->_user,
                $this->_planet,
                $value
            );
        });
    }
    
    /**
     * Display a message that indicating that the shipyard building is required
     * 
     * @return void
     */
    private function showShipyardRequiredMessage()
    {
        if ($this->_planet[$this->getObjects()->getObjects(21)] == 0) {

            FunctionsLib::message($this->getLang()['bd_shipyard_required'], '', '', true);
        }
    }
    
    /**
     * Check if the robot factory, nanobots factory or hangar is being built
     * 
     * @return void
     */
    private function isAnyFacilityWorking()
    {
        // by default is false ...
        $this->_building_in_progress = false;
        
        // unless ...
        if ($this->_planet['planet_b_building_id'] != 0) {
            
            $queue          = explode(';', $this->_planet['planet_b_building_id']);
            $not_allowed    = [14, 15, 21];
            
            foreach ($queue as $building_data) {
                
                $building   = explode (',', $building_data);
                
                // $building[0] = Building ID
                if (in_array($building[0], $not_allowed)) {

                    $this->_building_in_progress = true;
                    break; // any of the "banned" buildings is being built
                }
            }
        }
    }
    
    /**
     * Get the maximum amount of items that can be build
     * 
     * @param int $item_id          Item ID
     * @param int $amount_requested Amount of items requested
     *
     * @return int The max amount of buildable items
     */
    private function getMaxBuildableItems($item_id, $amount_requested)
    {
        $buildable          = [];
        $price_metal        = $this->getObjects()->getPrice($item_id, 'metal');
        $price_crystal      = $this->getObjects()->getPrice($item_id, 'crystal');
        $price_deuterium    = $this->getObjects()->getPrice($item_id, 'deuterium');
        $price_energy       = $this->getObjects()->getPrice($item_id, 'energy');
        
        if ($price_metal != 0) {
            $buildable['metal']     = floor($this->_planet['planet_metal'] / $price_metal);
        }

        if ($price_crystal != 0) {
            $buildable['crystal']   = floor($this->_planet['planet_crystal'] / $price_crystal);
        }

        if ($price_deuterium != 0) {
            $buildable['deuterium'] = floor($this->_planet['planet_deuterium'] / $price_deuterium);
        }

        if ($price_energy != 0) {
            $buildable['energy']    = floor($this->_planet['planet_energy_max'] / $price_energy);
        }

        $max_buildable_by_resource  = max(min($buildable), 0);
        
        if ($amount_requested > $max_buildable_by_resource) {
            
            $amount_requested = $max_buildable_by_resource;
        }
        
        if ($amount_requested > MAX_FLEET_OR_DEFS_PER_ROW) {

            $amount_requested = MAX_FLEET_OR_DEFS_PER_ROW;
        }

        return $amount_requested;
    }

    /**
     * Get the maximum amount of items that can be build
     * 
     * @param int $item_id Item ID
     *
     * @return array
     */
    private function getItemNeededResourcesByAmount($item_id, $amount)
    {
        return [
            'metal'     => ($this->getObjects()->getPrice($item_id, 'metal') * $amount),
            'crystal'   => ($this->getObjects()->getPrice($item_id, 'crystal') * $amount),
            'deuterium' => ($this->getObjects()->getPrice($item_id, 'deuterium') * $amount)
        ];
    }
}

/* end of shipyard.php */
