<?php

namespace App\core;

use App\core\Database;
use App\libraries\Functions;

/**
 * Options Class
 */
class Options
{
    /**
     *
     * @var Options
     */
    private static $instance = null;

    /**
     * Contains the model
     *
     * @var Options
     */
    private $sessionsModel;

    /**
     * Constructor
     */
    public function __construct()
    {
        // load model
        $this->optionsModel = Functions::model('core/options');
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
            return $this->optionsModel->getAllOptions();
        } else {
            return $this->optionsModel->getOption($option);
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
            if ($this->optionsModel->writeOption($option, $value)) {
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
            if ($this->optionsModel->deleteOption($option)) {
                return true;
            }
        }

        return false;
    }
}
