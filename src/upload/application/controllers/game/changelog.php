<?php
/**
 * Changelog Controller
 *
 * PHP Version 7.1+
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\controllers\game;

use application\core\Controller;
use application\libraries\FunctionsLib;
use application\libraries\Timing_library as Timing;

/**
 * Change log Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Changelog extends Controller
{

    const MODULE_ID = 0;

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
        parent::loadModel('game/changelog');

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        // build the page
        $this->buildPage();
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage()
    {
        $changes = [];
        $entries = $this->Changelog_Model->getAllChangelogEntries();

        if ($entries) {
            foreach ($entries as $entry) {
                $changes[] = [
                    'version_number' => $entry['changelog_version'],
                    'description' => nl2br(
                        Timing::formatShortDate($entry['changelog_date']) . '<br>' . $entry['changelog_description']
                    ),
                ];
            }
        }

        parent::$page->display(
            $this->getTemplate()->set('game/changelog_view', array_merge(
                $this->getLang(),
                ['list_of_changes' => $changes]
            ))
        );
    }
}

/* end of changelog.php */
