<?php

declare (strict_types = 1);

/**
 * Statistics Controller
 *
 * PHP Version 7.1+
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\controllers\adm;

use application\core\Controller;
use application\core\enumerators\UserRanksEnumerator as UserRanks;
use application\libraries\adm\AdministrationLib;
use application\libraries\FunctionsLib;

/**
 * Statistics Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Statistics extends Controller
{
    const STATISTICS_SETTINGS = [
        'stat_points' => [
            'filter' => FILTER_VALIDATE_INT,
            'options' => ['min_range' => 1],
        ],
        'stat_update_time' => [
            'filter' => FILTER_VALIDATE_INT,
            'options' => ['min_range' => 1],
        ],
        'stat_admin_level' => [
            'filter' => FILTER_VALIDATE_INT,
            'options' => ['min_range' => UserRanks::PLAYER, 'max_range' => UserRanks::ADMIN],
        ],
    ];

    /**
     * Current user data
     *
     * @var array
     */
    private $user;

    /**
     * Contains the alert string
     *
     * @var string
     */
    private $alert = '';

    /**
     * Contains the current setting
     *
     * @var integer
     */
    private $user_level = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        AdministrationLib::checkSession();

        // load Language
        parent::loadLang(['adm/global', 'adm/statistics']);

        // set data
        $this->user = $this->getUserData();

        // Check if the user is allowed to access
        if (AdministrationLib::authorization($this->user['user_authlevel'], 'config_game') != 1) {
            AdministrationLib::noAccessMessage($this->langs->line('no_permissions'));
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
        $data = filter_input_array(INPUT_POST, self::STATISTICS_SETTINGS);

        if ($data) {
            $data = array_diff($data, [null, false]);

            foreach ($data as $option => $value) {
                FunctionsLib::updateConfig($option, $value);
            }

            $this->alert = AdministrationLib::saveMessage('ok', $this->langs->line('cs_all_ok_message'));
        }
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage(): void
    {
        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/statistics_view',
                array_merge(
                    $this->langs->language,
                    $this->getStatisticsSettings(),
                    $this->userLevels(),
                    [
                        'alert' => $this->alert ?? '',
                    ]
                )
            )
        );
    }

    /**
     * Get statistics settings
     *
     * @return void
     */
    private function getStatisticsSettings(): array
    {
        return array_filter(
            FunctionsLib::readConfig('', true),
            function ($value, $key) {
                if ($key == 'stat_admin_level') {
                    $this->user_level = $value;
                }

                return array_key_exists($key, self::STATISTICS_SETTINGS);
            },
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * Build the user level block
     *
     * @return void
     */
    private function userLevels()
    {
        $user_levels = [];
        $ranks = [
            UserRanks::PLAYER,
            UserRanks::GO,
            UserRanks::SGO,
            UserRanks::ADMIN,
        ];

        foreach ($ranks as $rank_id) {
            $user_levels[] = [
                'id' => $rank_id,
                'sel' => ($this->user_level == $rank_id ? 'selected="selected"' : ''),
                'name' => $this->langs->language['user_level'][$rank_id],
            ];
        }

        return [
            'user_levels' => $user_levels,
        ];
    }
}

/* end of statistics.php */
