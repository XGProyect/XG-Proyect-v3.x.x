<?php
/**
 * Shipyard Controller
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\controllers\game;

use App\core\BaseController;
use App\core\enumerators\DefensesEnumerator as Defenses;
use App\core\enumerators\ShipsEnumerator as Ships;
use App\libraries\DevelopmentsLib;
use App\libraries\FormatLib;
use App\libraries\Formulas;
use App\libraries\Functions;
use Exception;

/**
 * Shipyard Class
 */
class Shipyard extends BaseController
{
    /**
     * The module ID
     *
     * @var int
     */
    const MODULE_ID = 7;

    /**
     * Count variable that we'll use to build the missile queue
     *
     * @var array
     */
    private $missiles = [
        Defenses::defense_anti_ballistic_missile => 0,
        Defenses::defense_interplanetary_missile => 0,
    ];

    /**
     * The amount of resources that will need to decrease
     *
     * @var array
     */
    private $resources_consumed = [
        'metal' => 0,
        'crystal' => 0,
        'deuterium' => 0,
    ];

    /**
     * List of currently available buildings
     *
     * @var array
     */
    private $allowed_items = [];

    /**
     * Store if we are currently building or not
     *
     * @var boolean
     */
    private $building_in_progress = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // load Model
        parent::loadModel('game/shipyard');

        // load Language
        parent::loadLang(['game/global', 'game/shipyard', 'game/defenses', 'game/ships']);

        // init a new building object with the current building queue
        $this->setUpShipyard();
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
     * Creates a new building object that will handle all the building
     * creation methods and actions
     *
     * @return void
     */
    private function setUpShipyard()
    {
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
        $items = filter_input(INPUT_POST, 'fmenge', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        if (!is_null($items) && $items !== false) {
            $total_items_to_build = 0;
            $shipyard_queue = '';

            // set resources before build
            $this->resources_consumed['metal'] = $this->planet['planet_metal'];
            $this->resources_consumed['crystal'] = $this->planet['planet_crystal'];
            $this->resources_consumed['deuterium'] = $this->planet['planet_deuterium'];

            foreach ($items as $item => $amount) {
                // avoid elements that not match the criteria
                if (!in_array($item, $this->allowed_items)
                    or ($amount <= 0)
                    or $this->isShieldDomeAvailable($item)) {
                    continue;
                }

                $item = (int) $item;
                $amount = (int) $amount;

                // calculate the max amount of elements that can be build
                $amount = $this->getMaxBuildableItems($item, $amount);

                // If after every validation, the amount of items to build, is more than 0
                if ($amount > 0) {
                    $resources_needed = $this->getItemNeededResourcesByAmount($item, $amount);
                    $this->resources_consumed['metal'] -= $resources_needed['metal'];
                    $this->resources_consumed['crystal'] -= $resources_needed['crystal'];
                    $this->resources_consumed['deuterium'] -= $resources_needed['deuterium'];
                    $shipyard_queue .= $item . ',' . $amount . ';';
                    $total_items_to_build += $amount;
                }
            }

            if ($total_items_to_build > 0) {
                $this->Shipyard_Model->insertItemsToBuild(
                    $this->resources_consumed,
                    $shipyard_queue,
                    $this->planet['planet_id']
                );
            }

            Functions::redirect('game.php?page=' . $this->getCurrentPage());
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
        $page = [];
        $page['message'] = $this->showShipyardUpgradeMessage();
        $page['list_of_items'] = $this->buildListOfItems();
        $page['build_button'] = $this->getBuildItemsButton();
        $page['building_list'] = $this->buildItemsQueue();

        // display the page
        parent::$page->display(
            $this->getTemplate()->set('shipyard/shipyard_table', $page)
        );
    }

    /**
     * Show a message that indicates that the shipyard is being upgraded
     *
     * @return string
     */
    private function showShipyardUpgradeMessage()
    {
        if ($this->building_in_progress) {
            return FormatLib::colorRed($this->langs->line('sy_building_shipyard'));
        }

        return '';
    }

    /**
     * Build the list of ships and defenses
     *
     * @return array
     */
    private function buildListOfItems()
    {
        $buildings_list = [];

        if (!is_null($this->allowed_items)) {
            foreach ($this->allowed_items as $item_id) {
                $buildings_list[] = $this->setListOfShipyardItem($item_id);
            }
        }

        return $buildings_list;
    }

    /**
     * Build each item block
     *
     * @param int $item_id Building ID
     *
     * @return array
     */
    private function setListOfShipyardItem($item_id)
    {
        $item_to_parse = [];

        $item_to_parse['dpath'] = DPATH;
        $item_to_parse['element'] = $item_id;
        $item_to_parse['element_name'] = $this->langs->language[$this->getObjects()->getObjects($item_id)];
        $item_to_parse['element_description'] = $this->getItemDescription($item_id);
        $item_to_parse['element_price'] = $this->getItemPriceWithFormat($item_id);
        $item_to_parse['building_time'] = $this->getItemTimeWithFormat($item_id);
        $item_to_parse['element_nbre'] = $this->getItemAmountWithFormat($item_id);
        $item_to_parse['add_element'] = $this->getItemInsertBlock($item_id);

        return $item_to_parse;
    }

    /**
     * Return the item short description
     *
     * @param integer $item_id
     * @return string
     */
    private function getItemDescription(int $item_id): string
    {
        if ($item_id == Defenses::defense_interplanetary_missile) {
            return strtr(
                $this->langs->language['descriptions'][$this->getObjects()->getObjects($item_id)],
                ['%s' => Formulas::missileRange($this->user['research_impulse_drive'])]
            );
        }

        return $this->langs->language['descriptions'][$this->getObjects()->getObjects($item_id)];
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
            $this->user,
            $this->planet,
            $item_id,
            $this->langs,
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
            $this->getItemTime($item_id),
            $this->langs->line('sy_time')
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
            $this->user,
            $this->planet,
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

        return ' (' . $this->langs->line('sy_available') . FormatLib::prettyNumber($amount) . ')';
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
        if (!$this->building_in_progress && !parent::$users->isOnVacations($this->user)
        ) {
            if ($this->isShieldDomeAvailable($item_id)) {
                return FormatLib::colorRed($this->langs->line('sy_protection_shield_only_one'));
            } else {
                $box_data = [];
                $box_data['item_id'] = $item_id;
                $box_data['tab_index'] = $item_id;

                return $this->getTemplate()->set(
                    'shipyard/shipyard_build_box',
                    $box_data
                );
            }
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
        return $this->planet[$this->getObjects()->getObjects()[$item_id]];
    }

    /**
     * Get build items button, if the shipyard is not being improved
     *
     * @return string
     */
    private function getBuildItemsButton()
    {
        if (!$this->building_in_progress && !parent::$users->isOnVacations($this->user)) {
            return $this->getTemplate()->set(
                'shipyard/shipyard_build_button',
                $this->langs->language
            );
        }

        return '';
    }

    /**
     * Build the block that will display the queue of items
     *
     * @return string
     */
    private function buildItemsQueue()
    {
        $queue = explode(';', $this->planet['planet_b_hangar_id']);
        $queue_time = 0;
        $item_time_per_type = '';
        $item_name_per_type = '';
        $item_amount_per_type = '';

        if (!is_null($queue[0]) && !empty($queue[0])) {
            foreach ($queue as $item_data) {
                if (!empty($item_data)) {
                    $item_values = explode(',', $item_data);

                    // $item_values[0] = item ID
                    $item_time = $this->getItemTime($item_values[0]);

                    $item_time_per_type .= $item_time . ',';
                    $item_name_per_type .= '\'' . html_entity_decode($this->langs->language[$this->getObjects()->getObjects($item_values[0])], ENT_COMPAT, "utf-8") . '\',';
                    $item_amount_per_type .= $item_values[1] . ',';

                    // $item_values[1] = amount
                    $queue_time += $item_time * $item_values[1];
                }
            }

            $block = $this->langs->language;
            $block['a'] = $item_amount_per_type;
            $block['b'] = $item_name_per_type;
            $block['c'] = $item_time_per_type;
            $block['b_hangar_id_plus'] = $this->planet['planet_b_hangar'];
            $block['current_page'] = $this->getCurrentPage();
            $block['pretty_time_b_hangar'] = FormatLib::prettyTime($queue_time - $this->planet['planet_b_hangar']);

            return $this->getTemplate()->set('shipyard/shipyard_script', $block);
        }
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
            $get_value = filter_input(INPUT_GET, 'page');
            $allowed_pages = ['shipyard', 'defense'];

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
            'shipyard' => [
                Ships::ship_small_cargo_ship,
                Ships::ship_big_cargo_ship,
                Ships::ship_light_fighter,
                Ships::ship_heavy_fighter,
                Ships::ship_cruiser,
                Ships::ship_battleship,
                Ships::ship_colony_ship,
                Ships::ship_recycler,
                Ships::ship_espionage_probe,
                Ships::ship_bomber,
                Ships::ship_solar_satellite,
                Ships::ship_destroyer,
                Ships::ship_deathstar,
                Ships::ship_battlecruiser,
            ],
            'defense' => [
                Defenses::defense_rocket_launcher,
                Defenses::defense_light_laser,
                Defenses::defense_heavy_laser,
                Defenses::defense_gauss_cannon,
                Defenses::defense_ion_cannon,
                Defenses::defense_plasma_turret,
                Defenses::defense_small_shield_dome,
                Defenses::defense_large_shield_dome,
                Defenses::defense_anti_ballistic_missile,
                Defenses::defense_interplanetary_missile,
            ],
        ];

        $this->allowed_items = array_filter($allowed_buildings[$this->getCurrentPage()], function ($value) {
            return DevelopmentsLib::isDevelopmentAllowed(
                $this->user,
                $this->planet,
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
        if ($this->planet[$this->getObjects()->getObjects(21)] == 0) {
            Functions::message($this->langs->line('sy_shipyard_required'), '', '', true);
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
        $this->building_in_progress = false;

        // unless ...
        if ($this->planet['planet_b_building_id'] != 0) {
            $queue = explode(';', $this->planet['planet_b_building_id']);
            $not_allowed = [14, 15, 21];

            foreach ($queue as $building_data) {
                $building = explode(',', $building_data);

                // $building[0] = Building ID
                if (in_array($building[0], $not_allowed)) {
                    $this->building_in_progress = true;
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
        // set construction limit based on resources
        $max_by_resource = $this->getMaxBuildableItemsByResource($item_id);

        // set construction limit based system config
        $max_by_system = $this->getMaxBuildableItemsBySystemLimit();

        // set the construction limit for shields
        if (in_array($item_id, [Defenses::defense_small_shield_dome, Defenses::defense_large_shield_dome])) {
            $max_shields = $this->getShieldDomeItemLimit($item_id, $amount_requested);

            if ($amount_requested > $max_shields) {
                $amount_requested = $max_shields;
            }
        }

        // set the construction limit for missiles
        if (in_array($item_id, [Defenses::defense_anti_ballistic_missile, Defenses::defense_interplanetary_missile])) {
            $max_missiles = $this->getMissilesItemLimit($item_id, $amount_requested);

            if ($amount_requested > $max_missiles) {
                $amount_requested = $max_missiles;
            }
        }

        //validations
        if ($amount_requested > $max_by_resource) {
            $amount_requested = $max_by_resource;
        }

        if ($amount_requested > $max_by_system) {
            $amount_requested = $max_by_system;
        }

        // last verification for missiles,
        // I'm sure I can do all this process better
        if (in_array($item_id, [Defenses::defense_anti_ballistic_missile, Defenses::defense_interplanetary_missile])) {
            // keep track of the amount of missiles
            $this->missiles[$item_id] += $amount_requested;
        }

        return $amount_requested;
    }

    /**
     * Get max amount of buildable items based on the planet current resources
     *
     * @param int $item_id Item ID
     *
     * @return int
     */
    private function getMaxBuildableItemsByResource($item_id)
    {
        $buildable = [];
        $price_metal = $this->getObjects()->getPrice($item_id, 'metal');
        $price_crystal = $this->getObjects()->getPrice($item_id, 'crystal');
        $price_deuterium = $this->getObjects()->getPrice($item_id, 'deuterium');

        if ($price_metal != 0) {
            $buildable['metal'] = floor($this->resources_consumed['metal'] / $price_metal);
        }

        if ($price_crystal != 0) {
            $buildable['crystal'] = floor($this->resources_consumed['crystal'] / $price_crystal);
        }

        if ($price_deuterium != 0) {
            $buildable['deuterium'] = floor($this->resources_consumed['deuterium'] / $price_deuterium);
        }

        return max(min($buildable), 0);
    }

    /**
     * Get max amount of buildable items based on the system configuration
     *
     * @param int $amount_requested Amount of items requested
     *
     * @return int
     */
    private function getMaxBuildableItemsBySystemLimit()
    {
        return MAX_FLEET_OR_DEFS_PER_ROW;
    }

    /**
     * Return the max amount of buildable shield domes
     *
     * @param int $item_id Item ID
     *
     * @return int
     */
    private function getShieldDomeItemLimit($item_id)
    {
        // set construction limit for shield dome
        $shields_ids = [Defenses::defense_small_shield_dome, Defenses::defense_large_shield_dome];

        if (in_array($item_id, $shields_ids)) {
            if (!$this->isShieldDomeAvailable($item_id)) {
                return 1;
            }

            return 0;
        }
    }

    /**
     * Return the max amount of buildable missiles
     *
     * @param int $item_id Item ID
     *
     * @return int
     */
    private function getMissilesItemLimit($item_id, $amount_requested)
    {
        // calculate missile amount
        $this->calculateMissilesAmount();

        // start applying formulas
        $silo_size = $this->planet[$this->getObjects()->getObjects(44)] * 10;
        $taken_space = $this->missiles[Defenses::defense_anti_ballistic_missile] + ($this->missiles[Defenses::defense_interplanetary_missile] * 2);
        $max_amount = $silo_size - $taken_space;
        $amount = 0;

        if ($item_id == Defenses::defense_anti_ballistic_missile) {
            $amount = $max_amount;
        }

        if ($item_id == Defenses::defense_interplanetary_missile) {
            $amount = floor($max_amount / 2);
        }

        if ($amount_requested > $amount) {
            $amount_requested = $amount;
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
            'metal' => ($this->getObjects()->getPrice($item_id, 'metal') * $amount),
            'crystal' => ($this->getObjects()->getPrice($item_id, 'crystal') * $amount),
            'deuterium' => ($this->getObjects()->getPrice($item_id, 'deuterium') * $amount),
        ];
    }

    /**
     * Check if any of the shield domes are currently available in the planet
     *
     * @param int $item_id Item ID
     * @return boolean
     */
    private function isShieldDomeAvailable($item_id)
    {
        if (in_array($item_id, [Defenses::defense_small_shield_dome, Defenses::defense_large_shield_dome])) {
            // check if something is already built
            if ($this->planet[$this->getObjects()->getObjects($item_id)] >= 1) {
                return true;
            }

            // check if something is being built
            $in_queue = strpos($this->planet['planet_b_hangar_id'], $item_id . ',');

            if ($in_queue !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate missiles amount considering the planet current storage and queue
     *
     * @return array
     */
    private function calculateMissilesAmount()
    {
        // get the amount of missiles stored in the planet
        $planet_missiles = [
            Defenses::defense_anti_ballistic_missile => $this->planet[$this->getObjects()->getObjects(Defenses::defense_anti_ballistic_missile)],
            Defenses::defense_interplanetary_missile => $this->planet[$this->getObjects()->getObjects(Defenses::defense_interplanetary_missile)],
        ];

        // get the amount of missiles in the current queue
        $current_queue = $this->processQueueToArray();
        $queue_missiles = [
            Defenses::defense_anti_ballistic_missile => 0,
            Defenses::defense_interplanetary_missile => 0,
        ];

        foreach ($current_queue as $item => $amount) {
            if ($item == Defenses::defense_anti_ballistic_missile
                or $item == Defenses::defense_interplanetary_missile) {
                $queue_missiles[$item] += $amount;
            }
        }

        // add the amount of missiles stored in the planet, and the amount of
        // missiles in the current queue, and finally the amount of missiles in
        //  the queue that's being developed.
        $this->missiles[Defenses::defense_anti_ballistic_missile] += $planet_missiles[Defenses::defense_anti_ballistic_missile] + $queue_missiles[Defenses::defense_anti_ballistic_missile];
        $this->missiles[Defenses::defense_interplanetary_missile] += $planet_missiles[Defenses::defense_interplanetary_missile] + $queue_missiles[Defenses::defense_interplanetary_missile];
    }

    /**
     * Convert the queue from string to array
     *
     * @return array
     */
    private function processQueueToArray()
    {
        $queue = explode(';', $this->planet['planet_b_hangar_id']);
        $array_queue = [];

        if (!empty($queue[0])) {
            foreach ($queue as $item_data) {
                if (!empty($item_data[0])) {
                    $item = explode(',', $item_data);

                    if (!isset($array_queue[$item[0]])) {
                        $array_queue[$item[0]] = 0;
                    }

                    $array_queue[$item[0]] += $item[1];
                }
            }
        }

        return $array_queue;
    }
}
