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
        parent::$users->check_session();

        // Check module access
        FunctionsLib::module_message(FunctionsLib::is_module_accesible(self::MODULE_ID));

        $this->buildPage();
    }

    /**
     * __destructor
     *
     * @return void
     */
    public function __destruct()
    {
        parent::$db->closeConnection();
    }

    /**
     * buildPage
     *
     * @return void
     */
    private function buildPage()
    {
        FunctionsLib::redirect(FunctionsLib::read_config('forum_url'));
    }
}

/* end of forum.php */
