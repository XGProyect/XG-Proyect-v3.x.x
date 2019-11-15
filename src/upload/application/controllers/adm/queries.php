<?php
/**
 * Queries Controller
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
use application\libraries\adm\AdministrationLib;
use mysqli;

/**
 * Queries Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Queries extends Controller
{

    private $langs;
    private $current_user;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        AdministrationLib::checkSession();

        $this->langs = parent::$lang;
        $this->current_user = parent::$users->getUserData();

        // Check if the user is allowed to access
        if (AdministrationLib::haveAccess($this->current_user['user_authlevel'])
            && $this->current_user['user_authlevel'] == 3
            && ADMIN_ACCESS_QUERY === true) {
            $this->buildPage();
        } else {
            die(AdministrationLib::noAccessMessage($this->langs['ge_no_permissions']));
        }
    }

    /**
     * method build_page
     * param
     * return main method, loads everything
     */
    private function buildPage()
    {
        $parse = $this->langs;
        $query = isset($_POST['querie']) ? $_POST['querie'] : null;

        if (isset($_POST) && !empty($query)) {
            // clean
            $query = str_replace("\'", "'", str_replace('\"', '"', $query));

            // connect
            $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            // do
            if (!$connection->query($query)) {
                $parse['alert'] = AdministrationLib::saveMessage('error', $connection->error);
            } else {
                $parse['alert'] = AdministrationLib::saveMessage('ok', $this->langs['qe_succes']);
            }
        }

        parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate('adm/queries_view'), $parse));
    }
}

/* end of queries.php */
