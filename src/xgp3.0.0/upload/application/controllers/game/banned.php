<?php

/**
 * Banned Controller.
 *
 * PHP Version 5.5+
 *
 * @category Controller
 *
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 *
 * @link     http://www.xgproyect.org
 *
 * @version  3.0.0
 */

namespace application\controllers\game;

use application\core\XGPCore;
use application\libraries\FunctionsLib;

/**
 * Banned Class.
 *
 * @category Classes
 *
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 *
 * @link     http://www.xgproyect.org
 *
 * @version  3.0.0
 */
class Banned extends XGPCore
{
    const MODULE_ID = 22;

    private $_lang;

    /**
     * __construct().
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->check_session();

        // Check module access
        FunctionsLib::module_message(FunctionsLib::is_module_accesible(self::MODULE_ID));

        $this->_lang = parent::$lang;

        $this->build_page();
    }

    /**
     * method __destruct
     * param
     * return close db connection.
     */
    public function __destruct()
    {
        parent::$db->closeConnection();
    }

    /**
     * method build_page
     * param
     * return main method, loads everything.
     */
    private function build_page()
    {
        $parse = $this->_lang;
        $query = parent::$db->query('SELECT *
											FROM ' . BANNED . '
											ORDER BY `banned_id`;');

        $i            = 0;
        $sub_template = parent::$page->get_template('banned/banned_row');
        $body         = '';

        while ($u = parent::$db->fetchArray($query)) {
            $parse['player'] = $u[1];
            $parse['reason'] = $u[2];
            $parse['since']  = date(FunctionsLib::read_config('date_format_extended'), $u[4]);
            $parse['until']  = date(FunctionsLib::read_config('date_format_extended'), $u[5]);
            $parse['by']     = $u[6];

            ++$i;

            $body .= parent::$page->parse_template($sub_template, $parse);
        }

        if ($i == 0) {
            $parse['banned_msg'] = $this->_lang['bn_no_players_banned'];
        } else {
            $parse['banned_msg'] = $this->_lang['bn_exists'] . $i . $this->_lang['bn_players_banned'];
        }

        $parse['banned_players'] = $body;

        parent::$page->display(parent::$page->parse_template(parent::$page->get_template('banned/banned_body'), $parse));
    }
}

/* end of banned.php */
