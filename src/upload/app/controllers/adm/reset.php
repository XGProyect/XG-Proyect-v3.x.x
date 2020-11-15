<?php
/**
 * Reset Controller
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\controllers\adm;

use App\core\BaseController;
use App\libraries\adm\AdministrationLib as Administration;

/**
 * Reset Class
 */
class Reset extends BaseController
{
    /**
     * Contains the alert string
     *
     * @var string
     */
    private $alert = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Model
        parent::loadModel('adm/reset');

        // load Language
        parent::loadLang(['adm/global', 'adm/reset']);
    }

    /**
     * Users land here
     *
     * @return void
     */
    public function index(): void
    {
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
        if ($_POST) {
            if (!isset($_POST['resetall'])) {
                // reset defenses
                if (isset($_POST['defenses']) && $_POST['defenses'] == 'on') {
                    $this->Reset_Model->resetDefenses();
                }

                // reset ships
                if (isset($_POST['ships']) && $_POST['ships'] == 'on') {
                    $this->Reset_Model->resetShips();
                }

                // reset shipyard queues
                if (isset($_POST['h_d']) && $_POST['h_d'] == 'on') {
                    $this->Reset_Model->resetShipyardQueues();
                }

                // reset planet buildings
                if (isset($_POST['edif_p']) && $_POST['edif_p'] == 'on') {
                    $this->Reset_Model->resetPlanetBuildings();
                }

                // reset moon buildings
                if (isset($_POST['edif_l']) && $_POST['edif_l'] == 'on') {
                    $this->Reset_Model->resetMoonBuildings();
                }

                // reset buildings queues
                if (isset($_POST['edif']) && $_POST['edif'] == 'on') {
                    $this->Reset_Model->resetBuildingsQueues();
                }

                // reset research
                if (isset($_POST['inves']) && $_POST['inves'] == 'on') {
                    $this->Reset_Model->resetResearch();
                }

                // reset research queues
                if (isset($_POST['inves_c']) && $_POST['inves_c'] == 'on') {
                    $this->Reset_Model->resetResearchQueues();
                }

                // reset officiers
                if (isset($_POST['ofis']) && $_POST['ofis'] == 'on') {
                    $this->Reset_Model->resetOfficiers();
                }

                // reset dark matter
                if (isset($_POST['dark']) && $_POST['dark'] == 'on') {
                    $this->Reset_Model->resetDarkMatter();
                }

                // reset resources
                if (isset($_POST['resources']) && $_POST['resources'] == 'on') {
                    $this->Reset_Model->resetResources();
                }

                // reset notes
                if (isset($_POST['notes']) && $_POST['notes'] == 'on') {
                    $this->Reset_Model->resetNotes();
                }

                // reset reports
                if (isset($_POST['rw']) && $_POST['rw'] == 'on') {
                    $this->Reset_Model->resetReports();
                }

                // reset friends
                if (isset($_POST['friends']) && $_POST['friends'] == 'on') {
                    $this->Reset_Model->resetFriends();
                }

                // reset alliances
                if (isset($_POST['alliances']) && $_POST['alliances'] == 'on') {
                    $this->Reset_Model->resetAlliances();
                }

                // reset fleets
                if (isset($_POST['fleets']) && $_POST['fleets'] == 'on') {
                    $this->Reset_Model->resetFleets();
                }

                // reset banned
                if (isset($_POST['banneds']) && $_POST['banneds'] == 'on') {
                    $this->Reset_Model->resetBanned();
                }

                // reset messages
                if (isset($_POST['messages']) && $_POST['messages'] == 'on') {
                    $this->Reset_Model->resetMessages();
                }

                // reset statistics
                if (isset($_POST['statpoints']) && $_POST['statpoints'] == 'on') {
                    $this->Reset_Model->resetStatistics();
                }

                // reset moons
                if (isset($_POST['moons']) && $_POST['moons'] == 'on') {
                    $this->Reset_Model->resetMoons();
                }
            } else {
                // reset everything
                $this->Reset_Model->resetAll();
            }

            $this->alert = Administration::saveMessage('ok', $this->langs->line('re_reset_excess'));
        }
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
                'adm/reset_view',
                array_merge(
                    $this->langs->language,
                    [
                        'alert' => $this->alert ? $this->alert : '',
                    ]
                )
            )
        );
    }
}
