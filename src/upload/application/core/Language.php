<?php
/**
 * Language
 *
 * PHP Version 5.5+
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\core;

/**
 * Language Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Language
{

    /**
     *
     * @var array
     */
    private $lang;

    /**
     *
     * @var string
     */
    private $lang_extension = 'php';

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $languages_loaded = $this->getFileName();

        if (defined('DEFAULT_LANG')) {

            foreach ($languages_loaded as $load) {

                $route = XGP_ROOT . LANG_PATH . DEFAULT_LANG . '/' . $load . '.' . $this->lang_extension;

                // WE GOT SOMETHING
                if (file_exists($route)) {

                    // GET THE LANGUAGE PACK
                    include $route;
                }
            }

            // WE GOT SOMETHING
            if (!empty($lang)) {

                // SET DATA
                $this->lang = $lang;
            } else {

                // THROW EXCEPTION
                die('Language not found or empty: <strong>' . $load . '</strong><br/>
                    Location: <strong>' . $route . '</strong>');
            }
        }
    }

    /**
     * lang
     *
     * @return array
     */
    public function lang()
    {
        return $this->lang;
    }

    /**
     * getFileName
     *
     * @return array
     */
    private function getFileName()
    {
        if (defined('IN_ADMIN')) {

            $required[] = 'ADMIN';
        }

        if (defined('IN_GAME')) {

            $required[] = 'INGAME';
        }

        if (defined('IN_INSTALL')) {

            $required[] = 'INSTALL';
        }

        if (defined('IN_LOGIN')) {

            $required[] = 'HOME';
        }

        return $required;
    }
}

/* end of Language.php */
