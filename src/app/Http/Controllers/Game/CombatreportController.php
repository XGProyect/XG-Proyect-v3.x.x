<?php

namespace App\Http\Controllers\Game;

use App\Core\BaseController;
use App\Libraries\Combatreport\Report;
use App\Libraries\Functions;
use App\Libraries\Users;
use App\Models\Game\Combatreport;

class CombatreportController extends BaseController
{
    public const MODULE_ID = 23;

    private ?Report $report = null;
    private Combatreport $combatreportModel;

    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Users::checkSession();

        // load Language
        parent::loadLang(['game/combatreport']);

        $this->combatreportModel = new Combatreport();

        // init a new report object
        $this->setUpReport();
    }

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
     * Creates a new report object that will handle all the report actions
     *
     * @return void
     */
    private function setUpReport()
    {
        $this->report = new Report(
            [$this->combatreportModel->getReportById(filter_input(INPUT_GET, 'report'))],
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
        $owners = $this->report->getFirstReportOwnersAsArray();

        if (!isset($owners) or !in_array($this->user['user_id'], $owners)) {
            Functions::message($this->langs->line('cr_no_access'), '', 0, false, false, false);
        }
    }

    private function buildPage(): void
    {
        $this->page->display(
            $this->getReportTemplate(),
            false,
            '',
            false
        );
    }

    /**
     * Get report template based on different conditions
     *
     * @return string The template
     */
    private function getReportTemplate()
    {
        // any other case
        $content = stripslashes($this->report->getAllReports()[0]->getReportContent());
        /*
        foreach ($this->langs->line('cr_tech_short') as $id => $s_name) {
        $search = [$id];
        $replace = [$s_name];
        $content = str_replace($search, $replace, $content);
        }

        $no_fleet = $this->template->set('combatreport/combatreport_no_fleet_view', $this->langs->language);
        $destroyed = $this->template->set('combatreport/combatreport_destroyed_view', $this->langs->language);

        $search = [$no_fleet];
        $replace = [$destroyed];
        $content = str_replace($search, $replace, $content);*/

        return $content;
    }
}
