<?php

/**
 * Home Controller.
 *
 * PHP Version 5.5+
 *
 * @category Controller
 *
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 *
 * @link     http://www.xgproyect.org
 *
 * @version  3.0.0
 */

namespace application\controllers\home;

use application\core\XGPCore;
use application\libraries\FunctionsLib;

/**
 * Home Class.
 *
 * @category Classes
 *
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 *
 * @link     http://www.xgproyect.org
 *
 * @version  3.0.0
 */
class Home extends XGPCore
{
    /**
     * @var array
     */
    private $langs;

    /**
     * __construct.
     */
    public function __construct()
    {
        parent::__construct();

        $this->langs = parent::$lang;

        $this->buildPage();
    }

    /**
     * __destruct.
     */
    public function __destruct()
    {
        parent::$db->closeConnection();
    }

    /**
     * buildPages.
     */
    private function buildPage()
    {
        $parse = $this->langs;

        if ($_POST) {
            $login = parent::$db->queryFetch(
                'SELECT `user_id`, `user_name`, `user_password`, `user_banned`
                FROM ' . USERS . "
                WHERE `user_name` = '" . parent::$db->escapeValue($_POST['login']) . "'
                AND `user_password` = '" . sha1($_POST['pass']) . "'
                LIMIT 1"
            );

            if ($login['user_banned'] <= time()) {
                $this->removeBan($login['user_name']);
            }

            if ($login) {

                // User login
                if (parent::$users->user_login($login['user_id'], $login['user_name'], $login['user_password'])) {

                    // Update current planet
                    parent::$db->query(
                        'UPDATE ' . USERS . " SET
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
            $parse['year']       = date('Y');
            $parse['version']    = VERSION;
            $parse['servername'] = FunctionsLib::read_config('game_name');
            $parse['game_logo']  = FunctionsLib::read_config('game_logo');
            $parse['forum_url']  = FunctionsLib::read_config('forum_url');
            $parse['js_path']    = JS_PATH . 'home/';
            $parse['css_path']   = CSS_PATH . 'home/';
            $parse['img_path']   = IMG_PATH . 'home/';
            $parse['base_path']  = BASE_PATH;

            parent::$page->display(
                parent::$page->parse_template(parent::$page->get_template('home/index_body'), $parse),
                false,
                '',
                false
            );
        }
    }

    /**
     * removeBan.
     *
     * @param string $user_name User name
     */
    private function removeBan($user_name)
    {
        parent::$db->query(
            'UPDATE ' . USERS . " SET
            `user_banned` = '0'
            WHERE `user_name` = '" . $user_name . "' LIMIT 1;"
        );

        parent::$db->query(
            'DELETE FROM ' . BANNED . "
            WHERE `banned_who` = '" . $user_name . "'"
        );
    }
}

/* end of home.php */
