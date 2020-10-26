<?php
/**
 * Home Controller
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
use application\libraries\FunctionsLib as Functions;

/**
 * Home Class
 */
class Home extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // load Model
        parent::loadModel('home/home');

        // load Language
        parent::loadLang('home/home');

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
    private function runAction(): void
    {
        $login_data = filter_input_array(INPUT_POST, [
            'login' => FILTER_VALIDATE_EMAIL,
            'pass' => FILTER_SANITIZE_STRING,
        ]);

        if ($login_data) {
            $login = $this->Home_Model->getUserWithProvidedCredentials($login_data['login']);

            if (isset($login) && password_verify($login_data['pass'], $login['user_password'])) {
                if (isset($login['banned_longer']) && $login['banned_longer'] <= time()) {
                    $this->Home_Model->removeBan($login['user_name']);
                }

                if (parent::$users->userLogin($login['user_id'], $login['user_password'])) {
                    $this->Home_Model->setUserHomeCurrentPlanet($login['user_id']);

                    // redirect to game
                    Functions::redirect('game.php?page=overview');
                }
            }

            // if login failed
            Functions::redirect('index.php');
        }
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage(): void
    {
        parent::$page->display(
            $this->getTemplate()->set(
                'home/index_body',
                array_merge(
                    $this->langs->language,
                    $this->getPageData()
                )
            ),
            false,
            '',
            false
        );
    }

    /**
     * Get the page data to fully parse it
     *
     * @return array
     */
    private function getPageData(): array
    {
        return [
            'servername' => strtr($this->langs->line('hm_title'), ['%s' => Functions::readConfig('game_name')]),
            'css_path' => CSS_PATH . 'home/',
            'js_path' => JS_PATH . 'home/',
            'game_logo' => Functions::readConfig('game_logo'),
            'extra_js_error' => $this->getErrors(),
            'img_path' => IMG_PATH . 'home/',
            'base_path' => BASE_PATH,
            'user_name' => isset($_GET['character']) ? $_GET['character'] : '',
            'user_email' => isset($_GET['email']) ? $_GET['email'] : '',
            'forum_url' => Functions::readConfig('forum_url'),
            'version' => SYSTEM_VERSION,
            'year' => date('Y'),
        ];
    }

    /**
     * Get the error data
     *
     * @return string
     */
    private function getErrors(): string
    {
        $errors = filter_input(INPUT_GET, 'error', FILTER_VALIDATE_INT, [
            'options' => [
                'default' => 0,
                'min_range' => 1,
                'max_range' => 2,
            ],
        ]);

        switch ($errors) {
            case 1:
                $div_id = '#username';
                $message = $this->langs->line('hm_username_not_available');
                break;

            case 2:
                $div_id = '#email';
                $message = $this->langs->line('hm_email_not_available');
                break;

            case 0:
            default:
                $div_id = '';
                $message = '';
                break;
        }

        return '$.validationEngine.buildPrompt("' . $div_id . '", "' . $message . '", "error");';
    }
}

/* end of home.php */
