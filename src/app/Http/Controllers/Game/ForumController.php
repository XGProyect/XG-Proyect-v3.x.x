<?php

namespace App\Http\Controllers\Game;

use App\Core\BaseController;
use App\Libraries\Functions;
use App\Libraries\Users;

class ForumController extends BaseController
{
    public const MODULE_ID = 14;

    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Users::checkSession();
    }

    public function index(): void
    {
        // Check module access
        Functions::moduleMessage(Functions::isModuleAccesible(self::MODULE_ID));

        // build the page
        $this->buildPage();
    }

    private function buildPage(): void
    {
        Functions::redirect(Functions::readConfig('forum_url'));
    }
}
