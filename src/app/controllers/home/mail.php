<?php

declare(strict_types=1);

/**
 * Mail Controller
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */

namespace App\controllers\home;

use App\core\BaseController;
use App\libraries\Functions;

/**
 * Mail Class
 */
class Mail extends BaseController
{
    /**
     * Contains the game name
     *
     * @var string
     */
    private $game_name = '';

    /**
     * Contains the send email result message
     *
     * @var string
     */
    private $send_result = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // load Model
        parent::loadModel('home/mail');

        // load Language
        parent::loadLang(['home/mail']);

        // init some recurrent data
        $this->setUpData();
    }

    /**
     * Users land here
     *
     * @return void
     */
    public function index(): void
    {
        // time to do something
        $this->runAction();

        // build the page
        $this->buildPage();
    }

    /**
     * Set up some data that will be accessed many times
     *
     * @return void
     */
    private function setUpData(): void
    {
        $this->game_name = Functions::readConfig('game_name');
    }

    /**
     * Run an action
     *
     * @return void
     */
    private function runAction(): void
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

        if ($email) {
            if ($this->processRequest($email)) {
                $this->send_result = $this->langs->line('ma_sent');
            } else {
                $this->send_result = $this->langs->line('ma_error');
            }
        }
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage(): void
    {
        $this->page->display(
            $this->template->set(
                'home/mail_view',
                array_merge(
                    $this->langs->language,
                    [
                        'game_name' => $this->game_name,
                        'css_path' => CSS_PATH . 'home/',
                        'display' => $this->send_result != '' ? 'display: block' : 'display: none',
                        'error_msg' => $this->send_result,
                        'ma_send_pwd_title' => strtr($this->langs->line('ma_send_pwd_title'), ['%s' => $this->game_name]),
                    ]
                )
            ),
            false,
            '',
            false
        );
    }

    /**
     * Process the request
     *
     * @param string $email
     * @return boolean
     */
    private function processRequest(string $email): bool
    {
        $user_name = $this->Mail_Model->getEmailUsername($email);

        if ($user_name) {
            $new_password = Functions::generatePassword();

            if ($this->sendPassEmail($email, $new_password)) {
                $this->Mail_Model->setUserNewPassword($email, $new_password);

                return true;
            }
        }

        return false;
    }

    /**
     * Send email with the new password
     *
     * @param string $email
     * @param string $new_password
     * @return boolean
     */
    private function sendPassEmail(string $email, string $new_password): bool
    {
        $email_template = $this->template->set(
            'home/recover_password_email_template_view',
            array_merge(
                $this->langs->language,
                [
                    'user_pass' => $new_password,
                    'game_url' => GAMEURL,
                    'ma_mail_text_part5' => strtr($this->langs->line('ma_mail_text_part5'), ['%s' => $this->game_name]),
                ]
            )
        );

        return Functions::sendEmail(
            $email,
            $this->langs->line('ma_mail_title'),
            $email_template,
            [
                'mail' => Functions::readConfig('admin_email'),
                'name' => $this->game_name,
            ],
            'html'
        );
    }
}
