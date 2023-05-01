<?php

namespace App\Libraries;

class SecurePageLib
{
    private static $instance = null;

    public function __construct()
    {
        //apply controller to all
        $_GET = array_map([$this, 'validate'], $_GET);
        $_POST = array_map([$this, 'validate'], $_POST);
        $_REQUEST = array_map([$this, 'validate'], $_REQUEST);
        $_SERVER = array_map([$this, 'validate'], $_SERVER);
        $_COOKIE = array_map([$this, 'validate'], $_COOKIE);
    }

    private function validate($value)
    {
        if (!is_array($value)) {
            $value = str_ireplace('script', 'blocked', $value);

            if (version_compare(PHP_VERSION, '7.4.0', '<')) {
                if (get_magic_quotes_gpc()) {
                    $value = htmlentities(stripslashes($value), ENT_QUOTES, 'UTF-8', false);
                } else {
                    $value = htmlentities($value, ENT_QUOTES, 'UTF-8', false);
                }
            }
        } else {
            $c = 0;

            foreach ($value as $val) {
                $value[$c] = $this->validate($val);
                $c++;
            }
        }

        return $value;
    }

    public static function run(): void
    {
        if (self::$instance == null) {
            $c = __CLASS__;
            self::$instance = new $c();
        }
    }
}
