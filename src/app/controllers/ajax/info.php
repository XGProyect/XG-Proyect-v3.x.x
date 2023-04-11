<?php

namespace App\controllers\ajax;

use App\core\BaseController;

class Info extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        // load Language
        parent::loadLang(['ajax/info']);
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
            $this->template->set('ajax/info_view', $this->langs->language),
            false,
            '',
            false
        );
    }
}
