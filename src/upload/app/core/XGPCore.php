<?php
/**
 * XGPCore
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\core;

use App\libraries\Page;
use App\libraries\UsersLibrary;
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
    protected static $users;
    /**
     * @var mixed
     */
    protected static $objects;
    /**
     * @var mixed
     */
    protected static $page;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->setUsersClass(); // USERS
        $this->setObjectsClass(); // OBJECTS
        $this->setTemplateClass(); // TEMPLATE
    }

    /**
     * setUsersClass
     *
     * @return void
     */
    private function setUsersClass()
    {
        self::$users = new UsersLibrary();
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
     * setTemplateClass
     *
     * @return void
     */
    private function setTemplateClass()
    {
        self::$page = new Page(self::$users);
    }

    /**
     * Load the provided model, support a dir path
     *
     * @param string $class Mandatory field, if not will throw an exception
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function loadModel($class)
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
