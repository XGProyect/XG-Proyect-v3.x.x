<?php
/**
 * XG Proyect
 *
 * Open-source OGame Clon
 *
 * This content is released under the GPL-3.0 License
 *
 * Copyright (c) 2008-2020 XG Proyect
 *
 * @package    XG Proyect
 * @author     XG Proyect Team
 * @copyright  2008-2020 XG Proyect
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0 License
 * @link       https://github.com/XGProyect/
 * @since      Version 3.0.0
 */
namespace App\core;

use CI_Lang;
use Exception;

/**
 * XGPCore Class
 */
abstract class XGPCore
{
    /**
     * @var mixed
     */
    protected static $lang;

    /**
     * @var mixed
     */
    protected static $objects;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->setObjectsClass();
    }

    /**
     * setObjectsClass
     *
     * @return void
     */
    private function setObjectsClass()
    {
        self::$objects = new Objects();
    }

    /**
     * Load a language file using CI Library
     *
     * @param string|array $language_file
     * @return void
     */
    protected function loadLang($language_file): void
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

            $this->langs = new CI_Lang;
            $this->langs->load($language_file, DEFAULT_LANG);
        } catch (Exception $e) {
            die('Fatal error: ' . $e->getMessage());
        }
    }
}
