<?php
/**
 * Combatreport Controller
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
use App\libraries\combatreport\Report;
use App\libraries\Functions;

/**
 * Combatreport Class
 */
class Combatreport extends BaseController
{
    const MODULE_ID = 23;

    /**
     *
     * @var \Report
     */
    private $report = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // load Model
        parent::loadModel('game/combatreport');

        // load Language
        parent::loadLang(['game/combatreport']);

        // init a new report object
        $this->setUpReport();
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
     * Creates a new report object that will handle all the report actions
     *
     * @return void
     */
    private function setUpReport()
    {
        $this->report = new Report(
            [$this->Combatreport_Model->getReportById(filter_input(INPUT_GET, 'report'))],
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

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage()
    {
        parent::$page->display(
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

        $no_fleet = $this->getTemplate()->set('combatreport/combatreport_no_fleet_view', $this->langs->language);
        $destroyed = $this->getTemplate()->set('combatreport/combatreport_destroyed_view', $this->langs->language);

        $search = [$no_fleet];
        $replace = [$destroyed];
        $content = str_replace($search, $replace, $content);*/

        return $content;
    }
}
