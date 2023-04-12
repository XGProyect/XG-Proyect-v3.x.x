<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Objects;
use App\Core\Template;
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

    public function loadModel(string $class): void
    {
        try {
            // some validations
            if ((string) $class && $class != '' && !is_null($class)) {
                $class_route = strtolower(substr($class, 0, strrpos($class, '/')));
                $class_name = ucfirst(strtolower(substr($class, strrpos($class, '/') + 1, strlen($class))));
                $model_file = XGP_ROOT . MODELS_PATH . strtolower($class) . '.php';

                // check if the file exists
                if (file_exists($model_file)) {
                    require_once $model_file;

                    $class_route = strtr(MODELS_PATH . $class_route . DIRECTORY_SEPARATOR . $class_name, ['/' => '\\']);
                    $this->{strtolower($class_name) . 'Model'} = new $class_route();
                    return;
                }
            }

            // not found
            throw new Exception('Model not defined');
        } catch (Exception $e) {
            die('Fatal error: ' . $e->getMessage());
        }
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
