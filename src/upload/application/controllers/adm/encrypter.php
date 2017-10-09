<?php
/**
 * Encrypter Controller
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

use application\core\Controller;
use application\libraries\adm\AdministrationLib;

/**
 * Encrypter Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Encrypter extends Controller
{
    private $langs;
    private $current_user;

    /**
     * __construct
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
        if (AdministrationLib::haveAccess($this->current_user['user_authlevel'])
            && AdministrationLib::authorization($this->current_user['user_authlevel'], 'use_tools') == 1) {

            $this->buildPage();
        } else {

            die(AdministrationLib::noAccessMessage($this->langs['ge_no_permissions']));
        }
    }

    /**
     * buildPage
     *
     * @return void
     */
    private function buildPage()
    {
        $parse              = $this->langs;
        $parse['uncrypted'] = '';
        $parse['encrypted'] = sha1('');

        if (isset($_POST['uncrypted']) && $_POST['uncrypted'] != '') {

            $parse['uncrypted'] = $_POST['uncrypted'];
            $parse['encrypted'] = sha1($_POST['encrypted']);
        }

        parent::$page->display(
            parent::$page->parseTemplate(parent::$page->getTemplate('adm/encrypter_view'), $parse)
        );
    }
}

/* end of encrypter.php */
