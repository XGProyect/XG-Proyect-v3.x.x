<?php
/**
 * XGPCore
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
abstract class Controller extends XGPCore
{

    /**
     * Contains the current user data
     * 
     * @var array 
     */
    private $_current_user = [];

    /**
     * Contains the current planet data
     * 
     * @var array 
     */
    private $_current_planet = [];

    /**
     * Contains the whole set of objects by request
     * 
     * @var array 
     */
    private $_objects = [];

    /**
     * Contains the whole set of language lines
     * 
     * @var array 
     */
    private $_langs = [];

    /**
     *
     * @var type 
     */
    private $_template = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setUserData();
        $this->setPlanetData();
        $this->setObjects();
        $this->setLang();
        $this->setTemplate();
    }

    /**
     * Set the user Data
     */
    private function setUserData()
    {
        $this->_current_user = parent::$users->getUserData();
    }

    /**
     * Set the planet Data
     */
    private function setPlanetData()
    {
        $this->_current_planet = parent::$users->getPlanetData();
    }

    /**
     * Set objects data
     */
    private function setObjects()
    {
        $this->_objects = parent::$objects;
    }

    /**
     * Set languages data
     */
    private function setLang()
    {
        $this->_langs = parent::$lang;
    }

    /**
     * Set languages data
     */
    private function setTemplate()
    {
        $this->_template = new Template();
    }

    /**
     * Return the user data
     * 
     * @return array
     */
    protected function getUserData()
    {
        return $this->_current_user;
    }

    /**
     * Return the planet data
     * 
     * @return Planet
     */
    protected function getPlanetData()
    {
        return $this->_current_planet;
    }

    /**
     * Return the objects data
     * 
     * @return Objects
     */
    protected function getObjects()
    {
        return $this->_objects;
    }

    /**
     * Return the languages
     * 
     * @return array
     */
    protected function getLang()
    {
        return $this->_langs;
    }

    /**
     * 
     * @return type
     */
    protected function getTemplate()
    {
        return $this->_template;
    }
}

/* end of Controller.php */
