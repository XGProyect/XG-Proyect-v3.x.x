<?php

namespace App\Http\Controllers\Game;

use App\Core\BaseController;
use App\Core\Enumerators\ShipsEnumerator as Ships;
use App\Helpers\UrlHelper;
use App\Libraries\FleetsLib;
use App\Libraries\FormatLib;
use App\Libraries\Functions;
use App\Libraries\Game\Fleets;
use App\Libraries\Premium\Premium;
use App\Libraries\Research\Researches;
use App\Libraries\Users;
use App\Models\Game\Fleet;

class Fleet1Controller extends BaseController
{
    public const MODULE_ID = 8;

    private ?Fleets $_fleets = null;
    private ?Researches $_research = null;
    private ?Premium $_premium = null;
    private int $_ship_count = 0;
    private Fleet $fleetModel;

    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Users::checkSession();

        // load Language
        parent::loadLang(['game/ships', 'game/fleet']);

        $this->fleetModel = new Fleet();

        // init a new fleets object
        $this->setUpFleets();
    }

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
            $this->fleetModel->getAllFleetsByUserId($this->user['user_id']),
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

    private function buildPage(): void
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
            'continue_button' => $this->buildContinueBlock(),
        ];

        // display the page
        $this->page->display(
            $this->template->set(
                'fleet/fleet1_view',
                array_merge(
                    $this->langs->language,
                    $page,
                    $this->setInputsData()
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
            return $this->template->set('fleet/fleet1_noslots_row', $this->langs->language);
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
        $objects = $this->objects->getObjects();
        $price = $this->objects->getPrice();

        $ships = $this->fleetModel->getShipsByPlanetId($this->planet['planet_id']);

        $list_of_ships = [];

        if ($ships != null) {
            foreach ($ships as $ship_name => $ship_amount) {
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
                        'consumption' => FleetsLib::shipConsumption($ship_id, $this->user),
                        'speed' => FleetsLib::fleetMaxSpeed(null, $ship_id, $this->user),
                        'capacity' => FleetsLib::getMaxStorage(
                            $price[$ship_id]['capacity'],
                            $this->_research->getCurrentResearch()->getResearchHyperspaceTechnology()
                        ),
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
        $title = $this->langs->line('fl_speed_title') . FleetsLib::fleetMaxSpeed(null, $ship_id, $this->user);

        return UrlHelper::setUrl('', $this->langs->line($ship_name), $title);
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
        if ($ship_id == Ships::ship_solar_satellite) {
            return null;
        }

        return UrlHelper::setUrl('#', $this->langs->line('fl_max'), '', 'onclick="javascript:maxShip(\'ship' . $ship_id . '\');"');
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
        if ($ship_id == Ships::ship_solar_satellite) {
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
            return $this->template->set('fleet/fleet1_selector_row', $this->langs->language);
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
            return $this->template->set('fleet/fleet1_noships_row', $this->langs->language);
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
            return $this->template->set('fleet/fleet1_button', $this->langs->language);
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
            'target_mission' => FILTER_VALIDATE_INT,
        ]);

        // always reset, and define as array
        $_SESSION['fleet_data'] = [];

        return [
            'galaxy' => $data['galaxy'] ?? $this->planet['planet_galaxy'],
            'system' => $data['system'] ?? $this->planet['planet_system'],
            'planet' => $data['planet'] ?? $this->planet['planet_planet'],
            'planettype' => $data['planettype'] ?? $this->planet['planet_type'],
            'target_mission' => $data['target_mission'] ?? 0,
        ];
    }
}
