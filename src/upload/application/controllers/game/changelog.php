<?php
/**
 * Changelog Controller
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
use application\libraries\FunctionsLib;

define('IN_CHANGELOG', true);

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

        foreach ($this->getLang()['changelog'] as $v => $d) {

            $changes[] = [
                'version_number' => $v,
                'description' => nl2br($d)
            ];
        }

        parent::$page->display(
            $this->getTemplate()->set('changelog/changelog_view', array_merge(
                $this->getLang(), ['list_of_changes' => $changes]
            ))
        );
    }
}

/* end of changelog.php */
