<?php
/**
 * Update Controller
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

namespace application\controllers\adm;

use application\core\XGPCore;
use application\libraries\adm\AdministrationLib;

/**
 * Migrate Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Update extends XGPCore
{
    private $langs;
    private $current_user;

    /**
     * __construct()
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        AdministrationLib::checkSession();

        $this->langs        = parent::$lang;
        $this->current_user = parent::$users->getUserData();

        // Check if the user is allowed to access
        if (AdministrationLib::haveAccess(
            $this->current_user['user_authlevel']
        ) && $this->current_user['user_authlevel'] == 3) {

            $this->buildPage();
        } else {

            die(AdministrationLib::noAccessMessage($this->langs['ge_no_permissions']));
        }
    }

    /**
     * __destruct
     * 
     * @return void
     */
    public function __destruct()
    {
        parent::$db->closeConnection();
    }

    /**
     * build_page
     *
     * @return void
     */
    private function buildPage()
    {
        $parse  = $this->langs;

        parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate('adm/update_view'), $parse));
    }
}

/* end of update.php */
