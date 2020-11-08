<?php
/**
 * Banned Controller
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
use application\helpers\UrlHelper;
use application\libraries\FunctionsLib;
use application\libraries\TimingLibrary as Timing;

/**
 * Banned Class
 */
class Banned extends Controller
{
    /**
     * The module ID
     *
     * @var int
     */
    const MODULE_ID = 22;

    /**
     * Count of banned players
     *
     * @var integer
     */
    private $bans_count = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        // load Model
        parent::loadModel('game/banned');

        // load Language
        parent::loadLang('game/banned');

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
        parent::$page->display(
            $this->getTemplate()->set(
                'game/banned_view',
                array_merge(
                    $this->langs->language,
                    [
                        'banned_players' => $this->buildBannedPlayersList(),
                        'banned_msg' => $this->getBannedAmountLabel(),
                    ]
                )
            )
        );
    }

    /**
     * Return the list of banned players
     *
     * @return array
     */
    private function buildBannedPlayersList(): array
    {
        $bans = $this->Banned_Model->getBannedUsers();
        $list_of_bans = [];

        if (!empty($bans)) {
            foreach ($bans as $u) {
                $this->bans_count++;

                $list_of_bans[] = [
                    'player' => $u['banned_who'],
                    'reason' => $u['banned_theme'],
                    'since' => Timing::formatExtendedDate($u['banned_time']),
                    'until' => Timing::formatExtendedDate($u['banned_longer']),
                    'by' => UrlHelper::setUrl(
                        'mailto:' . $u['banned_email'],
                        $u['banned_author'],
                        $u['banned_author']
                    ),
                ];
            }
        }

        return $list_of_bans;
    }

    /**
     * Get banned amount label
     *
     * @return string
     */
    private function getBannedAmountLabel(): string
    {
        if ($this->bans_count > 0) {
            return strtr($this->langs->line('bn_exists_players_banned'), ['%s' => $this->bans_count]);
        }

        return $this->langs->line('bn_no_players_banned');
    }
}
