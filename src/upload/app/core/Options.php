<?php
/**
 * Options
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\core;

use App\core\Database;

/**
 * Options Class
 */
class Options extends XGPCore
{
    /**
     *
     * @var Options
     */
    private static $instance = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // load model
        parent::loadModel('core/options');
    }

    /**
     * Static function used to istance this class: implements singleton pattern to avoid multiple parsing.
     *
     * @return Options
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            //make new istance of this class and save it to field for next usage
            $class = __class__;
            self::$instance = new $class();
        }

        return self::$instance;
    }

    /**
     * Get the game options, leaving the param $option empty will return all of them
     *
     * @param string $option Option
     *
     * @return mixed
     */
    public function getOptions($option = '')
    {
        if ($option == '') {
            return $this->Options_Model->getAllOptions();
        } else {
            return $this->Options_Model->getOption($option);
        }
    }

    /**
     * Update the option in the database
     *
     * @param string $option Option
     * @param string $value  Value
     *
     * @return boolean
     */
    public function writeOptions($option, $value = '')
    {
        if ($option != '') {
            if ($this->Options_Model->writeOption($option, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Insert a new option into database
     *
     * @param string $option Option
     * @param string $value  Value
     *
     * @return boolean
     */
    public function insertOption($option, $value = '')
    {
        return $this->writeOptions($option, $value);
    }

    /**
     * Delete an option permanently
     *
     * @param string $option Option
     *
     * @return boolean
     */
    public function deleteOption($option)
    {
        if ($option != '') {
            if ($this->Options_Model->deleteOption($option)) {
                return true;
            }
        }

        return false;
    }
}
