<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game;

use App\Core\BaseController;
use App\Core\Enumerators\SwitchIntEnumerator as SwitchInt;
use App\Helpers\UrlHelper;
use App\Libraries\FormatLib;
use App\Libraries\Functions;
use App\Libraries\NoobsProtectionLib;
use App\Libraries\Users;
use App\Models\Game\Search;

class SearchController extends BaseController
{
    public const MODULE_ID = 17;

    private ?NoobsProtectionLib $noob = null;
    private array $search_terms = [
        'search_type' => '',
        'player_name' => '',
        'alliance_tag' => '',
        'planet_names' => '',
        'search_text' => '',
        'error_block' => '',
    ];
    private array $results = [];
    private Search $searchModel;

    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Users::checkSession();

        // load Language
        parent::loadLang(['game/search']);

        // load library
        $this->noob = new NoobsProtectionLib();
        $this->searchModel = new Search();
    }

    public function index(): void
    {
        // Check module access
        Functions::moduleMessage(Functions::isModuleAccesible(self::MODULE_ID));

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
        $search_query = filter_input_array(INPUT_POST, [
            'search_type' => FILTER_UNSAFE_RAW,
            'search_text' => FILTER_UNSAFE_RAW,
        ]);

        $this->search_terms['error_block'] = $this->langs->line('sh_error_empty');

        if (!empty($search_query['search_text'])) {
            $this->search_terms['search_type'] = $search_query['search_type'];
            $this->search_terms[$search_query['search_type']] = 'selected = "selected"';
            $this->search_terms['search_text'] = $search_query['search_text'];

            switch ($search_query['search_type']) {
                case 'player_name':
                default:
                    $this->results = $this->searchModel->getResultsByPlayerName($search_query['search_text']);
                    break;
                case 'alliance_tag':
                    $this->results = $this->searchModel->getResultsByAllianceTag($search_query['search_text']);
                    break;
                case 'planet_names':
                    $this->results = $this->searchModel->getResultsByPlanetName($search_query['search_text']);
                    break;
            }

            if (count($this->results) <= 0) {
                $this->search_terms['error_block'] = $this->langs->line('sh_error_no_results_' . $this->search_terms['search_type']);
            }
        }
    }

    private function buildPage(): void
    {
        $this->page->display(
            $this->template->set(
                'game/search_view',
                array_merge(
                    [
                        'search_results' => $this->buildResultsBlock(),
                    ],
                    $this->search_terms,
                    $this->langs->language
                )
            )
        );
    }

    /**
     * Build the results block
     *
     * @return string
     */
    private function buildResultsBlock(): string
    {
        if (count($this->results) > 0) {
            $this->search_terms['error_block'] = '';

            return $this->template->set(
                'game/search_' . $this->search_terms['search_type'] . '_results_view',
                array_merge(
                    $this->langs->language,
                    [
                        'results' => $this->parseResults(),
                    ]
                )
            );
        }

        return '';
    }

    /**
     * Parse the list of results
     *
     * @return array
     */
    private function parseResults(): array
    {
        $list_of_results = [];

        foreach ($this->results as $results) {
            if ($this->search_terms['search_type'] == 'player_name') {
                $list_of_results[] = array_merge(
                    $results,
                    [
                        'planet_position' => FormatLib::prettyCoords((int) $results['planet_galaxy'], (int) $results['planet_system'], (int) $results['planet_planet']),
                        'user_rank' => $this->setPosition((int) $results['user_rank'], (int) $results['user_authlevel']),
                        'user_actions' => $this->getPlayersActions((int) $results['user_id']),
                    ]
                );
            }

            if ($this->search_terms['search_type'] == 'alliance_tag') {
                $list_of_results[] = array_merge(
                    $results,
                    [
                        'alliance_points' => FormatLib::prettyNumber($results['alliance_points']),
                        'alliance_actions' => $this->getAllianceApplicationAction((int) $results['alliance_id'], (int) $results['alliance_requests']),
                    ]
                );
            }

            if ($this->search_terms['search_type'] == 'planet_names') {
                $list_of_results[] = array_merge(
                    $results,
                    [
                        'planet_position' => FormatLib::prettyCoords((int) $results['planet_galaxy'], (int) $results['planet_system'], (int) $results['planet_planet']),
                        'user_rank' => $this->setPosition((int) $results['user_rank'], (int) $results['user_authlevel']),
                        'user_actions' => $this->getPlayersActions((int) $results['user_id']),
                    ]
                );
            }
        }

        return $list_of_results;
    }

    /**
     * Set the user position or not based on its level
     *
     * @param integer $user_rank
     * @param integer $user_level
     * @return string
     */
    private function setPosition(int $user_rank, int $user_level): string
    {
        if ($this->noob->isRankVisible($user_level)) {
            return UrlHelper::setUrl(
                'game.php?page=statistics&start=' . $user_rank,
                FormatLib::prettyNumber($user_rank)
            );
        } else {
            return '-';
        }
    }

    /**
     * Undocumented function
     *
     * @param integer $user_id
     * @return string
     */
    private function getPlayersActions(int $user_id): string
    {
        $chatLink = UrlHelper::setUrl(
            'game.php?page=chat&playerId=' . $user_id,
            Functions::setImage(DPATH . '/img/m.gif', $this->langs->line('sh_tip_write')),
            $this->langs->line('sh_tip_apply')
        );

        $buddyLink = UrlHelper::setUrl(
            '#',
            Functions::setImage(DPATH . '/img/b.gif', $this->langs->line('sh_tip_buddy_request')),
            $this->langs->line('sh_tip_apply'),
            'onClick="f(\'game.php?page=buddies&mode=2&u=' . $user_id . '\', \'' . $this->langs->line('sh_tip_buddy_request') . '\')"'
        );

        return $chatLink . ' ' . $buddyLink;
    }

    /**
     * Get alliance application action based on alliance permission
     *
     * @param integer $alliance_id
     * @param integer $alliance_requests
     * @return string
     */
    private function getAllianceApplicationAction(int $alliance_id, int $alliance_requests): string
    {
        if ($alliance_requests == SwitchInt::on) {
            return UrlHelper::setUrl(
                'game.php?page=alliance&mode=apply&allyid=' . $alliance_id,
                Functions::setImage(DPATH . '/img/m.gif', $this->langs->line('sh_tip_apply')),
                $this->langs->line('sh_tip_apply')
            );
        }

        return '';
    }
}
