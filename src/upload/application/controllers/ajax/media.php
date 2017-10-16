<?php
/**
 * Media Controller
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

use application\core\Controller;

/**
 * Media Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Media extends Controller
{

    /**
     *
     * @var array Language data
     */
    private $_lang;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->_lang = $this->getLang();

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
        parent::$page->display(
            $this->getTemplate()->set('ajax/media_view', $this->_lang), false, '', false
        );
    }
}

/* end of media.php */
