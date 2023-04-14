<?php

namespace App\Core;

use App\Models\Core\Options as OptionsModel;

class Options
{
    private static $instance = null;
    private OptionsModel $optionsModel;

    public function __construct()
    {
        $this->optionsModel = new OptionsModel();
    }

    public static function getInstance(): Options
    {
        if (self::$instance == null) {
            //make new istance of this class and save it to field for next usage
            $class = __class__;
            self::$instance = new $class();
        }

        return self::$instance;
    }

    /**
     * @return mixed
     */
    public function getOptions(string $option = '')
    {
        if ($option == '') {
            return $this->optionsModel->getAllOptions();
        } else {
            return $this->optionsModel->getOption($option);
        }
    }

    public function writeOptions(string $option, string $value = ''): bool
    {
        if ($option != '') {
            if ($this->optionsModel->writeOption($option, $value)) {
                return true;
            }
        }

        return false;
    }

    public function insertOption(string $option, string $value = ''): bool
    {
        return $this->writeOptions($option, $value);
    }

    public function deleteOption(string $option): bool
    {
        if ($option != '') {
            if ($this->optionsModel->deleteOption($option)) {
                return true;
            }
        }

        return false;
    }
}
