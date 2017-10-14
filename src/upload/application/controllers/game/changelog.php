<?php
/**
 * Changelog Controller
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

define('IN_CHANGELOG', true);

/**
 * Change log Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Changelog extends Controller
{

    const MODULE_ID = 0;

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

        $this->_lang = parent::$lang;

        $this->build_page();
    }

    /**
     * method build_page
     * param
     * return main method, loads everything
     */
    private function build_page()
    {
        $template = parent::$page->getTemplate('changelog/changelog_table');
        $body = '';

        foreach ($this->_lang['changelog'] as $version => $description) {
            $parse['version_number'] = $version;
            $parse['description'] = nl2br($description);

            $body .= parent::$page->parseTemplate($template, $parse);
        }

        $parse = $this->_lang;
        $parse['body'] = $body;

        parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate('changelog/changelog_body'), $parse));
    }
}

/* end of changelog.php */
