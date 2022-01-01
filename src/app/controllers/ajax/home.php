<?php
/**
 * Home Controller
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */

namespace App\controllers\ajax;

use App\core\BaseController;

/**
 * Home Class
 */
class Home extends BaseController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // load Language
        parent::loadLang(['ajax/home']);
    }

    /**
     * Users land here
     *
     * @return void
     */
    public function index(): void
    {
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
        $this->page->display(
            $this->template->set('ajax/home_view', $this->langs->language),
            false,
            '',
            false
        );
    }
}
