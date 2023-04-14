<?php

declare(strict_types=1);

namespace App\Http\Controllers\Home;

use App\Core\BaseController;
use App\Libraries\Functions;
use App\Models\Home\Mail;

class MailController extends BaseController
{
    private string $game_name = '';
    private string $send_result = '';
    private Mail $mailModel;

    public function __construct()
    {
        parent::__construct();

        // load Language
        parent::loadLang(['home/mail']);

        $this->mailModel = new Mail();

        // init some recurrent data
        $this->setUpData();
    }

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
        $user_name = $this->mailModel->getEmailUsername($email);

        if ($user_name) {
            $new_password = Functions::generatePassword();

            if ($this->sendPassEmail($email, $new_password)) {
                $this->mailModel->setUserNewPassword($email, $new_password);

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
