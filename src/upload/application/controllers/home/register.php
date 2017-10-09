<?php

/**
 * Register Controller
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

use application\core\Controller;
use application\core\Database;
use application\libraries\FunctionsLib;
use application\libraries\PlanetLib;

/**
 * Register Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Register extends Controller
{
    private $creator;
    private $langs;
    private $current_user;
            
    /**
     * Contains the error 
     * 
     * @var int
     */
    private $error_id;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        $this->_db      = new Database();
        $this->langs    = parent::$lang;

        if (FunctionsLib::readConfig('reg_enable') == 1) {

            $this->creator      = new PlanetLib();
            $this->current_user = parent::$users;
            
            $this->buildPage();
        } else {

            die(FunctionsLib::message($this->langs['re_disabled'], 'index.php', '5', false, false));
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
    private function buildPage()
    {
        if ($_POST) {

            if (!$this->runValidations()) {

                if ($this->error_id != '') {
                    
                    $url    = 'index.php?character=' . $_POST['character'] . '&email=' . $_POST['email'] . '&error=' . $this->error_id;
                } else {
                    
                    $url    = 'index.php';
                }
                
                FunctionsLib::redirect($url);
            } else {

                $user_password      = $_POST['password'];
                $user_name          = $_POST['character'];
                $user_email         = $_POST['email'];
                $hashed_password    = sha1($user_password);

                $user_id            = $this->current_user->createUserWithOptions(
                    [
                        'user_name' => $this->_db->escapeValue(strip_tags($user_name)),
                        'user_email' => $this->_db->escapeValue($user_email),
                        'user_email_permanent' => $this->_db->escapeValue($user_email),
                        'user_ip_at_reg' => $_SERVER['REMOTE_ADDR'],
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                        'user_home_planet_id' => 0,
                        'user_register_time' => time(),
                        'user_password' => $hashed_password
                    ]
                );

                $last_galaxy = FunctionsLib::readConfig('lastsettedgalaxypos');
                $last_system = FunctionsLib::readConfig('lastsettedsystempos');
                $last_planet = FunctionsLib::readConfig('lastsettedplanetpos');

                while (!isset($newpos_checked)) {
                    for ($galaxy = $last_galaxy; $galaxy <= MAX_GALAXY_IN_WORLD; $galaxy++) {
                        for ($system = $last_system; $system <= MAX_SYSTEM_IN_GALAXY; $system++) {
                            for ($pos = $last_planet; $pos <= 4; $pos++) {
                                $planet = round(mt_rand(4, 12));

                                switch ($last_planet) {
                                    case 1:
                                        $last_planet += 1;

                                        break;

                                    case 2:
                                        $last_planet += 1;

                                        break;

                                    case 3:
                                        if ($last_system == MAX_SYSTEM_IN_GALAXY) {
                                            $last_galaxy += 1;
                                            $last_system = 1;
                                            $last_planet = 1;

                                            break;
                                        } else {
                                            $last_planet = 1;
                                        }

                                        $last_system += 1;

                                        break;
                                }
                                break;
                            }
                            break;
                        }
                        break;
                    }

                    $planet_row = $this->_db->queryFetch("SELECT *
                        FROM " . PLANETS . "
                        WHERE `planet_galaxy` = '" . $galaxy . "' AND
                                        `planet_system` = '" . $system . "' AND
                                        `planet_planet` = '" . $planet . "' LIMIT 1;"
                    );

                    if ($planet_row['id'] == '0') {
                        $newpos_checked = true;
                    }

                    if (!$planet_row) {
                        $this->creator->setNewPlanet($galaxy, $system, $planet, $user_id, '', true);
                        $newpos_checked = true;
                    }

                    if ($newpos_checked) {
                        FunctionsLib::updateConfig('lastsettedgalaxypos', $last_galaxy);
                        FunctionsLib::updateConfig('lastsettedsystempos', $last_system);
                        FunctionsLib::updateConfig('lastsettedplanetpos', $last_planet);
                    }
                }

                $this->_db->query(
                    "UPDATE " . USERS . " SET
                    `user_home_planet_id` = (SELECT `planet_id` 
                        FROM " . PLANETS . " 
                        WHERE `planet_user_id` = '" . $user_id . "' 
                        LIMIT 1),
                    `user_current_planet` = (SELECT `planet_id` 
                        FROM " . PLANETS . " 
                        WHERE `planet_user_id` = '" . $user_id . "' 
                        LIMIT 1),
                    `user_galaxy` = '" . $galaxy . "',
                    `user_system` = '" . $system . "',
                    `user_planet` = '" . $planet . "'
                     WHERE `user_id` = '" . $user_id . "' LIMIT 1;"
                );

                $from       = $this->langs['re_welcome_message_from'];
                $subject    = $this->langs['re_welcome_message_subject'];
                $message    = str_replace('%s', $user_name, $this->langs['re_welcome_message_content']);

                // Send Welcome Message to the user if the feature is enabled
                if (FunctionsLib::readConfig('reg_welcome_message')) {
                    FunctionsLib::sendMessage($user_id, 0, '', 5, $from, $subject, $message);
                }

                // Send Welcome Email to the user if the feature is enabled
                if (FunctionsLib::readConfig('reg_welcome_email')) {
                    $this->sendPassEmail($user_email, $user_name, $user_password);
                }

                // User login
                if (parent::$users->userLogin($user_id, $user_name, $hashed_password)) {
                    // Redirect to game
                    FunctionsLib::redirect('game.php?page=overview');
                }
            }
        }

        // If login fails
        FunctionsLib::redirect('index.php');
    }

    /**
     * sendPassEmail
     *
     * param1 $emailaddress
     * param2 $password
     *
     * return prepare the email and return mail status, delivered or not
     **/
    private function sendPassEmail($emailaddress, $user_name, $password)
    {
        $game_name = FunctionsLib::readConfig('game_name');

        $parse                          = $this->langs;
        $parse['user_name']             = $user_name;
        $parse['user_pass']             = $password;
        $parse['game_url']              = GAMEURL;
        $parse['re_mail_text_part1']    = str_replace('%s', $game_name, $this->langs['re_mail_text_part1']);
        $parse['re_mail_text_part7']    = str_replace('%s', $game_name, $this->langs['re_mail_text_part7']);

        $email = parent::$page->parseTemplate(parent::$page->getTemplate('home/email_template'), $parse);
        $status = FunctionsLib::sendEmail(
            $emailaddress,
            $this->langs['re_mail_register_at'] . FunctionsLib::readConfig('game_name'),
            $email,
            [
                'mail' => FunctionsLib::readConfig('admin_email'),
                'name' => $game_name
            ],
            'html'
        );

        return $status;
    }

    /**
     * runValidations
     * param
     * return run validations and return bool result
     * */
    private function runValidations()
    {
        $errors = 0;

        if (!FunctionsLib::validEmail($_POST['email'])) {
            $errors++;
        }

        if (!$_POST['character']) {
            $errors++;
        }

        if (strlen($_POST['password']) < 8) {
            $errors++;
        }

        if (preg_match("/[^A-z0-9_\-]/", $_POST['character']) == 1) {
            $errors++;
        }

        if ($_POST['agb'] != 'on') {
            $errors++;
        }

        if ($this->checkUser()) {
            $errors++;
            $this->error_id = 1;
        }

        if ($this->checkEmail()) {
            $errors++;
            $this->error_id = 2;
        }

        if ($errors > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * checkUser
     *
     * @return boolean
     * */
    private function checkUser()
    {
        return $this->_db->queryFetch(
            "SELECT `user_name`
            FROM " . USERS . "
            WHERE `user_name` = '" . $this->_db->escapeValue($_POST['character']) . "'
            LIMIT 1;"
        );
    }

    /**
     * checkEmail()
     *
     * @return boolean
     **/
    private function checkEmail()
    {
        return $this->_db->queryFetch(
            "SELECT `user_email`
            FROM " . USERS . "
            WHERE `user_email` = '" . $this->_db->escapeValue($_POST['email']) . "'
            LIMIT 1;"
        );
    }
}

/* end of register.php */
