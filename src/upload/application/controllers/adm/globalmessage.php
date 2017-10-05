<?php
/**
 * Globalmessage Controller
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

use application\core\Database;
use application\core\XGPCore;
use application\libraries\adm\AdministrationLib;
use application\libraries\FunctionsLib;

/**
 * Globalmessage Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Globalmessage extends XGPCore
{
    private $_lang;
    private $_current_user;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        AdministrationLib::checkSession();

        $this->_db = new Database();
        $this->_lang = parent::$lang;
        $this->_current_user = parent::$users->getUserData();

        // Check if the user is allowed to access
        if (AdministrationLib::haveAccess($this->_current_user['user_authlevel']) && AdministrationLib::authorization($this->_current_user['user_authlevel'], 'use_tools') == 1) {
            $this->build_page();
        } else {
            die(AdministrationLib::noAccessMessage($this->_lang['ge_no_permissions']));
        }
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
        $parse['js_path'] = XGP_ROOT . JS_PATH;

        if (isset($_POST) && $_POST && $_GET['mode'] == "change") {
            $info = array(
                1 => array('color' => 'yellow'),
                2 => array('color' => 'skyblue'),
                3 => array('color' => 'red'),
            );

            $color = $info[$this->_current_user['user_authlevel']]['color'];
            $level = $this->_lang['user_level'][$this->_current_user['user_authlevel']];

            if (( isset($_POST['tresc']) && $_POST['tresc'] != '' ) && ( isset($_POST['temat']) && $_POST['temat'] != '' ) && ( isset($_POST['message']) or isset($_POST['mail']) )) {
                $sq = $this->_db->query("SELECT `user_id` , `user_name`, `user_email`
														FROM " . USERS . "");

                if (isset($_POST['message'])) {
                    $time = time();
                    $from = '<font color="' . $color . '">' . $level . ' ' . $this->_current_user['user_name'] . '</font>';
                    $subject = '<font color="' . $color . '">' . $_POST['temat'] . '</font>';
                    $message = '<font color="' . $color . '"><b>' . $_POST['tresc'] . '</b></font>';

                    while ($u = $this->_db->fetchArray($sq)) {
                        FunctionsLib::sendMessage($u['user_id'], $this->_current_user['user_id'], $time, 5, $from, $subject, $message);
                        $_POST['tresc'] = str_replace(":name:", $u['user_name'], $_POST['tresc']);
                    }
                }

                if (isset($_POST['mail'])) {
                    $i      = 0;
                    $from   = [
                        'mail' => FunctionsLib::readConfig('admin_email'),
                        'name' => FunctionsLib::readConfig('game_name')
                    ];
                    
                    while ($u = $this->_db->fetchArray($sq)) {
                        
                        FunctionsLib::sendEmail(
                            $u['user_email'],
                            $_POST['temat'],
                            $_POST['tresc'],
                            $from
                        );

                        // 20 per row
                        if ($i % 20 == 0) {
                            sleep(1); // wait, prevent flooding
                        }

                        $i++;
                    }
                }

                $parse['alert'] = AdministrationLib::saveMessage('ok', $this->_lang['ma_message_sended']);
            } else {
                $parse['alert'] = AdministrationLib::saveMessage('warning', $this->_lang['ma_subject_needed']);
            }

            ;
        }

        parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate('adm/global_message_view'), $parse));
    }
}

/* end of globalmessage.php */
