<?php

namespace App\Http\Controllers\Game;

use App\Core\BaseController;
use App\Libraries\Functions;
use App\Libraries\Users;

class TraderOverviewController extends BaseController
{
    /**
     * The module ID
     *
     * @var int
     */
    public const MODULE_ID = 5;

    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Users::checkSession();

        // load Language
        parent::loadLang(['game/trader']);

        // Check module access
        Functions::moduleMessage(Functions::isModuleAccesible(self::MODULE_ID));

        // build the page
        $this->buildPage();
    }

    private function buildPage(): void
    {
        $this->page->display(
            $this->template->set(
                'game/trader_overview_view',
                array_merge(
                    $this->langs->language,
                    [
                        'status_message' => [],
                        'error_color' => '',
                        'error_text' => '',
                        'current_mode' => '',
                    ]
                )
            )
        );
    }
}
