<?php
/**
 * Banned Controller
 *
 * PHP Version 5.5+
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
use application\libraries\FunctionsLib;

/**
 * Banned Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Banned extends Controller
{

    /**
     * @var int Module ID
     */
    const MODULE_ID = 22;

    /**
     *
     * @var array Language data
     */
    private $_lang;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // load Model
        parent::loadModel('game/banned');

        $this->_lang = $this->getLang();

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

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
        $parse = $this->_lang;
        $result = $this->Banned_Model->getBannedUsers();

        $parse['banned_msg'] = $this->_lang['bn_no_players_banned'];
        $parse['banned_players'] = [];

        if (!empty($result)) {

            $body = [];
            $parse['player'] = '';
            $parse['reason'] = '';
            $parse['since'] = '';
            $parse['until'] = '';
            $parse['by'] = '';

            foreach ($result as $u) {

                $body[] = [
                    'player' => $u['banned_who'],
                    'reason' => $u['banned_theme'],
                    'since' => date(FunctionsLib::readConfig('date_format_extended'), $u['banned_time']),
                    'until' => date(FunctionsLib::readConfig('date_format_extended'), $u['banned_longer']),
                    'by' => FunctionsLib::setUrl('mailto:' . $u['banned_email'], $u['banned_author'], $u['banned_author'])
                ];
            }

            $parse['banned_players'] = $body;
        }

        if (count($result) > 0) {

            $parse['banned_msg'] = $this->_lang['bn_exists'] . $i . $this->_lang['bn_players_banned'];
        }

        parent::$page->display(
            $this->getTemplate()->set('banned/banned_view', $parse)
        );
    }
}

/* end of banned.php */
