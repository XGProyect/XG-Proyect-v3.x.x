<?php

/**
 * XGPCore.
 *
 * PHP Version 5.5+
 *
 * @category Core
 *
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 *
 * @link     http://www.xgproyect.org
 *
 * @version  3.0.0
 */

namespace application\core;

use application\libraries\TemplateLib;
use application\libraries\UsersLib;

/**
 * XGPCore Class.
 *
 * @category Classes
 *
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 *
 * @link     http://www.xgproyect.org
 *
 * @version  3.0.0
 */
abstract class XGPCore
{
    protected static $db;
    protected static $lang;
    protected static $users;
    protected static $objects;
    protected static $page;

    /**
     * __construct.
     */
    public function __construct()
    {
        $this->setDbClass(); // DATABASE
        $this->setLangClass(); // LANGUAGE
        $this->setUsersClass(); // USERS
        $this->setObjectsClass(); // OBJECTS
        $this->setTemplateClass(); // TEMPLATE
    }

    /**
     * setDbClass.
     */
    private function setDbClass()
    {
        require_once XGP_ROOT . '/application/core/Database.php';
        self::$db = new Database();
    }

    /**
     * setLangClass.
     */
    private function setLangClass()
    {
        require_once XGP_ROOT . '/application/core/Language.php';
        $languages  = new Language();
        self::$lang = $languages->lang();
    }

    /**
     * setUsersClass.
     */
    private function setUsersClass()
    {
        require_once XGP_ROOT . '/application/libraries/UsersLib.php';
        self::$users = new UsersLib();
    }

    /**
     * setObjectsClass.
     */
    private function setObjectsClass()
    {
        require_once XGP_ROOT . '/application/core/Objects.php';
        self::$objects = new Objects();
    }

    /**
     * setTemplateClass.
     */
    private function setTemplateClass()
    {
        require_once XGP_ROOT . '/application/libraries/TemplateLib.php';
        self::$page = new TemplateLib(self::$lang, self::$users);
    }
}

/* end of XGPCore.php */
