<?php

class LangManager
{
    private $impl;
    private static $instance;

    public function setImplementation(Lang $implementation)
    {
        $this->impl = $implementation;
    }

    public static function getInstance()
    {
        if (empty(self::$instance))
        {
            self::$instance = new LangManager();
        }
        return self::$instance;
    }

    public function __call($name, $arguments)
    {
        if (empty($this->impl))
        {
            if (empty($arguments))
            {
                return $name;
            }
            return $arguments[0];
        }
        return call_user_func_array(array($this->impl, $name), $arguments);
    }

    public function implementationExist()
    {
        return !empty($this->impl);
    }
}

?>