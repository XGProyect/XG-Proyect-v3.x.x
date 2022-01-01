<?php

declare(strict_types=1);
/**
 * XG Proyect
 *
 * Open-source OGame Clon
 *
 * This content is released under the GPL-3.0 License
 *
 * Copyright (c) 2008-2020 XG Proyect
 *
 * @package    XG Proyect
 * @author     XG Proyect Team
 * @copyright  2008-2020 XG Proyect
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0 License
 * @link       https://github.com/XGProyect/
 * @since      3.0.0
 */

namespace App\controllers\adm;

use App\core\BaseController;
use App\libraries\adm\AdministrationLib as Administration;
use App\libraries\FormatLib as Format;
use App\libraries\Functions;
use App\libraries\StatisticsLibrary as Statistics;

/**
 * RebuildHighscores Class
 */
class RebuildHighscores extends BaseController
{
    /**
     * Contains the statistics result
     *
     * @var array
     */
    private $result = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Language
        parent::loadLang(['adm/global', 'adm/rebuildhighscores']);
    }

    /**
     * Users land here
     *
     * @return void
     */
    public function index(): void
    {
        // check if the user is allowed to access
        if (!Administration::authorization(__CLASS__, (int) $this->user['user_authlevel'])) {
            die(Administration::noAccessMessage($this->langs->line('no_permissions')));
        }

        // time to do something
        $this->runAction();

        // build the page
        $this->buildPage();
    }

    /**
     * Run an action
     *
     * @return void
     */
    private function runAction(): void
    {
        $stObject = new Statistics();
        $this->result = $stObject->makeStats();

        Functions::updateConfig('stat_last_update', $this->result['stats_time']);
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage(): void
    {
        $this->page->displayAdmin(
            $this->template->set(
                'adm/rebuildhighscores_view',
                array_merge(
                    $this->langs->language,
                    $this->getStatisticsResult()
                )
            )
        );
    }

    /**
     * Get the statistics regeneration results
     *
     * @return array
     */
    private function getStatisticsResult(): array
    {
        return [
            'memory_p' => strtr('%i / %m', [
                '%i' => Format::prettyBytes($this->result['memory_peak'][0]),
                '%m' => Format::prettyBytes($this->result['memory_peak'][0]),
            ]),
            'memory_i' => strtr('%i / %m', [
                '%i' => Format::prettyBytes($this->result['initial_memory'][0]),
                '%m' => Format::prettyBytes($this->result['initial_memory'][0]),
            ]),
            'memory_e' => strtr('%i / %m', [
                '%i' => Format::prettyBytes($this->result['end_memory'][0]),
                '%m' => Format::prettyBytes($this->result['end_memory'][0]),
            ]),
            'alert' => Administration::saveMessage('ok', strtr(
                $this->langs->line('sb_stats_update'),
                ['%t' => $this->result['totaltime']]
            )),
        ];
    }
}
