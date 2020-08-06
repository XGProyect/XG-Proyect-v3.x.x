<?php
/**
 * Mail Controller
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
namespace application\controllers\home;

use application\core\Controller;
use application\core\Database;
use application\libraries\FunctionsLib;

/**
 * Recoverpassword Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Mail extends Controller
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

        $this->_db = new Database();
        $this->langs = parent::$lang;

        $this->buildPage();
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->_db->closeConnection();
    }

    /**
     * buildPage
     *
     * @return void
     */
    private function buildPage()
    {
        $parse = $this->langs;
        $game_name = FunctionsLib::readConfig('game_name');

        $parse['game_name'] = $game_name;
        $parse['lp_send_pwd_title'] = strtr($this->langs['lp_send_pwd_title'], ['%s' => $game_name]);
        $parse['display'] = 'display: none';
        $parse['error_msg'] = '';
        $parse['css_path'] = CSS_PATH . 'home/';

        if ($_POST) {
            $parse['display'] = 'display: block';

            if ($this->processRequest($_POST['email'])) {
                $parse['error_msg'] = $this->langs['lp_sent'];
            } else {
                $parse['error_msg'] = $this->langs['lp_error'];
            }
        }

        parent::$page->display(
            $this->getTemplate()->set(
                'home/mail_view',
                $parse
            ),
            false,
            '',
            false
        );
    }

    /**
     * Generate a random string, using a cryptographically secure
     * pseudorandom number generator (random_int)
     *
     * For PHP 7, random_int is a PHP core function
     * For PHP 5.x, depends on https://github.com/paragonie/random_compat
     *
     * @param int $length      How many characters do we want?
     * @param string $keyspace A string of all possible characters to select from
     *
     * @return string
     *
     * @link https://stackoverflow.com/questions/6101956/generating-a-random-password-in-php/31284266#31284266
     */
    private function generatePassword(int $length, string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string
    {
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        if ($max < 1) {
            throw new Exception('$keyspace must be at least two characters long');
        }
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
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
        $ExistMail = $this->_db->queryFetch(
            "SELECT `user_name`
            FROM " . USERS . "
            WHERE `user_email` = '" . $this->_db->escapeValue($mail) . "'
            LIMIT 1;"
        );

        if (empty($ExistMail['user_name'])) {
            return false;
        } else {
            $new_password = $this->sendPassEmail($mail, $ExistMail['user_name']);

            $this->_db->query(
                "UPDATE " . USERS . " SET
                `user_password` ='" . sha1($new_password) . "'
                WHERE `user_email`='" . $this->_db->escapeValue($mail) . "'
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

        $parse = $this->langs;
        $parse['user_name'] = $UserName;
        $parse['user_pass'] = $this->generatePassword();
        $parse['game_url'] = GAMEURL;
        $parse['re_mail_text_part1'] = str_replace('%s', $game_name, $this->langs['re_mail_text_part1']);
        $parse['re_mail_text_part7'] = str_replace('%s', $game_name, $this->langs['re_mail_text_part7']);

        $email = $this->getTemplate()->set(
            'home/recover_password_email_template_view',
            $parse
        );

        FunctionsLib::sendEmail(
            $emailaddress,
            $this->langs['lp_mail_title'],
            $email,
            [
                'mail' => FunctionsLib::readConfig('admin_email'),
                'name' => $game_name,
            ]
        );

        return $parse['user_pass'];
    }
}

/* end of recoverpassword.php */
