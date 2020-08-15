<?php
/**
 * Language
 *
 * PHP Version 7.1+
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\core;

use CI_Lang;
use Exception;

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

        if (defined('DEFAULT_LANG') && $languages_loaded) {
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
        $required = [];

        if (defined('IN_GAME')) {
            $required[] = 'INGAME';
        }

        return $required;
    }

    /**
     * Load a language file using CI Library
     *
     * @param string|array $language_file
     * @return void
     */
    public function loadLang($language_file, $return = false)
    {
        try {
            // require langugage library
            $ci_lang_path = XGP_ROOT . SYSTEM_PATH . 'ci3_custom' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'Lang.php';

            if (!file_exists($ci_lang_path)) {
                // not found
                throw new Exception('Language file "' . $language_file . '" not defined');
                return;
            }

            // required by the library
            if (!defined('BASEPATH')) {
                define('BASEPATH', XGP_ROOT . APP_PATH);
            }

            // use CI library
            require_once $ci_lang_path;

            if ($return) {
                $lang = new CI_Lang;
                $lang->load($language_file, DEFAULT_LANG);
                return $lang;
            }

            $this->langs = new CI_Lang;
            $this->langs->load($language_file, DEFAULT_LANG);
        } catch (Exception $e) {
            die('Fatal error: ' . $e->getMessage());
        }
    }
}

/* end of Language.php */
