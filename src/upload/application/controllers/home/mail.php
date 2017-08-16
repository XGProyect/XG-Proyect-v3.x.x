<?php

/**
 * Mail Controller
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

namespace application\controllers\home;

use application\core\XGPCore;
use application\libraries\FunctionsLib;

/**
 * Recoverpassword Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Mail extends XGPCore
{
    /**
     *
     * @var array
     */
    private $langs;

    /**
     * __construct
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->langs    = parent::$lang;
        
        $this->buildPage();
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
        $parse      = $this->langs;
        $game_name  = FunctionsLib::readConfig('game_name');

        $parse['game_name']         = $game_name;
        $parse['lp_send_pwd_title'] = strtr($this->langs['lp_send_pwd_title'], ['%s' => $game_name]);
        $parse['display']           = 'display: none';
        $parse['error_msg']         = '';
        $parse['css_path']          = XGP_ROOT . CSS_PATH . 'home/';
        
        if ($_POST) {

            $parse['display']   = 'display: block';
            
            if ($this->processRequest($_POST['email'])) {

                $parse['error_msg'] = $this->langs['lp_sent'];
            } else {

                $parse['error_msg'] = $this->langs['lp_error'];
            } 
        }

        parent::$page->display(
            parent::$page->parseTemplate(parent::$page->getTemplate('home/mail_view'), $parse),
            false,
            '',
            false
        );
    }

    /**
     * generatePassword
     * 
     * @return string
     */
    private function generatePassword()
    {
        $characters = "aazertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN1234567890";
        $count = strlen($characters);
        $new_pass = "";
        $lenght = 6;
        srand((double) microtime() * 1000000);

        for ($i = 0; $i < $lenght; $i++) {
            $character_boucle = mt_rand(0, $count - 1);
            $new_pass = $new_pass . substr($characters, $character_boucle, 1);
        }

        return $new_pass;
    }

    /**
     * processRequest
     * 
     * @param string $mail E-Mail
     * 
     * @return void
     */
    private function processRequest($mail)
    {
        $ExistMail  = parent::$db->queryFetch(
            "SELECT `user_name`
            FROM " . USERS . "
            WHERE `user_email` = '" . parent::$db->escapeValue($mail) . "' 
            LIMIT 1;"
        );

        if (empty($ExistMail['user_name'])) {

            return false;
        } else {
            $new_password = $this->sendPassEmail($mail, $ExistMail['user_name']);

            parent::$db->query(
                "UPDATE " . USERS . " SET
                `user_password` ='" . sha1($new_password) . "'
                WHERE `user_email`='" . parent::$db->escapeValue($mail) . "' 
                LIMIT 1;"
            );
            
            return true;
        }
    }

    /**
     * sendPassEmail
     * 
     * @param string $emailaddress Email Address
     * @param string $UserName     User Name
     * 
     * @return string
     */
    private function sendPassEmail($emailaddress, $UserName)
    {
        $game_name = FunctionsLib::readConfig('game_name');

        $parse                          = $this->langs;
        $parse['user_name']             = $UserName;
        $parse['user_pass']             = $this->generatePassword();
        $parse['game_url']              = GAMEURL;
        $parse['re_mail_text_part1']    = str_replace('%s', $game_name, $this->langs['re_mail_text_part1']);
        $parse['re_mail_text_part7']    = str_replace('%s', $game_name, $this->langs['re_mail_text_part7']);

        $email  = parent::$page->parseTemplate(
            parent::$page->getTemplate('home/recover_password_email_template_view'), $parse
        );

        FunctionsLib::sendEmail(
            $emailaddress,
            $this->langs['lp_mail_title'],
            $email,
            [
                'mail' => FunctionsLib::readConfig('admin_email'),
                'name' => $game_name
            ]
        );

        return $parse['user_pass'];
    }
}

/* end of recoverpassword.php */
