<?php
/**
 * Register Controller
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
 * Register Class
 */
class Register extends BaseController
{
    /**
     * Contains the set of coords for an available position
     *
     * @var array
     */
    private $available_coords = [];

    /**
     * Contains the error
     *
     * @var int
     */
    private $error_id;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // load Model
        parent::loadModel('home/register');

        // load Language
        parent::loadLang(['home/register']);
    }

    /**
     * Users land here
     *
     * @return void
     */
    public function index(): void
    {
        if (Functions::readConfig('reg_enable') != 1) {
            die(Functions::message($this->langs->line('re_disabled'), 'index.php', '5', false, false));
        }

        // build the page
        $this->buildPage();
    }

    /**
     * Build the page
     *
     * @return void
     */
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

                $this->Register_Model->createNewUser(
                    parent::$users,
                    [
                        'new_user_name' => $user_name,
                        'new_user_email' => $user_email,
                        'new_user_password' => $user_password,
                    ],
                    $this->available_coords
                );

                $new_user = $this->Register_Model->getNewUserData();

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
                if (parent::$users->userLogin($new_user['user_id'], $new_user['user_hashed_password'])) {
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

        $email = $this->getTemplate()->set(
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

        if ($this->Register_Model->checkUser($_POST['character'])) {
            $errors++;
            $this->error_id = 1;
        }

        if ($this->Register_Model->checkEmail($_POST['email'])) {
            $errors++;
            $this->error_id = 2;
        }

        return ($errors <= 0);
    }

    /**
     * Determine what's going to be the position for the new planet
     *
     * @return void
     */
    private function calculateNewPlanetPosition(): void
    {
        $last_galaxy = Functions::readConfig('lastsettedgalaxypos');
        $last_system = Functions::readConfig('lastsettedsystempos');
        $last_planet = Functions::readConfig('lastsettedplanetpos');

        while (true) {
            for ($galaxy = $last_galaxy; $galaxy <= MAX_GALAXY_IN_WORLD; $galaxy++) {
                for ($system = $last_system; $system <= MAX_SYSTEM_IN_GALAXY; $system++) {
                    for ($pos = $last_planet; $pos <= 4; $pos++) {
                        $planet = mt_rand(4, 12);

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

            if (!$this->Register_Model->checkIfPlanetExists($galaxy, $system, $planet)) {
                Functions::updateConfig('lastsettedgalaxypos', $last_galaxy);
                Functions::updateConfig('lastsettedsystempos', $last_system);
                Functions::updateConfig('lastsettedplanetpos', $last_planet);

                $this->available_coords = [
                    'galaxy' => $galaxy,
                    'system' => $system,
                    'planet' => $planet,
                ];

                // break
                return;
            }
        }
    }
}
