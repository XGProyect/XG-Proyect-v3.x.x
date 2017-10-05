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

use application\core\Database;
use application\core\XGPCore;
use application\libraries\FunctionsLib;

/**
 * Banned Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Banned extends XGPCore
{
    const MODULE_ID = 22;

    private $_lang;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        $this->_db = new Database();
        $this->_lang = parent::$lang;

        $this->build_page();
    }

    /**
     * method __destruct
     * param
     * return close db connection
     */
    public function __destruct()
    {
        $this->_db->closeConnection();
    }

    /**
     * method build_page
     * param
     * return main method, loads everything
     */
    private function build_page()
    {
        $parse = $this->_lang;
        $query = $this->_db->query("SELECT *
											FROM " . BANNED . "
											ORDER BY `banned_id`;");

        $i = 0;
        $sub_template = parent::$page->getTemplate('banned/banned_row');
        $body = '';

        while ($u = $this->_db->fetchArray($query)) {
            $parse['player'] = $u[1];
            $parse['reason'] = $u[2];
            $parse['since'] = date(FunctionsLib::readConfig('date_format_extended'), $u[4]);
            $parse['until'] = date(FunctionsLib::readConfig('date_format_extended'), $u[5]);
            $parse['by'] = $u[6];

            $i++;

            $body .= parent::$page->parseTemplate($sub_template, $parse);
        }

        if ($i == 0) {
            $parse['banned_msg'] = $this->_lang['bn_no_players_banned'];
        } else {
            $parse['banned_msg'] = $this->_lang['bn_exists'] . $i . $this->_lang['bn_players_banned'];
        }

        $parse['banned_players'] = $body;

        parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate('banned/banned_body'), $parse));
    }
}

/* end of banned.php */
