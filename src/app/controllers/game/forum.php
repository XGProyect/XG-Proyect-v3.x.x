<?php
/**
 * Forum Controller
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
use App\libraries\Functions;
use App\libraries\Users;

/**
 * Forum Class
 */
class Forum extends BaseController
{
    /**
     * @var int Module ID
     */
    public const MODULE_ID = 14;

    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Users::checkSession();
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
        Functions::redirect(Functions::readConfig('forum_url'));
    }
}
