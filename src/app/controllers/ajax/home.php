<?php

namespace App\Controllers\Ajax;

use App\Core\BaseController;

class Home extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        // load Language
        parent::loadLang(['ajax/home']);
    }

    public function index(): void
    {
        // build the page
        $this->buildPage();
    }

    private function buildPage(): void
    {
        $this->page->display(
            $this->template->set('ajax/home_view', $this->langs->language),
            false,
            '',
            false
        );
    }
}
