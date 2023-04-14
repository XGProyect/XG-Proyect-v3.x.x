<?php

namespace App\Http\Controllers\Adm;

use App\Core\BaseController;
use App\Libraries\Adm\AdministrationLib as Administration;
use App\Models\Adm\Reset;

class ResetController extends BaseController
{
    private string $alert = '';
    private Reset $resetModel;

    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Language
        parent::loadLang(['adm/global', 'adm/reset']);

        $this->resetModel = new Reset();
    }

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
                    $this->resetModel->resetDefenses();
                }

                // reset ships
                if (isset($_POST['ships']) && $_POST['ships'] == 'on') {
                    $this->resetModel->resetShips();
                }

                // reset shipyard queues
                if (isset($_POST['h_d']) && $_POST['h_d'] == 'on') {
                    $this->resetModel->resetShipyardQueues();
                }

                // reset planet buildings
                if (isset($_POST['edif_p']) && $_POST['edif_p'] == 'on') {
                    $this->resetModel->resetPlanetBuildings();
                }

                // reset moon buildings
                if (isset($_POST['edif_l']) && $_POST['edif_l'] == 'on') {
                    $this->resetModel->resetMoonBuildings();
                }

                // reset buildings queues
                if (isset($_POST['edif']) && $_POST['edif'] == 'on') {
                    $this->resetModel->resetBuildingsQueues();
                }

                // reset research
                if (isset($_POST['inves']) && $_POST['inves'] == 'on') {
                    $this->resetModel->resetResearch();
                }

                // reset research queues
                if (isset($_POST['inves_c']) && $_POST['inves_c'] == 'on') {
                    $this->resetModel->resetResearchQueues();
                }

                // reset officiers
                if (isset($_POST['ofis']) && $_POST['ofis'] == 'on') {
                    $this->resetModel->resetOfficiers();
                }

                // reset dark matter
                if (isset($_POST['dark']) && $_POST['dark'] == 'on') {
                    $this->resetModel->resetDarkMatter();
                }

                // reset resources
                if (isset($_POST['resources']) && $_POST['resources'] == 'on') {
                    $this->resetModel->resetResources();
                }

                // reset notes
                if (isset($_POST['notes']) && $_POST['notes'] == 'on') {
                    $this->resetModel->resetNotes();
                }

                // reset reports
                if (isset($_POST['rw']) && $_POST['rw'] == 'on') {
                    $this->resetModel->resetReports();
                }

                // reset friends
                if (isset($_POST['friends']) && $_POST['friends'] == 'on') {
                    $this->resetModel->resetFriends();
                }

                // reset alliances
                if (isset($_POST['alliances']) && $_POST['alliances'] == 'on') {
                    $this->resetModel->resetAlliances();
                }

                // reset fleets
                if (isset($_POST['fleets']) && $_POST['fleets'] == 'on') {
                    $this->resetModel->resetFleets();
                }

                // reset banned
                if (isset($_POST['banneds']) && $_POST['banneds'] == 'on') {
                    $this->resetModel->resetBanned();
                }

                // reset messages
                if (isset($_POST['messages']) && $_POST['messages'] == 'on') {
                    $this->resetModel->resetMessages();
                }

                // reset statistics
                if (isset($_POST['statpoints']) && $_POST['statpoints'] == 'on') {
                    $this->resetModel->resetStatistics();
                }

                // reset moons
                if (isset($_POST['moons']) && $_POST['moons'] == 'on') {
                    $this->resetModel->resetMoons();
                }
            } else {
                // reset everything
                $this->resetModel->resetAll();
            }

            $this->alert = Administration::saveMessage('ok', $this->langs->line('re_reset_excess'));
        }
    }

    private function buildPage(): void
    {
        $this->page->displayAdmin(
            $this->template->set(
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
