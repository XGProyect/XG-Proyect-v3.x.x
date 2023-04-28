<?php

namespace App\Http\Controllers\Home;

use App\Core\BaseController;
use App\Libraries\Functions;
use App\Models\Home\Register;

class RegisterController extends BaseController
{
    private array $available_coords = [];
    private int $error_id;
    private Register $registerModel;

    public function __construct()
    {
        parent::__construct();

        // load Language
        parent::loadLang(['home/register']);

        $this->registerModel = new Register();
    }

    public function index(): void
    {
        if (Functions::readConfig('reg_enable') != 1) {
            die(Functions::message($this->langs->line('re_disabled'), 'index.php', '5', false, false));
        }

        // build the page
        $this->buildPage();
    }

    private function buildPage(): void
    {
        if ($_POST) {
            $user_name = $_POST['character'];
            $user_email = $_POST['email'];
            $user_password = $_POST['password'];

            if (!$this->runValidations()) {
                if ($this->error_id != '') {
                    $url = 'index.php?character=' . $user_name . '&email=' . $user_email . '&error=' . $this->error_id;
                } else {
                    $url = 'index.php';
                }

                Functions::redirect(SYSTEM_ROOT . $url);
            } else {
                // start user creation
                $this->calculateNewPlanetPosition();

                $this->registerModel->createNewUser(
                    $this->userLibrary,
                    [
                        'new_user_name' => $user_name,
                        'new_user_email' => $user_email,
                        'new_user_password' => $user_password,
                    ],
                    $this->available_coords
                );

                $new_user = $this->registerModel->getNewUserData();

                // Send Welcome Message to the user if the feature is enabled
                if (Functions::readConfig('reg_welcome_message')) {
                    Functions::sendMessage(
                        $new_user['user_id'],
                        0,
                        '',
                        5,
                        $this->langs->line('re_welcome_message_from'),
                        $this->langs->line('re_welcome_message_subject'),
                        str_replace('%s', $new_user['user_name'], $this->langs->line('re_welcome_message_content'))
                    );
                }

                // Send Welcome Email to the user if the feature is enabled
                if (Functions::readConfig('reg_welcome_email')) {
                    $this->sendPassEmail($new_user['user_email'], $new_user['user_name'], $user_password);
                }

                // User login
                if ($this->userLibrary->userLogin($new_user['user_id'], $new_user['user_hashed_password'])) {
                    // Redirect to game
                    Functions::redirect(SYSTEM_ROOT . 'game.php?page=overview');
                }
            }
        }

        // If login fails
        Functions::redirect('index.php');
    }

    /**
     * Send the password by email
     *
     * @param string $email_address
     * @param string $user_name
     * @param string $password
     * @return void
     */
    private function sendPassEmail(string $email_address, string $user_name, string $password): void
    {
        $game_name = Functions::readConfig('game_name');

        $parse = $this->langs->language;
        $parse['user_name'] = $user_name;
        $parse['user_pass'] = $password;
        $parse['game_url'] = GAMEURL;
        $parse['re_mail_text_part1'] = str_replace('%s', $game_name, $this->langs->line('re_mail_text_part1'));
        $parse['re_mail_text_part7'] = str_replace('%s', $game_name, $this->langs->line('re_mail_text_part7'));

        $email = $this->template->set(
            'home/welcome_email_template_view',
            $parse
        );

        Functions::sendEmail(
            $email_address,
            $this->langs->line('re_mail_register_at') . Functions::readConfig('game_name'),
            $email,
            [
                'mail' => Functions::readConfig('admin_email'),
                'name' => $game_name,
            ],
            'html'
        );
    }

    /**
     * Run validations for the registration fields
     *
     * @return boolean
     */
    private function runValidations(): bool
    {
        $errors = 0;

        if (!Functions::validEmail($_POST['email'])) {
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

        if ($this->registerModel->checkUser($_POST['character'])) {
            $errors++;
            $this->error_id = 1;
        }

        if ($this->registerModel->checkEmail($_POST['email'])) {
            $errors++;
            $this->error_id = 2;
        }

        return ($errors <= 0);
    }

    private function calculateNewPlanetPosition(): void
    {
        $this->isPlanetFree(
            Functions::readConfig('lastsettedgalaxypos'),
            Functions::readConfig('lastsettedsystempos'),
            max(Functions::readConfig('lastsettedplanetpos'), 4) // new users need to start at position 4
        );
    }

    private function isPlanetFree($galaxy, $system, $position)
    {
        // Check if the planet is free
        $isFree = !$this->registerModel->checkIfPlanetExists($galaxy, $system, $position);
        if ($isFree) {
            Functions::updateConfig('lastsettedgalaxypos', $galaxy);
            Functions::updateConfig('lastsettedsystempos', $system);
            Functions::updateConfig('lastsettedplanetpos', $position);

            $this->available_coords = [
                'galaxy' => $galaxy,
                'system' => $system,
                'planet' => $position,
            ];

            return true;
        }

        // If the planet is not free, try the next position
        if ($position < 12) {
            return $this->isPlanetFree($galaxy, $system, $position + PLANET_SEPARATION_FACTOR);
        }

        // If we've tried all positions in this system, try the next system
        if ($system < MAX_SYSTEM_IN_GALAXY) {
            return $this->isPlanetFree($galaxy, $system + SYSTEM_SEPARATION_FACTOR, 4);
        }

        // If we've tried all systems in this galaxy, try the next galaxy
        if ($galaxy < MAX_GALAXY_IN_WORLD) {
            return $this->isPlanetFree($galaxy + GALAXY_SEPARATION_FACTOR, 1, 4);
        }

        // If we've tried all galaxies and haven't found a free planet, restart the search
        return $this->isPlanetFree(1, 1, 4);
    }
}
