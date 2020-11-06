<?php
/**
 * Media Controller
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
 */
class Media extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // load Language
        parent::loadLang('ajax/media');

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
            $this->getTemplate()->set('ajax/media_view', $this->langs->language),
            false,
            '',
            false
        );
    }
}
