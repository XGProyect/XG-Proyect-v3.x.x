<?php
/**
 * Forum Controller
 *
 * PHP Version 5.5+
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */

namespace application\controllers\game;

use application\core\XGPCore;
use application\libraries\FunctionsLib;

/**
 * Forum Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Forum extends XGPCore
{
    const MODULE_ID = 14;

    /**
     * __construct
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

        $this->buildPage();
    }

    /**
     * buildPage
     *
     * @return void
     */
    private function buildPage()
    {
        FunctionsLib::redirect(FunctionsLib::readConfig('forum_url'));
    }
}

/* end of forum.php */
