<?php

namespace App\Http\Controllers\Home;

use App\Core\BaseController;
use App\Libraries\Functions;
use App\Models\Home\Home;

class HomeController extends BaseController
{
    private Home $homeModel;

    public function __construct()
    {
        parent::__construct();

        // load Language
        parent::loadLang(['home/home']);

        $this->homeModel = new Home();
    }

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
    private function runAction(): void
    {
        $loginData = filter_input_array(INPUT_POST, [
            'login' => FILTER_VALIDATE_EMAIL,
            'pass' => FILTER_UNSAFE_RAW,
        ]);

        if (!empty($loginData['login']) && !empty($loginData['pass'])) {
            $login = $this->homeModel->getUserWithProvidedCredentials($loginData['login']);

            if (isset($login) && password_verify($loginData['pass'], $login['user_password'])) {
                if (isset($login['banned_longer']) && $login['banned_longer'] <= time()) {
                    $this->homeModel->removeBan($login['user_name']);
                }

                if ($this->userLibrary->userLogin($login['user_id'], $login['user_password'])) {
                    $this->homeModel->setUserHomeCurrentPlanet($login['user_id']);

                    // redirect to game
                    Functions::redirect('game.php?page=overview');
                }
            }

            // if login failed
            Functions::redirect('index.php');
        }
    }

    private function buildPage(): void
    {
        $this->page->display(
            $this->template->set(
                'home/index_body',
                array_merge(
                    $this->langs->language,
                    $this->getErrors(),
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
    private function getErrors(): array
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

        return [
            'div_id' => $div_id,
            'message' => $message,
        ];
    }
}
