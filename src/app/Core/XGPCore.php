<?php

namespace App\Core;

use CiLang;
use Exception;

abstract class XGPCore
{
    protected ?CiLang $langs;
    protected static $objects;

    public function __construct()
    {
        $this->setObjectsClass();
    }

    private function setObjectsClass(): void
    {
        self::$objects = new Objects();
    }

    /**
     * Load a language file using CI Library
     *
     * @param string|array $language_file
     */
    protected function loadLang($language_file): void
    {
        try {
            // require langugage library
            $ci_lang_path = XGP_ROOT . LIB_PATH . 'Ci' . DIRECTORY_SEPARATOR . 'CiLang.php';

            if (!file_exists($ci_lang_path)) {
                // not found
                throw new Exception('Language file "' . $language_file . '" not defined');
                return;
            }

            // required by the library
            if (!defined('BASEPATH')) {
                define('BASEPATH', XGP_ROOT . RESOURCES_PATH);
            }

            // use CI library
            require_once $ci_lang_path;

            $this->langs = new CiLang();
            $this->langs->load($language_file, DEFAULT_LANG);
        } catch (Exception $e) {
            die('Fatal error: ' . $e->getMessage());
        }
    }
}
