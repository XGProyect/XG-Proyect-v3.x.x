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

use application\core\XGPCore;
use application\libraries\adm\AdministrationLib;
use application\libraries\FunctionsLib;

/**
 * Encrypter Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Encrypter extends XGPCore
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
        parent::$users->check_session();

        $this->langs        = parent::$lang;
        $this->current_user = parent::$users->get_user_data();

        // Check if the user is allowed to access
        if (AdministrationLib::have_access($this->current_user['user_authlevel'])
            && AdministrationLib::authorization($this->current_user['user_authlevel'], 'use_tools') == 1) {

            $this->buildPage();
        } else {

            die(FunctionsLib::message($this->langs['ge_no_permissions']));
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
            parent::$page->parse_template(parent::$page->get_template('adm/encrypter_view'), $parse)
        );
    }
}

/* end of encrypter.php */
