<?php
/**
 * XGPCore
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

use application\libraries\TemplateLib;
use application\libraries\Users;

/**
 * XGPCore Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
abstract class XGPCore
{
    protected static $lang;
    protected static $users;
    protected static $objects;
    protected static $page;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->setLangClass(); // LANGUAGE
        $this->setUsersClass(); // USERS
        $this->setObjectsClass(); // OBJECTS
        $this->setTemplateClass(); // TEMPLATE
    }

    /**
     * setLangClass
     *
     * @return void
     */
    private function setLangClass()
    {
        require_once XGP_ROOT. '/application/core/Language.php';
        $languages  = new Language();
        self::$lang = $languages->lang();
    }

    /**
     * setUsersClass
     *
     * @return void
     */
    private function setUsersClass()
    {
        require_once XGP_ROOT . '/application/libraries/users.php';
        self::$users    = new Users();
    }

    /**
     * setObjectsClass
     *
     * @return void
     */
    private function setObjectsClass()
    {
        require_once XGP_ROOT. '/application/core/Objects.php';
        self::$objects  = new Objects();
    }

    /**
     * setTemplateClass
     *
     * @return void
     */
    private function setTemplateClass()
    {
        require_once XGP_ROOT. '/application/libraries/TemplateLib.php';
        self::$page = new TemplateLib(self::$lang, self::$users);
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
            if ((string)$class && $class != '' && !is_null($class)) {
                
                $class_route    = strtolower(substr($class, 0, strrpos($class, '/')));
                $class_name     = ucfirst(strtolower(substr($class, strrpos($class, '/') + 1, strlen($class))));
                $model_file     = XGP_ROOT . MODELS_PATH . strtolower($class) . '.php';

                // check if the file exists
                if (file_exists($model_file)) {

                    require_once $model_file;
                    
                    $class_route                    = strtr(MODELS_PATH . $class_route . '/' . $class_name, ['/' => '\\']);
                    $this->{$class_name . '_Model'} = new $class_route(new Database());
                    return;
                }
            }
            
            // not found
            throw new \Exception('Model not defined');

        } catch (\Exception $e) {

            die('Fatal error: ' . $e->getMessage());
        } 
    }
}

/* end of XGPCore.php */
