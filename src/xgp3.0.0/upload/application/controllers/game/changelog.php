<?php

/**
 * Changelog Controller.
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

define('IN_CHANGELOG', true);

/**
 * Changelog Class.
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
class Changelog extends XGPCore
{
    const MODULE_ID = 0;

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
        $template = parent::$page->get_template('changelog/changelog_table');
        $body     = '';

        foreach ($this->_lang['changelog'] as $version => $description) {
            $parse['version_number'] = $version;
            $parse['description']    = nl2br($description);

            $body .= parent::$page->parse_template($template, $parse);
        }

        $parse         = $this->_lang;
        $parse['body'] = $body;

        parent::$page->display(parent::$page->parse_template(parent::$page->get_template('changelog/changelog_body'), $parse));
    }
}

/* end of changelog.php */
