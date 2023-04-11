<?php

declare(strict_types=1);

namespace App\Http\Controllers\Adm;

use App\Core\BaseController;
use App\Core\Enumerators\UserRanksEnumerator as UserRanks;
use App\Libraries\Adm\AdministrationLib as Administration;
use App\Libraries\Functions;

class StatisticsController extends BaseController
{
    public const STATISTICS_SETTINGS = [
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

    private string $alert = '';

    /**
     * Contains the current setting
     *
     * @var integer
     */
    private $user_level = 0;

    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Language
        parent::loadLang(['adm/global', 'adm/statistics']);
    }

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
        $data = filter_input_array(INPUT_POST, self::STATISTICS_SETTINGS);

        if ($data) {
            $data = array_diff($data, [null, false]);

            foreach ($data as $option => $value) {
                Functions::updateConfig($option, $value);
            }

            $this->alert = Administration::saveMessage('ok', $this->langs->line('cs_all_ok_message'));
        }
    }

    private function buildPage(): void
    {
        $this->page->displayAdmin(
            $this->template->set(
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
            Functions::readConfig('', true),
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
