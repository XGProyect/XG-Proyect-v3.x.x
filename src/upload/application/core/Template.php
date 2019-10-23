<?php
/**
 * Template
 *
 * PHP Version 7.1+
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\core;

use CI_Parser;

/**
 * XGPCore Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Template
{

    /**
     *
     * @var CI_Parser CodeIgniter Parser Class 
     */
    private $_parserObject = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createNewParser();
    }

    /**
     * Get the template that we'll need
     * 
     * @param string $template Template Name to get
     * 
     * @return string
     */
    public function get($template)
    {
        $route = XGP_ROOT . TEMPLATE_DIR . strtr($template, ['/' => DIRECTORY_SEPARATOR]) . '.php';
        $template = @file_get_contents($route);

        if ($template) { // We got something
            return $template; // Return
        } else {

            // Throw Exception
            die('Template not found or empty: <strong>' . $template_name . '</strong><br />
                Location: <strong>' . $route . '</strong>');
        }
    }

    /**
     * 
     * @param type $template
     * @param type $data
     * @param type $return
     */
    public function set($template, $data, $return = FALSE)
    {
        return $this->_parserObject->parse($this->get($template), $data, $return);
    }

    /**
     * Create a new parser object that we'll need from now on
     * 
     * @return type
     */
    private function createNewParser()
    {
        // require email library
        $parser_library_path = XGP_ROOT . SYSTEM_PATH . 'libraries' . DIRECTORY_SEPARATOR . 'Parser.php';

        if (!file_exists($parser_library_path)) {

            return;
        }

        // required by the library
        if (!defined('BASEPATH')) {

            define('BASEPATH', true);
        }

        // use CI library
        require_once $parser_library_path;

        $this->_parserObject = new CI_Parser();
    }
}

/* end of Controller.php */
