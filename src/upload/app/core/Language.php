<?php
/**
 * Language
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\core;

use CI_Lang;
use Exception;

/**
 * Language Class
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
     * Constructor
     */
    public function __construct()
    {
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
