<?php

/**
 * Globalmessage Controller.
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

namespace application\controllers\adm;

use application\core\XGPCore;
use application\libraries\adm\AdministrationLib;
use application\libraries\FunctionsLib;

/**
 * Globalmessage Class.
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
class Globalmessage extends XGPCore
{
    private $_lang;
    private $_current_user;

    /**
     * __construct().
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->check_session();

        $this->_lang         = parent::$lang;
        $this->_current_user = parent::$users->get_user_data();

        // Check if the user is allowed to access
        if (AdministrationLib::have_access($this->_current_user['user_authlevel']) && AdministrationLib::authorization($this->_current_user['user_authlevel'], 'use_tools') == 1) {
            $this->build_page();
        } else {
            die(FunctionsLib::message($this->_lang['ge_no_permissions']));
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
    private function build_page()
    {
        $parse            = $this->_lang;
        $parse['js_path'] = XGP_ROOT . JS_PATH;

        if (isset($_POST) && $_POST && $_GET['mode'] == 'change') {
            $info = array(
                                1 => array('color' => 'yellow'),
                                2 => array('color' => 'skyblue'),
                                3 => array('color' => 'red'),
                            );

            $color = $info[$this->_current_user['user_authlevel']]['color'];
            $level = $this->_lang['user_level'][$this->_current_user['user_authlevel']];

            if ((isset($_POST['tresc']) && $_POST['tresc'] != '') && (isset($_POST['temat']) && $_POST['temat'] != '') && (isset($_POST['message']) or isset($_POST['mail']))) {
                $sq = parent::$db->query('SELECT `user_id` , `user_name`, `user_email`
														FROM ' . USERS . '');

                if (isset($_POST['message'])) {
                    $time    = time();
                    $from    = '<font color="' . $color . '">' . $level . ' ' . $this->_current_user['user_name'] . '</font>';
                    $subject = '<font color="' . $color . '">' . $_POST['temat'] . '</font>';
                    $message = '<font color="' . $color . '"><b>' . FunctionsLib::format_text($_POST['tresc']) . '</b></font>';

                    while ($u = parent::$db->fetchArray($sq)) {
                        FunctionsLib::send_message($u['user_id'], $this->_current_user['user_id'], $time, 5, $from, $subject, $message);
                        $_POST['tresc'] = str_replace(':name:', $u['user_name'], $_POST['tresc']);
                    }
                }

                if (isset($_POST['mail'])) {
                    $i = 0;

                    while ($u = parent::$db->fetchArray($sq)) {
                        mail($u['user_email'], $_POST['temat'], $_POST['tresc']);

                        // 20 per row
                        if ($i % 20 == 0) {
                            sleep(1); // wait, prevent flooding
                        }

                        ++$i;
                    }
                }

                $parse['alert'] = AdministrationLib::save_message('ok', $this->_lang['ma_message_sended']);
            } else {
                $parse['alert'] = AdministrationLib::save_message('warning', $this->_lang['ma_subject_needed']);
            }

            ;
        }

        parent::$page->display(parent::$page->parse_template(parent::$page->get_template('adm/global_message_view'), $parse));
    }
}

/* end of globalmessage.php */
