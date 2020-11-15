<?php
/**
 * Movement Controller
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
use App\core\entities\FleetEntity;
use App\core\enumerators\MissionsEnumerator as Missions;
use App\helpers\UrlHelper;
use App\libraries\FleetsLib;
use App\libraries\FormatLib;
use App\libraries\Functions;
use App\libraries\game\Fleets;
use App\libraries\premium\Premium;
use App\libraries\research\Researches;
use App\libraries\TimingLibrary as Timing;

/**
 * Movement Class
 */
class Movement extends BaseController
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
    const REDIRECT_TARGET = 'game.php?page=movement';

    /**
     *
     * @var \Fleets
     */
    private $fleets = null;

    /**
     *
     * @var \Research
     */
    private $research = null;

    /**
     *
     * @var \Premium
     */
    private $premium = null;

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
        parent::loadLang(['game/missions', 'game/ships', 'game/fleet']);

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

        // time to do something
        $this->runAction();

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
        $this->fleets = new Fleets(
            $this->Fleet_Model->getAllFleetsByUserId($this->user['user_id']),
            $this->user['user_id']
        );

        $this->research = new Researches(
            [$this->user],
            $this->user['user_id']
        );

        $this->premium = new Premium(
            [$this->user],
            $this->user['user_id']
        );
    }

    /**
     * Run an action
     *
     * @return void
     */
    private function runAction()
    {
        $fleet_action = filter_input(INPUT_GET, 'action');

        if (in_array($fleet_action, ['return'])) {
            $this->{'execFleet' . ucfirst($fleet_action)}();
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
        $page = [
            'js_path' => JS_PATH,
            'fleets' => $this->fleets->getFleetsCount(),
            'max_fleets' => FleetsLib::getMaxFleets(
                $this->research->getCurrentResearch()->getResearchComputerTechnology(),
                $this->premium->getCurrentPremium()->getPremiumOfficierAdmiral()
            ),
            'expeditions' => $this->fleets->getExpeditionsCount(),
            'max_expeditions' => FleetsLib::getMaxExpeditions(
                $this->research->getCurrentResearch()->getResearchAstrophysics()
            ),
            'list_of_movements' => $this->buildMovements(),
        ];

        // display the page
        parent::$page->display(
            $this->getTemplate()->set(
                'game/movements_view',
                array_merge(
                    $this->langs->language,
                    $page
                )
            )
        );
    }

    /**
     * Build the list of movements
     *
     * @return array
     */
    private function buildMovements(): array
    {
        $list_of_movements[] = [
            'num' => '-',
            'fleet_mission' => '-',
            'title' => '',
            'fleet_amount' => '-',
            'fleet_start' => '-',
            'fleet_start_time' => '-',
            'fleet_end' => '-',
            'fleet_end_time' => '-',
            'fleet_arrival' => '-',
            'fleet_actions' => '-',
        ];

        if ($this->fleets->getFleetsCount() > 0) {
            // reset
            unset($list_of_movements);

            $fleet_count = 0;

            foreach ($this->fleets->getFleets() as $fleet) {
                $list_of_movements[] = [
                    'num' => ++$fleet_count,
                    'fleet_mission' => $this->langs->language['type_mission'][$fleet->getFleetMission()],
                    'title' => $this->buildTitleBlock($fleet->getFleetMess()),
                    'tooltip' => $this->buildToolTipBlock($fleet->getFleetMess()),
                    'fleet_amount' => FormatLib::prettyNumber($fleet->getFleetAmount()),
                    'fleet' => $this->buildShipsBlock($fleet->getFleetArray()),
                    'fleet_start' => FormatLib::prettyCoords(
                        $fleet->getFleetStartGalaxy(),
                        $fleet->getFleetStartSystem(),
                        $fleet->getFleetStartPlanet()
                    ),
                    'fleet_start_time' => Timing::formatExtendedDate($fleet->getFleetCreation()),
                    'fleet_end' => FormatLib::prettyCoords(
                        $fleet->getFleetEndGalaxy(),
                        $fleet->getFleetEndSystem(),
                        $fleet->getFleetEndPlanet()
                    ),
                    'fleet_end_time' => Timing::formatExtendedDate($fleet->getFleetStartTime()),
                    'fleet_arrival' => Timing::formatExtendedDate($fleet->getFleetEndTime()),
                    'fleet_actions' => $this->buildActionsBlock($fleet),
                ];
            }
        }

        return $list_of_movements;
    }

    /**
     * Build the title block
     *
     * @param int $fleet_mess Fleet Mess
     *
     * @return array
     */
    private function buildTitleBlock(int $fleet_mess): string
    {
        if (FleetsLib::isFleetReturning($fleet_mess)) {
            return $this->langs->line('fl_r');
        }

        return $this->langs->line('fl_a');
    }

    /**
     * Build the topltip block
     *
     * @param int $fleet_mess Fleet Mess
     *
     * @return array
     */
    private function buildToolTipBlock(int $fleet_mess): string
    {
        if (FleetsLib::isFleetReturning($fleet_mess)) {
            return $this->langs->line('fl_returning');
        }

        return $this->langs->line('fl_onway');
    }

    /**
     * Create the ships tool tip block
     *
     * @param string $fleet_array Fleet array
     *
     * @return string
     */
    private function buildShipsBlock(string $fleet_array): string
    {
        $objects = parent::$objects->getObjects();
        $ships = FleetsLib::getFleetShipsArray($fleet_array);
        $tooltips = [];

        foreach ($ships as $ship => $amount) {
            $tooltips[] = $this->langs->language[$objects[$ship]] . ' :' . $amount;
        }

        return count($tooltips) > 0 ? join("\n", $tooltips) : '';
    }

    /**
     * Build the list of actions block
     *
     * @param FleetEntity $fleet
     *
     * @return string
     */
    private function buildActionsBlock(FleetEntity $fleet): string
    {
        $actions = '-';

        if ($fleet->getFleetMess() == 0) {
            $actions = '<form action="game.php?page=movement&action=return" method="post">';
            $actions .= '<input type="hidden" name="fleetid" value="' . $fleet->getFleetId() . '">';
            $actions .= '<input type="submit" name="send" value="' . $this->langs->line('fl_send_back') . '">';
            $actions .= '</form>';

            if ($fleet->getFleetMission() == Missions::ATTACK) {
                $content = '<input type="button" value="' . $this->langs->line('fl_acs') . '">';
                $attributes = 'onClick="f(\'game.php?page=federationlayer&fleet=' . $fleet->getFleetId() . '\', \'\')"';

                $actions .= UrlHelper::setUrl('#', $content, '', $attributes);
            }
        }

        return $actions;
    }

    /**
     * Execute the fleet return if possible
     *
     * @return void
     */
    private function execFleetReturn(): void
    {
        $fleet_id = filter_input(INPUT_POST, 'fleetid', FILTER_VALIDATE_INT);

        if ($fleet_id) {
            $fleet = $this->fleets->getOwnFleetById($fleet_id);

            if (!is_null($fleet) && $fleet->getFleetMess() != 1) {
                $this->Fleet_Model->returnFleet(
                    $fleet,
                    $this->user['user_id']
                );

                Functions::redirect(self::REDIRECT_TARGET);
            }
        }
    }
}
