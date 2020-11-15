<?php declare (strict_types = 1);

/**
 * Search Controller
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\controllers\game;

use App\core\BaseController;
use App\core\enumerators\SwitchIntEnumerator as SwitchInt;
use App\helpers\UrlHelper;
use App\libraries\FormatLib;
use App\libraries\Functions;

/**
 * Search Class
 */
class Search extends BaseController
{
    const MODULE_ID = 17;

    /**
     *
     * @var \NoobsProtectionLib
     */
    private $noob = null;

    /**
     * Contains the search terms provided by the player
     *
     * @var array
     */
    private $search_terms = [
        'search_type' => '',
        'player_name' => '',
        'alliance_tag' => '',
        'planet_names' => '',
        'search_text' => '',
        'error_block' => '',
    ];

    /**
     * Contains the search results
     *
     * @var array
     */
    private $results = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // load Model
        parent::loadModel('game/search');

        // load Language
        parent::loadLang(['game/search']);

        // load library
        $this->noob = Functions::loadLibrary('NoobsProtectionLib');
    }

    /**
     * Users land here
     *
     * @return void
     */
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
            'search_type' => FILTER_SANITIZE_STRING,
            'search_text' => FILTER_SANITIZE_STRING,
        ]);

        $this->search_terms['error_block'] = $this->langs->line('sh_error_empty');

        if (!empty($search_query['search_text'])) {
            $this->search_terms['search_type'] = $search_query['search_type'];
            $this->search_terms[$search_query['search_type']] = 'selected = "selected"';
            $this->search_terms['search_text'] = $search_query['search_text'];

            switch ($search_query['search_type']) {
                case 'player_name':
                default:
                    $this->results = $this->Search_Model->getResultsByPlayerName($search_query['search_text']);
                    break;
                case 'alliance_tag':
                    $this->results = $this->Search_Model->getResultsByAllianceTag($search_query['search_text']);
                    break;
                case 'planet_names':
                    $this->results = $this->Search_Model->getResultsByPlanetName($search_query['search_text']);
                    break;
            }

            if (count($this->results) <= 0) {
                $this->search_terms['error_block'] = $this->langs->line('sh_error_no_results_' . $this->search_terms['search_type']);
            }
        }
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage(): void
    {
        parent::$page->display(
            $this->getTemplate()->set(
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

            return $this->getTemplate()->set(
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
