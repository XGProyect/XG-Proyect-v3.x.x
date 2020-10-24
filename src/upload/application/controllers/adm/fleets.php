<?php

declare (strict_types = 1);

/**
 * Fleets Controller
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\controllers\adm;

use application\core\Controller;
use application\libraries\adm\AdministrationLib as Administration;
use application\libraries\FleetsLib;
use application\libraries\FormatLib as Format;
use application\libraries\TimingLibrary as Timing;

/**
 * Fleets Class
 */
class Fleets extends Controller
{
    /**
     * Current user data
     *
     * @var array
     */
    private $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Model
        parent::loadModel('adm/fleets');

        // load Language
        parent::loadLang(['adm/global', 'adm/objects', 'adm/fleets']);

        // set data
        $this->user = $this->getUserData();

        // check if the user is allowed to access
        if (!Administration::authorization(__CLASS__, (int) $this->user['user_authlevel'])) {
            die(Administration::noAccessMessage($this->langs->line('no_permissions')));
        }

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
    private function runAction(): void
    {
        $action = filter_input(INPUT_GET, 'action');
        $fleet_id = filter_input(INPUT_GET, 'fleetId', FILTER_VALIDATE_INT);

        if (in_array($action, ['restart', 'end', 'return', 'delete']) && $fleet_id) {
            $this->{'do' . ucfirst($action) . 'Action'}($fleet_id);
        }
    }

    /**
     * * Update the fleet to simulate a mission restart
     *
     * @param integer $fleet_id
     * @return void
     */
    private function doRestartAction(int $fleet_id): void
    {
        $this->Fleets_Model->restartFleetById($fleet_id);
    }

    /**
     * Update the fleet to simulate a mission completion
     *
     * @param integer $fleet_id
     * @return void
     */
    private function doEndAction(int $fleet_id): void
    {
        $this->Fleets_Model->endFleetById($fleet_id);
    }

    /**
     * Update the fleet to simulate a return
     *
     * @param integer $fleet_id
     * @return void
     */
    private function doReturnAction(int $fleet_id): void
    {
        $this->Fleets_Model->returnFleetById($fleet_id);
    }

    /**
     * Delete the fleet from the DB
     *
     * @param integer $fleet_id
     * @return void
     */
    private function doDeleteAction(int $fleet_id): void
    {
        $this->Fleets_Model->deleteFleetById($fleet_id);
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage(): void
    {
        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/fleets_view',
                array_merge(
                    $this->langs->language,
                    $this->buildFleetMovementsBlock()
                )
            )
        );
    }

    /**
     * Build the list of fleet movements currently taking place
     *
     * @return array
     */
    private function buildFleetMovementsBlock(): array
    {
        $fleets = $this->Fleets_Model->getAllFleets();
        $fleet_movements = [];

        foreach ($fleets as $fleet) {
            $fleet_movements[] = array_merge(
                $this->langs->language,
                $this->buildMissionBlock($fleet),
                $this->buildAmountBlock($fleet),
                $this->buildBeginningBlock($fleet),
                $this->buildDepartureBlock($fleet),
                $this->buildObjectiveBlock($fleet),
                $this->buildArrivalBlock($fleet),
                $this->buildReturnBlock($fleet),
                $this->buildActionsBlock($fleet)
            );
        }

        return ['fleet_movements' => $fleet_movements];
    }

    /**
     * Build the mission block including the resources
     *
     * @param array $fleet
     * @return array
     */
    private function buildMissionBlock(array $fleet): array
    {
        return [
            'mission' => $this->langs->language['ff_type_mission'][$fleet['fleet_mission']] . ' ' . (FleetsLib::isFleetReturning($fleet['fleet_mess']) ? $this->langs->line('ff_r') : $this->langs->line('ff_a')),
            'metal' => Format::prettyNumber($fleet['fleet_resource_metal']),
            'crystal' => Format::prettyNumber($fleet['fleet_resource_crystal']),
            'deuterium' => Format::prettyNumber($fleet['fleet_resource_deuterium']),
        ];
    }

    /**
     * Build the amount of ships block including the ship type popup
     *
     * @param array $fleet
     * @return array
     */
    private function buildAmountBlock(array $fleet): array
    {
        $pop_up = [];

        foreach (FleetsLib::getFleetShipsArray($fleet['fleet_array']) as $ship => $amount) {
            $pop_up[] = $this->langs->language['objects'][$ship] . ': ' . Format::prettyNumber($amount);
        }

        return [
            'amount' => $this->langs->line('ff_ships'),
            'amount_content' => join('<br>', $pop_up),
        ];
    }

    /**
     * Build the fleet beginning coords block
     *
     * @param array $fleet
     * @return array
     */
    private function buildBeginningBlock(array $fleet): array
    {
        return [
            'beginning' => Format::prettyCoords(
                (int) $fleet['fleet_start_galaxy'],
                (int) $fleet['fleet_start_system'],
                (int) $fleet['fleet_start_planet']
            ),
        ];
    }

    /**
     * Build the departure time from the beginning block
     *
     * @param array $fleet
     * @return array
     */
    private function buildDepartureBlock(array $fleet): array
    {
        return ['departure' => Timing::formatExtendedDate($fleet['fleet_creation'])];
    }

    /**
     * Build the fleet objective coords block
     *
     * @param array $fleet
     * @return void
     */
    private function buildObjectiveBlock(array $fleet): array
    {
        return [
            'objective' => Format::prettyCoords(
                (int) $fleet['fleet_end_galaxy'],
                (int) $fleet['fleet_end_system'],
                (int) $fleet['fleet_end_planet']
            ),
        ];
    }

    /**
     * Build the arrival time to the objective block
     *
     * @param array $fleet
     * @return array
     */
    private function buildArrivalBlock(array $fleet): array
    {
        return ['arrival' => Timing::formatExtendedDate($fleet['fleet_start_time'])];
    }

    /**
     * Build the return time to the departure block
     *
     * @param array $fleet
     * @return array
     */
    private function buildReturnBlock(array $fleet): array
    {
        return ['return' => Timing::formatExtendedDate($fleet['fleet_end_time'])];
    }

    /**
     * Build the actions block
     *
     * @param array $fleet
     * @return array
     */
    private function buildActionsBlock(array $fleet): array
    {
        return ['fleet_id' => $fleet['fleet_id']];
    }
}

/* end of fleets.php */
