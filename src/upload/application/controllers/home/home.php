<?php
/**
 * Home Controller
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
 * Home Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Home extends Controller
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
     * buildPages
     *
     * @return void
     */
    private function buildPage()
    {
        $parse = $this->langs;

        if ($_POST) {

            $login = $this->_db->queryFetch(
                "SELECT `user_id`, `user_name`, `user_password`, `user_banned`
                FROM " . USERS . "
                WHERE `user_email` = '" . $this->_db->escapeValue($_POST['login']) . "'
                    AND `user_password` = '" . sha1($_POST['pass']) . "'
                LIMIT 1"
            );

            if ($login['user_banned'] <= time()) {
                $this->removeBan($login['user_name']);
            }

            if ($login) {

                // User login
                if (parent::$users->userLogin($login['user_id'], $login['user_name'], $login['user_password'])) {

                    // Update current planet
                    $this->_db->query(
                        "UPDATE " . USERS . " SET
                        `user_current_planet` = `user_home_planet_id`
                        WHERE `user_id` ='" . $login['user_id'] . "'"
                    );

                    // Redirect to game
                    FunctionsLib::redirect('game.php?page=overview');
                }
            }

            // If login fails
            FunctionsLib::redirect('index.php');
        } else {
            $parse['year'] = date('Y');
            $parse['version'] = SYSTEM_VERSION;
            $parse['servername'] = strtr($this->langs['hm_title'], ['%s' => FunctionsLib::readConfig('game_name')]);
            $parse['game_logo'] = FunctionsLib::readConfig('game_logo');
            $parse['forum_url'] = FunctionsLib::readConfig('forum_url');
            $parse['js_path'] = JS_PATH . 'home/';
            $parse['css_path'] = CSS_PATH . 'home/';
            $parse['img_path'] = IMG_PATH . 'home/';
            $parse['base_path'] = SYSTEM_ROOT;
            $parse['extra_js_error'] = '';
            $parse['user_name'] = isset($_GET['character']) ? $_GET['character'] : '';
            $parse['user_email'] = isset($_GET['email']) ? $_GET['email'] : '';

            if (isset($_GET['error']) && $_GET['error'] > 0) {

                switch ($_GET['error']) {

                    case 1:
                        $div_id = '#username';
                        $message = $this->langs['hm_username_not_available'];
                        break;

                    case 2:
                        $div_id = '#email';
                        $message = $this->langs['hm_email_not_available'];
                        break;

                    default:
                        $div_id = '';
                        $message = '';
                        break;
                }

                $parse['extra_js_error'] = '$.validationEngine.buildPrompt("' . $div_id . '", "' . $message . '", "error");';
            }

            parent::$page->display(
                parent::$page->parseTemplate(parent::$page->getTemplate('home/index_body'), $parse), false, '', false
            );
        }
    }

    /**
     * removeBan
     *
     * @param string $user_name User name
     *
     * @return void
     */
    private function removeBan($user_name)
    {
        $this->_db->query(
            "UPDATE " . USERS . " SET
            `user_banned` = '0'
            WHERE `user_name` = '" . $user_name . "' LIMIT 1;"
        );

        $this->_db->query(
            "DELETE FROM " . BANNED . "
            WHERE `banned_who` = '" . $user_name . "'"
        );
    }
}

/* end of home.php */
