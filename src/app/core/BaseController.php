<?php

declare(strict_types=1);

namespace App\core;

use App\core\Objects;
use App\core\Template;
use App\libraries\Page;
use App\libraries\Users;
use CI_Lang;
use Exception;

/**
 * Controller Class
 */
abstract class BaseController
{
    /**
     * Contains the User object
     *
     * @var User
     */
    protected $userLibrary = null;

    /**
     * Contains the current user data
     *
     * @var array
     */
    protected $user = [];

    /**
     * Contains the current planet data
     *
     * @var array
     */
    protected $planet = [];

    /**
     * Contains the whole set of objects by request
     *
     * @var Objects
     */
    protected $objects;

    /**
     * Contains the Page object
     *
     * @var Page
     */
    protected $page = null;

    /**
     * Contains the Template object
     *
     * @var Template
     */
    protected $template = null;

    /**
     * Constructor
     */
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
     * Will be removed
     *
     * @deprecated since 3.2.0 will be removed on 4.0.0
     *
     * @return array
     */
    protected function getUserData(): array
    {
        return $this->user;
    }

    /**
     * Will be removed
     *
     * @deprecated since 3.2.0 will be removed on 4.0.0
     *
     * @return array
     */
    protected function getPlanetData(): array
    {
        return $this->planet;
    }

    /**
     * Will be removed
     *
     * @deprecated since 3.2.0 will be removed on 4.0.0
     *
     * @return array
     */
    protected function getTemplate(): array
    {
        return $this->template;
    }

    /**
     * Will be removed
     *
     * @deprecated since 3.2.0 will be removed on 4.0.0
     *
     * @return array
     */
    protected function getObjects(): array
    {
        return $this->objects;
    }

    /**
     * Load the provided model, support a dir path
     *
     * @param string $class Mandatory field, if not will throw an exception
     *
     * @return void
     *
     * @throws Exception
     */
    public function loadModel($class)
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
                    $this->{$class_name . '_Model'} = new $class_route();
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
     * Load a language file using CI Library
     *
     * @param string|array $language_file
     * @return void
     */
    public function loadLang($language_file): void
    {
        try {
            // require langugage library
            $ci_lang_path = XGP_ROOT . LIB_PATH . 'Ci' . DIRECTORY_SEPARATOR . 'Lang.php';

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

            $this->langs = new CI_Lang();
            $this->langs->load($language_file, DEFAULT_LANG);
        } catch (Exception $e) {
            die('Fatal error: ' . $e->getMessage());
        }
    }
}
