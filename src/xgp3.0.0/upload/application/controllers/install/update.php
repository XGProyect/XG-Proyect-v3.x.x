<?php

/**
 * Update Controller.
 *
 * PHP Version 5.5+
 *
 * @category Controllers
 *
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 *
 * @link     http://www.xgproyect.org
 *
 * @version  3.0.0
 */

namespace application\controllers\install;

use application\core\XGPCore;
use application\libraries\FunctionsLib;

/**
 * Update Class.
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
class Update extends XGPCore
{
    private $langs;

    /**
     * __construct().
     */
    public function __construct()
    {
        parent::__construct();

        $this->langs = parent::$lang;

        if ($this->serverRequirementes()) {
            $this->buildPage();
        } else {
            die(FunctionsLib::message($this->langs['ins_no_server_requirements']));
        }
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
    private function buildPage()
    {
        parent::$page->display(
            parent::$page->parse_template(parent::$page->get_template('install/in_update'), $this->langs)
        );
    }

    /**
     * method server_requirementes
     * param
     * return true if the required server requirements are met.
     */
    private function serverRequirementes()
    {
        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            return false;
        } else {
            return true;
        }
    }
}

/* end of update.php */
