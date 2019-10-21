<?php

declare(strict_types=1);

/**
 * Search Controller
 *
 * PHP Version 7+
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\controllers\game;

use application\core\Controller;
use application\core\Database;
use application\core\enumerators\SwitchIntEnumerator as SwitchInt;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;

use MODULE_ID;

/**
 * Search Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Search extends Controller
{

    const MODULE_ID = 17;


    /**
     *
     * @var type \Users_library
     */
    private $_user;

    /**
     * Contains the search terms provided by the player
     *
     * @var array
     */
    private $_search_terms = [
        'search_type' => '',
        'player_name' => '',
        'alliance_tag' => '',
        'planet_names' => '',
        'search_text' => '',
        'error_block' => ''
    ];

    /**
     * Contains the search results
     *
     * @var array
     */
    private $_results = [];

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // load Model
        parent::loadModel('game/search');

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        // set data
        $this->_user = $this->getUserData();

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
            'search_text' => FILTER_SANITIZE_STRING
        ]);
        
        $this->_search_terms['search_type'] = $search_query['search_type'];
        $this->_search_terms[$search_query['search_type']] = 'selected = "selected"';
        $this->_search_terms['search_text'] = $search_query['search_text'];
        $this->_search_terms['error_block'] = $this->getLang()['sh_error_empty'];

        if (!empty($search_query['search_text'])) {

            switch($search_query['search_type']) {
                case 'player_name':
                default:
    
                    $this->_results = $this->Search_Model->getResultsByPlayerName($search_query['search_text']);
    
                break;
                case 'alliance_tag':
                    
                    $this->_results = $this->Search_Model->getResultsByAllianceTag($search_query['search_text']);
    
                break;
                case 'planet_names':
    
                    $this->_results = $this->Search_Model->getResultsByPlanetName($search_query['search_text']);
    
                break;
            }

            if (count($this->_results) <= 0 ) {

                $this->_search_terms['error_block'] = $this->getLang()['sh_error_no_results_' . $this->_search_terms['search_type']];
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
                'search/search_view',
                array_merge(
                    [
                        'search_results' => $this->buildResultsBlock() ?? [],
                    ],
                    $this->_search_terms,
                    $this->getLang()
                )
            ) 
        );
        /*while ($s = $this->_db->fetchArray($search)) {
                if ($type == 'playername' or $type == 'planetname') {
                    if ($this->_current_user['user_id'] != $s['user_id']) {
                        $s['actions'] = '<a href="game.php?page=chat&playerId=' . $s['user_id'] . '" title="' . $this->_lang['write_message'] . '"><img src="' . DPATH . 'img/m.gif"/></a>&nbsp;';
                        $s['actions'] .= '<a href="#" title="' . $this->_lang['sh_buddy_request'] . '" onClick="f(\'game.php?page=buddies&mode=2&u=' . $s['user_id'] . '\', \'' . $this->_lang['sh_buddy_request'] . '\')"><img src="' . DPATH . 'img/b.gif" border="0"></a>';
                    }

                    $s['planet_name'] = $s['planet_name'];
                    $s['username'] = $s['user_name'];
                    $s['alliance_name'] = ($s['alliance_name'] != '') ? "<a href=\"game.php?page=alliance&mode=ainfo&allyid={$s['alliance_id']}\">{$s['alliance_name']}</a>" : '';
                    $s['position'] = $this->setPosition($s['rank'], $s['user_authlevel']);
                    $s['coordinated'] = "{$s['planet_galaxy']}:{$s['planet_system']}:{$s['planet_planet']}";
                    $result_list .= parent::$page->parseTemplate($row, $s);
                } elseif ($type == 'allytag' or $type == 'allyname') {
                    $s['ally_points'] = FormatLib::prettyNumber($s['points']);
                    $s['ally_tag'] = "<a href=\"game.php?page=alliance&mode=ainfo&allyid={$s['alliance_id']}\">{$s['alliance_tag']}</a>";
                    $result_list .= parent::$page->parseTemplate($row, $s);
                }
            }*/
    }

    private function buildResultsBlock()
    {
        if (count($this->_results) > 0) {

            $this->_search_terms['error_block'] = '';

            return $this->getTemplate()->set(
                'search/search_' . $this->_search_terms['search_type'] . '_results_view',
                array_merge(
                    $this->getLang(),
                    [
                        'results' => $this->parseResults(),
                    ]
                )
            );
        }

        return null;
    }

    /**
     * Parse the list of results
     *
     * @return array
     */
    private function parseResults(): array
    {
        $list_of_results = [];

        foreach ($this->_results as $results) {

            if ($this->_search_terms['player_name'] == 'alliance_tag') {

                $list_of_results[] = array_merge(
                    $results,
                    [
                        'alliance_points'  => FormatLib::prettyNumber($results['alliance_points']),
                        'alliance_actions' => $this->getAllianceApplicationAction((int)$results['alliance_id'], (int)$results['alliance_requests'])
                    ]
                );
            }

            if ($this->_search_terms['search_type'] == 'alliance_tag') {

                $list_of_results[] = array_merge(
                    $results,
                    [
                        'alliance_points'  => FormatLib::prettyNumber($results['alliance_points']),
                        'alliance_actions' => $this->getAllianceApplicationAction((int)$results['alliance_id'], (int)$results['alliance_requests'])
                    ]
                );
            }

            if ($this->_search_terms['search_type'] == 'planet_names') {

                $list_of_results[] = array_merge(
                    $results,
                    [
                        'alliance_points'  => FormatLib::prettyNumber($results['alliance_points']),
                        'alliance_actions' => $this->getAllianceApplicationAction((int)$results['alliance_id'], (int)$results['alliance_requests'])
                    ]
                );
            }
        }


        return $list_of_results;
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

            return FunctionsLib::setUrl(
                'game.php?page=alliance&mode=apply&allyid=' . $alliance_id,
                $this->getLang()['sh_tip_apply'],
                FunctionsLib::setImage(DPATH . '/img/m.gif', $this->getLang()['sh_tip_apply'])
            );
        }

        return '';
    }

    /**
     * Set the user position or not based on its level
     * 
     * @param int $user_rank  User rank
     * @param int $user_level User level
     * 
     * @return string
     */
    private function setPosition($user_rank, $user_level)
    {
        if ($this->_noob->isRankVisible($user_level)) {

            return '<a href="game.php?page=statistics&start=' . $user_rank . '">' . $user_rank . '</a>';
        } else {

            return '-';
        }
    }
}

/* end of search.php */