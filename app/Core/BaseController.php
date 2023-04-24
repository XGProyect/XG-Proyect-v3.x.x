<?php

declare(strict_types=1);

namespace App\Core;

use App\Libraries\Page;
use App\Libraries\Users;
use CiLang;
use Exception;

abstract class BaseController
{
    protected ?Users $userLibrary = null;
    protected ?array $user = [];
    protected ?array $planet = [];
    protected Objects $objects;
    protected ?Page $page = null;
    protected ?Template $template = null;
    protected CiLang $langs;

    public function __construct()
    {
        $this->userLibrary = new Users();
        $this->user = $this->userLibrary->getUserData();
        $this->planet = $this->userLibrary->getPlanetData();

        $this->objects = new Objects();
        $this->page = new Page($this->userLibrary);
        $this->template = new Template();
    }

    /**
     * @param string|array $languageFile
     */
    public function loadLang($languageFile): void
    {
        try {
            // require langugage library
            $langPath = XGP_ROOT . LIB_PATH . 'Ci' . DIRECTORY_SEPARATOR . 'CiLang.php';

            if (!file_exists($langPath)) {
                // not found
                throw new Exception('Language file "' . $languageFile . '" not defined');
                return;
            }

            // required by the library
            if (!defined('BASEPATH')) {
                define('BASEPATH', XGP_ROOT . RESOURCES_PATH);
            }

            // use CI library
            require_once $langPath;

            $this->langs = new CiLang();
            $this->langs->load($languageFile, DEFAULT_LANG);
        } catch (Exception $e) {
            die('Fatal error: ' . $e->getMessage());
        }
    }
}
