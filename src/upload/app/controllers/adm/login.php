<?php declare (strict_types = 1);

/**
 * Login Controller
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\controllers\adm;

use App\core\BaseController;
use App\libraries\adm\AdministrationLib as Administration;
use App\libraries\Functions;

/**
 * Login Class
 */
class Login extends BaseController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Model
        parent::loadModel('adm/login');

        // load Language
        parent::loadLang(['adm/login']);
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
     * Run an action
     *
     * @return void
     */
    private function runAction()
    {
        $login_data = filter_input_array(INPUT_POST, [
            'inputEmail' => FILTER_VALIDATE_EMAIL,
            'inputPassword' => FILTER_SANITIZE_STRING,
        ]);

        if ($login_data) {
            $login = $this->Login_Model->getLoginData($login_data['inputEmail']);

            if ($login) {
                if (password_verify($login_data['inputPassword'], $login['user_password'])
                    && Administration::adminLogin($login['user_id'], $login['user_password'])) {
                    $redirect = filter_input(INPUT_GET, 'redirect', FILTER_SANITIZE_STRING) ?? 'home';

                    if ($redirect == '') {
                        $redirect = 'home';
                    }

                    // Redirect to panel home
                    Functions::redirect(SYSTEM_ROOT . 'admin.php?page=' . $redirect);
                }
            }

            // If login fails
            Functions::redirect(SYSTEM_ROOT . 'admin.php?page=login&error=1');
        }
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage(): void
    {
        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/login_view',
                array_merge(
                    $this->langs->language,
                    [
                        'alert' => $this->getAlert(),
                        'redirect' => filter_input(INPUT_GET, 'redirect', FILTER_SANITIZE_STRING),
                    ]
                )
            ),
            false,
            false,
            false
        );
    }

    /**
     * Get the alert view
     *
     * @return string
     */
    private function getAlert(): string
    {
        $error = filter_input(INPUT_GET, 'error', FILTER_VALIDATE_INT);

        if ($error == 1) {
            return Administration::saveMessage('error', $this->langs->line('lg_error_wrong_data'), false);
        }

        return '';
    }
}
