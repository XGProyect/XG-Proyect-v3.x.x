<?php
/**
 * Home Controller
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

namespace application\controllers\ajax;

use application\core\XGPCore;

/**
 * Home Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Home extends XGPCore
{
    private $langs;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->langs    = parent::$lang;

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
        parent::$page->display(
            parent::$page->parseTemplate(parent::$page->getTemplate('ajax/home_view'), $this->langs),
            false,
            '',
            false
        );
    }
}

/* end of home.php */
